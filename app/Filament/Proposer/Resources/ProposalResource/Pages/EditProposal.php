<?php

namespace App\Filament\Proposer\Resources\ProposalResource\Pages;

use App\Filament\Proposer\Resources\ProposalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProposal extends EditRecord
{
    protected static string $resource = ProposalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
