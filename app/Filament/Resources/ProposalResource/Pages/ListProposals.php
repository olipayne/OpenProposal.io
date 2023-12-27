<?php

namespace App\Filament\Resources\ProposalResource\Pages;

use App\Enums\Status;
use App\Filament\Resources\ProposalResource;
use App\Models\Proposal;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListProposals extends ListRecords
{
    protected static string $resource = ProposalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getDefaultActiveTab(): string
    {
        return 'pending';
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Proposals'),
            'pending' => Tab::make(Status::Pending->getLabel())
                ->modifyQueryUsing(function ($query) {
                    return $query->where('status', Status::Pending);
                })
                ->badge(Proposal::query()->where('status', Status::Pending)->count()),
            'reviewing' => Tab::make(Status::Reviewing->getLabel())
                ->modifyQueryUsing(function ($query) {
                    return $query->where('status', Status::Reviewing);
                }),
            'revising' => Tab::make(Status::Revising->getLabel())
                ->modifyQueryUsing(function ($query) {
                    return $query->where('status', Status::Revising);
                }),
            'approved' => Tab::make(Status::Approved->getLabel())
                ->modifyQueryUsing(function ($query) {
                    return $query->where('status', Status::Approved);
                }),
            'rejected' => Tab::make(Status::Rejected->getLabel())
                ->modifyQueryUsing(function ($query) {
                    return $query->where('status', Status::Rejected);
                }),

        ];
    }
}
