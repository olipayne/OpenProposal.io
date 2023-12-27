<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProposalTopicResource\Pages;
use App\Models\ProposalTopic;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProposalTopicResource extends Resource
{
    protected static ?string $model = ProposalTopic::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('members')
                    ->preload()
                    ->relationship('members', 'name', modifyQueryUsing: fn ($query) => $query->where('is_reviewer', true))
                    ->multiple()
                    ->searchable()
                    ->placeholder('Select members'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('members.name')
                    ->label('Members')
                    ->listWithLineBreaks()
                    ->searchable(),
            ])
            ->filters([
                //
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
            'index' => Pages\ListProposalTopics::route('/'),
            'create' => Pages\CreateProposalTopic::route('/create'),
            'edit' => Pages\EditProposalTopic::route('/{record}/edit'),
        ];
    }
}
