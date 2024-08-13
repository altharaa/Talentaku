<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherResource\Pages;
use App\Models\Teacher;
use App\Models\Role;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Teachers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Name'),
                TextInput::make('email')
                    ->required()
                    ->email()
                    ->unique(ignorable: fn ($record) => $record)
                    ->label('Email'),
                TextInput::make('identification_number')
                    ->required()
                    ->unique(ignorable: fn ($record) => $record)
                    ->label('Identification Number'),
                TextInput::make('address')
                    ->required()
                    ->label('Address'),
                TextInput::make('place_of_birth')
                    ->required()
                    ->label('Place of Birth'),
                DatePicker::make('birth_date')
                    ->required()
                    ->label('Birth Date'),
                DatePicker::make('joining_year')
                    ->required()
                    ->label('Joining Year'),
                FileUpload::make('photo')
                    ->label('Photo')
                    ->image()
                    ->directory('teachers/photos')
                    ->nullable(),
                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->label('Password'),
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->options(Role::whereIn('name', ['Guru SD', 'Guru KB'])->pluck('name', 'id'))
                    ->required()
                    ->multiple()
                    ->label('Role')
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('identification_number')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('address')->limit(30),
                Tables\Columns\TextColumn::make('birth_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('joining_year')->date()->sortable(),
                Tables\Columns\ImageColumn::make('photo'),
                Tables\Columns\TextColumn::make('roles.name')->wrap(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Teacher Type')
                    ->options([
                        'Guru SD' => 'Guru SD',
                        'Guru KB' => 'Guru KB',
                        'Both' => 'Keduanya',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            function (Builder $query, $role) {
                                if ($role === 'Both') {
                                    return $query->whereHas('roles', function ($q) {
                                        $q->where('name', 'Guru SD');
                                    })->whereHas('roles', function ($q) {
                                        $q->where('name', 'Guru KB');
                                    });
                                } else {
                                    return $query->whereHas('roles', fn ($q) => $q->where('name', $role));
                                }
                            }
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Define your relations here if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeachers::route('/'),
            'create' => Pages\CreateTeacher::route('/create'),
            'edit' => Pages\EditTeacher::route('/{record}/edit'),
        ];
    }
}
