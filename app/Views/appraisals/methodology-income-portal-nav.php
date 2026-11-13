<nav class="sticky top-0 z-30 mb-4 rounded-xl border border-teal-200 bg-white p-3 shadow-sm" x-show="incomeStage==='research'" <?= in_array($incomeInitial,['search','capture'],true)?'':'x-cloak' ?> aria-label="Continuar por portal de Renta">
    <div class="flex flex-wrap items-end gap-3">
        <div class="min-w-0 w-full sm:flex-1">
            <label class="label" for="renta-portal-actual">Portal actual · Renta</label>
            <select id="renta-portal-actual" class="input mt-1" :value="incomePortal" :disabled="incomePortalBusy" @change="const field=$event.target; choosePortal(field.value, incomeTab==='search'?'search':'capture').then(ok=>{if(!ok)field.value=incomePortal})">
                <option value="">Selecciona un portal o inmobiliaria</option>
                <?php foreach ($incomeSourceList as $incomeSource): ?><option value="<?= e($incomeSource['label']) ?>"><?= e($incomeSource['label']) ?></option><?php endforeach; ?>
            </select>
        </div>
        <button type="button" class="btn-primary min-h-11 w-full sm:w-auto" :disabled="incomePortalBusy || !incomeNextPortal" @click="nextPortal()" x-text="incomeNextPortal?'Siguiente portal: '+incomeNextPortal:'Último portal de la lista'">Siguiente portal</button>
    </div>
    <div class="mt-2 flex flex-wrap gap-2">
        <button type="button" class="btn-secondary min-h-11" :disabled="incomePortalBusy || !incomePortal" @click="choosePortal(incomePortal)">Buscar y pegar</button>
        <button type="button" class="btn-secondary min-h-11" :disabled="incomePortalBusy || !incomePortal" @click="choosePortal(incomePortal,'review')">Ver muestras</button>
    </div>
    <p class="mt-2 text-sm" role="status" x-show="incomePortalBusy">Preparando el portal y comprobando el guardado…</p>
    <p class="mt-2 text-sm text-red-800" role="alert" x-show="incomePortalError" x-text="incomePortalError"></p>
</nav>
