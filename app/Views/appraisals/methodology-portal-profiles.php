<?php
$profileState=['portal'=>'fincaraiz','type'=>\App\Services\ComparablePortalProfiles::defaultType((string)($guide['type_label'] ?? '')),
    'profiles'=>\App\Services\ComparablePortalProfiles::all()];
?>
<section x-cloak x-show="searchTab === 'configuracion_portales'" class="rounded-xl border border-slate-200 bg-slate-50 p-4 sm:p-6"
    x-data="<?= e(json_encode($profileState, JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_THROW_ON_ERROR)) ?>">
    <p class="eyebrow">Configuración de referencia · Insumos</p>
    <h3 class="mt-2 text-xl font-semibold">Configuración por portal y tipo de inmueble</h3>
    <p class="mt-2 text-sm leading-6 text-slate-600">Consulta dónde publica cada portal la información y qué debe conservarse al recogerla. Investigación de fichas públicas de venta; no certifica todos los campos ni los formularios privados de publicación. Revisión: <time datetime="2026-10-04">4 de octubre de 2026</time>.</p>
    <div class="mt-4 grid gap-4 sm:grid-cols-2">
        <label class="block font-semibold">Portal
            <select x-model="portal" class="input mt-2 min-h-11 w-full"><option value="">Selecciona un portal</option>
                <?php foreach (\App\Services\ComparablePortalProfiles::portals() as $key=>$label): ?>
                    <option value="<?= e($key) ?>"><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="block font-semibold">Tipo de inmueble
            <select x-model="type" class="input mt-2 min-h-11 w-full"><option value="">Selecciona el tipo que vas a consultar</option>
                <?php foreach (\App\Services\ComparablePortalProfiles::types() as $key=>$label): ?>
                    <option value="<?= e($key) ?>"><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    </div>
    <p class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm font-semibold"
        x-text="!portal || !type ? 'Selecciona portal y tipo de inmueble.' : profiles[portal]?.[type]?.status || 'Pendiente de investigación: no se extrapolan campos de otro tipo o portal.'"></p>
    <template x-if="profiles[portal]?.[type]">
        <div>
            <div class="mt-4 grid gap-4 lg:grid-cols-2">
                <?php foreach (['basics'=>'Datos básicos: identificación, precio y áreas','descriptive'=>'Elementos descriptivos: características y texto'] as $key=>$title): ?>
                    <section class="rounded-xl border border-slate-200 bg-white p-4">
                        <h4 class="font-semibold"><?= e($title) ?></h4>
                        <ul class="mt-3 space-y-3 text-sm leading-6 text-slate-700">
                            <template x-for="(field, index) in profiles[portal][type].<?= e($key) ?>" :key="portal + type + '<?= e($key) ?>' + index"><li x-text="field"></li></template>
                        </ul>
                    </section>
                <?php endforeach; ?>
            </div>
            <div class="mt-4 rounded-xl border border-amber-200 bg-white p-4">
                <h4 class="font-semibold">Hallazgos que deben orientar la futura comparación</h4>
                <ul class="mt-2 space-y-2 text-sm leading-6"><template x-for="(note, index) in profiles[portal][type].notes" :key="portal + type + index"><li x-text="note"></li></template></ul>
                <a class="mt-3 inline-flex min-h-11 items-center font-semibold text-blue-700 underline" :href="profiles[portal][type].url" target="_blank" rel="noopener noreferrer">Consultar ficha que sustenta esta configuración</a>
            </div>
        </div>
    </template>
    <details class="mt-4 rounded-xl border border-slate-200 bg-white p-4">
        <summary class="min-h-11 cursor-pointer py-2 font-semibold">Criterios comunes y alcance de esta primera etapa</summary>
        <p class="mt-3 text-sm leading-6">Conservar casilla, descripción, fuente y fecha por separado. Un dato no publicado no significa cero ni ausencia. Precio, áreas, baños o parqueaderos diferentes requieren aclaración; la similitud no confirma que sean el mismo inmueble. La coordenada del portal queda sin verificar.</p>
        <p class="mt-3 text-sm leading-6">Garaje y depósito pueden aparecer como características de otra unidad. Ya se observó un parqueadero individual en Ciencuadras; la publicación independiente de depósitos sigue pendiente de documentación. La naturaleza jurídica se comprueba con soporte, no con el nombre del anuncio.</p>
        <p class="mt-3 text-sm leading-6">Esta consulta prepara la siguiente etapa: comparación entre fuentes y confirmación del analista. No integra anuncios ni cambia datos. Los lectores actuales no garantizan capturar cada campo documentado ni todos los tipos; cada nueva cobertura debe validarse antes de habilitarla.</p>
    </details>
</section>
