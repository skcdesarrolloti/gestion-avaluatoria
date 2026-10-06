<details x-show="searchTab === 'matriz' && mode === 'intake' && !['table','consolidation'].includes(intakeView)" class="mt-4 rounded-xl border p-3" x-cloak>
    <summary class="min-h-11 cursor-pointer font-semibold">Revisar una muestra, confirmar o completar datos</summary>
    <div class="my-4 grid gap-3 sm:grid-cols-2" @input.stop @change.stop>
        <label class="label">Estado de recogida
            <select class="input" x-model="intakeFilter" @change="intakePage = 1; rebuildIntake()"><option value="all">Todos</option><option value="pending">Por confirmar</option><option value="confirmed">Confirmados para investigación</option><option value="excluded">No participan</option></select>
        </label>
        <label class="label">Buscar inmueble o anuncio
            <input class="input" x-model="intakeSearch" @input="intakePage = 1; rebuildIntake()" placeholder="Edificio, barrio, código o portal">
        </label>
    </div>
    <p class="mb-3 text-sm"><strong x-text="intakeCount"></strong> inmuebles · <span x-text="total"></span> anuncios. Los posibles duplicados siguen separados hasta confirmar su identidad.</p>
    <p class="rounded-xl bg-slate-50 p-4 text-sm" x-show="!intakeCards.length" x-text="intakeView==='confirmed' ? 'No hay inmuebles confirmados con estos filtros. Revísalos en el paso 2.' : 'No hay anuncios de este portal con estos filtros. Puedes recogerlos en el paso 1.'"></p>
    <div class="grid gap-4">
        <template x-for="card in intakeCards" :key="card.key">
            <article class="min-w-0 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <h4 class="text-lg font-semibold" x-text="card.title"></h4>
                <p class="mt-1 text-sm text-slate-600" x-text="card.rows[0].neighborhood || 'Ubicación publicada pendiente'"></p>
                <template x-for="offer in card.rows" :key="offer.id"><p class="mt-2 rounded-lg bg-slate-50 p-2 text-sm" x-text="(offer.source_name || 'Fuente pendiente') + ': ' + (offer.price_amount || 'precio pendiente') + ' COP · ' + (offer.area_m2 || 'área pendiente') + ' m² · ' + (offer.listing_code || 'código pendiente')"></p></template>
                <p class="mt-2 rounded-lg p-2 text-sm" :class="card.rows.every(r => r.capture_confirmation==='confirmed') ? 'bg-emerald-50 text-emerald-900' : 'bg-amber-50 text-amber-900'" x-text="card.rows.every(r => r.capture_confirmation==='confirmed') ? 'Confirmado para investigación' : card.rows.every(r => r.capture_confirmation==='excluded') ? 'No participa en investigación' : 'Por confirmar'"></p>
                <p class="mt-2 text-sm text-amber-900" x-show="card.conflicts.length" x-text="'Diferencias entre anuncios: ' + card.conflicts.map(k => ({price_amount:'precio',area_m2:'área publicada',parking_spaces:'parqueaderos',ph_deposit_count:'depósitos',bathrooms:'baños',bedrooms:'habitaciones',view_quality:'vista',elevator:'ascensor'})[k]).join(', ')"></p>
                <p class="mt-2 text-sm" x-show="card.pending">Hay datos básicos pendientes. Consulta cada fuente.</p>
                <p class="mt-2 text-sm text-amber-900" x-show="card.candidates.length" x-text="'Posible mismo inmueble: ' + card.candidates.map(c => c.title).join(' · ') + '. Confirma antes de vincular.'"></p>
                <div class="mt-3 flex flex-wrap gap-2">
                    <button type="button" class="btn-primary" @click="intakeConfirm(card,'confirmed')">Confirmar para investigación</button>
                    <button type="button" class="btn-secondary" @click="intakeConfirm(card,'excluded')">No participa</button>
                    <button type="button" class="btn-secondary" @click="intakeConfirm(card,'')">Volver a pendiente</button>
                </div>
                <label class="label mt-3">Vincular a otro inmueble confirmado
                    <select class="input" @change.stop="intakeLink(card, $event.target.value); $event.target.value = ''"><option value="">Selecciona sólo si confirmaste que es el mismo</option><template x-for="target in intakeTargets.filter(t => !card.rows.some(r => (r.property_group || r.id) === t.key))" :key="target.key"><option :value="target.key" x-text="target.title + ' · ' + target.key.slice(0, 8)"></option></template></select>
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
                            <details class="mt-2" x-show="row.published_text"><summary class="min-h-11 cursor-pointer text-sm">Texto de la ficha conservado</summary><p class="whitespace-pre-wrap break-words text-sm" x-text="row.published_text"></p></details>
                            <details class="mt-2" x-data="{sourceText:''}"><summary class="min-h-11 cursor-pointer text-sm">Completar con la ficha de este anuncio</summary>
                                <label class="label">Texto publicado en este mismo anuncio<textarea class="input" rows="4" maxlength="16000" x-model="sourceText" @input.stop placeholder="Pega descripción y características de esta ficha; conserva sus etiquetas y saltos de línea."></textarea></label>
                                <button type="button" class="btn-secondary mt-2" @click="intakeComplement(row, sourceText); sourceText=''">Recoger atributos y completar vacíos</button>
                                <p class="mt-1 text-xs">Los datos existentes se conservan. Las diferencias quedan registradas para revisión.</p>
                            </details>
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
    <div class="mt-4 flex flex-wrap items-center gap-3">
        <button type="button" class="btn-secondary" :disabled="intakePage <= 1" @click="intakePage--; rebuildIntake()">Anterior</button>
        <span class="text-sm" x-text="(intakeView==='review' ? 'Anuncio ' : 'Inmueble ') + intakePage + ' de ' + intakePages"></span>
        <button type="button" class="btn-secondary" :disabled="intakePage >= intakePages" @click="intakePage++; rebuildIntake()">Siguiente</button>
        <button type="submit" class="btn-primary">Guardar decisiones</button>
        <p class="text-sm">Confirmar recopila información. No ejecuta correlación, depuración ni regresión.</p>
    </div>
</details>
