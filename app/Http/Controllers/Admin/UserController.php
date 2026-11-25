<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\PhoneNumber;
use App\Support\StrongPassword;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Users/Index', [
            'users' => User::query()
                ->whereIn('role', [User::ROLE_MANAGER, User::ROLE_ADMIN])
                ->latest()
                ->paginate(15),
            'counts' => [
                'managers' => User::where('role', User::ROLE_MANAGER)->count(),
                'admins' => User::where('role', User::ROLE_ADMIN)->count(),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Users/Create', [
            'roles' => [
                ['value' => User::ROLE_MANAGER, 'label' => 'Менеджер'],
                ['value' => User::ROLE_ADMIN, 'label' => 'Администратор'],
            ],
        ]);
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
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
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
            'role' => ['required', 'in:'.User::ROLE_MANAGER.','.User::ROLE_ADMIN],
            'password' => [
                'bail',
                'required',
                'confirmed',
                'string',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $message = StrongPassword::message((string) $value);

                    if ($message) {
                        $fail($message);
                    }
                },
            ],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'],
            'role' => $validated['role'],
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Аккаунт создан.');
    }
}
