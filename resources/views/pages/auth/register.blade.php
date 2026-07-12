<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        @if ($teamInvitation)
            <x-team-invitation-alert :invitation="$teamInvitation" :action="__('Register')" />
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Username -->
            <flux:input name="username" :label="__('fields.username')"
                :value="old('username', config('app.env') !== 'production' ? 'devuser' : '')" type="text" required
                autofocus placeholder="johndoe" />

            <div class="grid grid-cols-2 gap-4">
                <!-- First Name -->
                <flux:input name="firstname" :label="__('fields.first_name')"
                    :value="old('firstname', config('app.env') !== 'production' ? 'Developer' : '')" type="text"
                    required placeholder="John" />

                <!-- Last Name -->
                <flux:input name="lastname" :label="__('fields.last_name')"
                    :value="old('lastname', config('app.env') !== 'production' ? 'User' : '')" type="text" required
                    placeholder="Doe" />
            </div>

            <!-- Email Address -->
            <flux:input name="email" :label="__('fields.email_address')"
                :value="old('email', config('app.env') !== 'production' ? 'dev@example.com' : '')" type="email" required
                autocomplete="email" placeholder="email@example.com" />

            <!-- Password -->
            <flux:input name="password" :label="__('Password')"
                :value="config('app.env') !== 'production' ? 'Password123!' : ''" type="password" required
                autocomplete="new-password" :placeholder="__('Password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable />

            <!-- Confirm Password -->
            <flux:input name="password_confirmation" :label="__('Confirm password')"
                :value="config('app.env') !== 'production' ? 'Password123!' : ''" type="password" required
                autocomplete="new-password" :placeholder="__('Confirm password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Create account') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link
                :href="$teamInvitation ? route('login', ['invitation' => $teamInvitation['code']]) : route('login')"
                data-test="team-invitation-login-link" wire:navigate>
                {{ __('app.log_in') }}
            </flux:link>
        </div>
    </div>
</x-layouts::auth>