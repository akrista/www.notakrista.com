<script setup lang="ts">
import { ref } from 'vue';

const props = defineProps({
    initialCount: {
        type: Number,
        default: 0,
    },
});

const count = ref(props.initialCount);

const increment = () => {
    count.value++;
};

const decrement = () => {
    count.value--;
};
</script>

<template>
    <div
        class="rounded-lg border border-[#e3e3e0] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]"
    >
        <div class="mb-4 flex items-center gap-2">
            <div
                class="flex h-8 w-8 items-center justify-center rounded-full bg-[#42b883]/10"
            >
                <svg
                    class="h-5 w-5 text-[#42b883]"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                >
                    <path
                        d="M24,1.61H14.06L12,5.16,9.94,1.61H0L12,22.39ZM12,14.08,5.16,2.23H9.59L12,6.41l2.41-4.18h4.43Z"
                    />
                </svg>
            </div>
            <h3
                class="text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC]"
            >
                Vue Island
            </h3>
        </div>

        <div class="mb-4 text-center">
            <div class="text-4xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">
                {{ count }}
            </div>
            <p class="mt-1 text-xs text-[#706f6c] dark:text-[#A1A09A]">
                Current count
            </p>
        </div>

        <div class="flex gap-2">
            <button
                @click="decrement"
                class="flex-1 rounded-md border border-[#e3e3e0] bg-[#FDFDFC] px-4 py-2 text-sm font-medium text-[#1b1b18] transition-colors hover:bg-[#f5f5f3] dark:border-[#3E3E3A] dark:bg-[#1e1e1c] dark:text-[#EDEDEC] dark:hover:bg-[#252523]"
            >
                Decrement
            </button>
            <button
                @click="increment"
                class="flex-1 rounded-md bg-[#42b883] px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-[#35a372]"
            >
                Increment
            </button>
        </div>
    </div>
</template>
