<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\PhoneNumber;
use Closure;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->filled('phone')) {
            $request->merge([
                'phone' => PhoneNumber::normalize($request->string('phone')->toString()),
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'unique:users,phone',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (! PhoneNumber::isValid((string) $value)) {
                        $fail('Введите телефон от 7 до 15 цифр: можно с +, пробелами и скобками.');
                    }
                },
            ],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'string', Password::min(4)],
        ]);

        if (! $request->filled('phone') && ! $request->filled('email')) {
            throw ValidationException::withMessages([
                'email' => 'Укажите email или телефон.',
                'phone' => 'Укажите email или телефон.',
            ]);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'],
            'role' => User::ROLE_USER,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
