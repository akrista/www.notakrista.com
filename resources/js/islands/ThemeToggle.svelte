<script lang="ts">
    import { onMount } from 'svelte';

    let isDark = false;
    let mounted = false;

    onMount(() => {
        mounted = true;
        const stored = localStorage.getItem('theme');

        if (stored) {
            isDark = stored === 'dark';
        } else {
            isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        }

        applyTheme(isDark);

        window
            .matchMedia('(prefers-color-scheme: dark)')
            .addEventListener('change', (e) => {
                if (!localStorage.getItem('theme')) {
                    applyTheme(e.matches);
                    isDark = e.matches;
                }
            });
    });

    function toggle() {
        isDark = !isDark;
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        applyTheme(isDark);
    }

    function applyTheme(dark: boolean) {
        if (dark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
</script>

<button
    type="button"
    aria-label={isDark ? 'Switch to light mode' : 'Switch to dark mode'}
    on:click={toggle}
    class="theme-toggle"
    class:mounted
>
    <svg
        class="icon icon--sun"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <circle cx="12" cy="12" r="4" />
        <path d="M12 2v2" />
        <path d="M12 20v2" />
        <path d="m4.93 4.93 1.41 1.41" />
        <path d="m17.66 17.66 1.41 1.41" />
        <path d="M2 12h2" />
        <path d="M20 12h2" />
        <path d="m6.34 17.66-1.41 1.41" />
        <path d="m19.07 4.93-1.41 1.41" />
    </svg>
    <svg
        class="icon icon--moon"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
    </svg>
</button>

<style>
    .theme-toggle {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border: none;
        border-radius: 50%;
        background: transparent;
        cursor: pointer;
        color: var(--color-text-secondary-light);
        transition:
            color var(--duration-fast) var(--ease-out-quart),
            background-color var(--duration-fast) var(--ease-out-quart);
    }

    :global(.dark) .theme-toggle {
        color: var(--color-text-secondary-dark);
    }

    .theme-toggle:hover {
        background-color: var(--color-skeleton-light);
        color: var(--color-text-primary-light);
    }

    :global(.dark) .theme-toggle:hover {
        background-color: var(--color-skeleton-dark);
        color: var(--color-text-primary-dark);
    }

    .theme-toggle:active {
        transform: scale(0.92);
    }

    .icon {
        position: absolute;
        width: 20px;
        height: 20px;
        transition:
            opacity var(--duration-normal) var(--ease-out-quart),
            transform var(--duration-normal) var(--ease-out-quart);
    }

    .icon--sun {
        opacity: 0;
        transform: rotate(90deg) scale(0.5);
    }

    .icon--moon {
        opacity: 1;
        transform: rotate(0deg) scale(1);
    }

    :global(.dark) .icon--sun {
        opacity: 1;
        transform: rotate(0deg) scale(1);
    }

    :global(.dark) .icon--moon {
        opacity: 0;
        transform: rotate(-90deg) scale(0.5);
    }
</style>
