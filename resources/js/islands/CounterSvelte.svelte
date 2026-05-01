<script>
    let { initialCount = 0 } = $props();
    // svelte-ignore state_referenced_locally
    let count = $state(initialCount);

    function increment() {
        count++;
    }

    function decrement() {
        count--;
    }
</script>

<div
    class="rounded-lg border border-[#e3e3e0] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]"
>
    <div class="mb-4 flex items-center gap-2">
        <div
            class="flex h-8 w-8 items-center justify-center rounded-full bg-[#ff3e00]/10"
        >
            <svg
                class="h-5 w-5 text-[#ff3e00]"
                viewBox="0 0 24 24"
                fill="currentColor"
            >
                <path
                    d="M10.354 21.125a4.44 4.44 0 0 1-4.765-1.767 4.109 4.109 0 0 1-.703-3.107 3.898 3.898 0 0 1 .134-.522l.105-.321.287.21a7.21 7.21 0 0 0 2.186 1.092l.208.063-.02.208a1.253 1.253 0 0 0 .226.83 1.337 1.337 0 0 0 1.435.533 1.231 1.231 0 0 0 .343-.15l8.604-5.389a1.262 1.262 0 0 0 .563-.708 1.279 1.279 0 0 0-.065-.942 1.334 1.334 0 0 0-1.435-.533 1.231 1.231 0 0 0-.343.15l-3.282 2.054a4.108 4.108 0 0 1-1.145.499 4.44 4.44 0 0 1-4.765-1.767 4.109 4.109 0 0 1-.703-3.107 4.118 4.118 0 0 1 1.869-2.361l8.604-5.389a4.108 4.108 0 0 1 1.145-.499 4.44 4.44 0 0 1 4.765 1.767 4.109 4.109 0 0 1 .703 3.107 3.898 3.898 0 0 1-.134.522l-.105.321-.286-.21a7.21 7.21 0 0 0-2.187-1.092l-.208-.063.02-.208a1.253 1.253 0 0 0-.226-.83 1.337 1.337 0 0 0-1.435-.533 1.231 1.231 0 0 0-.343.15L8.62 9.368a1.262 1.262 0 0 0-.563.708 1.279 1.279 0 0 0 .065.942 1.334 1.334 0 0 0 1.435.533 1.231 1.231 0 0 0 .343-.15l3.282-2.054a4.108 4.108 0 0 1 1.145-.499 4.44 4.44 0 0 1 4.765 1.767 4.109 4.109 0 0 1 .703 3.107 4.118 4.118 0 0 1-1.869 2.361l-8.604 5.389a4.108 4.108 0 0 1-1.145.499z"
                />
            </svg>
        </div>
        <h3 class="text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">
            Svelte Island
        </h3>
    </div>

    <div class="mb-4 text-center">
        <div class="text-4xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">
            {count}
        </div>
        <p class="mt-1 text-xs text-[#706f6c] dark:text-[#A1A09A]">
            Current count
        </p>
    </div>

    <div class="flex gap-2">
        <button
            onclick={decrement}
            class="flex-1 rounded-md border border-[#e3e3e0] bg-[#FDFDFC] px-4 py-2 text-sm font-medium text-[#1b1b18] transition-colors hover:bg-[#f5f5f3] dark:border-[#3E3E3A] dark:bg-[#1e1e1c] dark:text-[#EDEDEC] dark:hover:bg-[#252523]"
        >
            Decrement
        </button>
        <button
            onclick={increment}
            class="flex-1 rounded-md bg-[#ff3e00] px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-[#e63900]"
        >
            Increment
        </button>
    </div>
</div>
