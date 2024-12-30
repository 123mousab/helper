<?php

namespace App\Filament\Resources\ApprovalChainResource\Pages;

use App\Models\ApprovalChainStep;
use App\Models\Project;
use Filament\Tables\Actions\Action;

class ApproveAndForwardAction extends Action
{
    public static function make($name = 'approveAndForward'): static
    {
        return parent::make($name)
            ->label(__('Approve'))
            ->icon(function (ApprovalChainStep $record) {
                return $record->approved ? 'heroicon-o-check' : 'heroicon-o-arrow-right';
            })
            ->color(function (ApprovalChainStep $record) {
                return $record->approved ? 'success' : 'primary';
            })
            ->disabled(function (ApprovalChainStep $record) {
                $currentStep = ApprovalChainStep::where('approval_chain_id', $record->approval_chain_id)
                    ->where('approved', 0)
                    ->orderBy('step_order', 'asc')
                    ->first();

                return !($currentStep && $record->id === $currentStep->id && $record->user_id === auth()->id());
            })
            ->action(function (ApprovalChainStep $record) {
                static::approveAndForwardStep($record);
            });
    }

    protected static function approveAndForwardStep(ApprovalChainStep $step): void
    {
        $step->update(['approved' => 1, 'approved_at' => now()]);

        $nextStep = ApprovalChainStep::where('approval_chain_id', $step->approval_chain_id)
            ->where('step_order', '>', $step->step_order)
            ->orderBy('step_order', 'asc')
            ->first();

        if (!$nextStep) {
            $allStepsApproved = ApprovalChainStep::where('approval_chain_id', $step->approval_chain_id)
                ->where('approved', 0)
                ->doesntExist();

            if ($allStepsApproved) {
                $project = Project::find($step->approvalChain->project_id);
                $project->update(['status_id' => 2]);
            }
        }
    }
}
