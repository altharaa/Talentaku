<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherResource\Pages;
use App\Filament\Resources\TeacherResource\RelationManagers;
use App\Models\Role;
use App\Models\Teacher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Guru';
    
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
                    ->label('Tahun Masuk'),
                Forms\Components\Fieldset::make('Informasi Pribadi')
                    ->schema([
                        Forms\Components\TextInput::make('username')
                            ->required()
                            ->label('Username'),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('Nama Lengkap'),
                        Forms\Components\TextInput::make('address')
                            ->required()
                            ->label('Alamat'),
                        Forms\Components\TextInput::make('place_of_birth')
                            ->required()
                            ->label('Tempat Lahir'),
                        Forms\Components\DatePicker::make('birth_date')
                            ->required()
                            ->label('Tanggal Lahir'),
                    ]),
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->options(Role::whereIn('name', ['Guru SD', 'Guru KB'])->pluck('name', 'id'))
                    ->required()
                    ->multiple()
                    ->label('Peran')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->label('Password'),
                Forms\Components\FileUpload::make('photo')
                    ->label('Photo')
                    ->image()
                    ->directory('profile')
                    ->nullable(),
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
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Peran')
                    ->wrap()
                    ->getStateUsing(function ($record) {
                        return $record->roles->pluck('name')->implode(', ') ?? 'No roles';
                    })
                    ->badge()
                    ->color(function (string $state): string {
                        $roles = explode(', ', $state);

                        if (in_array('Guru SD', $roles) && in_array('Guru KB', $roles)) {
                            return 'warning'; 
                        }
                    
                        if (in_array('Guru SD', $roles)) {
                            return 'primary'; 
                        }
                    
                        if (in_array('Guru KB', $roles)) {
                            return 'info'; 
                        }
                    }),
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
                Tables\Filters\SelectFilter::make('role')
                    ->label('Peran')
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeachers::route('/'),
            'create' => Pages\CreateTeacher::route('/create'),
            'view' => Pages\ViewTeacher::route('/{record}'),
            'edit' => Pages\EditTeacher::route('/{record}/edit'),
        ];
    }
}
