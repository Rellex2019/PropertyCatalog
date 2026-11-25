<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_users_cannot_open_admin_panel(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_main_admin_can_open_user_pages(): void
    {
        $mainAdmin = User::factory()->mainAdmin()->create();

        $this->actingAs($mainAdmin)
            ->get(route('admin.users.index'))
            ->assertOk();

        $this->actingAs($mainAdmin)
            ->get(route('admin.users.create'))
            ->assertOk();
    }

    public function test_managers_cannot_manage_accounts(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->get(route('admin.users.index'))
            ->assertForbidden();

        $this->actingAs($manager)
            ->get(route('admin.users.create'))
            ->assertForbidden();

        $this->actingAs($manager)
            ->post(route('admin.users.store'), [
                'name' => 'Forbidden User',
                'email' => 'forbidden@example.com',
                'role' => User::ROLE_ADMIN,
                'password' => 'StrongPass123!',
                'password_confirmation' => 'StrongPass123!',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'forbidden@example.com']);
    }

    public function test_main_admin_can_create_manager_with_strong_password(): void
    {
        $mainAdmin = User::factory()->mainAdmin()->create();

        $response = $this->actingAs($mainAdmin)->post(route('admin.users.store'), [
            'name' => 'New Manager',
            'email' => 'manager@example.com',
            'phone' => '+7 999 111-22-33',
            'role' => User::ROLE_MANAGER,
            'password' => 'StrongPass123!',
            'password_confirmation' => 'StrongPass123!',
        ]);

        $response->assertRedirect(route('admin.users.index', absolute: false));
        $this->assertDatabaseHas('users', [
            'email' => 'manager@example.com',
            'phone' => '+79991112233',
            'role' => User::ROLE_MANAGER,
        ]);
    }

    public function test_weak_staff_password_is_rejected(): void
    {
        $mainAdmin = User::factory()->mainAdmin()->create();

        $response = $this->actingAs($mainAdmin)->post(route('admin.users.store'), [
            'name' => 'Weak Manager',
            'email' => 'weak@example.com',
            'role' => User::ROLE_MANAGER,
            'password' => '1234',
            'password_confirmation' => '1234',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'Пароль не соответствует требованиям: минимум 12 символов; заглавная и строчная буквы; хотя бы один специальный символ.',
        ]);
        $this->assertDatabaseMissing('users', ['email' => 'weak@example.com']);
    }

    public function test_password_rule_messages_are_in_russian(): void
    {
        $this->assertSame('Пароль должен содержать заглавную и строчную буквы.', __('validation.password.mixed'));
        $this->assertSame('Пароль должен содержать хотя бы одну цифру.', __('validation.password.numbers'));
        $this->assertSame('Пароль должен содержать хотя бы один специальный символ.', __('validation.password.symbols'));
    }

    public function test_pagination_labels_are_in_russian(): void
    {
        $this->assertSame('Назад', __('pagination.previous'));
        $this->assertSame('Вперёд', __('pagination.next'));
    }
}
