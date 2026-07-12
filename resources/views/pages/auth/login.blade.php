<x-layouts::auth :title="__('app.log_in')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('app.log_in_to_account')" :description="__('app.log_in_description')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        @if ($teamInvitation)
            <x-team-invitation-alert :invitation="$teamInvitation" :action="__('app.log_in')" />
        @endif

        <x-passkey-verify />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address or Username -->
            <flux:input name="email" :label="__('Email address or Username')"
                :value="old('email', config('app.env') !== 'production' ? (config('bizkit.admin_username') ?: config('bizkit.admin_email')) : '')"
                type="text" required autofocus autocomplete="username" placeholder="email@example.com or username" />

            <!-- Password -->
            <div class="relative">
                <flux:input name="password" :label="__('Password')"
                    :value="config('app.env') !== 'production' ? config('bizkit.admin_password') : ''" type="password"
                    required autocomplete="current-password" :placeholder="__('Password')" viewable />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        {{ __('Forgot your password?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('app.log_in') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Don\'t have an account?') }}</span>
            <flux:link
                :href="$teamInvitation ? route('register', ['invitation' => $teamInvitation['code']]) : route('register')"
                data-test="register-link" wire:navigate>
                {{ __('Sign up') }}
            </flux:link>
        </div>
    </div>
</x-layouts::auth>