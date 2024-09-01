<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MuridSdResource\Pages;
use App\Filament\Resources\MuridSdResource\RelationManagers;
use App\Models\Role;
use App\Models\Student;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class MuridSdResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Murid SD';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('username')
                    ->required()
                    ->label('Username'),
                TextInput::make('name')
                    ->required()
                    ->label('Nama'),
                TextInput::make('nomor_induk')
                    ->required()
                    ->unique(ignorable: fn ($record) => $record)
                    ->label('Nomor Induk'),
                TextInput::make('address')
                    ->required()
                    ->label('Alamat'),
                TextInput::make('place_of_birth')
                    ->required()
                    ->label('Tempat Lahir'),
                DatePicker::make('birth_date')
                    ->required()
                    ->label('Tanggal Lahir'),
                DatePicker::make('joining_year')
                    ->required()
                    ->label('Tanggal Masuk'),
                FileUpload::make('photo')
                    ->label('Photo')
                    ->image()
                    ->directory('students/photos')
                    ->nullable(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'aktif' => 'Aktif',
                        'non-aktif' => 'Tidak Aktif',
                    ])
                    ->required()
                    ->default('aktif'),
                PasswordInput::make('password')
                    ->label('Password')
                    ->placeholder('Enter new password to change')
                    ->required(fn (string $context): bool => $context === 'create')
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state)),
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->options(Role::whereIn('name', ['Murid SD', 'Murid KB'])->pluck('name', 'id'))
                    ->required()
                    ->label('Tipe')
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('username')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('nomor_induk')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('address')->limit(30),
                Tables\Columns\TextColumn::make('birth_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('joining_year')->date()->sortable(),
                Tables\Columns\ImageColumn::make('photo'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state === 'aktif' ? 'Aktif' : 'Tidak Aktif')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->wrap()
                    ->getStateUsing(function ($record) {
                        return $record->roles->pluck('name')->implode(', ') ?? 'No roles';
                    }),
            ])
            ->filters([
                //
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
            //
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->whereHas('roles', function ($query) {
            $query->where('name', 'Murid SD');
        });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMuridSd::route('/'),
            'create' => Pages\CreateMuridSd::route('/create'),
            'edit' => Pages\EditMuridSd::route('/{record}/edit'),
        ];
    }
}
