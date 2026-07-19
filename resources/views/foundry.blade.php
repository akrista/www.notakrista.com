<x-layouts::guest>
    <div x-data="{
        projects: [
            {
                name: 'todoticket calculator',
                name_es: 'calculadora todoticket',
                type: 'Tool',
                type_es: 'Herramienta',
                url: '{{ route('todoticket') }}',
                external: false,
                desc: 'A local voucher and balance calculator tailored for Venezuelan employees, maximizing efficiency for salary ticket cards.',
                desc_es: 'Calculadora local de vales y saldos diseñada para empleados venezolanos, optimizando el uso de tarjetas de ticket alimentario.',
                tech: ['PHP 8.5', 'Laravel 13', 'Livewire v4', 'Tailwind CSS']
            },
            {
                name: 'bizkit starter kit',
                name_es: 'bizkit starter kit',
                type: 'Open Source',
                type_es: 'Código Abierto',
                url: 'https://github.com/akrista/bizkit',
                external: true,
                desc: 'A premium, opinionated starter kit for Laravel applications including workspace structures, preset test harnesses, and code generators.',
                desc_es: 'Un kit de inicio premium para aplicaciones Laravel, que incluye estructuras de espacio de trabajo, arneses de prueba preestablecidos y generadores de código.',
                tech: ['Laravel 13', 'Pest v4', 'GitHub Actions']
            },
            {
                name: 'rockery',
                name_es: 'rockery',
                type: 'Knowledge Base',
                type_es: 'Base de Conocimientos',
                url: 'https://rockery.notakrista.com',
                external: true,
                desc: 'My personal knowledge base and blog built with Quartz, mapping technical notes, thoughts, and writings.',
                desc_es: 'Mi base de conocimientos y blog personal desarrollado con Quartz, recopilando notas técnicas, pensamientos y escritos.',
                tech: ['Quartz', 'Markdown', 'Git']
            },
            {
                name: 'bytebase contributions',
                name_es: 'bytebase contributions',
                type: 'Collaboration',
                type_es: 'Colaboración',
                url: 'https://github.com/bytebase/bytebase/releases/tag/1.17.0',
                external: true,
                desc: 'Contributed code to Bytebase, an open-source database schema change management tool, during the launch cycle for v1.17.0.',
                desc_es: 'Aportaciones de código a Bytebase, una herramienta de código abierto para la gestión de cambios en bases de datos, durante el ciclo v1.17.0.',
                tech: ['Go', 'SQL', 'Database Schema']
            },
            {
                name: 'sqlchat contribution',
                name_es: 'colaboración en sqlchat',
                type: 'Collaboration',
                type_es: 'Colaboración',
                url: 'https://github.com/sqlchat/sqlchat/pull/33',
                external: true,
                desc: 'Contributed code to SQLChat to add support for Microsoft SQL Server (MSSQL) databases, creating a new connection adapter and integrating it into the client.',
                desc_es: 'Colaboración en SQLChat para añadir soporte a bases de datos Microsoft SQL Server (MSSQL), creando un nuevo adaptador de conexión e integrándolo en el cliente.',
                tech: ['TypeScript', 'Next.js', 'MSSQL', 'SQL']
            },
            {
                name: 'Immich Backgrounds Gallery',
                name_es: 'Galería de Fondos de Immich',
                type: 'backgrounds',
                type_es: 'fondos',
                url: 'https://img.notakrista.com/share/t5lqpc6yCwbEwD8GRfa9r9Ed8CbCTXsJ12HkLeRCckSUhuz_mWCw2Y3HnKyMvG2qmjg',
                external: true,
                desc: 'A shared Immich photo gallery containing backgrounds, setup logs, and curated photography from my personal workshop setup.',
                desc_es: 'Galería fotográfica compartida en Immich que contiene fondos de pantalla, registros de configuraciones y fotografía de mi taller.',
                tech: ['Immich', 'Photography', 'Self-Hosted']
            }
        ]
    }" class="w-full flex flex-col gap-8">
        
        <!-- PAGE TITLE -->
        <div class="w-full border-b border-[var(--border)] pb-4 flex flex-col gap-2">
            <h1 class="text-headline text-[var(--ink)]">
                <span x-text="language === 'en' ? 'The Foundry' : 'La Fundición'"></span>
            </h1>
            <p class="text-mono text-xs text-[var(--muted)]" x-text="language === 'en'
                ? 'The forge where code is shaped, open-source kits are constructed, and tools are crafted.'
                : 'La forja donde se da forma al código, se construyen kits de código abierto y se crean herramientas.'">
            </p>
        </div>

        <!-- PROJECTS LIST / GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
            <template x-for="project in projects" :key="project.name">
                <a 
                    :href="project.url" 
                    :target="project.external ? '_blank' : '_self'" 
                    class="card-bench flex flex-col justify-between gap-4 focus-ring-signature hover:border-[var(--primary)] hover:bg-[var(--surface-raised)] transition-all"
                >
                    <div class="flex flex-col gap-2">
                        <div class="flex justify-between items-start">
                            <h2 class="text-title text-[var(--ink)] font-bold">
                                <span x-text="language === 'en' ? project.name : project.name_es"></span>
                                <span x-show="project.external"> ↗</span>
                            </h2>
                            <span class="text-mono text-[10px] text-[var(--accent)] font-semibold uppercase tracking-wider" x-text="language === 'en' ? project.type : project.type_es"></span>
                        </div>
                        <p class="text-body text-sm text-[var(--muted)] leading-relaxed" x-text="language === 'en' ? project.desc : project.desc_es"></p>
                    </div>
                    
                    <!-- Tech tags -->
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        <template x-for="tag in project.tech" :key="tag">
                            <span class="text-mono text-[9px] px-2 py-0.5 rounded bg-[var(--surface-raised)] text-[var(--ink)] border border-[var(--border)]" x-text="tag"></span>
                        </template>
                    </div>
                </a>
            </template>
        </div>

    </div>
</x-layouts::guest>
