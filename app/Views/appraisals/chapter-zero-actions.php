<?php
?>
<div id="cierre-expediente" class="mt-8 rounded-2xl border border-teal-100 bg-teal-50 p-5">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h3 class="font-semibold text-teal-950">Cierre del numeral 1</h3>
            <p class="mt-1 text-sm leading-6 text-teal-900">
                Al terminar la configuración o identificación, guarda aquí. Permanecerás en expediente.
            </p>
            <p class="mt-1 text-xs font-semibold text-teal-800" data-autosave-status>Autoguardado activo</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button class="btn-primary" type="submit" :disabled="busy"
                x-text="busy ? 'Guardando...' : 'Guardar y permanecer aquí'">Guardar y permanecer aquí</button>
            <button class="btn-secondary" type="submit" name="next" value="sector" :disabled="busy">
                Guardar y seguir a 2 · Sector
            </button>
        </div>
    </div>
</div>
