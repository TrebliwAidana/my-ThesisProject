<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin role
        $adminRole = Role::create([
            'name' => 'System Administrator',
            'abbreviation' => 'SA',
            'desc' => 'Full system access',
            'level' => 1,
            'is_system' => true,
            'is_visible' => true,
        ]);

        // Create admin user
        $this->adminUser = User::factory()->create([
            'role_id' => $adminRole->id,
            'email_verified_at' => now(),
        ]);
    }

    // =========================================================================
    // USERS LIST
    // =========================================================================

    public function test_admin_can_view_users_list(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
        $response->assertViewHas(['users', 'roles', 'stats']);
    }

    public function test_users_list_displays_statistics(): void
    {
        User::factory(5)->create(['is_active' => true, 'email_verified_at' => now()]);
        User::factory(3)->create(['is_active' => false, 'email_verified_at' => null]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.users.index'));

        $response->assertStatus(200);
        $stats = $response->viewData('stats');
        $this->assertNotNull($stats);
        $this->assertGreaterThanOrEqual(5, $stats->total);
    }

    public function test_users_list_can_filter_by_search(): void
    {
        $user = User::factory()->create(['full_name' => 'John Doe']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.users.index', ['search' => 'John']));

        $response->assertStatus(200);
        $users = $response->viewData('users');
        $this->assertTrue($users->contains($user));
    }

    public function test_users_list_can_filter_by_status(): void
    {
        User::factory()->create(['is_active' => true]);
        User::factory()->create(['is_active' => false]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.users.index', ['status' => 'active']));

        $response->assertStatus(200);
    }

    // =========================================================================
    // CREATE USER
    // =========================================================================

    public function test_admin_can_view_create_user_form(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.users.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.create');
        $response->assertViewHas('roles');
    }

    public function test_admin_can_create_user(): void
    {
        $role = Role::factory()->create(['is_visible' => true]);

        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'role_id' => $role->id,
            'gender' => 'Male',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.users.store'), $userData);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'first_name' => 'John',
        ]);
    }

    public function test_admin_cannot_create_user_with_duplicate_email(): void
    {
        $role = Role::factory()->create(['is_visible' => true]);
        User::factory()->create(['email' => 'existing@example.com']);

        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'existing@example.com',
            'role_id' => $role->id,
            'gender' => 'Male',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.users.store'), $userData);

        $response->assertSessionHasErrors('email');
    }

    // =========================================================================
    // EDIT USER
    // =========================================================================

    public function test_admin_can_view_edit_user_form(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.users.edit', $user->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.edit');
    }

    public function test_admin_can_update_user_email(): void
    {
        $user = User::factory()->create(['email' => 'old@example.com']);

        $updateData = [
            'first_name' => 'Updated',
            'last_name' => $user->last_name,
            'email' => 'new@example.com',
            'role_id' => $user->role_id,
            'gender' => $user->gender,
        ];

        $response = $this->actingAs($this->adminUser)
            ->patch(route('admin.users.update', $user->id), $updateData);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'new@example.com',
        ]);
    }

    public function test_admin_cannot_update_user_to_duplicate_email(): void
    {
        $user1 = User::factory()->create(['email' => 'user1@example.com']);
        $user2 = User::factory()->create(['email' => 'user2@example.com']);

        $updateData = [
            'first_name' => $user2->first_name,
            'last_name' => $user2->last_name,
            'email' => 'user1@example.com',
            'role_id' => $user2->role_id,
            'gender' => $user2->gender,
        ];

        $response = $this->actingAs($this->adminUser)
            ->patch(route('admin.users.update', $user2->id), $updateData);

        $response->assertSessionHasErrors('email');
    }

    // =========================================================================
    // DELETE USER
    // =========================================================================

    public function test_admin_can_soft_delete_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.users.destroy', $user->id));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertTrue($user->fresh()->trashed());
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.users.destroy', $this->adminUser->id));

        $response->assertSessionHasErrors();
        $this->assertFalse($this->adminUser->fresh()->trashed());
    }

    public function test_admin_can_restore_deleted_user(): void
    {
        $user = User::factory()->create();
        $user->delete();

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.users.restore', $user->id));

        $response->assertRedirect();
        $this->assertFalse($user->fresh()->trashed());
    }

    // =========================================================================
    // ROLES
    // =========================================================================

    public function test_admin_can_view_roles_list(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.roles.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.roles.index');
        $response->assertViewHas(['roles', 'permissions']);
    }

    public function test_admin_can_create_role(): void
    {
        $roleData = [
            'name' => 'Manager',
            'abbreviation' => 'MNG',
            'desc' => 'Manager role',
            'level' => 3,
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.roles.store'), $roleData);

        $response->assertRedirect(route('admin.roles.index'));
        $this->assertDatabaseHas('roles', ['name' => 'Manager']);
    }

    public function test_admin_can_update_role(): void
    {
        $role = Role::factory()->create(['name' => 'Old Name']);

        $updateData = [
            'name' => 'New Name',
            'abbreviation' => 'NN',
            'desc' => 'Updated',
            'level' => 5,
        ];

        $response = $this->actingAs($this->adminUser)
            ->patch(route('admin.roles.update', $role->id), $updateData);

        $response->assertRedirect(route('admin.roles.index'));
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'New Name',
        ]);
    }
}
