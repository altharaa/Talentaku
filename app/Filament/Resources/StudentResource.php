<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Models\Student;
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

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Students';

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
                    ->directory('students/photos')
                    ->nullable(),
                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->label('Password'),
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->options(Role::whereIn('name', ['Murid SD', 'Murid KB'])->pluck('name', 'id'))
                    ->required()
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
                // You can add filters here if needed
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
