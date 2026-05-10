@extends('layouts.app')

@section('title', __('welcome.title'))

@section('content')
<div class="flex-1 flex items-center pt-20 pb-8 sm:pt-24 sm:pb-12 lg:pt-28 lg:pb-16" style="padding-bottom: max(2rem, env(safe-area-inset-bottom));">
    <div class="w-full max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-fluid">
        <div class="flex flex-col lg:flex-row lg:items-stretch gap-8 lg:gap-12">

            {{-- Left: The Hero --}}
            <div class="flex flex-col justify-center lg:w-[55%] min-w-0">
                <h1 class="font-display font-extrabold leading-[0.9] sm:leading-[0.85] tracking-tighter text-[clamp(2.5rem,10vw+0.5rem,9rem)] text-primary-surface break-words">
                    {!! __('welcome.headline') !!}
                </h1>
                <p class="mt-3 sm:mt-4 text-base lg:text-lg font-medium text-secondary-surface max-w-md break-words">
                    {{ __('welcome.tagline') }}
                </p>

                <div class="mt-6 flex flex-wrap gap-x-5 gap-y-2">
                    <a href="/projects"
                        class="text-sm font-semibold uppercase tracking-wider text-primary-surface underline underline-offset-[6px] decoration-brand-primary decoration-2 transition-colors hover:text-brand-primary">
                        {{ __('welcome.projects') }}
                    </a>
                </div>
            </div>

            {{-- Right: The Details --}}
            <div class="flex flex-col justify-center gap-4 sm:gap-5 lg:gap-6 lg:w-[45%] lg:pl-8 min-w-0">
                <div class="rounded-2xl bg-brand-primary p-5 sm:p-6 lg:p-8">
                    <h2 class="font-display font-bold text-[clamp(1.4rem,3vw+0.3rem,2.5rem)] leading-[0.9] tracking-tight text-primary-surface break-words">
                        {{ __('welcome.lets_talk') }}
                    </h2>
                    <p class="mt-2 sm:mt-3 text-sm font-medium text-[oklch(28%_0.08_65)] max-w-sm break-words">
                        {{ __('welcome.lets_talk_sub') }}
                    </p>
                    <div class="mt-4 sm:mt-5 flex flex-wrap gap-2">
                        <a href="mailto:info@notakrista.com"
                            class="inline-flex items-center gap-2 rounded-lg bg-primary-surface px-4 py-3 text-sm font-semibold text-surface-light transition-colors hover:brightness-110 active:scale-[0.98] focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-brand-primary">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="2" y="4" width="20" height="16" rx="2" />
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                            </svg>
                            <span class="break-words">{{ __('welcome.email') }}</span>
                        </a>
                        <a href="https://github.com/akrista" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 rounded-lg bg-primary-surface px-4 py-3 text-sm font-semibold text-surface-light transition-colors hover:brightness-110 active:scale-[0.98] focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-brand-primary"
                            aria-label="{{ __('welcome.github') }} ({{ __('app.external_link') }})">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0 0 24 12c0-6.63-5.37-12-12-12z" />
                            </svg>
                            <span class="break-words">{{ __('welcome.github') }}</span>
                        </a>
                        <a href="https://linkedin.com/in/akrista" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 rounded-lg bg-primary-surface px-4 py-3 text-sm font-semibold text-surface-light transition-colors hover:brightness-110 active:scale-[0.98] focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-brand-primary"
                            aria-label="{{ __('welcome.linkedin') }} ({{ __('app.external_link') }})">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                            </svg>
                            <span class="break-words">{{ __('welcome.linkedin') }}</span>
                        </a>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-x-3 gap-y-1 text-sm font-medium text-[oklch(35%_0.08_65)]">
                        <a href="https://discordapp.com/users/Akrista#1410/" target="_blank"
                            rel="noopener noreferrer"
                            class="transition-colors hover:text-primary-surface break-words"
                            aria-label="{{ __('welcome.discord') }} ({{ __('app.external_link') }})">{{ __('welcome.discord') }}</a>
                        <span class="text-[oklch(45%_0.08_65)]">&middot;</span>
                        <a href="https://steamcommunity.com/id/akrista" target="_blank" rel="noopener noreferrer"
                            class="transition-colors hover:text-primary-surface break-words"
                            aria-label="{{ __('welcome.steam') }} ({{ __('app.external_link') }})">{{ __('welcome.steam') }}</a>
                        <span class="text-[oklch(45%_0.08_65)]">&middot;</span>
                        <a href="https://instagram.com/notakrista" target="_blank" rel="noopener noreferrer"
                            class="transition-colors hover:text-primary-surface break-words"
                            aria-label="{{ __('welcome.instagram') }} ({{ __('app.external_link') }})">{{ __('welcome.instagram') }}</a>
                        <span class="text-[oklch(45%_0.08_65)]">&middot;</span>
                        <a href="https://x.com/notakrista" target="_blank" rel="noopener noreferrer"
                            class="transition-colors hover:text-primary-surface break-words"
                            aria-label="{{ __('welcome.twitter') }} ({{ __('app.external_link') }})">{{ __('welcome.twitter') }}</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
