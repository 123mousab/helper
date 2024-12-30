<?php

namespace App\Filament\Resources\ApprovalChainResource\Pages;

use App\Filament\Resources\ApprovalChainResource;
use App\Models\ApprovalChain;
use App\Models\ApprovalChainStep;
use App\Models\User;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListApprovalChains extends ListRecords
{
    protected static string $resource = ApprovalChainResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $userId = Auth::id();

        $userRole = User::query()->where('id', $userId)->first()->roles->first()->name;

        if ($userRole === 'Default role') {
            return ApprovalChainStep::whereHas('user', function (Builder $query) use ($userId) {});
        }

        return ApprovalChainStep::whereHas('user', function (Builder $query) use ($userId) {
            $query->where('id', $userId);
        });
    }
}
