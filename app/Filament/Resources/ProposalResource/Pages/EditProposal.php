<?php

namespace App\Filament\Resources\ProposalResource\Pages;

use App\Filament\Resources\ProposalResource;
use Filament\Actions;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProposal extends EditRecord
{
    protected static string $resource = ProposalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            // Actions\DeleteAction::make(),
            // ->before(function (DeleteAction $action) {
            //     Notification::make()
            //         ->warning()
            //         ->title('You don\'t have an active subscription!')
            //         ->body('Choose a plan to continue.')
            //         ->persistent()
            //         ->actions([
            //             Action::make('subscribe')
            //                 ->button()
            //                 ->url('/', shouldOpenInNewTab: true),
            //         ])
            //         ->send();

            //     $action->halt();
            // }),
        ];
    }
}
