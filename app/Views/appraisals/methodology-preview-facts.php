<dl class="mt-2 grid grid-cols-2 gap-2 text-sm">
    <template x-for="(fact,factIndex) in previewFacts(item.row)" :key="factIndex">
        <div class="min-w-0 rounded bg-white/70 p-2"><dt class="text-xs text-slate-600" x-text="fact.label"></dt><dd class="break-words font-semibold" x-text="fact.value"></dd></div>
    </template>
</dl>
<details class="mt-2"><summary class="min-h-11 cursor-pointer py-2 text-sm font-semibold">Texto original leído</summary><p class="max-h-56 overflow-auto whitespace-pre-wrap break-words text-sm" x-text="item.row.published_text || item.row.comparability_notes || 'Sin texto adicional'"></p></details>
