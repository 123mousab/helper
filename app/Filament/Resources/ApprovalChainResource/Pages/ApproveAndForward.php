<?php

namespace App\Filament\Resources\ApprovalChainResource\Pages;

use App\Filament\Resources\ApprovalChainResource;
use App\Models\ApprovalChainStep;
use App\Models\Project;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class ApproveAndForward extends CreateRecord
{
    protected static string $resource = ApprovalChainStep::class;
}
