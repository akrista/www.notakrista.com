<x-layouts::guest>
    <div x-data='{
        activeTab: "items",
        selectedId: @json($initialItemId),
        selectedType: "item",
        items: @json($items),
        mtgCards: @json($mtgCards),
        yugiohCards: @json($yugiohCards),
        
        ygoSearch: "",
        ygoPage: 1,
        ygoPageSize: 20,
        
        mtgSearch: "",
        
        get filteredMtg() {
            return this.mtgCards.filter(c => (c.name || "").toLowerCase().includes(this.mtgSearch.toLowerCase()));
        },
        
        get filteredYgo() {
            return this.yugiohCards.filter(c => (c.name || "").toLowerCase().includes(this.ygoSearch.toLowerCase()));
        },
        
        get paginatedYgo() {
            const start = (this.ygoPage - 1) * this.ygoPageSize;
            return this.filteredYgo.slice(start, start + this.ygoPageSize);
        },
        
        get totalYgoPages() {
            return Math.ceil(this.filteredYgo.length / this.ygoPageSize) || 1;
        },
        
        selectTab(tab) {
            this.activeTab = tab;
            this.selectedType = tab === "items" ? "item" : tab;
            if (tab === "items") {
                this.selectedId = Object.keys(this.items)[0] || null;
            } else if (tab === "mtg") {
                const first = this.filteredMtg[0];
                this.selectedId = first ? first.id : null;
            } else if (tab === "yugioh") {
                this.ygoPage = 1;
                const first = this.paginatedYgo[0];
                this.selectedId = first ? first.id : null;
            }
        },
        
        get selectedItem() {
            if (this.selectedType === "item") {
                return this.items[this.selectedId] || null;
            }
            if (this.selectedType === "mtg") {
                return this.mtgCards.find(c => c.id === this.selectedId) || null;
            }
            if (this.selectedType === "yugioh") {
                return this.yugiohCards.find(c => c.id === this.selectedId) || null;
            }
            return null;
        }
    }' class="w-full flex flex-col gap-6">
        
        <!-- PAGE TITLE (SEO & Accessibility) -->
        <div class="w-full border-b border-[var(--border)] pb-4">
            <h1 class="text-headline text-[var(--ink)]">
                <span x-text="language === 'en' ? 'Inventory Bag' : 'Bolsa de Inventario'"></span>
            </h1>
        </div>

        <!-- TABS SWITCHER -->
        <div class="flex border-b border-[var(--border)]">
            <button 
                type="button" 
                @click="selectTab('items')"
                :class="activeTab === 'items' ? 'border-[var(--primary)] text-[var(--ink)]' : 'border-transparent text-[var(--muted)] hover:text-[var(--ink)]'"
                class="border-b-2 px-4 py-2 font-mono text-xs uppercase tracking-wider transition-all focus:outline-none"
            >
                <span x-text="language === 'en' ? 'RPG Bag' : 'Bolsa RPG'"></span>
            </button>
            <button 
                type="button" 
                @click="selectTab('mtg')"
                :class="activeTab === 'mtg' ? 'border-[var(--primary)] text-[var(--ink)]' : 'border-transparent text-[var(--muted)] hover:text-[var(--ink)]'"
                class="border-b-2 px-4 py-2 font-mono text-xs uppercase tracking-wider transition-all focus:outline-none"
            >
                Magic: The Gathering
            </button>
            <button 
                type="button" 
                @click="selectTab('yugioh')"
                :class="activeTab === 'yugioh' ? 'border-[var(--primary)] text-[var(--ink)]' : 'border-transparent text-[var(--muted)] hover:text-[var(--ink)]'"
                class="border-b-2 px-4 py-2 font-mono text-xs uppercase tracking-wider transition-all focus:outline-none"
            >
                Yu-Gi-Oh!
            </button>
        </div>

        <div class="w-full flex flex-col lg:flex-row gap-12 lg:gap-16 items-start">
            <!-- LEFT: Inventory Bag Grid -->
            <section class="w-full lg:w-7/12 flex flex-col gap-4">
                
                <!-- RPG Bag View -->
                <div x-show="activeTab === 'items'" class="flex flex-col gap-4">
                    <div class="flex flex-col gap-1">
                        <h2 class="text-title text-[var(--ink)]">
                            <span x-text="language === 'en' ? 'Unified Inventory' : 'Inventario Unificado'"></span>
                        </h2>
                        <p class="text-mono text-[10px] text-[var(--muted)]" x-text="language === 'en'
                            ? 'Click on any item in the inventory slots to inspect its details.'
                            : 'Haz clic en cualquier objeto de la ranura de inventario para inspeccionar sus detalles.'">
                        </p>
                    </div>

                    <!-- RPG Bag Grid -->
                    <div class="grid grid-cols-4 sm:grid-cols-5 gap-3 bg-[var(--surface)] border border-[var(--border)] rounded-lg p-6">
                        <!-- Dynamic Items Loop -->
                        <template x-for="(item, id) in items" :key="id">
                            <button 
                                type="button"
                                @click="selectedId = id"
                                :class="selectedId === id ? 'border-[var(--primary)] text-[var(--ink)] ring-2 ring-[var(--primary)] ring-offset-2 ring-offset-[var(--bg)]' : 'border-[var(--border)] text-[var(--muted)] hover:border-[var(--ink)] hover:text-[var(--ink)]'"
                                class="aspect-square border bg-[var(--surface-raised)] rounded-lg flex flex-col items-center justify-center p-1 focus-ring-signature transition-all relative overflow-hidden"
                            >
                                <!-- Rarity bar -->
                                <div 
                                    :class="{
                                        'bg-[var(--muted)]': item.rarity === 'Common',
                                        'bg-[var(--blue)]': item.rarity === 'Rare',
                                        'bg-[var(--accent)]': item.rarity === 'Epic',
                                        'bg-[var(--yellow)]': item.rarity === 'Legendary'
                                    }"
                                    class="absolute top-0 left-0 w-full h-1"
                                ></div>

                                <span class="text-2xl sm:text-3xl" x-text="item.icon"></span>
                                <span class="text-[9px] font-mono mt-1 text-center leading-tight truncate w-full px-1" x-text="language === 'en' ? item.name : item.name_es"></span>
                            </button>
                        </template>

                        <!-- Padding slots to complete a standard 20-slot grid bags -->
                        <template x-for="i in Array.from({length: 20 - Object.keys(items).length})" :key="i">
                            <div class="aspect-square border border-[var(--border)] bg-opacity-20 bg-[var(--bg)] border-dashed rounded-lg flex items-center justify-center text-[var(--muted)] opacity-30 select-none">
                                <span class="font-mono text-xs">...</span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Magic: The Gathering View -->
                <div x-show="activeTab === 'mtg'" class="flex flex-col gap-4" x-cloak>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex flex-col gap-1">
                            <h2 class="text-title text-[var(--ink)]">Magic: The Gathering</h2>
                            <p class="text-mono text-[10px] text-[var(--muted)]" x-text="language === 'en'
                                ? 'Click on any card to view its Scryfall stats and artwork.'
                                : 'Haz clic en cualquier carta para ver sus estadísticas de Scryfall e ilustración.'">
                            </p>
                        </div>
                        <!-- Search Box -->
                        <div class="relative w-full sm:w-48">
                            <input 
                                type="text" 
                                x-model="mtgSearch" 
                                :placeholder="language === 'en' ? 'Search MTG...' : 'Buscar MTG...'" 
                                class="w-full bg-[var(--surface)] text-[var(--ink)] text-xs rounded border border-[var(--border)] px-3 py-1.5 focus:outline-none focus:border-[var(--primary)] transition-all font-mono"
                            />
                        </div>
                    </div>

                    <!-- Cards Grid -->
                    <div class="grid grid-cols-4 sm:grid-cols-5 gap-3 bg-[var(--surface)] border border-[var(--border)] rounded-lg p-6 min-h-[300px]">
                        <template x-for="card in filteredMtg" :key="card.id">
                            <button 
                                type="button"
                                @click="selectedId = card.id"
                                :class="{
                                    'border-[var(--primary)] ring-2 ring-[var(--primary)] ring-offset-2 ring-offset-[var(--bg)]': selectedId === card.id,
                                    'border-[var(--border)] hover:border-[var(--ink)]': selectedId !== card.id,
                                    'grayscale opacity-60': card.is_sold
                                }"
                                class="aspect-[2.5/3.5] border bg-[var(--surface-raised)] rounded-md flex flex-col items-center justify-center p-1 focus-ring-signature transition-all relative overflow-hidden group"
                            >
                                <template x-if="card.image_url">
                                    <img :src="card.image_url" :alt="card.name" class="w-full h-full object-cover rounded-sm" loading="lazy" />
                                </template>
                                <template x-if="!card.image_url">
                                    <div class="flex flex-col items-center justify-center p-2 text-center h-full">
                                        <span class="text-mono text-[8px] text-[var(--muted)] uppercase" x-text="card.set"></span>
                                        <span class="text-[9px] font-bold text-[var(--ink)] mt-1 leading-tight truncate w-full" x-text="card.name"></span>
                                    </div>
                                </template>
                                
                                <template x-if="card.quantity > 1">
                                    <span class="absolute top-1 right-1 bg-[var(--bg)] border border-[var(--border)] text-[var(--primary)] text-[8px] font-mono px-1 rounded-sm z-10" x-text="'x' + card.quantity"></span>
                                </template>

                                <template x-if="card.is_sold">
                                    <span class="absolute bottom-1 left-1 bg-[var(--red)] border border-[var(--red)] text-white text-[7px] font-mono font-bold px-1 rounded-sm z-10" x-text="language === 'en' ? 'SOLD' : 'VENDIDO'"></span>
                                </template>
                            </button>
                        </template>

                        <!-- Empty state if search returns nothing -->
                        <template x-if="filteredMtg.length === 0">
                            <div class="col-span-full flex flex-col items-center justify-center py-12 text-[var(--muted)] text-center">
                                <span class="text-xl mb-1">📭</span>
                                <p class="text-mono text-xs" x-text="language === 'en' ? 'No MTG cards found.' : 'No se encontraron cartas de MTG.'"></p>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Yu-Gi-Oh! View -->
                <div x-show="activeTab === 'yugioh'" class="flex flex-col gap-4" x-cloak>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex flex-col gap-1">
                            <h2 class="text-title text-[var(--ink)]">Yu-Gi-Oh!</h2>
                            <p class="text-mono text-[10px] text-[var(--muted)]" x-text="language === 'en'
                                ? 'Click on any card to view its TCGPlayer stats and database info.'
                                : 'Haz clic en cualquier carta para ver sus estadísticas de TCGPlayer e info de la base de datos.'">
                            </p>
                        </div>
                        <!-- Search Box -->
                        <div class="relative w-full sm:w-48">
                            <input 
                                type="text" 
                                x-model="ygoSearch" 
                                @input="ygoPage = 1"
                                :placeholder="language === 'en' ? 'Search YGO...' : 'Buscar YGO...'" 
                                class="w-full bg-[var(--surface)] text-[var(--ink)] text-xs rounded border border-[var(--border)] px-3 py-1.5 focus:outline-none focus:border-[var(--primary)] transition-all font-mono"
                            />
                        </div>
                    </div>

                    <!-- Cards Grid -->
                    <div class="grid grid-cols-4 sm:grid-cols-5 gap-3 bg-[var(--surface)] border border-[var(--border)] rounded-lg p-6 min-h-[300px]">
                        <template x-for="card in paginatedYgo" :key="card.id">
                            <button 
                                type="button"
                                @click="selectedId = card.id"
                                :class="{
                                    'border-[var(--primary)] ring-2 ring-[var(--primary)] ring-offset-2 ring-offset-[var(--bg)]': selectedId === card.id,
                                    'border-[var(--border)] hover:border-[var(--ink)]': selectedId !== card.id,
                                    'grayscale opacity-60': card.is_sold
                                }"
                                class="aspect-[2.5/3.5] border bg-[var(--surface-raised)] rounded-md flex flex-col items-center justify-center p-1 focus-ring-signature transition-all relative overflow-hidden group"
                            >
                                <template x-if="card.image_url">
                                    <img :src="card.image_url" :alt="card.name" class="w-full h-full object-cover rounded-sm" loading="lazy" />
                                </template>
                                <template x-if="!card.image_url">
                                    <div class="flex flex-col items-center justify-center p-2 text-center h-full">
                                        <span class="text-mono text-[8px] text-[var(--muted)] uppercase" x-text="card.setcode"></span>
                                        <span class="text-[9px] font-bold text-[var(--ink)] mt-1 leading-tight truncate w-full" x-text="card.name"></span>
                                    </div>
                                </template>

                                <template x-if="card.quantity > 1">
                                    <span class="absolute top-1 right-1 bg-[var(--bg)] border border-[var(--border)] text-[var(--primary)] text-[8px] font-mono px-1 rounded-sm z-10" x-text="'x' + card.quantity"></span>
                                </template>

                                <template x-if="card.is_sold">
                                    <span class="absolute bottom-1 left-1 bg-[var(--red)] border border-[var(--red)] text-white text-[7px] font-mono font-bold px-1 rounded-sm z-10" x-text="language === 'en' ? 'SOLD' : 'VENDIDO'"></span>
                                </template>
                            </button>
                        </template>

                        <!-- Empty state if search returns nothing -->
                        <template x-if="filteredYgo.length === 0">
                            <div class="col-span-full flex flex-col items-center justify-center py-12 text-[var(--muted)] text-center">
                                <span class="text-xl mb-1">📭</span>
                                <p class="text-mono text-xs" x-text="language === 'en' ? 'No Yu-Gi-Oh! cards found.' : 'No se encontraron cartas de Yu-Gi-Oh!.'"></p>
                            </div>
                        </template>
                    </div>

                    <!-- Pagination Controls -->
                    <template x-if="filteredYgo.length > ygoPageSize">
                        <div class="flex items-center justify-between mt-4 border-t border-[var(--border)] pt-4">
                            <span class="text-mono text-[10px] text-[var(--muted)]">
                                <span x-text="language === 'en' ? 'Showing page ' : 'Mostrando página '"></span>
                                <span class="text-[var(--ink)] font-bold" x-text="ygoPage"></span>
                                <span x-text="language === 'en' ? ' of ' : ' de '"></span>
                                <span class="text-[var(--ink)] font-bold" x-text="totalYgoPages"></span>
                                <span x-text="' (' + filteredYgo.length + ' cards)'"></span>
                            </span>
                            <div class="flex gap-2">
                                <button 
                                    type="button"
                                    @click="ygoPage = Math.max(1, ygoPage - 1); const first = paginatedYgo[0]; if (first) selectedId = first.id;"
                                    :disabled="ygoPage === 1"
                                    :class="ygoPage === 1 ? 'opacity-40 cursor-not-allowed' : 'hover:border-[var(--ink)] hover:text-[var(--ink)]'"
                                    class="border border-[var(--border)] px-3 py-1 rounded bg-[var(--surface-raised)] text-mono text-[10px] uppercase text-[var(--muted)] transition-all"
                                >
                                    Prev
                                </button>
                                <button 
                                    type="button"
                                    @click="ygoPage = Math.min(totalYgoPages, ygoPage + 1); const first = paginatedYgo[0]; if (first) selectedId = first.id;"
                                    :disabled="ygoPage === totalYgoPages"
                                    :class="ygoPage === totalYgoPages ? 'opacity-40 cursor-not-allowed' : 'hover:border-[var(--ink)] hover:text-[var(--ink)]'"
                                    class="border border-[var(--border)] px-3 py-1 rounded bg-[var(--surface-raised)] text-mono text-[10px] uppercase text-[var(--muted)] transition-all"
                                >
                                    Next
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </section>

            <!-- RIGHT: Selected Item Details -->
            <section class="w-full lg:w-5/12 flex flex-col gap-4">
                <div class="flex flex-col gap-1">
                    <h2 class="text-title text-[var(--ink)]">
                        <span x-text="language === 'en' ? 'Inspection' : 'Inspección'"></span>
                    </h2>
                    <p class="text-mono text-[10px] text-[var(--muted)]" x-text="language === 'en'
                        ? 'Attributes & details lore.'
                        : 'Atributos y detalles del lore.'">
                    </p>
                </div>

                <!-- Details Display Card -->
                <div 
                    x-show="selectedItem"
                    class="card-bench border border-[var(--border)] rounded-lg p-6 flex flex-col gap-6 w-full"
                >
                    <!-- Item Details View -->
                    <template x-if="selectedType === 'item'">
                        <div class="flex flex-col gap-6 w-full">
                            <!-- Item header -->
                            <div class="flex flex-col gap-1 border-b border-[var(--border)] pb-4">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-xl font-bold text-[var(--ink)]" x-text="language === 'en' ? selectedItem.name : selectedItem.name_es"></h3>
                                    <!-- Rarity Chip -->
                                    <span 
                                        :class="{
                                            'text-[var(--muted)] border-[var(--border)] bg-[var(--surface-raised)]': selectedItem.rarity === 'Common',
                                            'text-[var(--blue)] border-[var(--blue)] bg-opacity-10 bg-[var(--blue)]': selectedItem.rarity === 'Rare',
                                            'text-[var(--accent)] border-[var(--accent)] bg-opacity-10 bg-[var(--accent)]': selectedItem.rarity === 'Epic',
                                            'text-[var(--yellow)] border-[var(--yellow)] bg-opacity-10 bg-[var(--yellow)]': selectedItem.rarity === 'Legendary'
                                        }"
                                        class="font-mono text-[10px] uppercase border px-2.5 py-0.5 rounded-full"
                                        x-text="selectedItem.rarity"
                                    ></span>
                                </div>
                                <span class="text-mono text-xs text-[var(--muted)]" x-text="selectedItem.type"></span>
                             </div>

                             <!-- Item Lore -->
                             <div class="flex flex-col gap-2">
                                 <span class="text-mono text-[10px] text-[var(--muted)] uppercase" x-text="language === 'en' ? 'Lore Description' : 'Descripción del Lore'"></span>
                                 <p class="text-body text-sm italic text-[var(--ink)]" x-text="language === 'en' ? selectedItem.desc : selectedItem.desc_es"></p>
                             </div>

                             <!-- Item Stats / Effects -->
                             <div class="flex flex-col gap-3">
                                 <span class="text-mono text-[10px] text-[var(--muted)] uppercase" x-text="language === 'en' ? 'Active Effects' : 'Efectos Activos'"></span>
                                 <ul class="flex flex-col gap-1.5">
                                     <template x-for="stat in selectedItem.stats" :key="stat">
                                         <li class="text-mono text-xs text-[var(--primary)] flex items-center gap-2">
                                             <span class="w-1.5 h-1.5 bg-[var(--primary)] rounded-full"></span>
                                             <span x-text="stat"></span>
                                         </li>
                                     </template>
                                 </ul>
                             </div>

                             <!-- Weight / Slots visual metadata -->
                             <div class="border-t border-[var(--border)] pt-4 flex justify-between text-mono text-xs text-[var(--muted)]">
                                 <div>
                                     <span x-text="language === 'en' ? 'Weight: ' : 'Peso: '"></span>
                                     <span class="text-[var(--ink)]">1.5 kg</span>
                                 </div>
                                 <div>
                                     <span x-text="language === 'en' ? 'Slot: Bag' : 'Ranura: Bolsa'"></span>
                                 </div>
                             </div>
                        </div>
                    </template>

                    <!-- MTG Card Details View -->
                    <template x-if="selectedType === 'mtg'">
                        <div class="flex flex-col sm:flex-row gap-6 w-full items-center sm:items-start">
                            <!-- Left inside card details: image -->
                            <template x-if="selectedItem.image_url">
                                <div class="w-full sm:w-1/2 flex justify-center">
                                    <img :src="selectedItem.image_url" :alt="selectedItem.name" class="w-44 h-auto object-contain rounded-md shadow-lg border border-[var(--border)] transition-transform duration-200 hover:scale-105" />
                                </div>
                            </template>
                             
                            <!-- Right inside card details: stats -->
                            <div class="flex-1 flex flex-col gap-4 w-full">
                                <div class="flex flex-col gap-1 border-b border-[var(--border)] pb-3">
                                    <h3 class="text-lg font-bold text-[var(--ink)]" x-text="selectedItem.name"></h3>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-mono text-[10px] text-[var(--muted)]" x-text="selectedItem.type_line"></span>
                                        <span class="text-mono text-[10px] text-[var(--primary)]" x-text="selectedItem.mana_cost"></span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-2 text-mono text-xs">
                                    <div class="text-[var(--muted)]">Set:</div>
                                    <div class="text-[var(--ink)] uppercase" x-text="selectedItem.set"></div>
                                     
                                    <div class="text-[var(--muted)]">Number:</div>
                                    <div class="text-[var(--ink)]" x-text="'#' + selectedItem.number"></div>

                                    <div class="text-[var(--muted)]">Rarity:</div>
                                    <div class="text-[var(--ink)] capitalize" x-text="selectedItem.rarity"></div>

                                    <div class="text-[var(--muted)]">Quantity:</div>
                                    <div class="text-[var(--ink)]" x-text="selectedItem.quantity"></div>

                                    <div class="text-[var(--muted)]">Price (USD):</div>
                                    <div class="text-[var(--accent)] font-bold" x-text="selectedItem.price ? '$' + parseFloat(selectedItem.price).toFixed(2) : '-'"></div>

                                    <div class="text-[var(--muted)]">Status:</div>
                                    <div :class="selectedItem.is_sold ? 'text-[var(--red)] font-bold' : 'text-[var(--accent)] font-bold'" x-text="selectedItem.is_sold ? (language === 'en' ? 'Sold' : 'Vendido') : (language === 'en' ? 'Available' : 'Disponible')"></div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Yugioh Card Details View -->
                    <template x-if="selectedType === 'yugioh'">
                        <div class="flex flex-col sm:flex-row gap-6 w-full items-center sm:items-start">
                            <!-- Left inside card details: image -->
                            <template x-if="selectedItem.image_url">
                                <div class="w-full sm:w-1/2 flex justify-center">
                                    <img :src="selectedItem.image_url" :alt="selectedItem.name" class="w-44 h-auto object-contain rounded-md shadow-lg border border-[var(--border)] transition-transform duration-200 hover:scale-105" />
                                </div>
                            </template>

                            <!-- Right inside card details: stats -->
                            <div class="flex-1 flex flex-col gap-4 w-full">
                                <div class="flex flex-col gap-1 border-b border-[var(--border)] pb-3">
                                    <h3 class="text-lg font-bold text-[var(--ink)]" x-text="selectedItem.name"></h3>
                                    <span class="text-mono text-[10px] text-[var(--muted)]" x-text="selectedItem.type"></span>
                                </div>

                                <div class="grid grid-cols-2 gap-2 text-mono text-xs">
                                    <div class="text-[var(--muted)]">Code:</div>
                                    <div class="text-[var(--ink)]" x-text="selectedItem.setcode"></div>

                                    <div class="text-[var(--muted)]">Rarity:</div>
                                    <div class="text-[var(--ink)]" x-text="selectedItem.rarity"></div>

                                    <div class="text-[var(--muted)]">Quantity:</div>
                                    <div class="text-[var(--ink)]" x-text="selectedItem.quantity"></div>

                                    <div class="text-[var(--muted)]">Set Price:</div>
                                    <div class="text-[var(--accent)] font-bold" x-text="selectedItem.price ? '$' + parseFloat(selectedItem.price).toFixed(2) : '-'"></div>

                                    <div class="text-[var(--muted)]">Card Price (TCG):</div>
                                    <div class="text-[var(--accent)] font-bold" x-text="selectedItem.card_price ? '$' + parseFloat(selectedItem.card_price).toFixed(2) : '-'"></div>

                                    <div class="text-[var(--muted)]">Status:</div>
                                    <div :class="selectedItem.is_sold ? 'text-[var(--red)] font-bold' : 'text-[var(--accent)] font-bold'" x-text="selectedItem.is_sold ? (language === 'en' ? 'Sold' : 'Vendido') : (language === 'en' ? 'Available' : 'Disponible')"></div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Empty State -->
                <div 
                    x-show="!selectedItem"
                    class="card-bench border border-[var(--border)] rounded-lg p-6 flex flex-col items-center justify-center text-center text-[var(--muted)] min-h-[200px]"
                >
                    <span class="text-3xl mb-2">🔍</span>
                    <p class="text-mono text-xs" x-text="language === 'en' ? 'Select an item/card to inspect' : 'Selecciona un objeto/carta para inspeccionar'"></p>
                </div>
            </section>
        </div>

    </div>
</x-layouts::guest>

