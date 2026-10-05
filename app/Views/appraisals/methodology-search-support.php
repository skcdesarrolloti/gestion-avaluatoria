<dialog x-ref="searchSupport" @cancel="supportTab='context'" class="m-auto max-h-[85vh] w-[min(64rem,94vw)] overflow-y-auto rounded-xl border border-slate-300 bg-white p-5 shadow-xl backdrop:bg-slate-900/40" aria-labelledby="search-support-title"
    x-init="<?php if (($_GET['research'] ?? '')==='1'): ?>supportTab='plan'; $nextTick(() => $refs.searchSupport.showModal())<?php endif; ?>">
    <div class="sticky top-0 z-10 flex items-center justify-between gap-3 bg-white pb-3">
        <h2 id="search-support-title" class="text-xl font-semibold">Apoyo a la investigación</h2>
        <button type="button" class="btn-secondary min-h-11" @click="$refs.searchSupport.close()">Cerrar y volver</button>
    </div>
    <nav class="mb-4 flex flex-wrap gap-2" aria-label="Consultas de apoyo">
        <button type="button" class="btn-secondary" :aria-pressed="supportTab==='context'" @click="supportTab='context'">Contexto y reglas</button>
        <button type="button" class="btn-secondary" :aria-pressed="supportTab==='portals'" @click="supportTab='portals'">Qué publica cada portal</button>
        <button type="button" class="btn-secondary" :aria-pressed="supportTab==='plan'" @click="supportTab='plan'">Planificar la investigación</button>
        <button type="button" class="btn-secondary" :aria-pressed="supportTab==='criteria'" @click="supportTab='criteria'">Criterios y filtros</button>
    </nav>
    <div x-show="supportTab==='context'" x-data="{searchTab:'captura'}"><?php require __DIR__ . '/methodology-search-prompt.php'; ?></div>
    <div x-show="supportTab==='portals'" x-data="{searchTab:'configuracion_portales'}"><?php require __DIR__ . '/methodology-portal-profiles.php'; ?></div>
    <div x-show="supportTab==='plan'" x-data="{searchTab:'investigacion'}">
        <?php require __DIR__ . '/methodology-research-plan.php'; ?>
        <?php require __DIR__ . '/methodology-market-coverage.php'; ?>
    </div>
    <div x-show="supportTab==='criteria'">
        <?php require __DIR__ . '/valuation-methodology-search-diseno.php'; ?>
        <?php require __DIR__ . '/valuation-methodology-search-buscador.php'; ?>
        <?php require __DIR__ . '/valuation-methodology-search-filtros.php'; ?>
    </div>
</dialog>
