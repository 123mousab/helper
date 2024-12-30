<?php

namespace App\Filament\Resources\ApprovalChainResource\Pages;

use App\Filament\Resources\ApprovalChainResource;
use App\Models\ApprovalChainStep;
use App\Models\Project;
use App\Models\ProjectUser;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;

class CreateApprovalChain extends CreateRecord
{
    protected static string $resource = ApprovalChainResource::class;

    public function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            try {
                // Find the project
                $project = Project::find($data['project_id']);

                // Create the approval chain
                $approvalChain = parent::handleRecordCreation($data);

                // Get the ordered users for the project
                $orderedUsers = ProjectUser::query()->where('project_id', $project->id)->get();

                // Create approval chain steps for each user
                $stepOrder = 1;
                foreach ($orderedUsers->pluck('user_id')->unique() as $userId) {
                    $approvalChainStep = new ApprovalChainStep();
                    $approvalChainStep->approval_chain_id = $approvalChain->id;
                    $approvalChainStep->user_id = $userId;
                    $approvalChainStep->step_order = $stepOrder;
                    $approvalChainStep->save();
                    $stepOrder++;
                }

                // Return the approval chain
                return $approvalChain;
            } catch (Exception $e) {
                // Log the error or handle it as needed
                throw $e; // Re-throw the exception to trigger a rollback
            }
        });
    }
}
