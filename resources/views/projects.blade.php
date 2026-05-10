@extends('layouts.app')

@section('title', __('projects.title'))

@push('head')
    <meta name="description" content="{{ __('projects.description') }}">
@endpush

@section('content')
<div class="pt-20 pb-12 sm:pt-24 sm:pb-16 lg:pt-32 lg:pb-24" style="padding-bottom: max(3rem, env(safe-area-inset-bottom));">
    <div class="w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <h1 class="font-display font-extrabold leading-[0.9] sm:leading-[0.85] tracking-tighter text-[clamp(2.5rem,8vw+0.5rem,6rem)] text-primary-surface">
            {{ __('projects.heading') }}
        </h1>
        <p class="mt-2 sm:mt-3 text-base lg:text-lg font-medium text-secondary-surface max-w-lg">
            {{ __('projects.description') }}
        </p>

        <div
            x-data="{
                tab: @js($tab),
                language: @js(request()->query('language', 'all')),
                sort: @js(request()->query('sort', 'stars')),
                limit: @js((int) request()->query('limit', 10)),
                status: @js(request()->query('status', 'all')),
                projectSort: @js(request()->query('project_sort', 'recent')),
                updateUrl() {
                    const params = new URLSearchParams();
                    params.set('tab', this.tab);
                    if (this.language !== 'all') params.set('language', this.language);
                    if (this.sort !== 'stars') params.set('sort', this.sort);
                    if (this.limit !== 10) params.set('limit', this.limit);
                    if (this.status !== 'all') params.set('status', this.status);
                    if (this.projectSort !== 'recent') params.set('project_sort', this.projectSort);
                    window.history.replaceState({}, '', '?' + params.toString());
                },
                handleTabKeydown(e) {
                    const tabs = ['github', 'personal'];
                    const currentIndex = tabs.indexOf(this.tab);
                    if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                        e.preventDefault();
                        this.tab = tabs[(currentIndex + 1) % tabs.length];
                        this.updateUrl();
                        document.getElementById('tab-' + this.tab).focus();
                    } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                        e.preventDefault();
                        this.tab = tabs[(currentIndex - 1 + tabs.length) % tabs.length];
                        this.updateUrl();
                        document.getElementById('tab-' + this.tab).focus();
                    } else if (e.key === 'Home') {
                        e.preventDefault();
                        this.tab = tabs[0];
                        this.updateUrl();
                        document.getElementById('tab-' + this.tab).focus();
                    } else if (e.key === 'End') {
                        e.preventDefault();
                        this.tab = tabs[tabs.length - 1];
                        this.updateUrl();
                        document.getElementById('tab-' + this.tab).focus();
                    }
                }
            }"
            class="mt-6 sm:mt-10"
        >
            {{-- Tab toggle --}}
            <div class="flex gap-1 p-1 rounded-xl bg-subtle w-fit" role="tablist" aria-label="{{ __('projects.tabs.github') }} / {{ __('projects.tabs.personal') }}" @keydown="handleTabKeydown($event)">
                <button
                    type="button"
                    @click="tab = 'github'; updateUrl()"
                    :class="tab === 'github' ? 'bg-surface-light dark:bg-surface-dark shadow-sm text-primary-surface' : 'text-secondary-surface hover:text-primary-surface'"
                    class="px-4 py-2.5 sm:px-5 text-sm font-semibold rounded-lg transition-colors"
                    role="tab"
                    :aria-selected="tab === 'github' ? 'true' : 'false'"
                    aria-controls="panel-github"
                    id="tab-github">
                    {{ __('projects.tabs.github') }}
                </button>
                <button
                    type="button"
                    @click="tab = 'personal'; updateUrl()"
                    :class="tab === 'personal' ? 'bg-surface-light dark:bg-surface-dark shadow-sm text-primary-surface' : 'text-secondary-surface hover:text-primary-surface'"
                    class="px-4 py-2.5 sm:px-5 text-sm font-semibold rounded-lg transition-colors"
                    role="tab"
                    :aria-selected="tab === 'personal' ? 'true' : 'false'"
                    aria-controls="panel-personal"
                    id="tab-personal">
                    {{ __('projects.tabs.personal') }}
                </button>
            </div>

            {{-- GitHub tab --}}
            <div
                x-show="tab === 'github'"
                x-transition
                class="mt-6 sm:mt-8"
                role="tabpanel"
                id="panel-github"
                aria-labelledby="tab-github"
            >

                {{-- Filters bar --}}
                <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-center gap-3 mb-5 sm:mb-6">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-2">
                            <label for="repo-language" class="text-xs font-semibold uppercase tracking-wider text-secondary-surface">{{ __('projects.filters.language') }}</label>
                            <select
                                id="repo-language"
                                x-model="language"
                                @change="window.location.href = '?' + new URLSearchParams({tab: 'github', language: $event.target.value, sort: sort, limit: limit}).toString()"
                                class="rounded-lg bg-[oklch(95%_0.005_65)] dark:bg-[oklch(25%_0.012_65)] border border-border-light px-3 py-2.5 text-sm font-medium text-primary-surface focus:ring-2 focus:ring-brand-primary focus:ring-offset-2 focus:ring-offset-surface-light dark:focus:ring-offset-surface-dark">
                                <option value="all" @if(!request()->query('language') || request()->query('language') === 'all') selected @endif>{{ __('projects.filters.all') }}</option>
                                @foreach($languages as $lang)
                                    <option value="{{ $lang }}" @if(request()->query('language') === $lang) selected @endif>{{ $lang }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center gap-2">
                            <label for="repo-sort" class="text-xs font-semibold uppercase tracking-wider text-secondary-surface">{{ __('projects.filters.sort') }}</label>
                            <select
                                id="repo-sort"
                                x-model="sort"
                                @change="window.location.href = '?' + new URLSearchParams({tab: 'github', language: language, sort: $event.target.value, limit: limit}).toString()"
                                class="rounded-lg bg-[oklch(95%_0.005_65)] dark:bg-[oklch(25%_0.012_65)] border border-border-light px-3 py-2.5 text-sm font-medium text-primary-surface focus:ring-2 focus:ring-brand-primary focus:ring-offset-2 focus:ring-offset-surface-light dark:focus:ring-offset-surface-dark">
                                <option value="stars" @if(!request()->query('sort') || request()->query('sort') === 'stars') selected @endif>{{ __('projects.sort.stars') }}</option>
                                <option value="recent" @if(request()->query('sort') === 'recent') selected @endif>{{ __('projects.sort.recent') }}</option>
                                <option value="name" @if(request()->query('sort') === 'name') selected @endif>{{ __('projects.sort.name') }}</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-2">
                            <label for="repo-limit" class="text-xs font-semibold uppercase tracking-wider text-secondary-surface">{{ __('projects.filters.show') }}</label>
                            <select
                                id="repo-limit"
                                x-model="limit"
                                @change="window.location.href = '?' + new URLSearchParams({tab: 'github', language: language, sort: sort, limit: $event.target.value}).toString()"
                                class="rounded-lg bg-[oklch(95%_0.005_65)] dark:bg-[oklch(25%_0.012_65)] border border-border-light px-3 py-2.5 text-sm font-medium text-primary-surface focus:ring-2 focus:ring-brand-primary focus:ring-offset-2 focus:ring-offset-surface-light dark:focus:ring-offset-surface-dark">
                                <option value="5" @if(request()->query('limit') == 5) selected @endif>5</option>
                                <option value="10" @if(!request()->query('limit') || request()->query('limit') == 10) selected @endif>10</option>
                                <option value="20" @if(request()->query('limit') == 20) selected @endif>20</option>
                                <option value="50" @if(request()->query('limit') == 50) selected @endif>50</option>
                                <option value="all" @if(request()->query('limit') === 'all') selected @endif>{{ __('projects.filters.all') }}</option>
                            </select>
                        </div>
                    </div>

                    <span class="text-xs font-medium text-secondary-surface sm:ml-auto">
                        {{ trans_choice('projects.counts.repositories', $repos->count(), ['count' => $repos->count()]) }}
                    </span>
                </div>

                {{-- Repo list --}}
                @forelse($repos as $repo)
                    <a href="{{ $repo->html_url }}" target="_blank" rel="noopener noreferrer"
                        class="group block py-4 sm:py-5 border-b border-border-light transition-colors hover:bg-[oklch(97%_0.005_65)] dark:hover:bg-[oklch(22%_0.012_65)] -mx-4 px-4"
                        aria-label="{{ $repo->name }} ({{ __('projects.external_link') }})">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 sm:gap-4">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-semibold text-sm sm:text-base text-primary-surface group-hover:text-brand-primary transition-colors truncate">
                                        {{ $repo->name }}
                                    </h3>
                                    @if($repo->language)
                                        <span class="shrink-0 rounded-full bg-brand-primary/15 px-2 py-0.5 sm:px-2.5 text-[10px] sm:text-[11px] font-semibold text-brand-primary">
                                            {{ $repo->language }}
                                        </span>
                                    @endif
                                </div>
                                @if($repo->description)
                                    <p class="mt-1 text-xs sm:text-sm text-secondary-surface break-words line-clamp-2">
                                        {{ $repo->description }}
                                    </p>
                                @endif
                            </div>
                            <div class="flex items-center gap-3 sm:gap-4 text-xs font-medium text-secondary-surface shrink-0">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                    {{ number_format($repo->stargazers_count) }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.99 11L3 15l3.99 4v-3H14v-2H6.99v-3zM21 9l-3.99-4v3H10v2h7.01v3L21 9z"/></svg>
                                    {{ number_format($repo->forks_count) }}
                                </span>
                                @if($repo->last_push_at)
                                    <span class="hidden md:inline">{{ $repo->last_push_at->diffForHumans() }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="py-12 sm:py-16 text-center">
                        <p class="text-base sm:text-lg font-medium text-secondary-surface">{{ __('projects.empty.github') }}</p>
                        <p class="mt-2 text-sm text-secondary-surface">{!! __('projects.empty.github_hint') !!}</p>
                    </div>
                @endforelse
            </div>

            {{-- Personal tab --}}
            <div
                x-show="tab === 'personal'"
                x-cloak
                x-transition
                class="mt-6 sm:mt-8"
                role="tabpanel"
                id="panel-personal"
                aria-labelledby="tab-personal"
            >

                {{-- Filters bar --}}
                <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-center gap-3 mb-5 sm:mb-8">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-2">
                            <label for="project-status" class="text-xs font-semibold uppercase tracking-wider text-secondary-surface">{{ __('projects.filters.status') }}</label>
                            <select
                                id="project-status"
                                x-model="status"
                                @change="window.location.href = '?' + new URLSearchParams({tab: 'personal', status: $event.target.value, project_sort: projectSort}).toString()"
                                class="rounded-lg bg-[oklch(95%_0.005_65)] dark:bg-[oklch(25%_0.012_65)] border border-border-light px-3 py-2.5 text-sm font-medium text-primary-surface focus:ring-2 focus:ring-brand-primary focus:ring-offset-2 focus:ring-offset-surface-light dark:focus:ring-offset-surface-dark">
                                <option value="all" @if(!request()->query('status') || request()->query('status') === 'all') selected @endif>{{ __('projects.filters.all') }}</option>
                                @foreach($statuses as $s)
                                    <option value="{{ $s }}" @if(request()->query('status') === $s) selected @endif>{{ __('projects.statuses.' . $s, ucfirst($s)) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center gap-2">
                            <label for="project-sort" class="text-xs font-semibold uppercase tracking-wider text-secondary-surface">{{ __('projects.filters.sort') }}</label>
                            <select
                                id="project-sort"
                                x-model="projectSort"
                                @change="window.location.href = '?' + new URLSearchParams({tab: 'personal', status: status, project_sort: $event.target.value}).toString()"
                                class="rounded-lg bg-[oklch(95%_0.005_65)] dark:bg-[oklch(25%_0.012_65)] border border-border-light px-3 py-2.5 text-sm font-medium text-primary-surface focus:ring-2 focus:ring-brand-primary focus:ring-offset-2 focus:ring-offset-surface-light dark:focus:ring-offset-surface-dark">
                                <option value="recent" @if(!request()->query('project_sort') || request()->query('project_sort') === 'recent') selected @endif>{{ __('projects.sort.recently_added') }}</option>
                                <option value="name" @if(request()->query('project_sort') === 'name') selected @endif>{{ __('projects.sort.name') }}</option>
                            </select>
                        </div>
                    </div>

                    <span class="text-xs font-medium text-secondary-surface sm:ml-auto">
                        {{ trans_choice('projects.counts.projects', $projects->count(), ['count' => $projects->count()]) }}
                    </span>
                </div>

                {{-- Project list --}}
                @forelse($projects as $project)
                    <div class="group py-5 sm:py-6 border-b border-border-light">
                        <div class="flex items-start gap-3 sm:gap-5">
                            @if($project->icon)
                                <img src="{{ $project->icon }}" alt="{{ $project->name }} icon" width="48" height="48" loading="lazy" class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl object-cover shrink-0" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-brand-primary/15 items-center justify-center shrink-0 hidden">
                                    <span class="text-base sm:text-lg font-bold text-brand-primary">{{ mb_substr($project->name, 0, 1) }}</span>
                                </div>
                            @else
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-brand-primary/15 flex items-center justify-center shrink-0">
                                    <span class="text-base sm:text-lg font-bold text-brand-primary">{{ mb_substr($project->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-semibold text-sm sm:text-base text-primary-surface break-words">
                                        {{ $project->name }}
                                    </h3>
                                    <span class="shrink-0 rounded-full bg-subtle px-2 py-0.5 sm:px-2.5 text-[10px] sm:text-[11px] font-semibold text-secondary-surface">
                                        {{ __('projects.statuses.' . $project->status, ucfirst($project->status)) }}
                                    </span>
                                </div>
                                @if($project->description)
                                    <p class="mt-1 sm:mt-1.5 text-xs sm:text-sm text-secondary-surface break-words line-clamp-2">
                                        {{ $project->description }}
                                    </p>
                                @endif
                                @if(!empty($project->tech_tags))
                                    <div class="mt-1.5 sm:mt-2 flex flex-wrap gap-1 sm:gap-1.5">
                                        @foreach($project->tech_tags as $tag)
                                            <span class="rounded-md bg-subtle px-1.5 py-0.5 sm:px-2 text-[10px] sm:text-[11px] font-medium text-secondary-surface break-all">
                                                {{ $tag }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                                @if($project->url)
                                    <a href="{{ $project->url }}" target="_blank" rel="noopener noreferrer"
                                        class="mt-1.5 sm:mt-2 inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-wider text-brand-primary underline underline-offset-4 decoration-brand-primary/40 transition-colors hover:text-primary-surface hover:decoration-primary-surface"
                                        aria-label="{{ __('projects.visit') }}: {{ $project->name }} ({{ __('projects.external_link') }})">
                                        {{ __('projects.visit') }}
                                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17L17 7"/><path d="M7 7h10v10"/></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-12 sm:py-16 text-center">
                        <p class="text-base sm:text-lg font-medium text-secondary-surface">{{ __('projects.empty.personal') }}</p>
                        <p class="mt-2 text-sm text-secondary-surface">{{ __('projects.empty.personal_hint') }}</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="mt-8 sm:mt-12">
            <a href="/"
                class="text-sm font-semibold uppercase tracking-wider text-secondary-surface underline underline-offset-[6px] decoration-border-light transition-colors hover:text-brand-primary hover:decoration-brand-primary">
                {{ __('projects.back_home') }}
            </a>
        </div>
    </div>
</div>
@endsection
