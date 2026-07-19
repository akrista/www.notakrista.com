<x-layouts::guest>
    <div x-data="{
        personalSkills: [
            {
                name: 'Language: Spanish',
                name_es: 'Idioma: Español',
                level: 100,
                type: 'Passive',
                type_es: 'Pasiva',
                desc: 'Native speaker. Capable of complex technical and cultural communications.',
                desc_es: 'Hablante nativo. Capaz de comunicaciones técnicas y culturales complejas.'
            },
            {
                name: 'Language: English',
                name_es: 'Idioma: Inglés',
                level: 85,
                type: 'Passive',
                type_es: 'Pasiva',
                desc: 'Professional fluency (C1 level). Conversant in engineering and business domains.',
                desc_es: 'Fluidez profesional (nivel C1). Conversaciones fluidas en dominios de ingeniería y negocios.'
            },
            {
                name: 'Gaming Proficiency',
                name_es: 'Destreza en Juegos',
                level: 88,
                type: 'Active',
                type_es: 'Activa',
                desc: 'Specialized in retro platformers, RPG loops, and metroidvania maps. Steam Level 48.',
                desc_es: 'Especialista en juegos de plataformas retro, bucles de RPG y mapas de metroidvanias. Nivel 48 de Steam.'
            },
            {
                name: 'Coffee Brewing (Alchemy)',
                name_es: 'Preparación de Café',
                level: 75,
                type: 'Active',
                type_es: 'Activa',
                desc: 'Skilled in immersion extraction (AeroPress, French Press) and precise water-to-coffee ratios.',
                desc_es: 'Experto en extracción de inmersión y ratios precisos de agua y café.'
            },
            {
                name: 'DIY Hardware Hacks',
                name_es: 'Hardware Casero',
                level: 45,
                type: 'Passive',
                type_es: 'Pasiva',
                desc: 'Basic soldering, Raspberry Pi home automation, and network server tinkering.',
                desc_es: 'Soldadura básica, automatización del hogar con Raspberry Pi y servidores de red locales.'
            },
            {
                name: 'Adaptability & Survival',
                name_es: 'Adaptabilidad y Supervivencia',
                level: 90,
                type: 'Passive',
                type_es: 'Pasiva',
                desc: 'High resilience in unexpected challenges. Handles high-stress debugs and life events calmly.',
                desc_es: 'Alta resiliencia en desafíos inesperados. Maneja depuraciones complejas y eventos de la vida con calma.'
            }
        ],
        businessSkills: [
            {
                name: 'Backend Architecture (PHP)',
                name_es: 'Arquitectura Backend (PHP)',
                level: 95,
                type: 'Active',
                type_es: 'Activa',
                desc: 'Expertise in modern PHP 8.x development, clean design patterns, and solid API designs.',
                desc_es: 'Experto en desarrollo PHP 8.x moderno, patrones de diseño limpios y diseño sólido de APIs.'
            },
            {
                name: 'Laravel Framework Mastery',
                name_es: 'Dominio de Laravel',
                level: 95,
                type: 'Active',
                type_es: 'Activa',
                desc: 'Deep usage of Service Container, Eloquent, Queues, Reverb, and Horizon monitoring.',
                desc_es: 'Uso completo del contenedor de servicios, Eloquent, colas, Reverb y monitoreo con Horizon.'
            },
            {
                name: 'Frontend Engineering',
                name_es: 'Ingeniería Frontend',
                level: 88,
                type: 'Active',
                type_es: 'Activa',
                desc: 'Proficient in Livewire reactive flows, Alpine.js scripts, and Tailwind CSS design systems.',
                desc_es: 'Competente en flujos reactivos de Livewire, scripts de Alpine.js y sistemas de diseño Tailwind CSS.'
            },
            {
                name: 'Filament PHP Panel Build',
                name_es: 'Desarrollo en Filament PHP',
                level: 90,
                type: 'Active',
                type_es: 'Activa',
                desc: 'Fast assembly of data resources, complex forms, custom table actions, and permission gates.',
                desc_es: 'Rápida creación de recursos de datos, formularios complejos, acciones personalizadas y filtros de permisos.'
            },
            {
                name: 'Database Optimization',
                name_es: 'Optimización de Base de Datos',
                level: 82,
                type: 'Passive',
                type_es: 'Pasiva',
                desc: 'Optimizing SQL index usage, query plans, relationships eager loading, and key-value buffers.',
                desc_es: 'Optimización del uso de índices SQL, planes de consulta, carga ansiosa y búferes clave-valor.'
            },
            {
                name: 'Testing & Quality Assurance',
                name_es: 'Pruebas y Control de Calidad',
                level: 85,
                type: 'Passive',
                type_es: 'Pasiva',
                desc: 'Strict code coverage enforcement using Pest/PHPUnit tests. Safe refactoring routines.',
                desc_es: 'Estricta cumplimiento de cobertura de código usando Pest/PHPUnit. Rutinas de refactorización seguras.'
            }
        ]
    }" class="w-full flex flex-col gap-12">
        
        <!-- HEADER -->
        <div class="flex flex-col gap-2">
            <h2 class="text-headline text-[var(--ink)]">
                <span x-text="language === 'en' ? 'Skills & Attributes' : 'Habilidades y Atributos'"></span>
            </h2>
            <p class="text-mono text-xs text-[var(--muted)]" x-text="language === 'en'
                ? 'Proficiency bars representing active and passive capabilities.'
                : 'Barras de dominio que representan las capacidades activas y pasivas.'">
            </p>
        </div>

        <!-- SKILLS SECTIONS -->
        <div class="w-full flex flex-col gap-8">
            <!-- SECTION 1: Business Skills -->
            <div class="flex flex-col gap-4">
                <h3 class="text-title text-[var(--primary)] border-b border-[var(--border)] pb-2 uppercase text-mono text-sm tracking-wider">
                    <span x-text="language === 'en' ? 'Professional Stack (Ranked)' : 'Pila Profesional (Ranked)'"></span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
                    <template x-for="skill in businessSkills" :key="skill.name">
                        <div class="card-bench border border-[var(--border)] rounded-lg p-5 flex flex-col gap-4">
                            <div class="flex justify-between items-start">
                                <div class="flex flex-col">
                                    <span class="text-mono text-[9px] text-[var(--muted)] uppercase" x-text="language === 'en' ? skill.type : skill.type_es"></span>
                                    <h4 class="text-sm font-bold text-[var(--ink)]" x-text="language === 'en' ? skill.name : skill.name_es"></h4>
                                </div>
                                <div class="flex items-baseline gap-0.5 text-mono">
                                    <span class="text-[9px] text-[var(--muted)]" x-text="language === 'en' ? 'LVL' : 'NIV'"></span>
                                    <span class="text-base font-bold text-[var(--primary)]" x-text="skill.level"></span>
                                </div>
                            </div>
                            <div class="w-full h-2.5 bg-[var(--surface-raised)] border border-[var(--border)] rounded-full overflow-hidden relative">
                                <div class="h-full bg-[var(--primary)]" :style="`width: ${skill.level}%`"></div>
                            </div>
                            <p class="text-body text-[11px] text-[var(--muted)] leading-relaxed" x-text="language === 'en' ? skill.desc : skill.desc_es"></p>
                        </div>
                    </template>
                </div>
            </div>

            <!-- SECTION 2: Personal Skills -->
            <div class="flex flex-col gap-4">
                <h3 class="text-title text-[var(--accent)] border-b border-[var(--border)] pb-2 uppercase text-mono text-sm tracking-wider">
                    <span x-text="language === 'en' ? 'Life & Hobby Skills (Casual)' : 'Habilidades Personales (Casual)'"></span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
                    <template x-for="skill in personalSkills" :key="skill.name">
                        <div class="card-bench border border-[var(--border)] rounded-lg p-5 flex flex-col gap-4">
                            <div class="flex justify-between items-start">
                                <div class="flex flex-col">
                                    <span class="text-mono text-[9px] text-[var(--muted)] uppercase" x-text="language === 'en' ? skill.type : skill.type_es"></span>
                                    <h4 class="text-sm font-bold text-[var(--ink)]" x-text="language === 'en' ? skill.name : skill.name_es"></h4>
                                </div>
                                <div class="flex items-baseline gap-0.5 text-mono">
                                    <span class="text-[9px] text-[var(--muted)]" x-text="language === 'en' ? 'LVL' : 'NIV'"></span>
                                    <span class="text-base font-bold text-[var(--accent)]" x-text="skill.level"></span>
                                </div>
                            </div>
                            <div class="w-full h-2.5 bg-[var(--surface-raised)] border border-[var(--border)] rounded-full overflow-hidden relative">
                                <div class="h-full bg-[var(--accent)]" :style="`width: ${skill.level}%`"></div>
                            </div>
                            <p class="text-body text-[11px] text-[var(--muted)] leading-relaxed" x-text="language === 'en' ? skill.desc : skill.desc_es"></p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Passive trait box -->
        <div class="card-bench border border-[var(--border)] rounded-lg p-6 w-full bg-[var(--surface)] mt-4">
            <div class="flex items-center gap-3 mb-2">
                <span class="w-2 h-2 rounded-full bg-[var(--accent)] animate-pulse"></span>
                <span class="text-mono text-xs uppercase text-[var(--accent)] font-semibold" x-text="language === 'en' ? 'Passive Traits / Buffs' : 'Rasgos Pasivos / Buffs'"></span>
            </div>
            <p class="text-body text-sm text-[var(--muted)]" x-text="language === 'en'
                ? 'Driven by public build logs and side-project sandboxes. Earns experience multipliers through daily commits and active code exploration.'
                : 'Impulsado por logs de desarrollo público y proyectos secundarios. Obtiene multiplicadores de experiencia a través de commits diarios y exploración de código activo.'">
            </p>
        </div>

    </div>
</x-layouts::guest>
