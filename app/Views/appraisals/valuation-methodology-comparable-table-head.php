<thead class="bg-slate-50 text-xs font-bold uppercase text-slate-500">
    <tr>
        <th class="px-3 py-3">#</th><th class="px-3 py-3">Usar</th><th class="px-3 py-3">Estado</th>
        <th class="px-3 py-3">Factor M4 <?= $tip('Origen: factores sugeridos por la tipología del bien sujeto y la matriz de variables. Aquí marcas para qué variable servirá esta muestra en el análisis M4.') ?></th>
        <th class="px-3 py-3">Tipo fuente</th><th class="px-3 py-3">Fuente</th><th class="px-3 py-3">Enlace</th>
        <th class="px-3 py-3">Consulta de búsqueda</th><th class="px-3 py-3">Operación</th><th class="px-3 py-3">Tipo inmueble</th>
        <th class="px-3 py-3">Barrio/sector</th><th class="px-3 py-3">Dirección</th><th class="px-3 py-3">Edificio/proyecto</th>
        <th class="px-3 py-3">Latitud <?= $tip('Origen: ubicación del aviso, portal o Google Maps. Es la coordenada norte-sur de la muestra; si no es exacta, marca precisión aproximada o solo sector.') ?></th>
        <th class="px-3 py-3">Longitud <?= $tip('Origen: ubicación del aviso, portal o Google Maps. Es la coordenada oriente-occidente; junto con latitud alimenta el mapa del paso 3.') ?></th>
        <th class="px-3 py-3">Precisión mapa <?= $tip('Origen: criterio del analista al capturar la ubicación. Indica si la coordenada es exacta, aproximada o solo sector para no tratar una referencia general como exacta.') ?></th>
        <th class="px-3 py-3">Precio/canon</th><th class="px-3 py-3">Unidad</th><th class="px-3 py-3">Área publicada (m²)</th>
        <th class="px-3 py-3">Propiedad horizontal</th><th class="px-3 py-3">Administración</th><th class="px-3 py-3">IVA</th><th class="px-3 py-3">Alcobas</th>
        <th class="px-3 py-3">Baños</th><th class="px-3 py-3">Parqueaderos</th><th class="px-3 py-3">Piso</th>
        <th class="px-3 py-3">Estrato</th><th class="px-3 py-3">Edad</th><th class="px-3 py-3">Estado edif.</th>
        <th class="px-3 py-3">Conservación</th><th class="px-3 py-3">Vista</th><th class="px-3 py-3">Acabados</th>
        <th class="px-3 py-3">Ascensor</th><th class="px-3 py-3">Amenidades</th><th class="px-3 py-3">Seguridad</th>
        <th class="px-3 py-3">Planta</th><th class="px-3 py-3">Relación parq.</th><th class="px-3 py-3">Balcón/terraza</th>
        <th class="px-3 py-3">Ruido/humedad/sol</th><th class="px-3 py-3">Nota jurídica</th>
        <th class="px-3 py-3">Contacto</th><th class="px-3 py-3">Teléfono</th><th class="px-3 py-3">Código</th>
        <th class="px-3 py-3">Fecha aviso</th><th class="px-3 py-3">Fecha de captura</th><th class="px-3 py-3">Nota mapa</th><th class="px-3 py-3">Observación</th><th class="px-3 py-3">Descartar por</th>
        <?php foreach (\App\Services\ComparableCaptureDetail::fields() as [$label]): ?><th class="px-3 py-3"><?= e($label) ?></th><?php endforeach; ?>
        <th class="px-3 py-3">Tratamiento PH especial</th>
    </tr>
</thead>
