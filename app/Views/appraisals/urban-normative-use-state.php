{
    usePane: (() => {
        const pane = (location.hash || '').slice(1).replace(/^uso-/, '');
        return ['decision','indices','potencial','informe','catalogo'].includes(pane) ? pane : 'decision';
    })(),
    categorySlug: <?= $urbanJs('category_slug') ?>, modality: <?= $urbanJs('normative_modality') ?>, residential: <?= $residentialNormJson ?>,
    routes: <?= $potentialRoutesJson ?>, routeMatrixTab: 'resumen',
    land: <?= $urbanJs('land_area_normative_m2') ?>, front: <?= $urbanJs('lot_front_normative_m') ?>, depth: <?= $urbanJs('lot_depth_normative_m') ?>,
    affect: <?= $urbanJs('setback_area_percent') ?>, frontSetback: <?= $urbanJs('setback_front_m') ?>, rearSetback: <?= $urbanJs('setback_rear_m') ?>,
    leftSetback: <?= $urbanJs('setback_left_m') ?>, rightSetback: <?= $urbanJs('setback_right_m') ?>, net: <?= $urbanJs('net_land_area_m2') ?>,
    occ: <?= $urbanJs('occupancy_index') ?>, floors: <?= $urbanJs('max_floors') ?>, ci: <?= $urbanJs('construction_index') ?>,
    maxBuilt: <?= $urbanJs('normative_max_built_area_m2') ?>, actual: <?= $urbanJs('actual_built_area_m2') ?>,
    sellFactor: <?= $urbanJs('sellable_area_factor') ?>, sellable: <?= $urbanJs('sellable_area_m2') ?>, complianceSummary: <?= $urbanJs('normative_compliance_summary') ?>,
    isLot: <?= $isLotSubjectJson ?>,
    number(v) { const n = parseFloat(String(v || '').replace(',', '.').replace(/[^0-9.-]/g, '')); return Number.isFinite(n) ? n : null },
    rate(v) { const n = this.number(v); return n === null ? null : (n > 1 ? n / 100 : n) },
    fmt(n) { return Number.isFinite(n) ? n.toFixed(2) : '' },
    depthValue() { const manual = this.number(this.depth); if (manual !== null) return manual; const land = this.number(this.land), front = this.number(this.front); return land === null || front === null || front === 0 ? null : land / front },
    geometryFootprint() {
        const front=this.number(this.front), depth=this.depthValue();
        if (front === null || depth === null) return null;
        const width=Math.max(0, front - (this.number(this.leftSetback) || 0) - (this.number(this.rightSetback) || 0));
        const usableDepth=Math.max(0, depth - (this.number(this.frontSetback) || 0) - (this.number(this.rearSetback) || 0));
        return width * usableDepth;
    },
    occupancyFromGeometry() { const land=this.number(this.land), footprint=this.geometryFootprint(); return land === null || land === 0 || footprint === null ? null : footprint / land },
    occupancyRatio() { return this.number(this.occ) ?? this.occupancyFromGeometry() },
    occupancyArea() { const land=this.number(this.land), ratio=this.occupancyRatio(); if (land !== null && ratio !== null) return land * ratio; return this.geometryFootprint() },
    netArea() { const manual = this.number(this.net); if (manual !== null) return manual; const land = this.number(this.land), r = this.rate(this.affect); return land === null ? null : Math.max(0, land - (land * (r || 0))) },
    buildIndex() { const manual = this.number(this.ci); if (manual !== null) return manual; const o = this.occupancyRatio(), f = this.number(this.floors); return o === null || f === null ? null : o * f },
    maxBuild() {
        const index = this.number(this.ci), base = this.netArea();
        if (base !== null && index !== null) return base * index;
        const footprint = this.occupancyArea(), floors = this.number(this.floors);
        if (footprint !== null && floors !== null) return footprint * floors;
        return this.number(this.maxBuilt);
    },
    potential() { const max = this.maxBuild(), actual = this.number(this.actual); return max === null || actual === null ? null : Math.max(0, max - actual) },
    sellableArea() { const manual = this.number(this.sellable); if (manual !== null) return manual; const max = this.maxBuild(), factor = this.number(this.sellFactor); return max === null || factor === null ? null : max * factor },
    req() { return this.residential?.[this.categorySlug]?.data?.[this.modality] || null },
    routeKey(route) { return route.type + ':' + route.slug },
    routeOptions() {
        const out = [];
        for (const route of this.routes) for (const [key, rule] of Object.entries(route.options || {})) out.push({route, key, rule});
        return out;
    },
    currentRouteOptions(route) { return this.routeOptions().filter((row) => this.routeKey(row.route) === this.routeKey(route)) },
    typeLabel(type) { return type === 'principal' ? 'Principal' : 'Compatible' },
    routeStatus(row) {
        const area=this.number(this.land), front=this.number(this.front);
        const minArea=this.number(row.rule.min_area_m2), minFront=this.number(row.rule.min_front_m);
        if (minArea === null && minFront === null && this.number(row.rule.construction_index) === null && this.number(row.rule.occupancy_index) === null) return 'Requiere revisión';
        if ((minArea !== null && area === null) || (minFront !== null && front === null)) return 'Requiere dato';
        if ((minArea !== null && area < minArea) || (minFront !== null && front < minFront)) return 'No cumple';
        return 'Cumple';
    },
    routeStatusClass(row) {
        const s=this.routeStatus(row);
        return s === 'Cumple' ? 'bg-emerald-50 text-emerald-800' : (s === 'No cumple' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-800');
    },
    routeDecisionClass(row) {
        const s=this.routeStatus(row);
        return s === 'Cumple' ? 'border-emerald-200 bg-emerald-50 text-emerald-950' : (s === 'No cumple' ? 'border-red-200 bg-red-50 text-red-950' : 'border-amber-200 bg-amber-50 text-amber-950');
    },
    routeFailures(row) {
        const fails=[], area=this.number(this.land), front=this.number(this.front);
        const minArea=this.number(row.rule.min_area_m2), minFront=this.number(row.rule.min_front_m);
        if (minArea !== null && area !== null && area < minArea) fails.push('área mínima ' + minArea + ' m², predio ' + this.fmt(area) + ' m²');
        if (minFront !== null && front !== null && front < minFront) fails.push('frente mínimo ' + minFront + ' m, predio ' + this.fmt(front) + ' m');
        if ((minArea !== null && area === null) || (minFront !== null && front === null)) fails.push('faltan datos de área o frente para cerrar cumplimiento');
        return fails;
    },
    routeSelectionText(row) {
        const label=this.typeLabel(row.route.type) + ' · ' + row.route.label + ' · ' + row.rule.label;
        const status=this.routeStatus(row), max=this.fmt(this.routeMaxBuild(row)), pot=this.fmt(this.routePotential(row));
        if (status === 'No cumple') return 'No cumple: no se adopta como potencial constructivo porque ' + this.routeFailures(row).join(' y ') + '. Puede quedar solo como soporte u observación.';
        if (status === 'Cumple') return 'Cumple: puede seleccionarse como escenario de potencial constructivo para ' + label + ', con construible ' + (max || 'pendiente') + ' m² y potencial ' + (pot || 'pendiente') + ' m², sujeto a observaciones de norma, mercado y soporte.';
        return status + ': no debe seleccionarse sin completar soporte. Revise mínimos, índice, altura, retiros y concepto aplicable.';
    },
    routeBaseArea() { return this.netArea() ?? this.number(this.land) },
    routeIndex(row) { return this.number(row.rule.construction_index) ?? this.buildIndex() },
    routeMaxBuild(row) { const base=this.routeBaseArea(), index=this.routeIndex(row); return base === null || index === null ? null : base * index },
    routePotential(row) { const max=this.routeMaxBuild(row), actual=this.number(this.actual); return max === null || actual === null ? null : Math.max(0, max - actual) },
    routeOccupation(row) { const occ=this.number(row.rule.occupancy_index); if (occ !== null) return occ; const index=this.number(row.rule.construction_index), floors=this.number(this.floors); if (index !== null && floors !== null && floors !== 0) return index / floors; return this.occupancyRatio() },
    routeOccupationArea(row) { const base=this.routeBaseArea(), occ=this.routeOccupation(row); if (base !== null && occ !== null) return base * occ; return this.occupancyArea() },
    routeCalcSummary(row) {
        const max=this.fmt(this.routeMaxBuild(row)), occ=this.fmt(this.routeOccupationArea(row)), io=this.fmt(this.routeOccupation(row)), pot=this.fmt(this.routePotential(row));
        return 'Huella: ' + (occ || 'manual') + ' m² · IO: ' + (io || 'manual') + ' · Construible: ' + (max || 'manual') + ' m² · Potencial: ' + (pot || 'manual') + ' m²';
    },
    chk(actual, min, label, unit) { if (!min) return label + ': sin mínimo cargado'; if (actual === null) return label + ': falta dato para comparar con mínimo ' + min + ' ' + unit; return actual >= min ? label + ': cumple ' + actual + ' ' + unit + ' ≥ ' + min + ' ' + unit : label + ': no cumple ' + actual + ' ' + unit + ' < ' + min + ' ' + unit },
    compliance() { const r=this.req(); if (!r) return 'Selecciona una ruta residencial y modalidad para revisar área, frente e índice.'; return [this.chk(this.number(this.land), r.min_area_m2, 'Área del lote', 'm²'), this.chk(this.number(this.front), r.min_front_m, 'Frente del lote', 'm'), 'Índice de construcción de apoyo: ' + r.construction_index, 'Altura: ' + r.height, 'Área libre: ' + r.free_area, 'Estacionamientos: ' + r.parking].join('\n') },
    optMax(r) { const base = this.netArea(); return base === null ? null : base * Number(r.construction_index || 0) },
    optPot(r) { const max = this.optMax(r), actual = this.number(this.actual); return max === null || actual === null ? null : Math.max(0, max - actual) },
    optStatus(r) { const area=this.number(this.land), front=this.number(this.front); if (area===null || front===null) return 'Falta dato'; return area >= r.min_area_m2 && front >= r.min_front_m ? 'Cumple base' : 'No cumple base' },
    optClass(r) { const s=this.optStatus(r); return s === 'Cumple base' ? 'bg-emerald-50 text-emerald-800' : (s === 'No cumple base' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-800') },
    adoptMode(mode, r) { this.modality = mode; this.ci = String(r.construction_index); this.maxBuilt = ''; this.complianceSummary = this.compliance(); this.usePane = 'indices' },
    applyReq() { const r=this.req(); if (!r) return; this.ci = String(r.construction_index); this.maxBuilt = ''; this.complianceSummary = this.compliance(); this.usePane = 'indices' }
}
