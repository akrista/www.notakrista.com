<x-layouts::guest>
    <div x-data='{
        activeTab: "items",
        selectedId: @json($initialItemId),
        selectedType: "item",
        items: @json($items),
        mtgCards: @json($mtgCards),
        yugiohCards: @json($yugiohCards),
        warframeData: @json($warframeData),
        
        wfSearch: "",
        wfCategoryFilter: "all",
        
        ygoSearch: "",
        ygoCategoryFilter: "all",
        ygoRarityFilter: "all",
        ygoStatusFilter: "all",
        ygoSort: "name_asc",
        ygoPage: 1,
        ygoPageSize: 20,
        ygoViewMode: "grid",
        ygoZoomImage: null,
        
        mtgSearch: "",

        get filteredWarframeItems() {
            if (!this.warframeData || !this.warframeData.items) return [];
            return this.warframeData.items.filter(item => {
                if (this.wfCategoryFilter !== "all") {
                    if (this.wfCategoryFilter === "warframes" && !["Warframe", "Archwing", "Nechramech"].includes(item.category)) return false;
                    if (this.wfCategoryFilter === "weapons" && !["Primary", "Secondary", "Melee", "ArchWeapon"].includes(item.category)) return false;
                    if (this.wfCategoryFilter === "mods" && !["Mod", "Riven"].includes(item.category)) return false;
                    if (this.wfCategoryFilter === "relics" && item.category !== "Relic") return false;
                }
                if (this.wfSearch) {
                    const q = this.wfSearch.toLowerCase();
                    const nameMatch = (item.name || "").toLowerCase().includes(q);
                    const typeMatch = (item.item_type || "").toLowerCase().includes(q);
                    const catMatch = (item.category || "").toLowerCase().includes(q);
                    if (!nameMatch && !typeMatch && !catMatch) return false;
                }
                return true;
            });
        },
        
        get ygoStats() {
            let totalQty = 0;
            let totalVal = 0;
            let availableCount = 0;
            let soldCount = 0;
            (this.yugiohCards || []).forEach(c => {
                const qty = c.quantity || 1;
                totalQty += qty;
                const price = parseFloat(c.card_price || c.price || 0);
                totalVal += price * qty;
                if (c.is_sold) {
                    soldCount++;
                } else {
                    availableCount++;
                }
            });
            return {
                totalQuantity: totalQty,
                uniqueCount: (this.yugiohCards || []).length,
                totalValue: totalVal,
                availableCount: availableCount,
                soldCount: soldCount
            };
        },

        get availableYgoRarities() {
            const rarities = new Set();
            (this.yugiohCards || []).forEach(c => {
                if (c.rarity) rarities.add(c.rarity);
            });
            return Array.from(rarities).sort();
        },
        
        get filteredMtg() {
            return this.mtgCards.filter(c => (c.name || "").toLowerCase().includes(this.mtgSearch.toLowerCase()));
        },
        
        get filteredYgo() {
            let list = (this.yugiohCards || []).filter(c => {
                if (this.ygoSearch) {
                    const q = this.ygoSearch.toLowerCase();
                    const nameMatch = (c.name || "").toLowerCase().includes(q);
                    const setMatch = (c.setcode || "").toLowerCase().includes(q);
                    const typeMatch = (c.type || "").toLowerCase().includes(q);
                    if (!nameMatch && !setMatch && !typeMatch) return false;
                }

                if (this.ygoCategoryFilter !== "all") {
                    const typeStr = ((c.type || "") + " " + (c.frame_type || "")).toLowerCase();
                    if (this.ygoCategoryFilter === "monster") {
                        const isExtra = ["fusion", "synchro", "xyz", "link"].some(t => typeStr.includes(t));
                        if (isExtra || (!typeStr.includes("monster") && !["normal", "effect", "ritual", "pendulum"].includes(c.frame_type))) return false;
                    } else if (this.ygoCategoryFilter === "spell") {
                        if (!typeStr.includes("spell") && c.frame_type !== "spell") return false;
                    } else if (this.ygoCategoryFilter === "trap") {
                        if (!typeStr.includes("trap") && c.frame_type !== "trap") return false;
                    } else if (this.ygoCategoryFilter === "extra") {
                        const isExtra = ["fusion", "synchro", "xyz", "link"].some(t => typeStr.includes(t));
                        if (!isExtra) return false;
                    }
                }

                if (this.ygoRarityFilter !== "all" && c.rarity !== this.ygoRarityFilter) {
                    return false;
                }

                if (this.ygoStatusFilter === "available" && c.is_sold) return false;
                if (this.ygoStatusFilter === "sold" && !c.is_sold) return false;

                return true;
            });

            list.sort((a, b) => {
                if (this.ygoSort === "name_asc") {
                    return (a.name || "").localeCompare(b.name || "");
                }
                if (this.ygoSort === "name_desc") {
                    return (b.name || "").localeCompare(a.name || "");
                }
                if (this.ygoSort === "price_desc") {
                    const valA = parseFloat(a.card_price || a.price || 0);
                    const valB = parseFloat(b.card_price || b.price || 0);
                    return valB - valA;
                }
                if (this.ygoSort === "price_asc") {
                    const valA = parseFloat(a.card_price || a.price || 0);
                    const valB = parseFloat(b.card_price || b.price || 0);
                    return valA - valB;
                }
                if (this.ygoSort === "rarity") {
                    return (a.rarity || "").localeCompare(b.rarity || "");
                }
                if (this.ygoSort === "quantity_desc") {
                    return (b.quantity || 1) - (a.quantity || 1);
                }
                if (this.ygoSort === "setcode") {
                    return (a.setcode || "").localeCompare(b.setcode || "");
                }
                return 0;
            });

            return list;
        },
        
        get paginatedYgo() {
            if (this.ygoPageSize >= 9999) return this.filteredYgo;
            const start = (this.ygoPage - 1) * this.ygoPageSize;
            return this.filteredYgo.slice(start, start + this.ygoPageSize);
        },
        
        get totalYgoPages() {
            if (this.ygoPageSize >= 9999) return 1;
            return Math.ceil(this.filteredYgo.length / this.ygoPageSize) || 1;
        },

        resetYgoFilters() {
            this.ygoSearch = "";
            this.ygoCategoryFilter = "all";
            this.ygoRarityFilter = "all";
            this.ygoStatusFilter = "all";
            this.ygoSort = "name_asc";
            this.ygoPage = 1;
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
                <span x-text="language === 'es' ? 'Bolsa de Inventario' : 'Inventory Bag'"></span>
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
                <span x-text="language === 'es' ? 'Bolsa RPG' : 'RPG Bag'"></span>
            </button>
            <button 
                type="button" 
                @click="selectTab('yugioh')"
                :class="activeTab === 'yugioh' ? 'border-[var(--primary)] text-[var(--ink)]' : 'border-transparent text-[var(--muted)] hover:text-[var(--ink)]'"
                class="border-b-2 px-4 py-2 font-mono text-xs uppercase tracking-wider transition-all focus:outline-none"
            >
                Yu-Gi-Oh!
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
                @click="selectTab('warframe')"
                :class="activeTab === 'warframe' ? 'border-[var(--primary)] text-[var(--ink)]' : 'border-transparent text-[var(--muted)] hover:text-[var(--ink)]'"
                class="border-b-2 px-4 py-2 font-mono text-xs uppercase tracking-wider transition-all focus:outline-none"
            >
                Warframe
            </button>
        </div>

        <div class="w-full flex flex-col lg:flex-row gap-12 lg:gap-16 items-start">
            <!-- LEFT: Inventory Bag Grid -->
            <section class="w-full lg:w-7/12 flex flex-col gap-4">
                
                <!-- RPG Bag View -->
                <div x-show="activeTab === 'items'" class="flex flex-col gap-4">
                    <div class="flex flex-col gap-1">
                        <h2 class="text-title text-[var(--ink)]">
                            <span x-text="language === 'es' ? 'Inventario Unificado' : 'Unified Inventory'"></span>
                        </h2>
                        <p class="text-mono text-[10px] text-[var(--muted)]" x-text="language === 'es'
                            ? 'Haz clic en cualquier objeto de la ranura de inventario para inspeccionar sus detalles.'
                            : 'Click on any item in the inventory slots to inspect its details.'">
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
                                <span class="text-[9px] font-mono mt-1 text-center leading-tight truncate w-full px-1" x-text="language === 'es' ? item.name_es : item.name"></span>
                            </button>
                        </template>

                        <!-- Padding slots to complete a standard 20-slot grid bags -->
                        <template x-for="i in Array.from({length: 20 - Object.keys(items).length})" :key="i">
                            <div class="aspect-square border border-[var(--border)] bg-opacity-20 bg-[var(--bg)] border-dashed rounded-lg flex items-center justify-center text-[var(--muted)] opacity-60 select-none">
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
                            <p class="text-mono text-[10px] text-[var(--muted)]" x-text="language === 'es'
                                ? 'Haz clic en cualquier carta para ver sus estadísticas de Scryfall e ilustración.'
                                : 'Click on any card to view its Scryfall stats and artwork.'">
                            </p>
                        </div>
                        <!-- Search Box -->
                        <div class="relative w-full sm:w-48">
                            <input 
                                type="text" 
                                x-model="mtgSearch" 
                                :placeholder="language === 'es' ? 'Buscar MTG...' : 'Search MTG...'" 
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
                                    <span class="absolute bottom-1 left-1 bg-[var(--red)] border border-[var(--red)] text-white text-[7px] font-mono font-bold px-1 rounded-sm z-10" x-text="language === 'es' ? 'VENDIDO' : 'SOLD'"></span>
                                </template>
                            </button>
                        </template>

                        <!-- Empty state if search returns nothing -->
                        <template x-if="filteredMtg.length === 0">
                            <div class="col-span-full flex flex-col items-center justify-center py-12 text-[var(--muted)] text-center">
                                <span class="text-xl mb-1">📭</span>
                                <p class="text-mono text-xs" x-text="language === 'es' ? 'No se encontraron cartas de MTG.' : 'No MTG cards found.'"></p>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Yu-Gi-Oh! View -->
                <div x-show="activeTab === 'yugioh'" class="flex flex-col gap-4" x-cloak>
                    <!-- Collection Header & Stats Bar -->
                    <div class="flex flex-col gap-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="flex flex-col gap-1">
                                <h2 class="text-title text-[var(--ink)] flex items-center gap-2">
                                    <span>Yu-Gi-Oh!</span>
                                    <span class="text-mono text-xs font-normal text-[var(--muted)]" x-text="'(' + ygoStats.totalQuantity + ' cards / ' + ygoStats.uniqueCount + ' unique)'"></span>
                                </h2>
                                <p class="text-mono text-[10px] text-[var(--muted)]" x-text="language === 'es'
                                    ? 'Busca, filtra e inspecciona tu colección de Yu-Gi-Oh! TCG.'
                                    : 'Search, filter, and inspect your Yu-Gi-Oh! TCG collection.'">
                                </p>
                            </div>
                            
                            <!-- Overall Metrics Pill Badges -->
                            <div class="flex items-center gap-2 font-mono text-[10px]">
                                <div class="bg-[var(--surface-raised)] border border-[var(--border)] px-2.5 py-1 rounded flex items-center gap-1.5">
                                    <span class="text-[var(--muted)] uppercase" x-text="language === 'es' ? 'Valor Est.:' : 'Est. Value:'"></span>
                                    <span class="text-[var(--accent)] font-bold" x-text="'$' + ygoStats.totalValue.toFixed(2)"></span>
                                </div>
                                <div class="bg-[var(--surface-raised)] border border-[var(--border)] px-2.5 py-1 rounded flex items-center gap-1.5">
                                    <span class="text-[var(--accent)] font-bold" x-text="ygoStats.availableCount"></span>
                                    <span class="text-[var(--muted)]" x-text="language === 'es' ? 'disp' : 'avail'"></span>
                                    <span class="text-[var(--muted)]" x-show="ygoStats.soldCount > 0" x-text="'/ ' + ygoStats.soldCount + ' sold'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Search & Filters Toolbar -->
                        <div class="bg-[var(--surface)] border border-[var(--border)] rounded-lg p-3 flex flex-col gap-3">
                            <!-- Row 1: Search box & Category Pills -->
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                                <!-- Search Input with Clear Button -->
                                <div class="relative flex-1">
                                    <input 
                                        type="text" 
                                        x-model="ygoSearch" 
                                        @input="ygoPage = 1"
                                        :placeholder="language === 'es' ? 'Buscar por nombre, código de set (ej. LOB-001) o tipo...' : 'Search by name, set code (e.g. LOB-001), or type...'" 
                                        class="w-full bg-[var(--surface-raised)] text-[var(--ink)] text-xs rounded border border-[var(--border)] pl-8 pr-7 py-1.5 focus:outline-none focus:border-[var(--primary)] transition-all font-mono"
                                    />
                                    <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs text-[var(--muted)]">🔍</span>
                                    <button 
                                        type="button" 
                                        x-show="ygoSearch" 
                                        @click="ygoSearch = ''; ygoPage = 1"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-[var(--muted)] hover:text-[var(--ink)] focus:outline-none font-mono"
                                    >&times;</button>
                                </div>

                                <!-- Category Filter Pills -->
                                <div class="flex flex-wrap items-center gap-1">
                                    <template x-for="cat in [
                                        { id: 'all', label_en: 'All', label_es: 'Todos' },
                                        { id: 'monster', label_en: 'Monsters', label_es: 'Monstruos' },
                                        { id: 'spell', label_en: 'Spells', label_es: 'Magias' },
                                        { id: 'trap', label_en: 'Traps', label_es: 'Trampas' },
                                        { id: 'extra', label_en: 'Extra Deck', label_es: 'Deck Extra' }
                                    ]" :key="cat.id">
                                        <button 
                                            type="button" 
                                            @click="ygoCategoryFilter = cat.id; ygoPage = 1"
                                            :class="ygoCategoryFilter === cat.id ? 'bg-[var(--primary)] text-[var(--bg)] font-bold border-[var(--primary)]' : 'bg-[var(--surface-raised)] text-[var(--muted)] border-[var(--border)] hover:text-[var(--ink)]'"
                                            class="border text-[10px] font-mono uppercase px-2 py-1 rounded transition-all focus:outline-none"
                                            x-text="language === 'es' ? cat.label_es : cat.label_en"
                                        ></button>
                                    </template>
                                </div>
                            </div>

                            <!-- Row 2: Detailed Filters & View Controls -->
                            <div class="flex flex-wrap items-center justify-between gap-2 border-t border-[var(--border)] pt-2.5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <!-- Rarity Dropdown -->
                                    <select 
                                        x-model="ygoRarityFilter" 
                                        @change="ygoPage = 1"
                                        class="bg-[var(--surface-raised)] text-[var(--ink)] text-[11px] font-mono rounded border border-[var(--border)] px-2 py-1 focus:outline-none focus:border-[var(--primary)]"
                                    >
                                        <option value="all" x-text="language === 'es' ? 'Todas las Rarezas' : 'All Rarities'"></option>
                                        <template x-for="rarity in availableYgoRarities" :key="rarity">
                                            <option :value="rarity" x-text="rarity"></option>
                                        </template>
                                    </select>

                                    <!-- Status Dropdown -->
                                    <select 
                                        x-model="ygoStatusFilter" 
                                        @change="ygoPage = 1"
                                        class="bg-[var(--surface-raised)] text-[var(--ink)] text-[11px] font-mono rounded border border-[var(--border)] px-2 py-1 focus:outline-none focus:border-[var(--primary)]"
                                    >
                                        <option value="all" x-text="language === 'es' ? 'Todos los Estados' : 'All Status'"></option>
                                        <option value="available" x-text="language === 'es' ? 'Solo Disponibles' : 'Available Only'"></option>
                                        <option value="sold" x-text="language === 'es' ? 'Solo Vendidas' : 'Sold Only'"></option>
                                    </select>

                                    <!-- Sort Dropdown -->
                                    <select 
                                        x-model="ygoSort"
                                        class="bg-[var(--surface-raised)] text-[var(--ink)] text-[11px] font-mono rounded border border-[var(--border)] px-2 py-1 focus:outline-none focus:border-[var(--primary)]"
                                    >
                                        <option value="name_asc" x-text="language === 'es' ? 'Ordenar: Nombre (A-Z)' : 'Sort: Name (A-Z)'"></option>
                                        <option value="name_desc" x-text="language === 'es' ? 'Ordenar: Nombre (Z-A)' : 'Sort: Name (Z-A)'"></option>
                                        <option value="price_desc" x-text="language === 'es' ? 'Ordenar: Precio (Mayor)' : 'Sort: Price (High-Low)'"></option>
                                        <option value="price_asc" x-text="language === 'es' ? 'Ordenar: Precio (Menor)' : 'Sort: Price (Low-High)'"></option>
                                        <option value="quantity_desc" x-text="language === 'es' ? 'Ordenar: Cantidad' : 'Sort: Quantity'"></option>
                                        <option value="setcode" x-text="language === 'es' ? 'Ordenar: Código de Set' : 'Sort: Set Code'"></option>
                                        <option value="rarity" x-text="language === 'es' ? 'Ordenar: Rareza' : 'Sort: Rarity'"></option>
                                    </select>
                                </div>

                                <div class="flex items-center gap-2">
                                    <!-- Page Size Selector -->
                                    <select 
                                        x-model.number="ygoPageSize" 
                                        @change="ygoPage = 1"
                                        class="bg-[var(--surface-raised)] text-[var(--ink)] text-[11px] font-mono rounded border border-[var(--border)] px-2 py-1 focus:outline-none focus:border-[var(--primary)]"
                                    >
                                        <option :value="10">10 / pg</option>
                                        <option :value="20">20 / pg</option>
                                        <option :value="50">50 / pg</option>
                                        <option :value="9999" x-text="language === 'es' ? 'Mostrar Todo' : 'Show All'"></option>
                                    </select>

                                    <!-- Grid vs List View Toggle -->
                                    <div class="flex border border-[var(--border)] rounded overflow-hidden">
                                        <button 
                                            type="button" 
                                            @click="ygoViewMode = 'grid'"
                                            :class="ygoViewMode === 'grid' ? 'bg-[var(--primary)] text-[var(--bg)] font-bold' : 'bg-[var(--surface-raised)] text-[var(--muted)] hover:text-[var(--ink)]'"
                                            class="px-2 py-1 text-[11px] font-mono transition-all focus:outline-none"
                                            :aria-label="language === 'es' ? 'Vista Cuadrícula' : 'Grid View'"
                                            title="Grid View"
                                        >
                                            ⊞
                                        </button>
                                        <button 
                                            type="button" 
                                            @click="ygoViewMode = 'list'"
                                            :class="ygoViewMode === 'list' ? 'bg-[var(--primary)] text-[var(--bg)] font-bold' : 'bg-[var(--surface-raised)] text-[var(--muted)] hover:text-[var(--ink)]'"
                                            class="px-2 py-1 text-[11px] font-mono transition-all focus:outline-none"
                                            :aria-label="language === 'es' ? 'Vista Lista' : 'List View'"
                                            title="List View"
                                        >
                                            ☰
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cards Display Container (Grid Mode) -->
                    <div x-show="ygoViewMode === 'grid'" class="grid grid-cols-4 sm:grid-cols-5 gap-3 bg-[var(--surface)] border border-[var(--border)] rounded-lg p-6 min-h-[300px]">
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
                                    <span class="absolute bottom-1 left-1 bg-[var(--red)] border border-[var(--red)] text-white text-[7px] font-mono font-bold px-1 rounded-sm z-10" x-text="language === 'es' ? 'VENDIDO' : 'SOLD'"></span>
                                </template>
                            </button>
                        </template>

                        <!-- Empty state if search or filters return nothing -->
                        <template x-if="filteredYgo.length === 0">
                            <div class="col-span-full flex flex-col items-center justify-center py-12 text-[var(--muted)] text-center gap-2">
                                <span class="text-2xl">📭</span>
                                <p class="text-mono text-xs text-[var(--ink)]" x-text="language === 'es' ? 'No se encontraron cartas de Yu-Gi-Oh! con esos criterios.' : 'No Yu-Gi-Oh! cards match your filter criteria.'"></p>
                                <button 
                                    type="button" 
                                    @click="resetYgoFilters()" 
                                    class="border border-[var(--primary)] text-[var(--primary)] hover:bg-[var(--primary)] hover:text-[var(--bg)] font-mono text-[10px] uppercase px-3 py-1 rounded transition-all focus-ring-signature mt-1"
                                    x-text="language === 'es' ? 'Limpiar Filtros' : 'Clear Filters'"
                                ></button>
                            </div>
                        </template>
                    </div>

                    <!-- Cards Display Container (List Mode) -->
                    <div x-show="ygoViewMode === 'list'" class="flex flex-col bg-[var(--surface)] border border-[var(--border)] rounded-lg p-3 min-h-[300px] divide-y divide-[var(--border)]">
                        <template x-for="card in paginatedYgo" :key="card.id">
                            <div 
                                @click="selectedId = card.id"
                                :class="selectedId === card.id ? 'bg-[var(--surface-raised)] border-l-2 border-l-[var(--primary)]' : 'hover:bg-[var(--surface-raised)]/50'"
                                class="flex items-center justify-between p-2 rounded cursor-pointer transition-all gap-3"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-14 bg-[var(--bg)] rounded border border-[var(--border)] flex-shrink-0 overflow-hidden relative">
                                        <template x-if="card.image_url">
                                            <img :src="card.image_url" :alt="card.name" class="w-full h-full object-cover" />
                                        </template>
                                        <template x-if="!card.image_url">
                                            <div class="w-full h-full flex items-center justify-center text-[8px] font-mono text-[var(--muted)]">YGO</div>
                                        </template>
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="text-xs font-bold text-[var(--ink)] truncate" x-text="card.name"></span>
                                        <div class="flex items-center gap-2 text-[10px] font-mono text-[var(--muted)]">
                                            <span class="text-[var(--primary)] font-semibold" x-text="card.setcode"></span>
                                            <span>•</span>
                                            <span x-text="card.type"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 flex-shrink-0 font-mono text-xs">
                                    <span class="text-[10px] text-[var(--muted)] border border-[var(--border)] px-1.5 py-0.5 rounded" x-text="card.rarity || 'Common'"></span>
                                    <span class="text-[var(--ink)]" x-text="'x' + card.quantity"></span>
                                    <span class="text-[var(--accent)] font-bold" x-text="(card.card_price || card.price) ? '$' + parseFloat(card.card_price || card.price).toFixed(2) : '-'"></span>
                                    <span :class="card.is_sold ? 'text-[var(--red)] font-bold' : 'text-[var(--accent)]'" class="text-[10px] uppercase" x-text="card.is_sold ? 'Sold' : 'Avail'"></span>
                                </div>
                            </div>
                        </template>

                        <!-- Empty state for List View -->
                        <template x-if="filteredYgo.length === 0">
                            <div class="flex flex-col items-center justify-center py-12 text-[var(--muted)] text-center gap-2">
                                <span class="text-2xl">📭</span>
                                <p class="text-mono text-xs text-[var(--ink)]" x-text="language === 'es' ? 'No se encontraron cartas de Yu-Gi-Oh! con esos criterios.' : 'No Yu-Gi-Oh! cards match your filter criteria.'"></p>
                                <button 
                                    type="button" 
                                    @click="resetYgoFilters()" 
                                    class="border border-[var(--primary)] text-[var(--primary)] hover:bg-[var(--primary)] hover:text-[var(--bg)] font-mono text-[10px] uppercase px-3 py-1 rounded transition-all focus-ring-signature mt-1"
                                    x-text="language === 'es' ? 'Limpiar Filtros' : 'Clear Filters'"
                                ></button>
                            </div>
                        </template>
                    </div>

                    <!-- Pagination Controls -->
                    <template x-if="filteredYgo.length > ygoPageSize && ygoPageSize < 9999">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mt-2 border-t border-[var(--border)] pt-4">
                            <span class="text-mono text-[10px] text-[var(--muted)]">
                                <span x-text="language === 'es' ? 'Mostrando página ' : 'Showing page '"></span>
                                <span class="text-[var(--ink)] font-bold" x-text="ygoPage"></span>
                                <span x-text="language === 'es' ? ' de ' : ' of '"></span>
                                <span class="text-[var(--ink)] font-bold" x-text="totalYgoPages"></span>
                                <span x-text="' (' + filteredYgo.length + ' cards)'"></span>
                            </span>
                            <div class="flex items-center gap-2">
                                <button 
                                    type="button"
                                    @click="ygoPage = Math.max(1, ygoPage - 1); const first = paginatedYgo[0]; if (first) selectedId = first.id;"
                                    :disabled="ygoPage === 1"
                                    :class="ygoPage === 1 ? 'opacity-40 cursor-not-allowed' : 'hover:border-[var(--ink)] hover:text-[var(--ink)]'"
                                    class="border border-[var(--border)] px-3 py-1 rounded bg-[var(--surface-raised)] text-mono text-[10px] uppercase text-[var(--muted)] transition-all"
                                >
                                    Prev
                                </button>
                                <div class="flex items-center gap-1 font-mono text-xs">
                                    <span class="px-2 py-0.5 text-[var(--ink)]" x-text="ygoPage + ' / ' + totalYgoPages"></span>
                                </div>
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

                <!-- Warframe Profile & Inventory View -->
                <div x-show="activeTab === 'warframe'" class="flex flex-col gap-6" x-cloak>
                    <!-- Account Profile Summary Header -->
                    <template x-if="warframeData">
                        <div class="bg-[var(--surface)] border border-[var(--border)] rounded-lg p-6 flex flex-col gap-6">
                            <!-- Account Header & Avatar -->
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-[var(--border)] pb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-lg bg-[var(--raised)] border border-[var(--border)] flex items-center justify-center font-mono text-xl text-[var(--primary)] font-semibold">
                                        <span x-text="warframeData.account_name ? warframeData.account_name.charAt(0).toUpperCase() : 'W'"></span>
                                    </div>
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-2">
                                            <h2 class="text-headline text-[var(--ink)] text-xl font-bold" x-text="warframeData.account_name"></h2>
                                            <!-- Mastery Rank Badge -->
                                            <span class="px-2 py-0.5 rounded bg-[oklch(0.810_0.150_80/0.15)] border border-[oklch(0.810_0.150_80/0.3)] text-[oklch(0.810_0.150_80)] font-mono text-xs font-semibold" x-text="'MR ' + warframeData.mastery_rank"></span>
                                        </div>
                                        <span class="text-mono text-[11px] text-[var(--muted)]" x-text="language === 'es' ? 'Última sinc. ' + (warframeData.last_imported_human || 'recientemente') : 'Last synced ' + (warframeData.last_imported_human || 'recently')"></span>
                                    </div>
                                </div>

                                <!-- Summary Counter Badges -->
                                <div class="flex flex-wrap gap-2">
                                    <div class="px-3 py-1 rounded bg-[var(--raised)] border border-[var(--border)] flex items-center gap-1.5 font-mono text-xs text-[var(--ink)]">
                                        <span class="text-[var(--accent)] font-semibold" x-text="warframeData.total_warframes || 0"></span>
                                        <span class="text-[var(--muted)]" x-text="language === 'es' ? 'Frames' : 'Frames'"></span>
                                    </div>
                                    <div class="px-3 py-1 rounded bg-[var(--raised)] border border-[var(--border)] flex items-center gap-1.5 font-mono text-xs text-[var(--ink)]">
                                        <span class="text-[var(--primary)] font-semibold" x-text="warframeData.total_weapons || 0"></span>
                                        <span class="text-[var(--muted)]" x-text="language === 'es' ? 'Armas' : 'Weapons'"></span>
                                    </div>
                                    <div class="px-3 py-1 rounded bg-[var(--raised)] border border-[var(--border)] flex items-center gap-1.5 font-mono text-xs text-[var(--ink)]">
                                        <span class="text-[var(--accent)] font-semibold" x-text="warframeData.total_mods || 0"></span>
                                        <span class="text-[var(--muted)]" x-text="language === 'es' ? 'Mods' : 'Mods'"></span>
                                    </div>
                                    <div class="px-3 py-1 rounded bg-[var(--raised)] border border-[var(--border)] flex items-center gap-1.5 font-mono text-xs text-[var(--ink)]">
                                        <span class="text-[var(--muted)] font-semibold" x-text="warframeData.total_relics || 0"></span>
                                        <span class="text-[var(--muted)]" x-text="language === 'es' ? 'Reliquias' : 'Relics'"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Economy Wallet Cards -->
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <div class="bg-[var(--raised)] border border-[var(--border)] rounded-md p-3 flex flex-col">
                                    <span class="text-mono text-[10px] uppercase text-[var(--muted)]" x-text="language === 'es' ? 'Créditos' : 'Credits'"></span>
                                    <span class="font-mono text-sm font-semibold text-[var(--ink)]" x-text="'🪙 ' + (warframeData.credits ? warframeData.credits.toLocaleString() : '0')"></span>
                                </div>
                                <div class="bg-[var(--raised)] border border-[var(--border)] rounded-md p-3 flex flex-col">
                                    <span class="text-mono text-[10px] uppercase text-[var(--muted)]">Platinum</span>
                                    <span class="font-mono text-sm font-semibold text-[var(--primary)]" x-text="'💎 ' + (warframeData.platinum ? warframeData.platinum.toLocaleString() : '0')"></span>
                                </div>
                                <div class="bg-[var(--raised)] border border-[var(--border)] rounded-md p-3 flex flex-col">
                                    <span class="text-mono text-[10px] uppercase text-[var(--muted)]" x-text="language === 'es' ? 'Trazas del Vacío' : 'Void Traces'"></span>
                                    <span class="font-mono text-sm font-semibold text-[var(--accent)]" x-text="'⚡ ' + (warframeData.void_traces ? warframeData.void_traces.toLocaleString() : '0')"></span>
                                </div>
                                <div class="bg-[var(--raised)] border border-[var(--border)] rounded-md p-3 flex flex-col">
                                    <span class="text-mono text-[10px] uppercase text-[var(--muted)]">Endo</span>
                                    <span class="font-mono text-sm font-semibold text-[var(--ink)]" x-text="'✨ ' + (warframeData.endo ? warframeData.endo.toLocaleString() : '0')"></span>
                                </div>
                            </div>

                            <!-- Open-Source Parser Attribution Footer -->
                            <div class="border-t border-[var(--border)] pt-3 flex flex-wrap items-center gap-1.5 font-mono text-[11px] text-[var(--muted)]">
                                <span x-text="language === 'es' ? 'Exportación de datos gracias a' : 'Data export powered by'"></span>
                                <a 
                                    href="https://github.com/sainan/alecaframe-inventory-parser/" 
                                    target="_blank" 
                                    rel="noopener noreferrer" 
                                    class="text-[var(--accent)] hover:underline font-semibold flex items-center gap-0.5 transition-all"
                                >
                                    sainan/alecaframe-inventory-parser
                                    <span class="text-[9px]">↗</span>
                                </a>
                            </div>
                        </div>
                    </template>

                    <!-- Filter Controls & Search -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" @click="wfCategoryFilter = 'all'" :class="wfCategoryFilter === 'all' ? 'bg-[var(--primary)] text-[var(--bg)] font-semibold' : 'bg-[var(--surface)] text-[var(--muted)] hover:text-[var(--ink)] border border-[var(--border)]'" class="px-3 py-1 rounded-md font-mono text-xs transition-all focus:outline-none">All</button>
                            <button type="button" @click="wfCategoryFilter = 'warframes'" :class="wfCategoryFilter === 'warframes' ? 'bg-[var(--primary)] text-[var(--bg)] font-semibold' : 'bg-[var(--surface)] text-[var(--muted)] hover:text-[var(--ink)] border border-[var(--border)]'" class="px-3 py-1 rounded-md font-mono text-xs transition-all focus:outline-none">Warframes</button>
                            <button type="button" @click="wfCategoryFilter = 'weapons'" :class="wfCategoryFilter === 'weapons' ? 'bg-[var(--primary)] text-[var(--bg)] font-semibold' : 'bg-[var(--surface)] text-[var(--muted)] hover:text-[var(--ink)] border border-[var(--border)]'" class="px-3 py-1 rounded-md font-mono text-xs transition-all focus:outline-none">Weapons</button>
                            <button type="button" @click="wfCategoryFilter = 'mods'" :class="wfCategoryFilter === 'mods' ? 'bg-[var(--primary)] text-[var(--bg)] font-semibold' : 'bg-[var(--surface)] text-[var(--muted)] hover:text-[var(--ink)] border border-[var(--border)]'" class="px-3 py-1 rounded-md font-mono text-xs transition-all focus:outline-none">Mods & Rivens</button>
                            <button type="button" @click="wfCategoryFilter = 'relics'" :class="wfCategoryFilter === 'relics' ? 'bg-[var(--primary)] text-[var(--bg)] font-semibold' : 'bg-[var(--surface)] text-[var(--muted)] hover:text-[var(--ink)] border border-[var(--border)]'" class="px-3 py-1 rounded-md font-mono text-xs transition-all focus:outline-none">Relics</button>
                        </div>

                        <div class="relative w-full sm:w-64">
                            <input type="text" x-model="wfSearch" placeholder="Filter Warframe inventory..." class="w-full bg-[var(--surface)] border border-[var(--border)] rounded-md px-3 py-1.5 text-xs text-[var(--ink)] placeholder-[var(--muted)] focus:outline-none focus:border-[var(--primary)] font-mono transition-all" />
                        </div>
                    </div>

                    <!-- Items Grid -->
                    <template x-if="filteredWarframeItems.length > 0">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <template x-for="item in filteredWarframeItems" :key="item.id">
                                <div class="bg-[var(--surface)] border border-[var(--border)] rounded-lg p-3 flex items-start gap-3 transition-all hover:border-[var(--primary)] focus-within:ring-2 focus-within:ring-[var(--primary)]">
                                    <div class="w-10 h-10 rounded-md bg-[var(--raised)] border border-[var(--border)] flex-shrink-0 flex items-center justify-center overflow-hidden p-1">
                                        <template x-if="item.image_url">
                                            <img :src="item.image_url" :alt="item.name" class="w-full h-full object-contain" />
                                        </template>
                                        <template x-if="!item.image_url">
                                            <span class="font-mono text-xs text-[var(--muted)]">❖</span>
                                        </template>
                                    </div>

                                    <div class="flex flex-col flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-1">
                                            <span class="font-semibold text-xs text-[var(--ink)] truncate" x-text="item.name"></span>
                                            <span class="px-1.5 py-0.5 rounded bg-[var(--raised)] border border-[var(--border)] font-mono text-[9px] uppercase tracking-wider text-[var(--accent)]" x-text="item.category"></span>
                                        </div>

                                        <div class="flex items-center gap-2 mt-1.5 font-mono text-[10px]">
                                            <template x-if="item.level !== undefined && item.level > 0">
                                                <span class="text-[var(--primary)] font-medium" x-text="'Rank ' + item.level"></span>
                                            </template>
                                            <template x-if="item.formas !== undefined && item.formas > 0">
                                                <span class="text-[var(--accent)]" x-text="item.formas + ' Formas'"></span>
                                            </template>
                                            <template x-if="item.fusion_rank !== null && item.fusion_rank !== undefined">
                                                <span class="text-[var(--ink)]" x-text="'Rank ' + item.fusion_rank + '/' + (item.max_fusion_rank || 10)"></span>
                                            </template>
                                            <template x-if="item.refinement">
                                                <span class="text-[var(--accent)] font-semibold" x-text="item.refinement"></span>
                                            </template>
                                            <template x-if="item.item_count > 1">
                                                <span class="text-[var(--muted)] ml-auto" x-text="'x' + item.item_count"></span>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <template x-if="!warframeData || filteredWarframeItems.length === 0">
                        <div class="bg-[var(--surface)] border border-[var(--border)] rounded-lg p-8 flex flex-col items-center justify-center text-center text-[var(--muted)] min-h-[200px]">
                            <span class="text-3xl mb-2">❖</span>
                            <p class="font-mono text-xs" x-text="language === 'es' ? 'No se encontraron objetos de inventario de Warframe con esos filtros.' : 'No Warframe inventory items found matching filters.'"></p>
                        </div>
                    </template>
                </div>
            </section>

            <!-- RIGHT: Selected Item Details -->
            <section class="w-full lg:w-5/12 flex flex-col gap-4">
                <div class="flex flex-col gap-1">
                    <h2 class="text-title text-[var(--ink)]">
                        <span x-text="language === 'es' ? 'Inspección' : 'Inspection'"></span>
                    </h2>
                    <p class="text-mono text-[10px] text-[var(--muted)]" x-text="language === 'es'
                        ? 'Atributos y detalles del lore.'
                        : 'Attributes & details lore.'">
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
                                    <h3 class="text-xl font-bold text-[var(--ink)]" x-text="language === 'es' ? selectedItem.name_es : selectedItem.name"></h3>
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
                                 <span class="text-mono text-[10px] text-[var(--muted)] uppercase" x-text="language === 'es' ? 'Descripción del Lore' : 'Lore Description'"></span>
                                 <p class="text-body text-sm italic text-[var(--ink)]" x-text="language === 'es' ? selectedItem.desc_es : selectedItem.desc"></p>
                             </div>

                             <!-- Item Stats / Effects -->
                             <div class="flex flex-col gap-3">
                                 <span class="text-mono text-[10px] text-[var(--muted)] uppercase" x-text="language === 'es' ? 'Efectos Activos' : 'Active Effects'"></span>
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
                                     <span x-text="language === 'es' ? 'Peso: ' : 'Weight: '"></span>
                                     <span class="text-[var(--ink)]">1.5 kg</span>
                                 </div>
                                 <div>
                                     <span x-text="language === 'es' ? 'Ranura: Bolsa' : 'Slot: Bag'"></span>
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
                                        <template x-if="selectedItem.mana_cost_html">
                                            <div x-html="selectedItem.mana_cost_html"></div>
                                        </template>
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
                                    <div :class="selectedItem.is_sold ? 'text-[var(--red)] font-bold' : 'text-[var(--accent)] font-bold'" x-text="selectedItem.is_sold ? (language === 'es' ? 'Vendido' : 'Sold') : (language === 'es' ? 'Disponible' : 'Available')"></div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Yugioh Card Details View -->
                    <template x-if="selectedType === 'yugioh'">
                        <div class="flex flex-col sm:flex-row gap-6 w-full items-center sm:items-start">
                            <!-- Left inside card details: image with zoom button -->
                            <template x-if="selectedItem.image_url">
                                <div class="w-full sm:w-1/2 flex flex-col items-center gap-2 group">
                                    <div class="relative overflow-hidden rounded-md border border-[var(--border)] shadow-lg cursor-pointer" @click="ygoZoomImage = selectedItem.image_url">
                                        <img :src="selectedItem.image_url" :alt="selectedItem.name" class="w-44 h-auto object-contain rounded-md transition-transform duration-300 group-hover:scale-105" />
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white font-mono text-[10px]">
                                            🔍 Click to Zoom
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Right inside card details: stats & external links -->
                            <div class="flex-1 flex flex-col gap-4 w-full">
                                <div class="flex flex-col gap-1 border-b border-[var(--border)] pb-3">
                                    <h3 class="text-lg font-bold text-[var(--ink)]" x-text="selectedItem.name"></h3>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-mono text-[10px] text-[var(--muted)]" x-text="selectedItem.type"></span>
                                        <span x-show="selectedItem.rarity" class="text-mono text-[9px] uppercase border border-[var(--primary)] text-[var(--primary)] px-1.5 py-0.5 rounded-full" x-text="selectedItem.rarity"></span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-2 text-mono text-xs">
                                    <div class="text-[var(--muted)]">Code:</div>
                                    <div class="text-[var(--ink)] font-bold" x-text="selectedItem.setcode"></div>

                                    <div class="text-[var(--muted)]">Rarity:</div>
                                    <div class="text-[var(--ink)]" x-text="selectedItem.rarity || 'Common'"></div>

                                    <div class="text-[var(--muted)]">Quantity:</div>
                                    <div class="text-[var(--ink)]" x-text="selectedItem.quantity"></div>

                                    <div class="text-[var(--muted)]">Set Price:</div>
                                    <div class="text-[var(--accent)] font-bold" x-text="selectedItem.price ? '$' + parseFloat(selectedItem.price).toFixed(2) : '-'"></div>

                                    <div class="text-[var(--muted)]">Card Price (TCG):</div>
                                    <div class="text-[var(--accent)] font-bold" x-text="selectedItem.card_price ? '$' + parseFloat(selectedItem.card_price).toFixed(2) : '-'"></div>

                                    <div class="text-[var(--muted)]" x-show="selectedItem.quantity > 1">Lot Value:</div>
                                    <div class="text-[var(--accent)] font-bold" x-show="selectedItem.quantity > 1" x-text="'$' + ((parseFloat(selectedItem.card_price || selectedItem.price || 0)) * selectedItem.quantity).toFixed(2)"></div>

                                    <div class="text-[var(--muted)]">Status:</div>
                                    <div :class="selectedItem.is_sold ? 'text-[var(--red)] font-bold' : 'text-[var(--accent)] font-bold'" x-text="selectedItem.is_sold ? (language === 'es' ? 'Vendido' : 'Sold') : (language === 'es' ? 'Disponible' : 'Available')"></div>
                                </div>

                                <!-- External Database Links -->
                                <div class="border-t border-[var(--border)] pt-3 flex flex-col gap-2">
                                    <span class="text-mono text-[9px] text-[var(--muted)] uppercase" x-text="language === 'es' ? 'Enlaces de Base de Datos' : 'Database Links'"></span>
                                    <div class="flex flex-wrap gap-2">
                                        <a 
                                            :href="'https://db.ygoprodeck.com/card/?search=' + encodeURIComponent(selectedItem.name)" 
                                            target="_blank" 
                                            rel="noopener noreferrer" 
                                            class="border border-[var(--border)] hover:border-[var(--primary)] bg-[var(--surface-raised)] text-[var(--ink)] hover:text-[var(--primary)] text-mono text-[10px] px-2.5 py-1 rounded transition-all flex items-center gap-1 focus-ring-signature"
                                        >
                                            <span>YGOPRODeck</span>
                                            <span class="text-[8px]">↗</span>
                                        </a>
                                        <a 
                                            :href="'https://www.tcgplayer.com/search/yugioh/product?q=' + encodeURIComponent(selectedItem.name)" 
                                            target="_blank" 
                                            rel="noopener noreferrer" 
                                            class="border border-[var(--border)] hover:border-[var(--primary)] bg-[var(--surface-raised)] text-[var(--ink)] hover:text-[var(--primary)] text-mono text-[10px] px-2.5 py-1 rounded transition-all flex items-center gap-1 focus-ring-signature"
                                        >
                                            <span>TCGPlayer</span>
                                            <span class="text-[8px]">↗</span>
                                        </a>
                                    </div>
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
                    <p class="text-mono text-xs" x-text="language === 'es' ? 'Selecciona un objeto/carta para inspeccionar' : 'Select an item/card to inspect'"></p>
                </div>
            </section>
        </div>

        <!-- Yu-Gi-Oh! Image Lightbox Modal -->
        <template x-if="ygoZoomImage">
            <div 
                class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
                @click.self="ygoZoomImage = null"
                @keydown.escape.window="ygoZoomImage = null"
            >
                <div class="relative bg-[var(--surface)] border border-[var(--border)] rounded-lg p-4 max-w-md w-full flex flex-col items-center gap-3 modal-lift">
                    <button 
                        type="button" 
                        @click="ygoZoomImage = null" 
                        class="absolute top-2 right-3 text-[var(--muted)] hover:text-[var(--ink)] font-mono text-lg focus:outline-none"
                    >&times;</button>
                    <img :src="ygoZoomImage" alt="Card Large View" class="max-h-[75vh] w-auto object-contain rounded-md border border-[var(--border)]" />
                    <span class="text-mono text-xs text-[var(--muted)]" x-text="selectedItem ? selectedItem.name : ''"></span>
                </div>
            </div>
        </template>

    </div>
</x-layouts::guest>

