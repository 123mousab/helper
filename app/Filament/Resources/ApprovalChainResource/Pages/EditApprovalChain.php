<?php

namespace App\Filament\Resources\ApprovalChainResource\Pages;

use App\Filament\Resources\ApprovalChainResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApprovalChain extends EditRecord
{
    protected static string $resource = ApprovalChainResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
