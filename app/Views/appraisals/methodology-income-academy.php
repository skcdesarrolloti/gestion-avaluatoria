<section id="renta-orientacion" class="mt-5 rounded-2xl border border-teal-200 bg-white p-5 sm:p-8">
    <p class="eyebrow">Orientación y ruta del método · Documento de trabajo</p>
    <h3 class="mt-2 text-2xl font-semibold">Antes de empezar con Renta</h3>
    <p class="mt-3 leading-6">Primero comprendemos el inmueble, su ocupación, los derechos y los soportes. Después investigaremos ingresos y gastos, y estudiaremos el modelo matemático.</p>
    <a class="btn-secondary mt-4" href="<?= e(url('assets/normativa/renta-orientacion-v1.pdf')) ?>" target="_blank" rel="noopener">Abrir resumen completo · PDF de 6 páginas</a>
    <p class="mt-2 text-sm text-slate-600">Versión 1 · 12 de noviembre de 2026. Orientación de estudio; no es una norma ni aprueba fórmulas o tasas. El PDF se abre en otra pestaña para conservar el trabajo.</p>
    <?php require __DIR__.'/methodology-income-academy-topics.php'; ?>
    <details class="mt-4 rounded-xl border p-4">
        <summary class="min-h-11 cursor-pointer font-semibold">Qué debemos tener presente</summary>
        <ul class="mt-2 list-disc space-y-3 pl-5 text-sm leading-6">
            <li><strong>Qué valoramos:</strong> identificar el derecho y el alcance. El valor del inmueble y su canon de arrendamiento son objetivos distintos.</li>
            <li><strong>Contrato y mercado:</strong> conservar las condiciones contractuales y contrastarlas con la evidencia del mercado; no reemplazar una por otra sin explicación.</li>
            <li><strong>Solo lo atribuible al inmueble:</strong> separar rentas inmobiliarias de los ingresos de un negocio, marcas u otros intangibles.</li>
            <li><strong>Datos con soporte:</strong> reconocer la fuente, fecha, verificación y decisión del analista. Un documento disponible todavía requiere revisión.</li>
            <li><strong>Normas con su alcance:</strong> IGAC 22–26; IVS 101–106 y 400; NTS M01, GTS E01 y E02 e informes sectoriales. Las referencias internacionales se mantienen separadas del marco colombiano.</li>
            <li><strong>Decisiones pendientes:</strong> mínimos particulares de NTS M01, compatibilidad de rentas y tasas, horizonte, contratos y ajustes. Se resolverán antes de programar cálculos; los ejemplos no son parámetros del expediente.</li>
        </ul>
    </details>
    <p class="mt-4 text-sm">En Insumos encontrarás «1. Comprender el inmueble y su arriendo»: A. Inmueble y alcance, B. Ocupación y contrato, C. Gastos y antecedentes y D. Preparación reunida. Consulta el capítulo 3 y completa faltantes con ayudas (?), para capitalización directa y flujo de caja descontado. La captura de muestras existente se conserva.</p>
    <?php if (($selected['method'] ?? '')==='renta'): ?><a class="btn-primary mt-3" href="<?= e($flowUrl('3','renta')) ?>#renta-concepto">Ir al primer bloque conceptual</a><?php endif; ?>
</section>
