<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApprovalChainResource\Pages;
use App\Filament\Resources\ApprovalChainResource\Pages\ApproveAndForwardAction;
use App\Filament\Resources\ApprovalChainResource\RelationManagers;
use App\Models\ApprovalChain;
use App\Models\ApprovalChainStep;
use App\Models\Project;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\HtmlString;

class ApprovalChainResource extends Resource
{
    protected static ?string $model = ApprovalChain::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?int $navigationSort = 3;

    protected static function getNavigationLabel(): string
    {
        return __('Approval Chains');
    }

    public static function getPluralLabel(): ?string
    {
        return static::getNavigationLabel();
    }

    protected static function getNavigationGroup(): ?string
    {
        return __('Management');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->columns(4)
                            ->schema([
                                Forms\Components\Select::make('project_id')
                                    ->label(__('Project'))
                                    ->required()
                                    ->unique()
                                    ->options(fn() => Project::all()->pluck('name', 'id')->toArray()),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('approvalChain.project.name')
                    ->label(__('Project name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('approved')
                    ->label(__('Project status'))
                    ->formatStateUsing(fn($record) => new HtmlString('
                        <div class="flex items-center gap-2">
                            <span class="filament-tables-color-column relative flex h-6 w-6 rounded-md"
                                style="background-color: ' . ($record->approved == 1 ? 'green' : 'yellow') . '"></span>
                            <span>' . ($record->approved == 1 ? 'Approved' : 'Not Approved') . '</span>
                        </div>
                    '))
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('project_id')
                    ->label(__('Project'))
                    ->multiple()
                    ->options(fn() => Project::all()->pluck('name', 'id')->toArray()),
            ])
            ->actions([
                ApproveAndForwardAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApprovalChains::route('/'),
            'create' => Pages\CreateApprovalChain::route('/create'),
            'edit' => Pages\EditApprovalChain::route('/{record}/edit'),
        ];
    }

    protected static function approveAndForwardStep(ApprovalChainStep $step): void
    {
        // Approve the current step
        $step->update(['approved' => 1]);

        $nextStep = ApprovalChainStep::where('approval_chain_id', $step->approval_chain_id)
            ->where('step_order', '>', $step->step_order)
            ->orderBy('step_order', 'asc')
            ->first();

        if (!$nextStep) {

            $allStepsApproved = ApprovalChainStep::where('approval_chain_id', $step->approval_chain_id)
                ->where('approved', 0)
                ->doesntExist();

            if ($allStepsApproved) {
                // project for the approval chain that is being approved;
                $project = Project::find($step->approvalChain->project_id);

                // Update the project status to approved
                $project->update(['status_id' => 2]);
            }
        }
    }
}
