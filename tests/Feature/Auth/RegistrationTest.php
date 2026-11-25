<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register_with_email_and_simple_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '1234',
            'password_confirmation' => '1234',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => User::ROLE_USER,
        ]);
    }

    public function test_new_users_can_register_with_phone_only(): void
    {
        $response = $this->post('/register', [
            'name' => 'Phone User',
            'phone' => '+7 (999) 123-45-67',
            'password' => '1234',
            'password_confirmation' => '1234',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertDatabaseHas('users', [
            'phone' => '+79991234567',
            'role' => User::ROLE_USER,
        ]);
    }

    public function test_registration_requires_email_or_phone(): void
    {
        $response = $this->post('/register', [
            'name' => 'No Contact',
            'password' => '1234',
            'password_confirmation' => '1234',
        ]);

        $response->assertSessionHasErrors(['email', 'phone']);
        $this->assertGuest();
    }

    public function test_registration_cannot_create_staff_accounts(): void
    {
        $this->post('/register', [
            'name' => 'Fake Admin',
            'email' => 'fake-admin@example.com',
            'role' => User::ROLE_MAIN_ADMIN,
            'password' => '1234',
            'password_confirmation' => '1234',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'fake-admin@example.com',
            'role' => User::ROLE_USER,
        ]);
    }

    public function test_registration_validation_errors_are_in_russian(): void
    {
        $this->post('/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '12',
            'password_confirmation' => '12',
        ])->assertSessionHasErrors([
            'name' => 'Поле имя обязательно для заполнения.',
            'email' => 'Введите корректный адрес email.',
            'password' => 'Пароль должен содержать не менее 4 символов.',
        ]);
    }

    public function test_password_confirmation_error_has_no_double_capital(): void
    {
        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'confirm@example.com',
            'password' => '1234',
            'password_confirmation' => '4321',
        ])->assertSessionHasErrors([
            'password' => 'Поле пароль не совпадает с подтверждением.',
        ]);
    }
}
