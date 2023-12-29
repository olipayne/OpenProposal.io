<?php

namespace App\Filament\Proposer\Resources\ProposalResource\Pages;

use App\Filament\Proposer\Resources\ProposalResource;
use Filament\Actions;
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
}
