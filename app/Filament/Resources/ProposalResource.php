<?php

namespace App\Filament\Resources;

use App\Enums\Status;
use App\Filament\Resources\ProposalResource\Pages;
use App\Models\Proposal;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProposalResource extends Resource
{
    protected static ?string $model = Proposal::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereStatus(Status::Pending)->count();
    }

    public static function form(Form $form): Form
    {

        return $form;

    }

    public static function table(Table $table): Table
    {
        return $table
            ->persistFiltersInSession()
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable()
                    ->label('Proposer'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->searchable()
                    ->multiple()
                    ->label('Proposer')
                    ->options(fn (): array => \App\Models\User::all()->pluck('name', 'id')->toArray()),
            ])
            ->filtersTriggerAction(
                fn (Action $action) => $action->button()->label('Filter'))
            ->actions([
            ])
            ->bulkActions([
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Split::make([
                    Section::make([
                        TextEntry::make('title')
                            ->size(TextEntrySize::Large)
                            ->weight(FontWeight::Bold)
                            ->label(false),
                        TextEntry::make('user.name')
                            ->label('Proposer'),
                        TextEntry::make('description')
                            ->markdown()
                            ->prose(),
                    ]),
                    Section::make('Metadata')
                        ->label(false)
                        ->collapsible()
                        ->headerActions([
                            // \Filament\Infolists\Components\Actions::make([
                            \Filament\Infolists\Components\Actions\Action::make('Update')
                                ->icon('heroicon-o-pencil')
                                ->size('sm')
                                ->fillForm(fn (Proposal $record): array => [
                                    'status' => $record->status,
                                    'reviewers' => $record->reviewers->pluck('id')->toArray(),
                                ])
                                ->form([
                                    \Filament\Forms\Components\Select::make('status')
                                        ->required()
                                        ->options(Status::class)
                                        ->enum(Status::class)
                                        ->live()
                                        ->rules([
                                            fn (\Filament\Forms\Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                                $reviewers = $get('reviewers');
                                                // $fail('Reviewers must be selected before moving to review state.');
                                                // dd($reviewers);
                                                // If reviewers list empty, don't allow moving to review state
                                                if ($value == Status::Reviewing && empty($reviewers)) {
                                                    $fail('Reviewers must be selected before moving to review state.');
                                                }

                                            },
                                        ]),
                                    \Filament\Forms\Components\Select::make('reviewers')
                                        ->preload()
                                        ->searchable(['name', 'email'])
                                        ->searchPrompt('Search for reviewers')
                                        ->loadingMessage('Loading reviewers...')
                                        ->noSearchResultsMessage('No reviewers found matching search criteria.')
                                        ->relationship(
                                            name: 'reviewers',
                                            titleAttribute: 'name',
                                            modifyQueryUsing: fn ($query) => $query->where('is_reviewer', true))
                                        ->multiple()
                                        ->label('Reviewers')
                                        ->live()
                                        ->createOptionForm([
                                            Grid::make()
                                                ->schema([
                                                    TextInput::make('name')
                                                        ->disableAutocomplete()
                                                        ->required(),
                                                    TextInput::make('email')
                                                        ->disableAutocomplete()
                                                        ->required()
                                                        ->email(),
                                                    \Filament\Forms\Components\Fieldset::make('Roles')
                                                        ->schema([
                                                            Toggle::make('is_reviewer')
                                                                ->label('Is Reviewer')
                                                                ->default(true),
                                                            Toggle::make('is_default_reviewer')
                                                                ->label('Is Default Reviewer')
                                                                ->rules([
                                                                    // Only allow is_default_reviewer to be true if is_reviewer is true
                                                                    fn (\Filament\Forms\Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                                                        if ($value == true && $get('is_reviewer') == false) {
                                                                            $fail('Cannot be a default reviewer if not a reviewer.');
                                                                        }
                                                                    },
                                                                ])
                                                                ->default(true),
                                                        ]),

                                                ]),

                                        ])
                                        ->hintAction(
                                            \Filament\Forms\Components\Actions\Action::make('addDefaultReviewers')
                                                ->label('Add Default Reviewers')
                                                // Disable if all of the default reviewers are already selected, with $get
                                                ->disabled(fn (\Filament\Forms\Get $get): bool => empty(array_diff(\App\Models\User::whereIsDefaultReviewer(true)->pluck('id')->toArray(), $get('reviewers'))))
                                                ->action(function (\Filament\Forms\Set $set, $state) {
                                                    $defaultReviewers = \App\Models\User::whereIsDefaultReviewer(true)->pluck('id')->toArray();
                                                    // Append default reviewers to existing reviewers
                                                    $set('reviewers', array_merge($state, $defaultReviewers));

                                                })
                                        ),
                                ])
                                ->action(function (array $data, Proposal $record): void {
                                    $record->update($data);
                                    $record->touch();
                                })
                                ->closeModalByClickingAway(false),

                            // ]),
                        ])
                        ->schema([
                            FieldSet::make('Status')
                                ->schema([
                                    TextEntry::make('status')
                                        ->label(false)
                                        ->columnSpanFull()
                                        ->size(TextEntrySize::Large)
                                        ->alignCenter()
                                        ->weight(FontWeight::Bold)
                                        ->color(fn (Proposal $record): string => $record->status->getColor()),
                                ]),

                            Fieldset::make('Reviewers')
                                ->columns(1)
                                ->schema([
                                    RepeatableEntry::make('reviewers')
                                        ->label(false)
                                        ->schema([
                                            TextEntry::make('name')
                                                ->label(false),

                                        ])
                                        ->grid(2),
                                    Actions::make([
                                        \Filament\Infolists\Components\Actions\Action::make('Notify Reviewers')
                                            ->label('Notify Reviewers')
                                            ->icon('heroicon-o-envelope')
                                            ->action(function (\Filament\Forms\Set $set, $state) {
                                                // dd($state);
                                            })
                                            ->closeModalByClickingAway(false),
                                    ])
                                        ->visible(fn (Proposal $record): bool => $record->status == Status::Reviewing)
                                        ->fullWidth(),
                                ]),
                            Fieldset::make('Events')
                                ->schema([
                                    TextEntry::make('created_at')
                                        ->listWithLineBreaks()
                                        ->limitList(5)
                                        ->expandableLimitedList(),
                                    TextEntry::make('updated_at')
                                        ->listWithLineBreaks()
                                        ->limitList(5)
                                        ->expandableLimitedList(),

                                ]),

                        ])->grow(false),
                ])->columnSpanFull()->from('md'),

            ]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProposals::route('/'),
            'create' => Pages\CreateProposal::route('/create'),
            'edit' => Pages\EditProposal::route('/{record}/edit'),
            'view' => Pages\ViewProposal::route('/{record}'),
        ];
    }
}
