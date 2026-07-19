<x-layouts::guest>
    <div x-data='{
        selectedSlot: @json($initialSlot),
        activeBuild: "ranked",
        items: @json($items),
        changeBuild(build) {
            this.activeBuild = build;
            this.selectedSlot = build + "_main_hand";
        }
    }' class="w-full flex flex-col gap-6">
        
        <!-- PAGE TITLE (SEO & Accessibility) -->
        <div class="w-full border-b border-[var(--border)] pb-4">
            <h1 class="text-headline text-[var(--ink)]">
                <span x-text="language === 'en' ? 'Character Sheet' : 'Hoja de Personaje'"></span>
            </h1>
        </div>

        <div class="w-full flex flex-col lg:flex-row gap-12 lg:gap-16 items-start">
            
            <!-- LEFT COLUMN: Gear Silhouette and Switcher -->
            <section class="w-full lg:w-6/12 flex flex-col gap-6">
                
                <!-- Card Container -->
                <div class="card-bench border border-[var(--border)] rounded-lg p-6 flex flex-col gap-6">
                    
                    <!-- Inside Card Header & Switcher -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-[var(--border)] pb-4">
                        <div class="flex flex-col">
                            <span class="text-mono text-[10px] text-[var(--muted)] uppercase" x-text="language === 'en' ? 'Active Equipment' : 'Equipamiento Activo'"></span>
                            <h2 class="text-title text-[var(--ink)]" x-text="language === 'en' ? 'Build Configuration' : 'Configuración de Build'"></h2>
                        </div>
                        
                        <!-- Mini Switcher Button inside Card -->
                        <div class="segmented-control shrink-0" role="group" aria-label="Loadout select">
                            <button
                                type="button"
                                @click="changeBuild('ranked')"
                                :class="activeBuild === 'ranked' ? 'active' : ''"
                                class="segmented-pill focus-ring-signature text-[10px] uppercase font-mono py-1 px-3"
                                x-text="language === 'en' ? 'Ranked (PVP)' : 'Ranked (PVP)'"
                            ></button>
                            <button
                                type="button"
                                @click="changeBuild('casual')"
                                :class="activeBuild === 'casual' ? 'active' : ''"
                                class="segmented-pill focus-ring-signature text-[10px] uppercase font-mono py-1 px-3"
                                x-text="language === 'en' ? 'Casual (PVE)' : 'Casual (PVE)'"
                            ></button>
                        </div>
                    </div>

                    <!-- RPG Grid of Equipped Items -->
                    <div class="flex items-center justify-center min-h-[320px] py-4">
                        <div class="relative w-full max-w-[280px] aspect-[3/4] grid grid-cols-3 grid-rows-3 gap-3 p-2 bg-[var(--surface-raised)] border border-[var(--border)] rounded-lg relative overflow-hidden">
                            
                            <!-- Subtle Back-label -->
                            <div class="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none select-none">
                                <span class="text-mono text-xl font-bold tracking-widest text-[var(--ink)]" x-text="activeBuild.toUpperCase()"></span>
                            </div>

                            <!-- ROW 1 -->
                            <!-- Head Slot -->
                            <button type="button" @click="selectedSlot = activeBuild + '_head'"
                                :class="selectedSlot === activeBuild + '_head' ? 'border-[var(--primary)] text-[var(--primary)] ring-2 ring-[var(--primary)] ring-offset-2 ring-offset-[var(--bg)]' : 'border-[var(--border)] text-[var(--muted)] hover:border-[var(--ink)] hover:text-[var(--ink)]'"
                                class="border bg-[var(--surface)] rounded-md flex flex-col items-center justify-center p-1.5 focus-ring-signature transition-all">
                                <span class="text-lg" x-text="activeBuild === 'ranked' ? '🛡️' : '🎧'"></span>
                                <span class="text-mono text-[8px] mt-1" x-text="language === 'en' ? 'HEAD' : 'CABEZA'"></span>
                            </button>
                            
                            <div></div>
                            
                            <!-- Chest/Armor Slot -->
                            <button type="button" @click="selectedSlot = activeBuild + '_chest'"
                                :class="selectedSlot === activeBuild + '_chest' ? 'border-[var(--primary)] text-[var(--primary)] ring-2 ring-[var(--primary)] ring-offset-2 ring-offset-[var(--bg)]' : 'border-[var(--border)] text-[var(--muted)] hover:border-[var(--ink)] hover:text-[var(--ink)]'"
                                class="border bg-[var(--surface)] rounded-md flex flex-col items-center justify-center p-1.5 focus-ring-signature transition-all">
                                <span class="text-lg" x-text="activeBuild === 'ranked' ? '📦' : '👕'"></span>
                                <span class="text-mono text-[8px] mt-1" x-text="language === 'en' ? 'ARMOR' : 'PECHO'"></span>
                            </button>

                            <!-- ROW 2 -->
                            <!-- Main Hand (Weapon) -->
                            <button type="button" @click="selectedSlot = activeBuild + '_main_hand'"
                                :class="selectedSlot === activeBuild + '_main_hand' ? 'border-[var(--primary)] text-[var(--primary)] ring-2 ring-[var(--primary)] ring-offset-2 ring-offset-[var(--bg)]' : 'border-[var(--border)] text-[var(--muted)] hover:border-[var(--ink)] hover:text-[var(--ink)]'"
                                class="border bg-[var(--surface)] rounded-md flex flex-col items-center justify-center p-1.5 focus-ring-signature transition-all">
                                <span class="text-lg" x-text="activeBuild === 'ranked' ? '⚔️' : '⌨️'"></span>
                                <span class="text-mono text-[8px] mt-1" x-text="language === 'en' ? 'WEAPON' : 'ARMA'"></span>
                            </button>
                            
                            <div></div>
                            
                            <!-- Off Hand (Shield) -->
                            <button type="button" @click="selectedSlot = activeBuild + '_off_hand'"
                                :class="selectedSlot === activeBuild + '_off_hand' ? 'border-[var(--primary)] text-[var(--primary)] ring-2 ring-[var(--primary)] ring-offset-2 ring-offset-[var(--bg)]' : 'border-[var(--border)] text-[var(--muted)] hover:border-[var(--ink)] hover:text-[var(--ink)]'"
                                class="border bg-[var(--surface)] rounded-md flex flex-col items-center justify-center p-1.5 focus-ring-signature transition-all">
                                <span class="text-lg" x-text="activeBuild === 'ranked' ? '🔮' : '🖱️'"></span>
                                <span class="text-mono text-[8px] mt-1" x-text="language === 'en' ? 'SHIELD' : 'ESCUDO'"></span>
                            </button>

                            <!-- ROW 3 -->
                            <!-- Ring 1 Slot -->
                            <button type="button" @click="selectedSlot = activeBuild + '_acc_1'"
                                :class="selectedSlot === activeBuild + '_acc_1' ? 'border-[var(--primary)] text-[var(--primary)] ring-2 ring-[var(--primary)] ring-offset-2 ring-offset-[var(--bg)]' : 'border-[var(--border)] text-[var(--muted)] hover:border-[var(--ink)] hover:text-[var(--ink)]'"
                                class="border bg-[var(--surface)] rounded-md flex flex-col items-center justify-center p-1.5 focus-ring-signature transition-all">
                                <span class="text-lg" x-text="activeBuild === 'ranked' ? '💍' : '📖'"></span>
                                <span class="text-mono text-[8px] mt-1" x-text="language === 'en' ? 'RING 1' : 'ANILLO 1'"></span>
                            </button>
                            
                            <div></div>
                            
                            <!-- Ring 2 Slot -->
                            <button type="button" @click="selectedSlot = activeBuild + '_acc_2'"
                                :class="selectedSlot === activeBuild + '_acc_2' ? 'border-[var(--primary)] text-[var(--primary)] ring-2 ring-[var(--primary)] ring-offset-2 ring-offset-[var(--bg)]' : 'border-[var(--border)] text-[var(--muted)] hover:border-[var(--ink)] hover:text-[var(--ink)]'"
                                class="border bg-[var(--surface)] rounded-md flex flex-col items-center justify-center p-1.5 focus-ring-signature transition-all">
                                <span class="text-lg" x-text="activeBuild === 'ranked' ? '💍' : '☕'"></span>
                                <span class="text-mono text-[8px] mt-1" x-text="language === 'en' ? 'RING 2' : 'ANILLO 2'"></span>
                            </button>

                        </div>
                    </div>

                </div>
            </section>

            <!-- RIGHT COLUMN: Item Inspection -->
            <section class="w-full lg:w-6/12 flex flex-col gap-6">
                
                <div class="flex flex-col gap-1">
                    <h2 class="text-title text-[var(--ink)]">
                        <span x-text="language === 'en' ? 'Inspection Panel' : 'Panel de Inspección'"></span>
                    </h2>
                    <p class="text-mono text-[10px] text-[var(--muted)]" x-text="language === 'en' ? 'Stats and effects description of the selected gear.' : 'Atributos y descripción del objeto seleccionado.'"></p>
                </div>

                <!-- Inspection Panel Card -->
                <div 
                    x-data="{ item() { return items[selectedSlot] || { name: '', desc: '', stats: [], rarity: '', type: '' }; } }"
                    class="card-bench border border-[var(--border)] rounded-lg p-6 flex flex-col gap-6 w-full"
                >
                    <!-- Header with Rarity Color -->
                    <div class="flex flex-col gap-1 border-b border-[var(--border)] pb-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-[var(--ink)]" x-text="language === 'en' ? item().name : item().name_es"></h3>
                            <span 
                                :class="{
                                    'text-[var(--muted)] border-[var(--border)] bg-[var(--surface-raised)]': item().rarity === 'Common',
                                    'text-[var(--blue)] border-[var(--blue)] bg-opacity-10 bg-[var(--blue)]': item().rarity === 'Rare',
                                    'text-[var(--accent)] border-[var(--accent)] bg-opacity-10 bg-[var(--accent)]': item().rarity === 'Epic',
                                    'text-[var(--yellow)] border-[var(--yellow)] bg-opacity-10 bg-[var(--yellow)]': item().rarity === 'Legendary'
                                }"
                                class="font-mono text-[9px] uppercase border px-2.5 py-0.5 rounded-full"
                                x-text="item().rarity"
                            ></span>
                        </div>
                        <span class="text-mono text-[10px] text-[var(--muted)]" x-text="language === 'en' ? item().type : item().type_es"></span>
                    </div>

                    <!-- Lore -->
                    <div class="flex flex-col gap-1">
                        <span class="text-mono text-[9px] text-[var(--muted)] uppercase" x-text="language === 'en' ? 'Item Lore' : 'Lore del Objeto'"></span>
                        <p class="text-body text-xs italic text-[var(--ink)]" x-text="language === 'en' ? item().desc : item().desc_es"></p>
                    </div>

                    <!-- Effects -->
                    <div class="flex flex-col gap-2">
                        <span class="text-mono text-[9px] text-[var(--muted)] uppercase" x-text="language === 'en' ? 'Active Effects' : 'Efectos Activos'"></span>
                        <ul class="flex flex-col gap-1">
                            <template x-for="stat in item().stats" :key="stat">
                                <li class="text-mono text-xs text-[var(--primary)] flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 bg-[var(--primary)] rounded-full"></span>
                                    <span x-text="stat"></span>
                                </li>
                            </template>
                        </ul>
                    </div>

                    <!-- Buffs -->
                    <div class="border-t border-[var(--border)] pt-4 flex flex-col gap-2">
                        <span class="text-mono text-[9px] text-[var(--muted)] uppercase" x-text="language === 'en' ? 'Global Modifiers' : 'Modificadores Globales'"></span>
                        <div class="flex flex-wrap gap-2">
                            <div class="flex items-center gap-1 badge-chip bg-[var(--surface-raised)] border border-[var(--border)]">
                                <span class="w-2 h-2 rounded-full bg-[var(--accent)] animate-pulse"></span>
                                <span class="text-mono text-[9px]" x-text="language === 'en' ? 'Full Stack Engineer (+50% Polyglot)' : 'Ingeniero Full Stack (+50% Políglota)'"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

    </div>
</x-layouts::guest>
