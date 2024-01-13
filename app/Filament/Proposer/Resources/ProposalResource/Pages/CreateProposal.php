<?php

namespace App\Filament\Proposer\Resources\ProposalResource\Pages;

use App\Enums\Status;
use App\Filament\Proposer\Resources\ProposalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProposal extends CreateRecord
{
    protected static string $resource = ProposalResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();
        $data['user_id'] = $user ? $user->id : null;

        // Default status is always 'pending'
        $data['status'] = Status::Pending;

        return $data;
    }
}
