<x-layouts::guest>
    <div class="w-full flex flex-col lg:flex-row gap-12 lg:gap-16 items-start">
        <!-- LEFT COLUMN: Identity, Contact & Bio -->
        <section class="w-full lg:w-5/12 flex flex-col gap-8">
            <!-- Hero Header -->
            <div class="flex flex-col gap-4">
                <h1 class="text-display text-[var(--ink)]">Jorge Thomas</h1>

                <!-- Dynamic Subheading -->
                <div class="h-8 flex items-center">
                    <p class="text-mono text-[var(--muted)] flex gap-1.5 items-center">
                        <span x-text="language === 'en' ? 'a software engineer ' : 'un ingeniero de software '"></span>
                        <span x-text="currentAttribute"
                            class="text-[var(--primary)] border-b border-[var(--primary)] font-semibold transition-all duration-200 inline-block"></span>
                    </p>
                </div>
            </div>

            <!-- Status Indicator & Elevate Get in Touch CTA -->
            <div class="flex flex-col gap-4">
                <div>
                    <span class="badge-chip bg-[var(--surface-raised)] text-[var(--ink)] border border-[var(--border)]">
                        <span class="w-2.5 h-2.5 rounded-full bg-[var(--accent)] inline-block"></span>
                        <span class="text-xs text-[var(--ink)] font-mono"
                            x-text="language === 'en' ? 'Available for projects' : 'Disponible para proyectos'"></span>
                    </span>
                </div>

                <!-- Bio Section -->
                <div class="flex flex-col gap-4">
                    <p class="text-body text-[var(--muted)]">
                        <span x-show="language === 'en'">
                            I build reliable, production-grade web applications with Laravel, Livewire, Filament, and
                            Tailwind CSS, prioritizing clean architectures, database performance, and highly intuitive
                            interfaces.
                        </span>
                        <span x-show="language === 'es'">
                            Desarrollo aplicaciones confiables de nivel de producción con Laravel, Livewire, Filament y
                            Tailwind CSS, priorizando las arquitecturas limpias, el rendimiento de base de datos e
                            interfaces intuitivas.
                        </span>
                    </p>
                    <p class="text-body text-[var(--muted)]">
                        <span x-show="language === 'en'">
                            This website serves as my digital workbench, showcasing my professional work, side-project
                            sandboxes, and the real-life activities backing up the code.
                        </span>
                        <span x-show="language === 'es'">
                            Este sitio web sirve como mi banco de trabajo digital, mostrando mi labor profesional,
                            entornos de prueba de proyectos secundarios y las actividades de la vida real que respaldan
                            el código.
                        </span>
                    </p>
                </div>

                <!-- Recruiter CTA / Contact Channels (Moved Up) -->
                <div x-data="{ showMoreContacts: false }"
                    class="card-bench flex flex-col gap-4 border border-[var(--border)]">
                    <h2 class="text-title text-[var(--ink)]" x-text="language === 'en' ? 'Get in Touch' : 'Contacto'">
                    </h2>

                    <!-- Prominent Primary Email CTA -->
                    <a href="mailto:info@notakrista.com"
                        class="button-cta bg-[var(--primary)] text-[var(--primary-ink)] focus-ring-signature font-bold text-center py-3 w-full transition-all hover:opacity-90 flex items-center justify-center gap-2">
                        <svg class="w-4.5 h-4.5 fill-current shrink-0" viewBox="0 0 24 24">
                            <path
                                d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                        </svg>
                        <span x-text="language === 'en' ? 'SEND EMAIL' : 'ENVIAR CORREO'"></span>
                    </a>

                    <!-- Top Secondary Channels -->
                    <div class="grid grid-cols-2 gap-3 border-t border-[var(--border)] pt-4">
                        <a href="https://github.com/akrista" target="_blank"
                            class="interactive-link focus-ring-signature font-mono text-[var(--accent)] hover:underline text-xs inline-flex items-center gap-1.5 justify-center py-2 border border-[var(--border)] rounded-md hover:bg-[var(--surface-raised)] transition-all">
                            <svg class="w-3.5 h-3.5 fill-current shrink-0" viewBox="0 0 24 24">
                                <path
                                    d="M12 2A10 10 0 0 0 2 12c0 4.42 2.87 8.17 6.84 9.5.5.08.66-.23.66-.5v-1.69c-2.77.6-3.36-1.34-3.36-1.34-.46-1.16-1.11-1.47-1.11-1.47-.9-.62.07-.6.07-.6 1 .07 1.53 1.03 1.53 1.03.9 1.52 2.34 1.07 2.91.83.09-.65.35-1.09.63-1.34-2.22-.25-4.55-1.11-4.55-4.92 0-1.11.38-2 1.03-2.71-.1-.25-.45-1.29.1-2.64 0 0 .84-.27 2.75 1.02.79-.22 1.65-.33 2.5-.33.85 0 1.71.11 2.5.33 1.91-1.29 2.75-1.02 2.75-1.02.55 1.35.2 2.39.1 2.64.65.71 1.03 1.6 1.03 2.71 0 3.82-2.34 4.66-4.57 4.91.36.31.69.92.69 1.85V21c0 .27.16.59.67.5C19.14 20.16 22 16.42 22 12A10 10 0 0 0 12 2z" />
                            </svg>
                            <span>GITHUB ↗</span>
                        </a>
                        <a href="https://linkedin.com/in/akrista" target="_blank"
                            class="interactive-link focus-ring-signature font-mono text-[var(--accent)] hover:underline text-xs inline-flex items-center gap-1.5 justify-center py-2 border border-[var(--border)] rounded-md hover:bg-[var(--surface-raised)] transition-all">
                            <svg class="w-3.5 h-3.5 fill-current shrink-0" viewBox="0 0 24 24">
                                <path
                                    d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z" />
                            </svg>
                            <span>LINKEDIN ↗</span>
                        </a>
                    </div>

                    <!-- Collapsible Trigger -->
                    <div class="text-center">
                        <button type="button" @click="showMoreContacts = !showMoreContacts"
                            class="font-mono text-xs text-[var(--muted)] hover:text-[var(--ink)] focus-ring-signature py-1 px-2 rounded hover:bg-[var(--surface-raised)] transition-all"
                            x-text="showMoreContacts ? (language === 'en' ? '[- Hide Channels]' : '[- Ocultar Canales]') : (language === 'en' ? '[+ More Channels]' : '[+ Más Canales]')"></button>
                    </div>

                    <!-- Additional Channels (Collapsible) -->
                    <div x-show="showMoreContacts" x-transition
                        class="grid grid-cols-3 gap-2 border-t border-[var(--border)] pt-3">
                        <a href="https://wa.me/584142034875" target="_blank"
                            class="interactive-link focus-ring-signature font-mono text-[var(--accent)] hover:underline text-[11px] inline-flex items-center gap-1.5 justify-center py-1.5 border border-[var(--border)] rounded-md hover:bg-[var(--surface-raised)] transition-all">
                            <svg class="w-3 h-3 fill-current shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z" />
                            </svg>
                            <span>WHATSAPP</span>
                        </a>
                        <a href="https://t.me/Akrista" target="_blank"
                            class="interactive-link focus-ring-signature font-mono text-[var(--accent)] hover:underline text-[11px] inline-flex items-center gap-1.5 justify-center py-1.5 border border-[var(--border)] rounded-md hover:bg-[var(--surface-raised)] transition-all">
                            <svg class="w-3 h-3 fill-current shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M9.78 18.65l.28-4.23 7.68-6.92c.34-.31-.07-.46-.52-.19L7.74 13.3 3.64 12c-.88-.25-.89-.86.2-1.3l15.97-6.16c.73-.33 1.43.18 1.15 1.3l-2.72 12.81c-.19.91-.74 1.13-1.5.71L12.6 16.3l-1.99 1.93c-.23.23-.42.42-.83.42z" />
                            </svg>
                            <span>TELEGRAM</span>
                        </a>
                        <a href="https://instagram.com/notakrista" target="_blank"
                            class="interactive-link focus-ring-signature font-mono text-[var(--accent)] hover:underline text-[11px] inline-flex items-center gap-1.5 justify-center py-1.5 border border-[var(--border)] rounded-md hover:bg-[var(--surface-raised)] transition-all">
                            <svg class="w-3 h-3 fill-current shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2m-.2 2A3.6 3.6 0 0 0 4 7.6v8.8C4 18.39 5.61 20 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6C20 5.61 18.39 4 16.4 4H7.6m9.65 1.5a1.25 1.25 0 0 1 1.25 1.25A1.25 1.25 0 0 1 17.25 8 1.25 1.25 0 0 1 16 6.75a1.25 1.25 0 0 1 1.25-1.25M12 7a5 5 0 0 1 5 5 5 5 0 0 1-5 5 5 5 0 0 1-5-5 5 5 0 0 1 5-5m0 2a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3z" />
                            </svg>
                            <span>INSTAGRAM</span>
                        </a>
                        <a href="https://discord.com/users/Akrista#1410" target="_blank"
                            class="interactive-link focus-ring-signature font-mono text-[var(--accent)] hover:underline text-[11px] inline-flex items-center gap-1.5 justify-center py-1.5 border border-[var(--border)] rounded-md hover:bg-[var(--surface-raised)] transition-all">
                            <svg class="w-3 h-3 fill-current shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M19.27 5.33C17.94 4.71 16.5 4.26 15 4a.09.09 0 0 0-.07.03c-.18.33-.39.76-.53 1.09a16.09 16.09 0 0 0-4.8 0c-.14-.34-.35-.76-.54-1.09-.01-.02-.04-.03-.07-.03c-1.5.26-2.93.71-4.27 1.33c-.01 0-.02.01-.03.02c-2.72 4.07-3.47 8.03-3.1 11.95c0 .02.01.04.03.05c1.8 1.32 3.53 2.12 5.24 2.65c.03.01.06 0 .07-.02c.4-.55.76-1.13 1.07-1.74c.02-.04 0-.08-.04-.09c-.57-.22-1.11-.48-1.64-.78c-.04-.02-.04-.08-.01-.11c.11-.08.22-.17.33-.25c.02-.02.05-.02.07-.01c3.44 1.57 7.15 1.57 10.55 0c.02-.01.05-.01.07.01c.11.09.22.17.33.26c.04.03.04.09-.01.11c-.52.31-1.07.56-1.64.78c-.04.01-.05.06-.04.09c.32.61.68 1.19 1.07 1.74c.03.01.06.02.09.01c1.72-.53 3.45-1.33 5.25-2.65c.02-.01.03-.03.03-.05c.44-4.53-.73-8.46-3.1-11.95c-.01-.01-.02-.02-.04-.02zM8.52 14.91c-1.03 0-1.89-.95-1.89-2.12s.84-2.12 1.89-2.12c1.06 0 1.9.96 1.89 2.12c0 1.17-.84 2.12-1.89 2.12zm6.97 0c-1.03 0-1.89-.95-1.89-2.12s.84-2.12 1.89-2.12c1.06 0 1.9.96 1.89 2.12c0 1.17-.83 2.12-1.89 2.12z" />
                            </svg>
                            <span>DISCORD</span>
                        </a>
                        <a href="https://www.youtube.com/@notakrista" target="_blank"
                            class="interactive-link focus-ring-signature font-mono text-[var(--accent)] hover:underline text-[11px] inline-flex items-center gap-1.5 justify-center py-1.5 border border-[var(--border)] rounded-md hover:bg-[var(--surface-raised)] transition-all">
                            <svg class="w-3 h-3 fill-current shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M10 15l5.19-3L10 9v6m11.56-7.83c.13.47.22 1.1.28 1.9c.07.8.1 1.49.1 2.09L22 12c0 2.19-.16 3.8-.44 4.83c-.25.9-.83 1.48-1.73 1.73c-.47.13-1.33.22-2.65.28c-1.3.07-2.49.1-3.59.1L12 19c-4.19 0-6.8-.16-7.83-.44c-.9-.25-1.48-.83-1.73-1.73c-.13-.47-.22-1.1-.28-1.9c-.07-.8-.1-1.49-.1-2.09L2 12c0-2.19.16-3.8.44-4.83c.25-.9.83-1.48 1.73-1.73c.47-.13 1.33-.22 2.65-.28c1.3-.07 2.49-.1 3.59-.1L12 5c4.19 0 6.8.16 7.83.44c.9.25 1.48.83 1.73 1.73z" />
                            </svg>
                            <span>YOUTUBE</span>
                        </a>
                        <a href="https://x.com/notakrista" target="_blank"
                            class="interactive-link focus-ring-signature font-mono text-[var(--accent)] hover:underline text-[11px] inline-flex items-center gap-1.5 justify-center py-1.5 border border-[var(--border)] rounded-md hover:bg-[var(--surface-raised)] transition-all">
                            <svg class="w-3 h-3 fill-current shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                            </svg>
                            <span>X</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- RIGHT COLUMN: Relief Support & Directory -->
        <section class="w-full lg:w-7/12 flex flex-col gap-8">
            <!-- VENEZUELA EARTHQUAKE RELIEF SECTION -->
            <div class="alert-box flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-[var(--red)] inline-block"></span>
                    <h3 class="font-mono text-sm uppercase text-[var(--red)] font-semibold"
                        x-text="language === 'en' ? 'Emergency Relief Appeal' : 'Llamado de Emergencia'"></h3>
                </div>
                <p class="text-body text-sm"
                    x-text="language === 'en'
                    ? 'On June 24, 2026, a severe earthquake struck Venezuela, deeply affecting my family and our living situation. I am raising support to cover structural repairs and urgent recovery expenses.'
                    : 'El 24 de junio de 2026, un fuerte terremoto sacudió Venezuela, afectando gravemente a mi familia y nuestra vivienda. Estoy recaudando fondos para reparaciones estructurales y gastos urgentes de recuperación.'">
                </p>

                <div>
                    <a href="{{ route('donations') }}"
                        class="button-cta border border-[var(--primary)] text-[var(--primary)] hover:bg-[var(--surface-raised)] focus-ring-signature font-bold text-xs inline-block"
                        x-text="language === 'en' ? 'Support / Donate' : 'Apoyar / Donar'"></a>
                </div>
            </div>

            <!-- WORKBENCH DIRECTORY SECTION -->
            <div class="flex flex-col gap-6 w-full">
                <h2 class="text-headline text-[var(--ink)]"
                    x-text="language === 'en' ? 'Workbench Directory' : 'Directorio del Taller'"></h2>

                <div class="flex flex-col gap-4">
                    <!-- Prominent Link to Foundry -->
                    <a href="/foundry"
                        class="card-bench flex flex-col gap-2 focus-ring-signature hover:border-[var(--primary)] hover:bg-[var(--surface-raised)] transition-all">
                        <div class="flex justify-between items-center">
                            <h3 class="text-title text-[var(--ink)] font-bold">
                                <span x-text="language === 'en' ? 'The Foundry' : 'La Fundición'"></span>
                            </h3>
                            <span
                                class="text-mono text-xs text-[var(--accent)] font-semibold uppercase tracking-wider">ACTIVE
                                WORKSHOP</span>
                        </div>
                        <p class="text-body text-sm text-[var(--muted)]"
                            x-text="language === 'en' ? 'Inspect tools, open-source kits, and database schema controllers.' : 'Inspecciona herramientas, kits de código abierto y controladores de bases de datos.'">
                        </p>
                    </a>

                    <!-- Subpage Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full">
                        <!-- Character Sheet -->
                        <a href="/character"
                            class="card-bench flex flex-col gap-2 focus-ring-signature hover:border-[var(--primary)] hover:bg-[var(--surface-raised)] transition-all">
                            <div class="flex justify-between items-center">
                                <h3 class="text-title text-[var(--ink)] font-bold">
                                    <span x-text="language === 'en' ? 'Character Sheet' : 'Hoja de Personaje'"></span>
                                </h3>
                                <span class="text-mono text-xs text-[var(--muted)] uppercase">LOADOUT</span>
                            </div>
                            <p class="text-body text-sm text-[var(--muted)]"
                                x-text="language === 'en' ? 'View active equipment configurations and item stat scaling.' : 'Ver configuraciones de equipamiento activo y escalado de atributos.'">
                            </p>
                        </a>

                        <!-- Skills & Talents -->
                        <a href="/skills"
                            class="card-bench flex flex-col gap-2 focus-ring-signature hover:border-[var(--primary)] hover:bg-[var(--surface-raised)] transition-all">
                            <div class="flex justify-between items-center">
                                <h3 class="text-title text-[var(--ink)] font-bold">
                                    <span
                                        x-text="language === 'en' ? 'Skills & Talents' : 'Habilidades y Talentos'"></span>
                                </h3>
                                <span class="text-mono text-xs text-[var(--muted)] uppercase">ABILITIES</span>
                            </div>
                            <p class="text-body text-sm text-[var(--muted)]"
                                x-text="language === 'en' ? 'Check proficiency rankings for professional and casual attributes.' : 'Consulta los niveles de dominio para habilidades profesionales y casuales.'">
                            </p>
                        </a>

                        <!-- Stats & Achievements -->
                        <a href="/stats"
                            class="card-bench flex flex-col gap-2 focus-ring-signature hover:border-[var(--primary)] hover:bg-[var(--surface-raised)] transition-all">
                            <div class="flex justify-between items-center">
                                <h3 class="text-title text-[var(--ink)] font-bold">
                                    <span
                                        x-text="language === 'en' ? 'Stats & Achievements' : 'Estadísticas y Logros'"></span>
                                </h3>
                                <span class="text-mono text-xs text-[var(--muted)] uppercase">LADDERS</span>
                            </div>
                            <p class="text-body text-sm text-[var(--muted)]"
                                x-text="language === 'en' ? 'Browse project metrics, commit trackers, and unlocked achievements.' : 'Explora métricas de proyectos, registros de commits y logros de juego.'">
                            </p>
                        </a>

                        <!-- Inventory Bag -->
                        <a href="/inventory"
                            class="card-bench flex flex-col gap-2 focus-ring-signature hover:border-[var(--primary)] hover:bg-[var(--surface-raised)] transition-all">
                            <div class="flex justify-between items-center">
                                <h3 class="text-title text-[var(--ink)] font-bold">
                                    <span x-text="language === 'en' ? 'Inventory Bag' : 'Bolsa de Inventario'"></span>
                                </h3>
                                <span class="text-mono text-xs text-[var(--muted)] uppercase">GEAR</span>
                            </div>
                            <p class="text-body text-sm text-[var(--muted)]"
                                x-text="language === 'en' ? 'Inspect hardware gear, software tools, and alchemy setup.' : 'Examina componentes físicos, herramientas de software y alquimia.'">
                            </p>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layouts::guest>
