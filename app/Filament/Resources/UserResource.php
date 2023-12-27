<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Fieldset::make('Roles')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Toggle::make('is_reviewer')
                            ->label('Reviewer')
                            ->required()
                            ->hint('Reviewers can be assigned to proposals.'),
                        Forms\Components\Toggle::make('is_default_reviewer')
                            ->label('Default Reviewer')
                            ->required()
                            ->hint('Default reviewers are automatically assigned to new proposals.'),
                        Forms\Components\Toggle::make('is_admin')
                            ->label('Admin')
                            ->required()
                            ->hint('Admins can manage users and proposals.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_reviewer')
                    ->label('Reviewer')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_default_reviewer')
                    ->label('Default Reviewer')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_admin')
                    ->label('Admin')
                    ->rules([
                        'not_in:1',
                    ])
                    ->sortable()
                    ->beforeStateUpdated(function ($record, $state) {
                        Notification::make()
                            ->success()
                            ->title("{$record->name} admin status changed")
                            ->duration(1000)
                            ->send();
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_reviewer')->label('Reviewer'),
                TernaryFilter::make('is_default_reviewer')->label('Default Reviewer'),
                TernaryFilter::make('is_admin')->label('Admin'),

            ])
            ->actions([
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
