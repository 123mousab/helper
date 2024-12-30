<?php


use Tests\TestCase;
use App\Models\ApprovalChainStep;
use App\Models\Project;
use App\Models\User;
use App\Filament\Resources\ApprovalChainResource\Pages\ApproveAndForwardAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class ApproveAndForwardActionTest extends TestCase
{
    protected $user;
    protected $project;
    protected $approvalChainStep;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and assign a role
        $this->user = User::factory()->create();
        $role = Role::create(['name' => 'Admin']);
        $this->user->assignRole($role);

        // Create a project
        $this->project = Project::factory()->create();

        // Create an approval chain step
        $this->approvalChainStep = ApprovalChainStep::factory()->create([
            'approval_chain_id' => $this->project->id,
            'user_id' => $this->user->id,
            'step_order' => 1,
            'approved' => false,
        ]);
    }

    /**
     * Test the label, icon, and color of the action.
     */
    public function testActionLabelIconAndColor()
    {
        // Create the action
        $action = ApproveAndForwardAction::make();

        // Test label
        $this->assertEquals('Approve', $action->getLabel());

        // Test icon for unapproved step
        $this->assertEquals('heroicon-o-arrow-right', $action->getIcon($this->approvalChainStep));

        // Test color for unapproved step
        $this->assertEquals('primary', $action->getColor($this->approvalChainStep));

        // Update the step to approved
        $this->approvalChainStep->update(['approved' => true]);

        // Test icon for approved step
        $this->assertEquals('heroicon-o-check', $action->getIcon($this->approvalChainStep));

        // Test color for approved step
        $this->assertEquals('success', $action->getColor($this->approvalChainStep));
    }

    /**
     * Test the disabled state of the action.
     */
    public function testActionDisabledState()
    {
        // Create the action
        $action = ApproveAndForwardAction::make();

        // Test that the action is enabled for the current step and user
        $this->assertFalse($action->isDisabled($this->approvalChainStep));

        // Create another user and step
        $anotherUser = User::factory()->create();
        $anotherStep = ApprovalChainStep::factory()->create([
            'approval_chain_id' => $this->project->id,
            'user_id' => $anotherUser->id,
            'step_order' => 2,
            'approved' => false,
        ]);

        // Test that the action is disabled for a different step or user
        $this->assertTrue($action->isDisabled($anotherStep));
    }

    /**
     * Test the visibility of the action based on the user's role.
     */
    public function testActionVisibility()
    {
        // Create the action
        $action = ApproveAndForwardAction::make();

        // Test that the action is visible for a user with a role other than 'Default role'
        $this->assertTrue($action->isVisible());

        // Assign the 'Default role' to the user
        $defaultRole = Role::create(['name' => 'Default role']);
        $this->user->assignRole($defaultRole);

        // Test that the action is not visible for a user with the 'Default role'
        $this->assertFalse($action->isVisible());
    }

    /**
     * Test the approveAndForwardStep method.
     */
    public function testApproveAndForwardStep()
    {
        // Create the action
        $action = ApproveAndForwardAction::make();

        // Call the approveAndForwardStep method
        $action->approveAndForwardStep($this->approvalChainStep);

        // Verify that the step is approved
        $this->assertTrue($this->approvalChainStep->fresh()->approved);
        $this->assertNotNull($this->approvalChainStep->fresh()->approved_at);

        // Create a second step
        $secondStep = ApprovalChainStep::factory()->create([
            'approval_chain_id' => $this->project->id,
            'user_id' => $this->user->id,
            'step_order' => 2,
            'approved' => false,
        ]);

        // Call the approveAndForwardStep method again
        $action->approveAndForwardStep($secondStep);

        // Verify that the project status is updated when all steps are approved
        $this->assertEquals(2, $this->project->fresh()->status_id);
    }
}
