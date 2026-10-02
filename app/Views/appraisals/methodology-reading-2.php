<?php require __DIR__ . '/methodology-unit-reading.php'; ?>
    <?php if ($orientationPh === 'si'): ?>
    <h4 class="mt-4 font-semibold">2. PH: dos decisiones distintas que no deben confundirse</h4>
    <p class="mt-2 text-sm leading-6"><?= $orientationPh === 'si' ? 'El expediente está marcado como PH: revisa estas reglas antes de decidir el tratamiento.' : 'Consulta estas reglas si esta unidad o las muestras están sometidas a PH; confirma primero el régimen.' ?></p>
    <div class="mt-3 space-y-3 text-sm leading-6">
        <section class="rounded-lg border bg-white p-3">
            <h5 class="font-semibold">El inmueble que estás avaluando · artículo 36</h5>
            <p class="mt-2">Valora las áreas privadas legalmente constituidas, considerando los derechos derivados de los coeficientes de copropiedad. El valor se expresa integralmente por m².</p>
            <ul class="mt-2 list-disc space-y-2 pl-5">
                <li><strong>Matrícula independiente:</strong> parqueaderos, garajes, depósitos o áreas libres se valoran globalmente o por m² conforme al mercado; verifica servidumbres y restricciones.</li>
                <li><strong>Bien común de uso exclusivo:</strong> su valor queda implícito en la unidad principal; no lo liquides independientemente ni lo sumes otra vez.</li>
                <li><strong>Área privada construida y libre:</strong> asigna sus valores de manera diferenciada según sus características. Comprueba usos aprobados y concordancia con el reglamento.</li>
                <li><strong>Casos especiales:</strong> condominios con terreno privado y áreas atípicas requieren revisar los parágrafos; no aplicar automáticamente el tratamiento de un apartamento convencional.</li>
            </ul>
            <?php $readingNumber = 36; require __DIR__ . '/valuation-methodology-article-reading.php'; ?>
        </section>
        <section class="rounded-lg border bg-white p-3">
            <h5 class="font-semibold">Las muestras con las que comparas · artículo 19.2</h5>
            <p class="mt-2">El análisis estadístico en PH utiliza valores integrales por m² de área privada. Si el dato incluye garajes, depósitos, áreas libres u otras unidades privadas o comunes de uso exclusivo, el numeral 2.b dispone descontar su valor del precio negociado para permitir la comparación.</p>
            <p class="mt-2"><strong>No omitas esa depuración porque todos los avisos incluyan parqueadero.</strong> Investiga y sustenta los valores de los componentes; la norma no proporciona un porcentaje automático. Este tratamiento de las muestras no autoriza liquidar aparte los comunes de uso exclusivo del inmueble sujeto.</p>
            <p class="mt-2">Para PH tipo condominio o físicamente asimilable a NPH, revisa el numeral 2.c. Si falta evidencia, deja pendiente la decisión técnica.</p>
            <?php $readingNumber = 19; require __DIR__ . '/valuation-methodology-article-reading.php'; ?>
        </section>
    </div>
    <?php endif; ?>
