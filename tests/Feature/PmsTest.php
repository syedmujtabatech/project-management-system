<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Client;
use App\Models\Milestone;
use App\Models\TimeLog;
use App\Models\CompanySetting;

class PmsTest extends TestCase
{
    protected function getAdmin(): User
    {
        return User::where('role', 'super_admin')->first();
    }

    // 1. Auth & Login
    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('ApexPMS');
    }

    public function test_quick_login_redirects_to_dashboard(): void
    {
        $response = $this->get('/quick-login/admin');
        $response->assertStatus(302);
        $response->assertRedirect(route('dashboard'));
    }

    // 2. Dashboard
    public function test_dashboard_renders_for_authenticated_admin(): void
    {
        $response = $this->actingAs($this->getAdmin())->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Executive Project Dashboard');
    }

    // 3. Projects
    public function test_projects_index_renders(): void
    {
        $response = $this->actingAs($this->getAdmin())->get('/projects');
        $response->assertStatus(200);
        $response->assertSee('Enterprise Projects Portfolio');
    }

    public function test_projects_create_page_renders(): void
    {
        $response = $this->actingAs($this->getAdmin())->get('/projects/create');
        $response->assertStatus(200);
        $response->assertSee('Initiate New Enterprise Project');
    }

    public function test_project_show_page_renders(): void
    {
        $project = Project::first();
        $response = $this->actingAs($this->getAdmin())->get("/projects/{$project->id}");
        $response->assertStatus(200);
        $response->assertSee($project->code);
    }

    public function test_project_edit_page_renders(): void
    {
        $project = Project::first();
        $response = $this->actingAs($this->getAdmin())->get("/projects/{$project->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Modify Project');
    }

    public function test_project_update_works(): void
    {
        $project = Project::first();
        $response = $this->actingAs($this->getAdmin())->put("/projects/{$project->id}", [
            'title' => $project->title . ' Updated',
            'code' => $project->code,
            'client_id' => $project->client_id,
            'manager_id' => $project->manager_id,
            'category' => $project->category,
            'priority' => 'high',
            'status' => 'in_progress',
            'budget' => 99000,
            'currency' => 'USD',
        ]);
        $response->assertStatus(302);
        $this->assertEquals('high', $project->fresh()->priority);
    }

    // 4. Tasks & Kanban
    public function test_kanban_board_renders(): void
    {
        $response = $this->actingAs($this->getAdmin())->get('/tasks/kanban');
        $response->assertStatus(200);
        $response->assertSee('Agile Kanban Board');
        $response->assertSee('In Progress');
    }

    public function test_tasks_index_renders(): void
    {
        $response = $this->actingAs($this->getAdmin())->get('/tasks');
        $response->assertStatus(200);
        $response->assertSee('Tasks & Deliverables Directory', false);
    }

    public function test_tasks_create_page_renders(): void
    {
        $response = $this->actingAs($this->getAdmin())->get('/tasks/create');
        $response->assertStatus(200);
        $response->assertSee('Create Agile Task');
    }

    public function test_task_show_page_renders(): void
    {
        $task = Task::first();
        $response = $this->actingAs($this->getAdmin())->get("/tasks/{$task->id}");
        $response->assertStatus(200);
        $response->assertSee($task->task_number);
    }

    public function test_task_edit_page_renders(): void
    {
        $task = Task::first();
        $response = $this->actingAs($this->getAdmin())->get("/tasks/{$task->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Modify Task');
    }

    public function test_kanban_ajax_status_update(): void
    {
        $task = Task::first();
        $response = $this->actingAs($this->getAdmin())->postJson("/tasks/{$task->id}/status", [
            'status' => 'done',
        ]);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertEquals('done', $task->fresh()->status);
    }

    public function test_add_comment_to_task(): void
    {
        $task = Task::first();
        $response = $this->actingAs($this->getAdmin())->post("/tasks/{$task->id}/comments", [
            'comment' => 'Automated test suite comment verification.',
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('task_comments', [
            'task_id' => $task->id,
            'comment' => 'Automated test suite comment verification.',
        ]);
    }

    // 5. Team (Fix verification for user's screenshot error: /team/2/edit)
    public function test_team_index_renders(): void
    {
        $response = $this->actingAs($this->getAdmin())->get('/team');
        $response->assertStatus(200);
        $response->assertSee('Engineering Team & Specialists', false);
    }

    public function test_team_create_page_renders(): void
    {
        $response = $this->actingAs($this->getAdmin())->get('/team/create');
        $response->assertStatus(200);
        $response->assertSee('Onboard New Specialist');
    }

    public function test_team_edit_page_renders_without_missing_parameter_exception(): void
    {
        // Specifically testing user 2 (/team/2/edit) as reported in user's screenshot
        $user = User::find(2) ?? User::where('id', '!=', $this->getAdmin()->id)->first();
        $response = $this->actingAs($this->getAdmin())->get("/team/{$user->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Modify Specialist Details');
        $response->assertSee($user->name);
    }

    public function test_team_update_works(): void
    {
        $user = User::find(2) ?? User::where('id', '!=', $this->getAdmin()->id)->first();
        $response = $this->actingAs($this->getAdmin())->put("/team/{$user->id}", [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'phone' => '+1 555-999-8888',
            'job_title' => $user->job_title,
            'department' => $user->department,
            'is_active' => 1,
        ]);
        $response->assertStatus(302);
        $this->assertEquals('+1 555-999-8888', $user->fresh()->phone);
    }

    public function test_team_toggle_active(): void
    {
        $user = User::find(2) ?? User::where('id', '!=', $this->getAdmin()->id)->first();
        $originalStatus = $user->is_active;
        $response = $this->actingAs($this->getAdmin())->post("/team/{$user->id}/toggle");
        $response->assertStatus(302);
        $this->assertEquals(!$originalStatus, $user->fresh()->is_active);
    }

    // 6. Clients CRM
    public function test_clients_index_renders(): void
    {
        $response = $this->actingAs($this->getAdmin())->get('/clients');
        $response->assertStatus(200);
        $response->assertSee('Enterprise Clients');
    }

    public function test_clients_create_page_renders(): void
    {
        $response = $this->actingAs($this->getAdmin())->get('/clients/create');
        $response->assertStatus(200);
        $response->assertSee('Register New Client Company');
    }

    public function test_client_show_page_renders(): void
    {
        $client = Client::first();
        $response = $this->actingAs($this->getAdmin())->get("/clients/{$client->id}");
        $response->assertStatus(200);
        $response->assertSee($client->company_name);
    }

    public function test_client_edit_page_renders(): void
    {
        $client = Client::first();
        $response = $this->actingAs($this->getAdmin())->get("/clients/{$client->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Modify Client Profile');
    }

    // 7. Timesheets
    public function test_timelogs_index_renders(): void
    {
        $response = $this->actingAs($this->getAdmin())->get('/timelogs');
        $response->assertStatus(200);
        $response->assertSee('Engineering Timesheets');
    }

    public function test_timelogs_store_works(): void
    {
        $project = Project::first();
        $response = $this->actingAs($this->getAdmin())->post('/timelogs', [
            'project_id' => $project->id,
            'hours' => 3.5,
            'log_date' => date('Y-m-d'),
            'notes' => 'Automated test time logging verification.',
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('time_logs', [
            'project_id' => $project->id,
            'hours' => 3.5,
            'notes' => 'Automated test time logging verification.',
        ]);
    }

    // 8. Reports
    public function test_reports_index_renders(): void
    {
        $response = $this->actingAs($this->getAdmin())->get('/reports');
        $response->assertStatus(200);
        $response->assertSee('Executive Reports');
    }

    // 9. Settings & Profile
    public function test_settings_index_renders(): void
    {
        $response = $this->actingAs($this->getAdmin())->get('/settings');
        $response->assertStatus(200);
        $response->assertSee('Company Profile');
    }

    public function test_profile_page_renders(): void
    {
        $response = $this->actingAs($this->getAdmin())->get('/profile');
        $response->assertStatus(200);
        $response->assertSee('Account & Profile Settings', false);
    }
}
