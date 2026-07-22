<x-layouts::guest>
    <div x-data="{
        businessMetrics: [
            { label: 'Years of Laravel Experience', label_es: 'Años de Experiencia en Laravel', value: '5+ years / años' },
            { label: 'Shipped Client Projects', label_es: 'Proyectos Entregados a Clientes', value: '20+' },
            { label: 'Merged Pull Requests', label_es: 'Pull Requests Fusionados', value: '150+' },
            { label: 'Test Coverage Minimum', label_es: 'Cobertura Mínima de Pruebas', value: '100% target' },
            { label: 'Filament Panels Built', label_es: 'Paneles de Filament Creados', value: '12+' },
            { label: 'System Reliability Target', label_es: 'Objetivo de Confiabilidad de Sistemas', value: '99.99% Uptime' }
        ],
        personalMetrics: [
            { label: 'Wakatime Coding Time', label_es: 'Tiempo de Codificación Wakatime', value: '1,423 hrs' },
            { label: 'Steam Level', label_es: 'Nivel de Steam', value: 'LVL 48' },
            { label: 'RetroAchievements Points', label_es: 'Puntos RetroAchievements', value: '12,450 pts' },
            { label: 'Estimated Coffee Brewed', label_es: 'Café Preparado Estimado', value: '850+ cups / tazas' },
            { label: 'Local Server Uptime', label_es: 'Uptime de Servidor Local', value: '99.8%' },
            { label: 'Total Commits (2026)', label_es: 'Commits Totales (2026)', value: '3,420+' }
        ],
        education: [
            {
                degree: 'Bachelor of Systems Engineering',
                degree_es: 'Ingeniería de Sistemas',
                institution: 'UNEFA',
                institution_es: 'UNEFA',
                period: '2016 - 2021'
            }
        ],
        achievements: [
            {
                title: 'Production Shipped',
                title_es: 'Entregado a Producción',
                desc: 'Deployed high-traffic APIs and database setups to AWS and Laravel Cloud.',
                desc_es: 'Desplegó APIs de alto tráfico y bases de datos en AWS y Laravel Cloud.',
                unlocked: 'Persistent',
                icon: '💼'
            },
            {
                title: 'Bizkit Starter Unlocked',
                title_es: 'Bizkit Starter Desbloqueado',
                desc: 'Successfully crafted and released a Laravel workspace starter kit.',
                desc_es: 'Creó y lanzó con éxito un kit de inicio para espacios de trabajo Laravel.',
                unlocked: '2025-11-12',
                icon: '🏆'
            },
            {
                title: 'Open Source Contributor',
                title_es: 'Colaborador de Código Abierto',
                desc: 'Contributed key features to Bytebase repository during launch cycles.',
                desc_es: 'Aportó características clave al repositorio de Bytebase durante ciclos de lanzamiento.',
                unlocked: '2025-08-05',
                icon: '🏆'
            },
            {
                title: 'Refactoring Wizard',
                title_es: 'Mago de la Refactorización',
                desc: 'Successfully migrated legacy code bases into modern, testable PHP applications.',
                desc_es: 'Migró con éxito bases de código heredadas a aplicaciones PHP modernas y probables.',
                unlocked: 'Persistent',
                icon: '💼'
            },
            {
                title: 'Earthquake Resilience',
                title_es: 'Resiliencia ante Terremotos',
                desc: 'Maintained repository commits and code execution during the Venezuela June 2026 earthquake.',
                desc_es: 'Mantuvo commits y ejecución de código durante el terremoto de Venezuela en junio de 2026.',
                unlocked: '2026-06-25',
                icon: '🏆'
            },
            {
                title: 'Panel Architect',
                title_es: 'Arquitecto de Paneles',
                desc: 'Crafted complex custom layouts, action sheets, and resources for Filament dashboards.',
                desc_es: 'Diseñó layouts complejos, hojas de acción y recursos para tableros Filament.',
                unlocked: 'Persistent',
                icon: '💼'
            }
        ]
    }" class="w-full flex flex-col gap-12">
        
        <!-- TOP: Stats Columns -->
        <div class="w-full flex flex-col lg:flex-row gap-8 items-start">
            
            <!-- LEFT COLUMN: Professional Stats & Education -->
            <section class="w-full lg:w-6/12 flex flex-col gap-6">
                <div class="flex flex-col gap-2">
                    <h2 class="text-headline text-[var(--ink)]">
                        <span x-text="language === 'es' ? 'Tabla Clasificatoria: Trayectoria Profesional' : 'Ranked Ladder: Professional Track Record'"></span>
                    </h2>
                    <p class="text-mono text-xs text-[var(--muted)]" x-text="language === 'es' ? 'Estadísticas de ingeniería, proyectos entregados y credenciales.' : 'Engineering statistics, shipped projects, and credentials.'"></p>
                </div>

                <div class="card-bench border border-[var(--border)] rounded-lg p-6 flex flex-col gap-4">
                    <!-- Metrics list -->
                    <div class="flex flex-col gap-3.5">
                        <template x-for="metric in businessMetrics" :key="metric.label">
                            <div class="flex items-end justify-between font-mono text-xs w-full">
                                <span class="text-[var(--muted)] pr-2" x-text="language === 'es' ? metric.label_es : metric.label"></span>
                                <div class="flex-1 border-b border-dotted border-[var(--border)] mb-1"></div>
                                <span class="text-[var(--ink)] font-bold pl-2" x-text="metric.value"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Education -->
                    <div class="border-t border-[var(--border)] pt-4 mt-2 flex flex-col gap-3">
                        <span class="text-label text-[var(--muted)]" x-text="language === 'es' ? 'Educación' : 'Education'"></span>
                        <template x-for="edu in education" :key="edu.degree">
                            <div class="flex flex-col gap-1">
                                <div class="flex justify-between items-start font-mono text-xs text-[var(--ink)] font-bold">
                                    <span x-text="language === 'es' ? edu.degree_es : edu.degree"></span>
                                    <span class="text-[var(--primary)] shrink-0" x-text="edu.period"></span>
                                </div>
                                <span class="text-mono text-[10px] text-[var(--muted)]" x-text="language === 'es' ? edu.institution_es : edu.institution"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </section>

            <!-- RIGHT COLUMN: Personal / Casual Stats -->
            <section class="w-full lg:w-6/12 flex flex-col gap-6">
                <div class="flex flex-col gap-2">
                    <h2 class="text-headline text-[var(--ink)]">
                        <span x-text="language === 'es' ? 'Tabla Casual: Métricas Personales y Pasatiempos' : 'Casual Ladder: Personal Metrics & Hobbies'"></span>
                    </h2>
                    <p class="text-mono text-xs text-[var(--muted)]" x-text="language === 'es' ? 'Estadísticas casuales y logs de juego.' : 'Hobby metrics & activity stats.'"></p>
                </div>

                <div class="card-bench border border-[var(--border)] rounded-lg p-6 flex flex-col gap-4">
                    <div class="flex flex-col gap-3.5">
                        <template x-for="metric in personalMetrics" :key="metric.label">
                            <div class="flex items-end justify-between font-mono text-xs w-full">
                                <span class="text-[var(--muted)] pr-2" x-text="language === 'es' ? metric.label_es : metric.label"></span>
                                <div class="flex-1 border-b border-dotted border-[var(--border)] mb-1"></div>
                                <span class="text-[var(--ink)] font-bold pl-2" x-text="metric.value"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </section>

        </div>

        <!-- BOTTOM: Achievements List (Full Width) -->
        <section class="w-full flex flex-col gap-6">
            <div class="flex flex-col gap-2">
                <h2 class="text-headline text-[var(--ink)]">
                    <span x-text="language === 'es' ? 'Logros Desbloqueados' : 'Achievements Unlocked'"></span>
                </h2>
                <p class="text-mono text-xs text-[var(--muted)]" x-text="language === 'es'
                    ? 'Hitos y medallas combinadas del banco de trabajo profesional y casual.'
                    : 'Combined milestones and medals from the professional & casual workbench.'">
                </p>
            </div>

            <!-- Unified Timeline Grid of Achievements -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                <template x-for="ach in achievements" :key="ach.title">
                    <div class="card-bench border border-[var(--border)] rounded-lg p-5 flex items-start gap-4 hover:border-[var(--primary)] transition-all">
                        <!-- Trophy Icon Container -->
                        <div class="shrink-0 w-10 h-10 rounded-full border border-[var(--primary)] bg-opacity-10 bg-[var(--primary)] flex items-center justify-center text-[var(--primary)] font-mono text-lg" x-text="ach.icon"></div>
                        
                        <!-- Details -->
                        <div class="flex-1 flex flex-col gap-1.5">
                            <div class="flex justify-between items-baseline gap-2 flex-wrap">
                                <h3 class="text-sm font-bold text-[var(--ink)]" x-text="language === 'es' ? ach.title_es : ach.title"></h3>
                                <span class="text-mono text-[9px] text-[var(--primary)] font-semibold" x-text="ach.unlocked"></span>
                            </div>
                            <p class="text-body text-[11px] text-[var(--muted)] leading-normal" x-text="language === 'es' ? ach.desc_es : ach.desc"></p>
                        </div>
                    </div>
                </template>
            </div>
        </section>

    </div>
</x-layouts::guest>
