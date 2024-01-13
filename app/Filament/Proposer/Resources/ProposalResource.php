<?php

namespace App\Filament\Proposer\Resources;

use App\Filament\Proposer\Resources\ProposalResource\Pages;
use App\Models\Proposal;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
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
                                ->placeholder('Information about the applicant. E.g. Name, Department, Affiliation etc.')
                                // ->minLength(50)
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
                                ->defaultItems(1)
                                ->reorderable(false)
                                ->grid(4)
                                ->minItems(1),
                        ]),
                    Wizard\Step::make('Study Design')
                        ->schema([
                            RichEditor::make('study_background')
                                ->label('Study Background')
                                ->placeholder('Provide a brief background of the study.')
                                // ->minLength(50)
                                ->required(),
                            RichEditor::make('research_question')
                                ->label('Research Question')
                                ->placeholder('Provide the research question that you are trying to answer.')
                                // ->minLength(50)
                                ->required(),
                            RichEditor::make('data_and_population')
                                ->label('Data and Population')
                                ->placeholder('Provide the data and population that you are going to use to answer the research question.')
                                // ->minLength(50)
                                ->required(),

                        ]),
                    Wizard\Step::make('Analysis Plan')
                        ->schema([
                            RichEditor::make('analysis_plan')
                                ->label('Analysis Plan')
                                ->placeholder('Provide the analysis plan that you are going to use to answer the research question.')
                                // ->minLength(50)
                                ->required(),
                            Fieldset::make('Planned Dates')
                                ->schema([
                                    DatePicker::make('start_analysis_date')
                                        ->label('Start Analysis')
                                        ->required(),
                                    DatePicker::make('start_writing_date')
                                        ->label('Start Writing')
                                        ->required(),
                                    DatePicker::make('completion_date')
                                        ->label('Completion')
                                        ->required(),
                                ])
                                ->columns(3),

                        ]),
                ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('publication_title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(function (Proposal $record) {
                        return $record->status->getLabel();
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
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
