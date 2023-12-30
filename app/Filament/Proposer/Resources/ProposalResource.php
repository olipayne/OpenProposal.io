<?php

namespace App\Filament\Proposer\Resources;

use App\Filament\Proposer\Resources\ProposalResource\Pages;
use App\Models\Proposal;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProposalResource extends Resource
{
    protected static ?string $model = Proposal::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Applicant Information')
                        ->schema([
                            RichEditor::make('applicant_info')
                                ->default('lalala')
                                ->required(),
                        ]),
                    Wizard\Step::make('Proposal Information')
                        ->schema([
                            TextInput::make('publication_title')
                                ->label('Publication Title')
                                ->required(),
                            Repeater::make('proposed_authors')
                                ->simple(
                                    TextInput::make('name')
                                        ->label('Name')
                                        ->required()
                                        ->distinct(),
                                )
                                ->label('Proposed Authors')
                                ->addActionLabel('Add Author')
                                ->defaultItems(4)
                                ->reorderable(false)
                                ->grid(4)
                                ->minItems(1),
                        ]),
                    Wizard\Step::make('Research Timeline')
                        ->schema([

                        ]),
                ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('proposal_topic_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('applicant_info')
                    ->searchable(),
                Tables\Columns\TextColumn::make('publication_title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_analysis_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_writing_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('completion_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
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
            'index' => Pages\ListProposals::route('/'),
            'create' => Pages\CreateProposal::route('/create'),
            'edit' => Pages\EditProposal::route('/{record}/edit'),
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\App\Models\Proposal
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }
}
