<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Get;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->password()
                    ->label('Password')
                    ->placeholder('Kosongkan jika tidak ingin mengubah password')
                    ->dehydrateStateUsing(fn(?string $state): ?string => $state ? Hash::make($state) : null)
                    ->dehydrated(fn($state) => !empty($state)) // hanya update password jika ada isian
                    ->required(fn(string $context) => $context === 'create'),

                TextInput::make('department')
                    ->label('Departemen')
                    ->required(),

                TextInput::make('NIK')
                    ->label('NIK')
                    ->required(),

                Select::make('roles')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload()
                    ->label('Roles')
                    ->required(),

                Select::make('permissions')
                    ->multiple()
                    ->relationship('permissions', 'name')
                    ->preload()
                    ->label('Permissions')
                    ->visible(fn(Get $get) => !in_array('admin', $get('roles') ?? [])), // Sembunyikan jika role admin dipilih

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('department')->label('Departemen')->sortable()->searchable(),
                TextColumn::make('NIK')->searchable(),
                TextColumn::make('roles.name')->badge()->label('Roles')
                    ->color(fn(string $state): string => match ($state) {
                        'admin' => 'warning',
                        'user' => 'success',
                        default => 'secondary',   // fallback warna abu-abu
                    }),
                TextColumn::make('permissions.name')->badge()->label('Permissions')
                    ->color(fn($state) => match ($state) {
                        'view asset' => 'primary',
                        'view stationary' => 'success',
                        'view both' => 'warning',
                        default => 'secondary',
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
