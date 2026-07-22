<x-layouts::guest title="Todoticket Calculator | Jorge Thomas">
    <div class="w-full" x-data="{
        total: null,
        retiroOptimo: 0,
        comision: 0,
        restante: 0,
        rawInput: '',

        handleInput(e) {
            let inputValue = e.target.value.trim();
            inputValue = inputValue.replace(/[^\d.,]/g, '');
            let normalizedValue = inputValue;

            const dots = (normalizedValue.match(/\./g) || []).length;
            const commas = (normalizedValue.match(/,/g) || []).length;

            if (dots > 1 && normalizedValue.includes(',')) {
                normalizedValue = normalizedValue.replace(/\./g, '').replace(',', '.');
            } else if (commas > 1 && normalizedValue.includes('.')) {
                normalizedValue = normalizedValue.replace(/,/g, '');
            } else if (normalizedValue.includes(',') && !normalizedValue.includes('.')) {
                normalizedValue = normalizedValue.replace(',', '.');
            }

            const val = parseFloat(normalizedValue);
            this.total = isNaN(val) || val < 0 ? null : val;
            this.calculate();
        },

        applyPreset(amount) {
            this.total = amount;
            this.calculate();
            this.$refs.totalInput.value = amount;
        },

        clearAll() {
            this.total = null;
            this.retiroOptimo = 0;
            this.comision = 0;
            this.restante = 0;
            this.$refs.totalInput.value = '';
        },

        calculate() {
            if (this.total === null || this.total <= 0) {
                this.retiroOptimo = 0;
                this.comision = 0;
                this.restante = 0;
                return;
            }

            let retiroOptimo = Math.floor((this.total / 1.006) * 100) / 100;
            let comision = 0;
            let restante = 0;

            let iterations = 0;
            const maxIterations = 10;

            while (iterations < maxIterations) {
                comision = Math.round(retiroOptimo * 0.006 * 100) / 100;
                restante = this.total - (comision + retiroOptimo);
                restante = Math.round(restante * 100) / 100;

                if (restante <= 0 || Math.abs(restante) < 0.001) {
                    restante = 0;
                    break;
                }
                if (Object.is(restante, -0)) {
                    restante = 0;
                    break;
                }

                const nuevoRetiroOptimo = retiroOptimo + restante;
                let nuevoRetiroOptimoRedondeado = Math.floor(nuevoRetiroOptimo * 100) / 100;

                if (nuevoRetiroOptimoRedondeado <= retiroOptimo && restante > 0) {
                    nuevoRetiroOptimoRedondeado = Math.ceil(nuevoRetiroOptimo * 100) / 100;
                }

                retiroOptimo = nuevoRetiroOptimoRedondeado;
                iterations++;
            }

            comision = Math.round(retiroOptimo * 0.006 * 100) / 100;
            restante = this.total - (comision + retiroOptimo);
            restante = Math.round(restante * 100) / 100;

            if (retiroOptimo + comision > this.total) {
                retiroOptimo = Math.round((retiroOptimo - 0.01) * 100) / 100;
                comision = Math.round(retiroOptimo * 0.006 * 100) / 100;
                restante = this.total - (comision + retiroOptimo);
                restante = Math.round(restante * 100) / 100;
            }

            if (restante <= 0 || Math.abs(restante) < 0.001) {
                restante = 0;
            }
            if (Object.is(restante, -0)) {
                restante = 0;
            }

            this.retiroOptimo = retiroOptimo;
            this.comision = comision;
            this.restante = restante;
        },

        formatCurrency(value) {
            const normalized = value === 0 ? 0 : value;
            return new Intl.NumberFormat(language === 'es' ? 'es-VE' : 'en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(normalized);
        }
    }">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
            
            <!-- HEADER -->
            <div class="col-span-1 lg:col-span-12 order-1 flex flex-col gap-2">
                <h1 class="text-headline text-[var(--ink)]" x-text="language === 'es' ? 'Calculadora Todoticket: Optimizador de Vales' : 'Todoticket Calculator: Local Voucher Optimizer'"></h1>
                <p class="text-body text-sm text-[var(--muted)]" x-text="language === 'es' ? 'Optimiza retiros y comisiones de tickets alimenticios locales automáticamente.' : 'Optimize local food voucher withdrawals and commissions automatically.'"></p>
            </div>

            <!-- INPUT DECK -->
            <div class="col-span-1 lg:col-span-7 order-2 card-bench flex flex-col gap-4">
                <div class="flex flex-col gap-2">
                    <label for="total" class="text-label text-[var(--ink)]" x-text="language === 'es' ? 'Total Disponible' : 'Total Available'"></label>
                    <div class="relative w-full">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--muted)] font-mono text-lg select-none">Bs.</span>
                        <input
                            x-ref="totalInput"
                            id="total"
                            type="text"
                            inputmode="decimal"
                            placeholder="0,00"
                            @input="handleInput"
                            class="w-full bg-[var(--bg)] border border-[var(--border)] rounded-md py-3 pl-12 pr-4 text-lg text-[var(--ink)] focus:outline-none focus:ring-signature focus:border-[var(--primary)] font-mono transition-theme"
                        />
                    </div>
                </div>

                <!-- Tactile Preset Keys -->
                <div class="flex flex-wrap gap-2 pt-2">
                    <template x-for="preset in [100, 500, 1000, 5000]">
                        <button
                            type="button"
                            @click="applyPreset(preset)"
                            class="font-mono text-xs border border-[var(--border)] hover:border-[var(--primary)] hover:bg-[var(--surface-raised)] rounded px-3 py-1.5 transition-all text-[var(--muted)] hover:text-[var(--ink)] cursor-pointer focus-ring-signature"
                        >
                            +Bs. <span x-text="preset"></span>
                        </button>
                    </template>
                    <button
                        type="button"
                        @click="clearAll()"
                        class="font-mono text-xs border border-[var(--border)] hover:border-[var(--red)] hover:bg-[var(--red)]/10 rounded px-3 py-1.5 transition-all text-[var(--muted)] hover:text-[var(--red)] cursor-pointer focus-ring-signature"
                        x-text="language === 'es' ? 'Limpiar' : 'Clear'"
                    ></button>
                </div>
            </div>

            <!-- INTERACTIVE LEDGER (RESULTS) -->
            <div class="col-span-1 lg:col-span-5 lg:row-span-2 order-3 flex flex-col gap-6">
                <!-- Empty State (No amount entered) -->
                <div x-show="total === null || total <= 0" class="card-bench border border-[var(--border)] flex flex-col items-center justify-center py-20 text-center text-mono text-sm text-[var(--muted)]">
                    <p class="mb-2 font-bold text-[var(--primary)]">&gt; AWAITING INPUT_</p>
                    <p class="text-xs max-w-xs" x-text="language === 'es' ? 'Ingrese el saldo disponible en el panel de control para obtener el estado.' : 'Enter an available balance in the control deck to fetch statement.'"></p>
                </div>

                <!-- Active Ledger State -->
                <div x-show="total !== null && total > 0" class="card-bench border border-[var(--border)] p-6">
                    <!-- Terminal Header -->
                    <div class="border-b border-dashed border-[var(--border)] pb-3 mb-4 flex justify-between items-center text-xs text-[var(--muted)]">
                        <span class="font-bold uppercase tracking-wider text-label" x-text="language === 'es' ? 'Estado de Cuenta' : 'Account Statement'"></span>
                        <span class="font-mono">nk-tty0</span>
                    </div>

                    <!-- Ledger Rows -->
                    <div class="flex flex-col gap-2">
                        <div class="flex justify-between items-center py-2 border-b border-[var(--border)]">
                            <span class="text-[var(--muted)] uppercase tracking-wider text-xs" x-text="language === 'es' ? 'Balance Total' : 'Total Balance'"></span>
                            <span class="text-[var(--ink)] font-bold text-base font-mono">Bs. <span x-text="formatCurrency(total)"></span></span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-[var(--border)]">
                            <span class="text-[var(--muted)] uppercase tracking-wider text-xs" x-text="language === 'es' ? 'Comisión (0.6%)' : 'Commission (0.6%)'"></span>
                            <span class="text-[var(--red)] font-semibold font-mono">- Bs. <span x-text="formatCurrency(comision)"></span></span>
                        </div>
                        <div class="flex justify-between items-center py-3 bg-[var(--surface-raised)] light:bg-[var(--bg)] px-3 rounded-md border-y border-[var(--border)] transition-theme">
                            <span class="text-[var(--ink)] font-bold uppercase tracking-wider text-xs" x-text="language === 'es' ? 'Retiro Óptimo' : 'Optimal Withdrawal'"></span>
                            <span class="text-[var(--accent)] font-bold text-lg font-mono">Bs. <span x-text="formatCurrency(retiroOptimo)"></span></span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-[var(--muted)] uppercase tracking-wider text-xs" x-text="language === 'es' ? 'Restante' : 'Remaining'"></span>
                            <span class="font-bold font-mono" :class="restante > 0 ? 'text-[var(--blue)]' : 'text-[var(--muted)]'">
                                Bs. <span x-text="formatCurrency(restante)"></span>
                            </span>
                        </div>
                    </div>

                    <!-- Diagnostic/Alert Log Line -->
                    <div class="mt-6" x-show="restante > 0">
                        <div class="border border-[var(--yellow)] text-[var(--yellow)] px-3 py-2 rounded-md flex items-center gap-2 bg-[var(--yellow)]/5 text-xs">
                            <span class="font-bold">⚠</span>
                            <span x-text="language === 'es' ? 'Límite óptimo alcanzado. Quedará saldo restante.' : 'Optimized limit hit. Small remainder will be left.'"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TECHNICAL SPECS -->
            <div class="col-span-1 lg:col-span-7 order-4 bg-[var(--surface)] border border-[var(--border)] rounded-lg p-6 transition-theme flex flex-col gap-4">
                <div>
                    <h3 class="font-semibold text-xs text-[var(--ink)] mb-3 uppercase tracking-wide text-label" x-text="language === 'es' ? 'Especificaciones' : 'Specifications'"></h3>
                    <ul class="text-xs text-[var(--muted)] space-y-2 list-disc list-inside">
                        <li x-text="language === 'es' ? 'Cruza tu balance total frente a la comisión del 0.6% de la tarjeta.' : 'Cruises your total balance against the 0.6% card commission.'"></li>
                        <li x-text="language === 'es' ? 'Ejecuta un algoritmo de iteración para minimizar el saldo restante en cuenta.' : 'Runs standard iteration algorithm to minimize leftover pocket balances.'"></li>
                        <li x-text="language === 'es' ? 'Asegura el mayor retiro posible sin exceder los fondos disponibles.' : 'Ensures maximum possible cash-out without exceeding available funds.'"></li>
                    </ul>
                </div>

                <div class="border-t border-[var(--border)] pt-4">
                    <h4 class="font-semibold text-[10px] text-[var(--ink)] mb-2 uppercase tracking-wider text-label" x-text="language === 'es' ? '💡 Consejo de Ajuste Decimal' : '💡 Decimal Adjustment Tip'"></h4>
                    <p class="text-xs text-[var(--muted)] leading-relaxed" x-text="language === 'es' 
                            ? 'Dado que los cálculos del 0.6% generan fracciones infinitas y la plataforma solo acepta exactamente 2 decimales, es normal ver saldos restantes de Bs. 0.01 o Bs. 0.02. Si esto ocurre, intenta sumar manualmente Bs. 0.01 o más al Retiro Óptimo en el portal de Todoticket.' 
                            : 'Since 0.6% calculations generate infinite fractions, and the platform only accepts exactly 2 decimal values, minor remaining balances (Bs. 0.01 or Bs. 0.02) are normal. If a tiny remainder is left, try manually adding Bs. 0.01 or more to your Optimal Withdrawal in the Todoticket portal.'"></p>
                </div>
            </div>

            <!-- DISCLOSURE & FOOTER METADATA -->
            <div class="col-span-1 lg:col-span-12 order-5 text-xs text-[var(--muted)] flex flex-col gap-4 border-t border-[var(--border)] pt-6">
                <p class="text-[10px] leading-relaxed italic" x-show="language !== 'es'">
                    * This calculator is an independent utility and is not affiliated with, authorized, or endorsed by Todoticket Venezuela. It is created to assist workers with their biweekly pay-out workflow when logging into the <a href="https://mi.todoticketve.com/login" target="_blank" rel="noopener noreferrer" class="text-[var(--primary)] hover:underline">Todoticket Portal (mi.todoticketve.com)</a>.
                </p>
                <p class="text-[10px] leading-relaxed italic" x-show="language === 'es'">
                    * Esta calculadora es una herramienta independiente y no está afiliada, autorizada ni respaldada por Todoticket Venezuela. Se creó para ayudar a los trabajadores con su flujo de cobro quincenal al ingresar al <a href="https://mi.todoticketve.com/login" target="_blank" rel="noopener noreferrer" class="text-[var(--primary)] hover:underline">Portal Todoticket (mi.todoticketve.com)</a>.
                </p>
            </div>

        </div>
    </div>
</x-layouts::guest>
