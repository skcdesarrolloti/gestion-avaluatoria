<details class="mt-4 rounded-xl border p-4"><summary class="min-h-11 cursor-pointer font-semibold">Comportamiento del año · canon y ocupación</summary>
    <p class="text-sm">Azul: canon reconstruido. Verde: canon recibido. Un dato desconocido deja un espacio, no una barra de cero. Ocupación según soportes.</p>
    <div class="mt-3 space-y-2"><template x-for="row in ledgerRows" :key="row.month"><div class="grid gap-2" style="grid-template-columns:5rem 1fr">
        <span class="text-xs" x-text="row.month"></span><div>
            <div class="bg-blue-700" style="height:10px" :style="{width:Math.max(0,(row.contract || 0)/ledgerMax*100)+'%'}" :title="'Canon reconstruido: '+money(row.contract)"></div>
            <div class="mt-1 bg-teal-800" style="height:10px" :style="{width:Math.max(0,(row.rentReceived || 0)/ledgerMax*100)+'%'}" :title="'Canon recibido: '+money(row.rentReceived)"></div>
            <span class="text-xs" x-text="(row.occupied===null?'Ocupación sin dato':row.occupied+' de '+row.days+' días ocupados')+' · Pactado: '+money(row.contract)+' · Recibido: '+money(row.rentReceived)"></span>
        </div>
    </div></template></div>
</details>
