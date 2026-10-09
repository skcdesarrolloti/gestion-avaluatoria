<details class="rounded-lg border bg-slate-50 p-3">
    <summary class="min-h-11 cursor-pointer font-semibold">MAPE · fórmula e interpretación</summary>
    <div class="mt-3 space-y-3">
        <p class="font-mono">MAPE (%) = (100 / n) × Σᵢ₌₁ⁿ (|xᵢ − c| / |xᵢ|)</p>
        <p><strong>n:</strong> cantidad de valores unitarios disponibles. <strong>xᵢ:</strong> valor unitario observado de cada inmueble. <strong>c:</strong> estimador que se compara, como media o mediana. <strong>Σ:</strong> suma de todos los términos. Las barras | | indican valor absoluto: se considera la magnitud de la diferencia, sin su signo.</p>
        <p>Se resta el estimador a cada valor observado, se toma la diferencia absoluta y se divide por el valor observado. Se promedian esas proporciones y se multiplican por 100. Si algún valor observado es cero, esta fórmula no es estimable. Los valores pequeños tienen mayor peso relativo para una misma diferencia monetaria.</p>
        <p><strong>Ejemplo ilustrativo:</strong> valores de 8, 10 y 12 millones COP/m², comparados con un estimador de 10 millones COP/m²:</p>
        <p class="font-mono">MAPE = (100 / 3) × (2/8 + 0/10 + 2/12) = 13,89 %</p>
        <p>Este porcentaje resume diferencias dentro de la muestra; no mide el error del avalúo ni su exactitud frente a un precio de transacción desconocido. Aquí se compara cada centro con todos los valores disponibles, incluso cuando el centro se obtiene con un cálculo recortado.</p>
        <p class="text-sm"><strong>Referencias técnicas:</strong> <a class="underline" href="https://otexts.com/fpp3/accuracy.html" target="_blank" rel="noopener" data-no-fetch>Hyndman y Athanasopoulos · medidas porcentuales de error</a>. Apoyo estadístico; no son criterios jurídicos de adopción del valor.</p>
    </div>
</details>
