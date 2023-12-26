<?php

namespace App\Filament\Resources;

use App\Enums\Status;
use App\Filament\Resources\ProposalResource\Pages;
use App\Models\Proposal;
use App\Models\ProposalComment;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProposalResource extends Resource
{
    protected static ?string $model = Proposal::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Tabs::make('Tabs')
                    ->columnSpanFull()
                    ->tabs([
                        \Filament\Forms\Components\Tabs\Tab::make('Proposal')
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\Select::make('user_id')
                                    ->preload()
                                    ->searchable()
                                    ->relationship('user', 'name')
                                    ->required()
                                    ->label('Proposer'),
                                MarkdownEditor::make('description')
                                    ->required()
                                    ->maxLength(1000)
                                    ->columnSpan('full'),
                            ]),
                        \Filament\Forms\Components\Tabs\Tab::make('Status & Reviewers')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->required()
                                    ->options(Status::class)
                                    ->enum(Status::class)
                                    ->rules([
                                        fn (\Filament\Forms\Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                            $reviewers = $get('reviewers');
                                            // If reviewers list empty, don't allow moving to review state
                                            if ($value == Status::Reviewing->value && empty($reviewers)) {
                                                $fail('Reviewers must be selected before moving to review state.');
                                            }

                                        },
                                    ]),
                                // TODO - limit to reviewers role
                                Forms\Components\Select::make('reviewers')
                                    ->preload()
                                    ->searchable(['name', 'email'])
                                    ->searchPrompt('Search for reviewers')
                                    ->loadingMessage('Loading reviewers...')
                                    ->noSearchResultsMessage('No reviewers found matching search criteria.')
                                    ->relationship(
                                        name: 'reviewers',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->where('is_reviewer', true))
                                    ->multiple()
                                    ->live()
                                    ->label('Reviewers')
                                    ->createOptionForm([
                                        Grid::make()
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->disableAutocomplete()
                                                    ->required(),
                                                Forms\Components\TextInput::make('email')
                                                    ->disableAutocomplete()
                                                    ->required()
                                                    ->email(),
                                                Toggle::make('is_reviewer')
                                                    ->label('Is Reviewer')
                                                    ->default(true),
                                            ]),

                                    ])
                                    ->hintAction(
                                        \Filament\Forms\Components\Actions\Action::make('Email Reviewers')
                                            ->icon('heroicon-o-envelope')
                                            ->action(function (\Filament\Forms\Set $set, $state) {
                                                dd($state);
                                            })

                                    ),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(Status::class),
                SelectFilter::make('user_id')
                    ->searchable()
                    ->label('Proposer')
                    ->options(fn (): array => \App\Models\User::all()->pluck('name', 'id')->toArray()),
            ])
            ->actions([
                ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        // Change the title
            ->schema([
                Tabs::make('Tabs')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Proposal')
                            ->schema([
                                TextEntry::make('title'),
                                TextEntry::make('user.name')
                                    ->label('Proposer'),
                                TextEntry::make('description')
                                    ->markdown(),
                                TextEntry::make('status'),
                            ]),
                        Tab::make('Comments')
                            ->schema([
                                RepeatableEntry::make('proposalComments')
                                    ->label(false)
                                    ->schema([
                                        TextEntry::make('comment')
                                            ->label(fn (ProposalComment $record): string => "{$record->user->name} - {$record->created_at->format('Y-m-d H:i')} ({$record->created_at->diffForHumans()})")
                                            ->markdown(),
                                    ]),
                            ]),
                    ]),
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
