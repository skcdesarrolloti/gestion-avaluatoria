<nav x-show="mode==='intake'" class="mb-4 flex flex-wrap gap-2 rounded-xl bg-teal-50 p-3" aria-label="Portales de los anuncios recogidos" @input.stop @change.stop>
    <template x-for="portal in intakePortals" :key="portal">
        <button type="button" class="btn-secondary min-h-11" :aria-pressed="intakePortal===portal" :class="intakePortal===portal ? 'ring-2 ring-teal-700' : ''" @click="intakePortal=portal; sourcePortal=portal; intakePage=1; rebuildIntake()"><span x-text="portal"></span> (<span x-text="intakePortalCount(portal)"></span>)</button>
    </template>
    <p class="w-full text-sm" x-show="!intakePortals.length">Todavía no hay anuncios. Empieza en «Buscar por portal».</p>
</nav>
