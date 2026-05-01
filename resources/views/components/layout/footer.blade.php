<footer class="px-4 py-4 border-t border-border-light">
    <div class="w-full max-w-5xl mx-auto flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 text-xs text-secondary-surface">
        <span>&copy; {{ date('Y') }} notakrista</span>
        <span>{{ $slot ?? __('welcome.footer_copy') }}</span>
    </div>
</footer>
