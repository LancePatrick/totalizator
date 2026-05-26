<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        validator($input, [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],

            'password' => $this->passwordRules(),

            'role' => [
                'required',
                Rule::in(['admin', 'agent', 'player']),
            ],

            'agent_code' => [
                'nullable',
                'string',
                'max:100',
            ],

            'admin_code' => [
                'nullable',
                'string',
                'max:100',
            ],
        ])->validate();

        $role = $input['role'] ?? 'player';

        $agentId = null;
        $agentCode = null;

        /*
        |--------------------------------------------------------------------------
        | Admin Registration
        |--------------------------------------------------------------------------
        | Admin must use the secret code from .env:
        | ADMIN_REGISTER_CODE=meronadmin123
        */

        if ($role === 'admin') {
            $adminRegisterCode = env('ADMIN_REGISTER_CODE');

            if (empty($adminRegisterCode)) {
                throw ValidationException::withMessages([
                    'admin_code' => 'Admin registration code is not configured.',
                ]);
            }

            if (($input['admin_code'] ?? null) !== $adminRegisterCode) {
                throw ValidationException::withMessages([
                    'admin_code' => 'Invalid admin secret code.',
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Agent Registration
        |--------------------------------------------------------------------------
        | Agent gets an automatic unique agent code.
        | Example: AGT-A8K2QZ
        */

        if ($role === 'agent') {
            $agentCode = $this->generateAgentCode();
        }

        /*
        |--------------------------------------------------------------------------
        | Player Registration
        |--------------------------------------------------------------------------
        | Player must enter a valid active agent code.
        */

        if ($role === 'player') {
            if (empty($input['agent_code'])) {
                throw ValidationException::withMessages([
                    'agent_code' => 'Agent code is required for player registration.',
                ]);
            }

            $submittedAgentCode = strtoupper(trim($input['agent_code']));

            $agent = User::query()
                ->where('role', 'agent')
                ->where('agent_code', $submittedAgentCode)
                ->first();

            if (!$agent) {
                throw ValidationException::withMessages([
                    'agent_code' => 'Invalid agent code.',
                ]);
            }

            if (!$agent->is_active) {
                throw ValidationException::withMessages([
                    'agent_code' => 'This agent is currently inactive.',
                ]);
            }

            $agentId = $agent->id;
        }

        return User::create([
            'name' => $input['name'],
            'email' => strtolower($input['email']),
            'password' => Hash::make($input['password']),

            'role' => $role,
            'agent_id' => $agentId,
            'agent_code' => $agentCode,

            'wallet_balance' => 0,
            'is_active' => true,
            'kyc_status' => 'not_submitted',
        ]);
    }

    private function generateAgentCode(): string
    {
        do {
            $code = 'AGT-' . strtoupper(Str::random(6));
        } while (User::where('agent_code', $code)->exists());

        return $code;
    }
}