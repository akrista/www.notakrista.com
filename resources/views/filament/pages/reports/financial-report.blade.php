<x-filament-panels::page>
    <div x-data="{ activeSection: 'table' }" class="space-y-6">
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <button @click="activeSection = activeSection === 'table' ? null : 'table'" type="button"
                class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('app.dynamic_view') }}
                    </h3>
                </div>
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 transition-transform"
                    :class="{ 'rotate-180': activeSection === 'table' }" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="activeSection === 'table'" x-collapse>
                <div class="border-t border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-900">
                    {{ $this->table }}
                </div>
            </div>
        </div>

        @if ($metabaseToken && $metabaseInstanceUrl)
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <button @click="activeSection = activeSection === 'dashboard' ? null : 'dashboard'" type="button"
                    class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ __('app.static_view') }}
                        </h3>
                    </div>
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 transition-transform"
                        :class="{ 'rotate-180': activeSection === 'dashboard' }" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="activeSection === 'dashboard'" x-collapse>
                    <div class="border-t border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-900">
                        <div x-data="{
                                        init() {
                                            this.$watch('$store.theme', value => {
                                                if (window.metabaseConfig) {
                                                    window.metabaseConfig.theme.preset = (value === 'dark') ? 'night' : 'light';
                                                }
                                            });
                                        }
                                    }">
                            <metabase-question token="{{ $metabaseToken }}" instance-url="{{ $metabaseInstanceUrl }}"
                                style="min-height: 600px; width: 100%;"></metabase-question>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                window.metabaseConfig = {
                    theme: {
                        preset: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                    },
                    isGuest: true,
                    instanceUrl: '{{ $metabaseInstanceUrl }}'
                };
            </script>
            <script defer src="{{ $metabaseInstanceUrl }}/app/embed.js"></script>
        @endif
    </div>
</x-filament-panels::page>