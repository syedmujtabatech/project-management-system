<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;

class PmsTest extends TestCase
{
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

    public function test_dashboard_renders_for_authenticated_admin(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Executive Project Dashboard');
    }

    public function test_projects_index_renders(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $response = $this->actingAs($admin)->get('/projects');
        $response->assertStatus(200);
        $response->assertSee('Enterprise Projects Portfolio');
    }

    public function test_project_show_page_renders(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $project = Project::first();
        $response = $this->actingAs($admin)->get("/projects/{$project->id}");
        $response->assertStatus(200);
        $response->assertSee($project->code);
    }

    public function test_kanban_board_renders(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $response = $this->actingAs($admin)->get('/tasks/kanban');
        $response->assertStatus(200);
        $response->assertSee('Agile Kanban Board');
        $response->assertSee('In Progress');
    }

    public function test_kanban_ajax_status_update(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $task = Task::first();
        $response = $this->actingAs($admin)->postJson("/tasks/{$task->id}/status", [
            'status' => 'done',
        ]);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertEquals('done', $task->fresh()->status);
    }

    public function test_add_comment_to_task(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $task = Task::first();
        $response = $this->actingAs($admin)->post("/tasks/{$task->id}/comments", [
            'comment' => 'Integration tests verified successfully with automated staging pipeline.',
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('task_comments', [
            'task_id' => $task->id,
            'comment' => 'Integration tests verified successfully with automated staging pipeline.',
        ]);
    }

    public function test_clients_index_renders(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $response = $this->actingAs($admin)->get('/clients');
        $response->assertStatus(200);
        $response->assertSee('Enterprise Clients');
    }

    public function test_timelogs_index_renders(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $response = $this->actingAs($admin)->get('/timelogs');
        $response->assertStatus(200);
        $response->assertSee('Engineering Timesheets');
    }

    public function test_reports_index_renders(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $response = $this->actingAs($admin)->get('/reports');
        $response->assertStatus(200);
        $response->assertSee('Executive Reports');
    }

    public function test_settings_index_renders(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $response = $this->actingAs($admin)->get('/settings');
        $response->assertStatus(200);
        $response->assertSee('Company Profile');
    }
}
