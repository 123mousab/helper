<?php


// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\ApprovalChainStep;
use App\Filament\Resources\ApprovalChainResource\Pages\CreateApprovalChain;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateApprovalChainTest extends TestCase
{

    protected $createApprovalChain;

    protected function setUp(): void
    {
        parent::setUp();

        // Initialize the CreateApprovalChain class
        $this->createApprovalChain = new CreateApprovalChain();
    }

    /**
     * Test that the approval chain and its steps are created successfully.
     */
    public function testHandleRecordCreation()
    {
        // Arrange: Create a project and some users
        $project = Project::factory()->create();
        $users = ProjectUser::factory()->count(3)->create(['project_id' => $project->id]);

        // Prepare the data for the approval chain
        $data = [
            'project_id' => $project->id,
        ];

        // Act: Call the handleRecordCreation method
        $approvalChain = $this->createApprovalChain->handleRecordCreation($data);

        // Assert: Verify that the approval chain was created
        $this->assertDatabaseHas('approval_chains', [
            'id' => $approvalChain->id,
            'project_id' => $project->id,
        ]);

        // Verify that the correct number of steps were created
        $this->assertCount(3, ApprovalChainStep::where('approval_chain_id', $approvalChain->id)->get());

        // Verify that the steps are in the correct order
        $stepOrder = 1;
        foreach ($users->pluck('user_id')->unique() as $userId) {
            $this->assertDatabaseHas('approval_chain_steps', [
                'approval_chain_id' => $approvalChain->id,
                'user_id' => $userId,
                'step_order' => $stepOrder,
            ]);
            $stepOrder++;
        }
    }

    /**
     * Test that an exception is thrown when the project does not exist.
     */
    public function testHandleRecordCreationWithInvalidProject()
    {
        // Arrange: Use an invalid project ID
        $data = [
            'project_id' => 999, // Non-existent project ID
            // Add other necessary fields for the approval chain
        ];

        // Expect an exception to be thrown
        $this->expectException(Exception::class);

        // Act: Call the handleRecordCreation method
        $this->createApprovalChain->handleRecordCreation($data);

        // Assert: Verify that no approval chain or steps were created
        $this->assertDatabaseCount('approval_chains', 0);
        $this->assertDatabaseCount('approval_chain_steps', 0);
    }

    /**
     * Test that no steps are created when there are no users for the project.
     */
    public function testHandleRecordCreationWithNoUsers()
    {
        // Arrange: Create a project but no users
        $project = Project::factory()->create();

        // Prepare the data for the approval chain
        $data = [
            'project_id' => $project->id,
            // Add other necessary fields for the approval chain
        ];

        // Act: Call the handleRecordCreation method
        $approvalChain = $this->createApprovalChain->handleRecordCreation($data);

        // Assert: Verify that the approval chain was created
        $this->assertDatabaseHas('approval_chains', [
            'id' => $approvalChain->id,
            'project_id' => $project->id,
        ]);

        // Verify that no steps were created
        $this->assertCount(0, ApprovalChainStep::where('approval_chain_id', $approvalChain->id)->get());
    }
}
