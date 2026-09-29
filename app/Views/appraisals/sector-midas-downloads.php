<?php
$midasLayerHelp = [
    'Localidades' => ['contiene' => 'Localidades urbanas, límites distritales y división política principal.',
        'sirve' => 'Confirmar la localidad que describe el sector y la ubicación administrativa del predio.',
        'utilidad' => 'Muy útil para alimentar el numeral 2 y validar localidad en el numeral 3.'],
    'Unidades comuneras de gobierno' => ['contiene' => 'UCG urbanas y rurales descargadas desde MIDAS.',
        'sirve' => 'Precisar la unidad comunera de gobierno donde se ubica el barrio o predio.',
        'utilidad' => 'Útil para sectorización, trazabilidad territorial y campos de ubicación del expediente.'],
    'Uso del suelo y tratamientos' => ['contiene' => 'Ficha o resultado de consulta por predio: uso, tratamiento, actividad y clasificación urbanística.',
        'sirve' => 'Registrar la lectura práctica de MIDAS después de buscar la referencia, coordenada o hacer clic en el predio.',
        'utilidad' => 'Esencial para el numeral 5; el POT completo se consulta en Normatividad Urbana.'],
    'Circulares MIDAS' => ['contiene' => 'Circulares descargadas desde MIDAS o documentos asociados a criterios urbanísticos.',
        'sirve' => 'Respaldar salvedades, reglas complementarias y criterios de Planeación.',
        'utilidad' => 'Muy útil si el caso toca altura, parqueaderos, altillos o interpretaciones urbanísticas.'],
    'Circulares urbanísticas' => ['contiene' => 'Criterios de Planeación sobre altura, parqueaderos, altillos y reglas urbanas complementarias.',
        'sirve' => 'Respaldar decisiones del numeral 5 y preparar el análisis posterior de edificabilidad.',
        'utilidad' => 'Muy útil para explicar excepciones, exigencias o salvedades normativas del caso.'],
    'Servicios públicos' => ['contiene' => 'Coberturas o áreas de prestación de acueducto, alcantarillado, gas, alumbrado y aseo.',
        'sirve' => 'Soportar disponibilidad sectorial de servicios y calidad del entorno.',
        'utilidad' => 'Útil si el sector tiene carencias, dudas de cobertura o incidencia en valor.'],
    'Transporte y movilidad' => ['contiene' => 'Sistema vial, rutas, paraderos y transporte masivo.',
        'sirve' => 'Explicar accesibilidad, conectividad y soporte de movilidad del sector.',
        'utilidad' => 'Muy útil para inmuebles comerciales, oficinas y sectores donde la accesibilidad pesa.'],
    'Equipamiento urbano' => ['contiene' => 'Educación, salud, deporte, cultura, espacio público y equipamientos cercanos.',
        'sirve' => 'Identificar servicios urbanos del entorno y externalidades positivas.',
        'utilidad' => 'Útil para sustentar atractivo sectorial y comparación de mercado.'],
    'Educación' => ['contiene' => 'Instituciones educativas, colegios, universidades o equipamientos de formación.',
        'sirve' => 'Explicar equipamientos cercanos y dinámica de servicios del sector.',
        'utilidad' => 'Útil para el numeral 2 y para actividad económica del numeral 6.'],
    'Salud' => ['contiene' => 'Clínicas, hospitales, centros médicos y equipamientos asistenciales.',
        'sirve' => 'Identificar concentración de servicios de salud y mercado objetivo del sector.',
        'utilidad' => 'Muy útil cuando el inmueble se orienta a oficinas, comercio o servicios médicos.'],
    'Seguridad' => ['contiene' => 'CAI, estaciones, inspecciones u otros puntos institucionales de seguridad.',
        'sirve' => 'Documentar soporte o limitaciones de seguridad del entorno.',
        'utilidad' => 'Útil para condiciones restrictivas del numeral 7 si hay incidencia real.'],
    'Cultura' => ['contiene' => 'Equipamientos culturales, patrimoniales, comunitarios o recreativos.',
        'sirve' => 'Describir servicios urbanos y centralidades complementarias.',
        'utilidad' => 'Útil para caracterización sectorial y atractores urbanos.'],
    'Ambiente y riesgos' => ['contiene' => 'Amenazas naturales, riesgo, protección, inundación, erosión y determinantes ambientales.',
        'sirve' => 'Registrar restricciones, alertas, afectaciones o salvedades del sector.',
        'utilidad' => 'Crítico si MIDAS reporta amenaza, riesgo o condición ambiental relevante.'],
    'Cambio climático' => ['contiene' => 'Capas ambientales, adaptación, vulnerabilidad, inundación o amenazas climáticas.',
        'sirve' => 'Respaldar salvedades ambientales y condiciones restrictivas cuando aplique.',
        'utilidad' => 'Útil para numeral 7 y para explicar riesgos que puedan afectar uso, valor o comercialización.'],
    'Otro soporte MIDAS' => ['contiene' => 'Cualquier descarga complementaria no clasificada en los grupos anteriores.',
        'sirve' => 'Guardar evidencia adicional sin forzar una categoría incorrecta.',
        'utilidad' => 'Útil solo cuando el soporte tiene relación clara con el avalúo.'],
];
?>
<div class="mt-5 rounded-xl border border-slate-200 p-5" x-show="midasTab === 'descargas'">
    <div class="grid gap-4 lg:grid-cols-[0.85fr_1fr]">
        <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
            <p class="text-sm font-semibold text-blue-950">Soportes MIDAS del avalúo</p>
            <ol class="mt-3 list-decimal space-y-2 pl-5 text-sm leading-6 text-blue-950">
                <li>Los documentos comunes se guardan una sola vez en Biblioteca MIDAS.</li>
                <li>Sube aquí solo la evidencia específica del barrio, predio o consulta del caso.</li>
                <li>Si el soporte ya está en la biblioteca, consúltalo allí y cita su referencia.</li>
                <li>Barrios no tiene descarga común visible; usa la consulta del mapa o predio como soporte del caso.</li>
            </ol>
            <a class="btn-secondary mt-4 inline-flex bg-white" href="<?= e(url('midas')) ?>">Ver Biblioteca MIDAS</a>
        </div>
        <form class="grid gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4 md:grid-cols-2"
            method="post" enctype="multipart/form-data"
            action="<?= e(url('avaluos/' . $record['id'] . '/sector/midas/archivos')) ?>"
            x-data="{group: '<?= e($midasLayerGroups[0]) ?>', help: <?= e(json_encode($midasLayerHelp, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>}">
            <?= csrf_field() ?>
            <label class="label">Grupo de capa
                <select class="input mt-2" name="layer_group" required x-model="group">
                    <?php foreach ($midasLayerGroups as $group): ?><option value="<?= e($group) ?>"><?= e($group) ?></option><?php endforeach; ?>
                </select>
            </label>
            <label class="label">Archivo descargado
                <input class="input mt-2" type="file" name="midas_support[]" accept=".pdf,.csv,.json,.geojson,.zip" required>
                <span class="mt-1 block text-xs font-normal text-slate-500">Máximo 25 MB.</span>
            </label>
            <label class="label md:col-span-2">Nota de lectura
                <textarea class="input mt-2 min-h-24" name="notes" placeholder="Ej. Consulta del barrio Bocagrande; capa de transporte revisada para este avalúo."></textarea>
            </label>
            <div class="md:col-span-2 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950">
                <p><strong>Contiene:</strong> <span x-text="help[group]?.contiene"></span></p>
                <p class="mt-1"><strong>Sirve para:</strong> <span x-text="help[group]?.sirve"></span></p>
                <p class="mt-1"><strong>Utilidad:</strong> <span x-text="help[group]?.utilidad"></span></p>
            </div>
            <div class="md:col-span-2 flex justify-end"><button class="btn-primary min-h-11" type="submit">Subir soporte MIDAS</button></div>
        </form>
    </div>
    <?php if ($midasFiles): ?>
        <div class="mt-5 overflow-x-auto rounded-xl border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr><th class="px-4 py-3">Grupo</th><th class="px-4 py-3">Archivo</th><th class="px-4 py-3">Fecha</th><th class="px-4 py-3">Acción</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php foreach ($midasFiles as $file): ?>
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-800"><?= e($file['layer_group']) ?></td>
                            <td class="px-4 py-3">
                                <a class="font-semibold text-blue-800 underline" href="<?= e(url('avaluos/' . $record['id'] . '/sector/midas/archivos/' . $file['id'])) ?>"><?= e($file['source_filename']) ?></a>
                                <span class="block text-xs text-slate-500"><?= e($formatMidasBytes($file['file_size_bytes'])) ?></span>
                                <?php if (!empty($file['notes'])): ?><span class="block text-xs text-slate-600"><?= e($file['notes']) ?></span><?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-slate-600"><?= e($formatMidasDate($file['created_at'])) ?></td>
                            <td class="px-4 py-3">
                                <form method="post" action="<?= e(url('avaluos/' . $record['id'] . '/sector/midas/archivos/' . $file['id'] . '/eliminar')) ?>"
                                    onsubmit="return confirm('¿Eliminar este soporte MIDAS?');">
                                    <?= csrf_field() ?>
                                    <button class="btn-secondary min-h-11" type="submit">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
