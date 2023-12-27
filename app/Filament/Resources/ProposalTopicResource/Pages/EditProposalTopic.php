<?php

namespace App\Filament\Resources\ProposalTopicResource\Pages;

use App\Filament\Resources\ProposalTopicResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProposalTopic extends EditRecord
{
    protected static string $resource = ProposalTopicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
