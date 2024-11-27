<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Role;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\TextInput::make('nomor_induk')
                ->required()
                ->unique(ignorable: fn ($record) => $record)
                ->label('Nomor Induk'),
            Forms\Components\DatePicker::make('joining_year')
                ->required()
                ->label('Tanggal Masuk'),
            Forms\Components\Fieldset::make('Data Pribadi Siswa')
                ->schema([
                    Forms\Components\TextInput::make('username')
                    ->required()
                    ->label('Username'),
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->label('Nama Lengkap'),
                    Forms\Components\TextInput::make('place_of_birth')
                        ->required()
                        ->label('Tempat Lahir'),
                    Forms\Components\DatePicker::make('birth_date')
                        ->required()
                        ->label('Tanggal Lahir'),
                    Forms\Components\TextInput::make('address')
                        ->required()
                        ->label('Alamat'),
                ]),
            Forms\Components\FileUpload::make('photo')
                ->label('Photo')
                ->image()
                ->directory('profile')
                ->nullable(),
            Forms\Components\TextInput::make('password')
                ->label('Password')
                ->password()
                // ->placeholder('Enter new password to change')
                ->required(fn (string $context): bool => $context === 'create')
                ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                ->dehydrated(fn ($state) => filled($state)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->url(fn ($record) => "https://talentaku.site/image/{$record->photo}")
                    ->width(150)
                    ->height(150),
                Tables\Columns\TextColumn::make('username')
                    ->label('Username')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nomor_induk')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(30),
                Tables\Columns\TextColumn::make('birth_date')
                    ->label('Tempat, Tanggal Lahir')
                    ->getStateUsing(function ($record) {
                        return $record->place_of_birth . ', ' . \Carbon\Carbon::parse($record->birth_date)->format('d M Y');
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('joining_year')
                    ->label('Tahun Masuk')
                    ->date()
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('d M Y'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state === 'aktif' ? 'Aktif' : 'Tidak Aktif')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'success',
                        'non-aktif' => 'danger',
                    })
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn($record) => StudentResource::getUrl('view', [
                        'role' => request('role'),
                        'record' => $record->id
                    ])),
                Tables\Actions\EditAction::make()
                    ->url(fn($record) => StudentResource::getUrl('edit', [
                        'role' => request('role'),
                        'record' => $record->id
                    ])),
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

    public static function getEloquentQuery(): Builder
    {
        $role = Request::query('role'); 

        return parent::getEloquentQuery()->whereHas('roles', function ($query) use ($role) {
            $query->where('name', $role);
        });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
            'view' => Pages\ViewStudent::route('/{record}'),
        ];
    }
}