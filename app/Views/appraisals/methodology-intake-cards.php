<section x-show="searchTab === 'matriz' && mode === 'intake'" class="mt-4" x-cloak>
    <p class="rounded-xl bg-teal-50 p-4 text-sm">Una tarjeta por inmueble confirmado. Vincular anuncios conserva cada versión; no promedia precios ni completa un anuncio con datos de otro. Coordenadas del portal = referencia sin verificar.</p>
    <div class="my-4 grid gap-3 sm:grid-cols-2" @input.stop @change.stop>
        <label class="label">Estado de recogida
            <select class="input" x-model="intakeFilter" @change="intakePage = 1; rebuildIntake()"><option value="all">Todos</option><template x-for="(label, key) in intakeStates" :key="key"><option :value="key" x-text="label"></option></template></select>
        </label>
        <label class="label">Buscar inmueble o anuncio
            <input class="input" x-model="intakeSearch" @input="intakePage = 1; rebuildIntake()" placeholder="Edificio, barrio, código o portal">
        </label>
    </div>
    <p class="mb-3 text-sm"><strong x-text="intakeCount"></strong> inmuebles · <span x-text="total"></span> anuncios. Los posibles duplicados siguen separados hasta confirmar su identidad.</p>
    <div class="grid gap-4 lg:grid-cols-2">
        <template x-for="card in intakeCards" :key="card.key">
            <article class="min-w-0 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <h4 class="text-lg font-semibold" x-text="card.title"></h4>
                <p class="mt-1 text-sm text-slate-600" x-text="card.rows[0].neighborhood || 'Ubicación publicada pendiente'"></p>
                <template x-for="offer in card.rows" :key="offer.id"><p class="mt-2 rounded-lg bg-slate-50 p-2 text-sm" x-text="(offer.source_name || 'Fuente pendiente') + ': ' + (offer.price_amount || 'precio pendiente') + ' COP · ' + (offer.area_m2 || 'área pendiente') + ' m² · ' + (offer.listing_code || 'código pendiente')"></p></template>
                <p class="mt-2 rounded-lg p-2 text-sm" :class="card.state.startsWith('selected') ? 'bg-emerald-50 text-emerald-900' : 'bg-amber-50 text-amber-900'" x-text="intakeStates[card.state]"></p>
                <p class="mt-2 text-sm text-amber-900" x-show="card.conflicts.length" x-text="'Diferencias entre anuncios: ' + card.conflicts.map(k => ({price_amount:'precio',area_m2:'área publicada',parking_spaces:'parqueaderos',ph_deposit_count:'depósitos'})[k]).join(', ')"></p>
                <p class="mt-2 text-sm" x-show="card.pending">Hay datos básicos pendientes. Consulta cada fuente.</p>
                <p class="mt-2 text-sm text-amber-900" x-show="card.candidates.length" x-text="'Posible mismo inmueble: ' + card.candidates.map(c => c.title).join(' · ') + '. Confirma antes de vincular.'"></p>
                <label class="label mt-3">Decisión del analista
                    <select class="input" :value="card.state" @change.stop="intakeDecision(card, $event.target.value)"><template x-for="(label, key) in intakeStates" :key="key"><option :value="key" x-text="label"></option></template></select>
                </label>
                <label class="label mt-3">Vincular a otro inmueble confirmado
                    <select class="input" @change.stop="intakeLink(card, $event.target.value); $event.target.value = ''"><option value="">Selecciona sólo si confirmaste que es el mismo</option><template x-for="target in intakeTargets.filter(t => t.key !== card.key)" :key="target.key"><option :value="target.key" x-text="target.title + ' · ' + target.key.slice(0, 8)"></option></template></select>
                    <span class="mt-1 block text-xs font-normal">La vinculación vuelve a Por revisar. No altera precios, áreas, fotos ni fuentes.</span>
                </label>
                <details class="mt-3 rounded-lg border p-3"><summary class="min-h-11 cursor-pointer font-semibold">Anuncios, características y soportes (<span x-text="card.rows.length"></span>)</summary>
                    <template x-for="row in card.rows" :key="row.id">
                        <div class="mt-3 border-t pt-3">
                            <p class="font-semibold" x-text="(row.source_name || 'Fuente pendiente') + ' · ' + (row.listing_code || 'Código pendiente')"></p>
                            <p class="mt-1 text-sm" x-text="'Oferta: ' + (row.price_amount || 'pendiente') + ' COP · ' + (row.price_unit || 'unidad pendiente') + ' · Área publicada: ' + (row.area_m2 || 'pendiente') + ' m²'"></p>
                            <p class="mt-1 text-sm" x-text="'Parqueaderos: ' + (row.parking_spaces || 'por confirmar') + ' · Depósitos: ' + (row.ph_deposit_count || row.ph_deposit_presence || 'por confirmar')"></p>
                            <p class="mt-1 text-sm" x-text="'Consulta: ' + (row.consulted_at || 'pendiente') + ' · Contacto: ' + (row.contact_name || '') + ' ' + (row.contact_phone || 'pendiente')"></p>
                            <p class="mt-1 text-sm text-amber-900" x-show="row.published_location" x-text="'Referencia publicada sin verificar: ' + row.published_location"></p>
                            <p class="mt-2 whitespace-pre-wrap break-words text-sm" x-text="row.evidence_detail || row.comparability_notes || 'Sin descripción capturada'"></p>
                            <p class="mt-2 whitespace-pre-wrap break-words text-sm text-amber-900" x-show="row.source_updates" x-text="row.source_updates"></p>
                            <details x-show="row.latest_source_excerpt" class="mt-2"><summary class="min-h-11 cursor-pointer text-sm">Último texto leído · contrastar con original</summary><p class="whitespace-pre-wrap break-words text-sm" x-text="row.latest_source_excerpt"></p></details>
                            <a x-show="/^https?:\/\//i.test(row.source_url)" :href="/^https?:\/\//i.test(row.source_url) ? row.source_url : '#'" target="_blank" rel="noopener noreferrer" class="mt-2 inline-flex min-h-11 items-center text-blue-700 underline">Abrir anuncio original</a>
                            <label class="label mt-2">Pendientes o motivo de la decisión
                                <textarea class="input" rows="2" maxlength="1600" :value="row.intake_note" placeholder="Qué falta confirmar o por qué no se selecciona" @input.stop="intakeWrite(row.index, 'intake_note', $event.target.value); intakeChanged()"></textarea>
                            </label>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <button type="button" class="btn-secondary" @click="openPhotos(row.index)" :disabled="photoBusy">Fotos y soporte</button>
                                <button type="button" class="btn-secondary" @click="intakeEdit(row)">Consultar todos los campos</button>
                                <button type="button" x-show="card.rows.length > 1" class="btn-secondary" @click="intakeUnlink(row)">Separar este anuncio</button>
                            </div>
                        </div>
                    </template>
                </details>
            </article>
        </template>
    </div>
    <p x-show="!intakeCards.length" class="mt-4 rounded-lg bg-slate-50 p-4">No hay inmuebles en este estado. Busca avisos o cambia el filtro.</p>
    <div class="mt-4 flex flex-wrap items-center gap-3">
        <button type="button" class="btn-secondary" :disabled="intakePage <= 1" @click="intakePage--; rebuildIntake()">Anterior</button>
        <span class="text-sm" x-text="'Página ' + intakePage + ' de ' + intakePages"></span>
        <button type="button" class="btn-secondary" :disabled="intakePage >= intakePages" @click="intakePage++; rebuildIntake()">Siguiente</button>
        <button type="submit" class="btn-primary">Guardar decisiones</button>
        <a class="btn-secondary" data-intake-analysis href="<?= e(isset($flowUrl) ? $flowUrl('4') : url('avaluos/' . $record['id'] . '/metodologia-valuatoria?stage=4')) ?>">Continuar a Análisis con los seleccionados</a>
    </div>
</section>
