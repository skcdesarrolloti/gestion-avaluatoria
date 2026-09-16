<label class="label">Departamento <span class="help-dot" title="<?= e($fieldHelp('department_id')) ?>">?</span>
    <select class="input" name="department_id" x-model="departmentId" @change="changeDepartment()">
        <option value="">Selecciona departamento</option>
        <template x-for="item in departments" :key="item.id">
            <option :value="item.id" x-text="item.name"></option>
        </template>
    </select>
</label>
<label class="label">Municipio / distrito <span class="help-dot" title="<?= e($fieldHelp('city_id')) ?>">?</span>
    <select class="input" name="city_id" x-model="cityId" @change="changeCity()">
        <option value="">Selecciona municipio</option>
        <template x-for="item in filteredCities()" :key="item.id">
            <option :value="item.id" x-text="item.name"></option>
        </template>
    </select>
</label>
<label class="label">Barrio / microsector <span class="help-dot" title="<?= e($fieldHelp('neighborhood_id')) ?>">?</span>
    <select class="input" name="neighborhood_id" x-model="neighborhoodId">
        <option value="">Selecciona barrio</option>
        <template x-for="item in filteredNeighborhoods()" :key="item.id">
            <option :value="item.id" x-text="item.name"></option>
        </template>
    </select>
</label>
<label class="label">Localidad <span class="help-dot" title="<?= e($fieldHelp('locality_name')) ?>">?</span>
    <input class="input bg-slate-50" readonly :value="locationDisplay('locality_name')" placeholder="Se completa con el barrio">
</label>
<label class="label">Comuna / UCG <span class="help-dot" title="<?= e($fieldHelp('commune_ucg')) ?>">?</span>
    <input class="input bg-slate-50" readonly :value="locationDisplay('commune_ucg')" placeholder="Se completa con el barrio">
</label>
<label class="label">Zona / sector <span class="help-dot" title="<?= e($fieldHelp('zone_sector')) ?>">?</span>
    <input class="input bg-slate-50" readonly :value="locationDisplay('zone_sector')" placeholder="Se completa con el barrio">
</label>
