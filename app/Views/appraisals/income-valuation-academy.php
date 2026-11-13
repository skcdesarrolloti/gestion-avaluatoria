<details class="mt-4 rounded-xl border bg-teal-50 p-4">
<summary class="min-h-11 cursor-pointer font-semibold">Academia · <?= $ivAcademy==='rates'?'cómo obtener y elegir las tasas':'cómo sustentar el ingreso y los costos' ?></summary>
<?php if ($ivAcademy==='rates'): ?>
<ul class="mt-3 list-disc space-y-3 pl-5 text-sm">
    <li><strong>Mercado:</strong> tasa = ingreso anual / precio. Usa la misma fecha, alcance y base bruta o neta. Ventas y arriendos siguen en investigaciones separadas; cita ambas referencias y justifica su correspondencia. Compara alternativas por calidad de evidencia.</li>
    <li><strong>Descuento:</strong> referencia más prima, CAPM del activo o tasa documentada. Debe ser efectiva anual, nominal en COP y coherente con flujos antes de deuda e impuesto de renta. CAPM: referencia + beta desapalancado × prima. Una beta de promotoras extranjeras requiere adaptación al activo.</li>
    <li><strong>Costo ponderado:</strong> patrimonio × (1 − peso de deuda) + deuda × peso de deuda, antes de impuesto de renta y sin escudo fiscal. Es una alternativa avanzada, no una tasa obligatoria; no pegues un WACC corporativo después de impuestos.</li>
    <li><strong>Salida:</strong> ingreso neto del año siguiente / tasa terminal observada, o flujo después de reposiciones / (descuento − crecimiento). En perpetuidad, descuento &gt; crecimiento. La tasa de salida no se iguala automáticamente al descuento.</li>
    <li><strong>Directa y FCD:</strong> son dos conclusiones que se contrastan. Directa usa un año representativo; FCD reconoce calendario, inversiones y salida. No se suman sus valores ni se promedian sus tasas automáticamente.</li>
</ul>
<?php else: ?>
<ul class="mt-3 list-disc space-y-3 pl-5 text-sm">
    <li><strong>Ingreso operativo neto (NOI):</strong> canon potencial menos vacancia, pérdidas de cobro y gastos del propietario. En FCD se deducen después las reposiciones; pagos de deuda no pertenecen al flujo del activo.</li>
    <li><strong>Oficina o apartamento PH:</strong> revisar cuotas ordinarias, extraordinarias, seguros y mantenimiento cubierto por la copropiedad. La administración pagada por el arrendatario no es ingreso ni gasto propio. Revisa quién paga durante la desocupación.</li>
    <li><strong>Local o bodega:</strong> comprobar mantenimiento estructural, cubiertas, seguros, servicios y adecuaciones según contrato. Predial, gestión y comercialización necesitan soporte local y una base explícita.</li>
    <li><strong>Porcentajes:</strong> se admiten sobre renta potencial o efectiva, con muestra, ciudad, tipo de inmueble, periodo y responsable. No hay porcentajes universales precargados. Un cero también debe quedar explicado.</li>
    <li><strong>Calendario:</strong> no descuentes otra vez como vacancia una desocupación representada por fechas. Los eventos sustituyen el canon de un mes o agregan gastos e inversiones; conserva presupuesto o cláusula.</li>
</ul>
<?php endif; ?>
<p class="mt-3 text-sm">Soporte colombiano: Resolución IGAC 0941 de 2026, artículos 23–26 y anexo 2.2, según el alcance del encargo. Los ejemplos del anexo y de los cursos no son tasas ni gastos obligatorios. El CV ≤ 7,5 % pertenece al análisis pertinente de la muestra; no determina estas tasas.</p>
<a class="mt-2 inline-flex min-h-11 items-center font-semibold text-blue-800 underline" href="https://www.igac.gov.co/node/53595" target="_blank" rel="noopener">Consultar resolución en el IGAC</a>
<a class="ml-3 inline-flex min-h-11 items-center font-semibold text-blue-800 underline" href="<?= e($flowUrl('1')) ?>#renta-orientacion">Academia completa de Renta</a>
</details>
