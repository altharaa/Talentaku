<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MuridKbResource\Pages;
use App\Filament\Resources\MuridKbResource\RelationManagers;
use App\Models\MuridKb;
use App\Models\Role;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class MuridKbResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Murid KB';

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
                    ->directory('profile')
                    ->nullable(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'aktif' => 'Aktif',
                        'non-aktif' => 'Tidak Aktif',
                    ])
                    ->required()
                    ->default('aktif'),
                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->label('Password'),
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->options(Role::whereIn('name', [
                        'Murid SD' => 'Murid SD',
                        'Murid KB' => 'Murid KB'
                    ])->pluck('name', 'id'))
                    ->default( Role::where('name', 'Murid KB')->value('id'))
                    ->required()
                    ->label('Role')
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('username')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('nomor_induk')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('address')->label('Alamat')->limit(30),Tables\Columns\TextColumn::make('birth_date')
                    ->label('Tempat, Tanggal Lahir')
                    ->getStateUsing(function ($record) {
                        return $record->place_of_birth . ', ' . \Carbon\Carbon::parse($record->birth_date)->format('d M Y');
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('joining_year')->label('Tahun Masuk')->date()->sortable(),
                Tables\Columns\ImageColumn::make('photo')
                    ->url(fn ($record) => "https://talentaku.site/image/{$record->photo}")
                    ->width(150)
                    ->height(150),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state === 'aktif' ? 'Aktif' : 'Tidak Aktif')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Peran')
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
            $query->where('name', 'Murid KB');
        });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMuridKb::route('/'),
            'create' => Pages\CreateMuridKb::route('/create'),
            'edit' => Pages\EditMuridKb::route('/{record}/edit'),
        ];
    }
}
