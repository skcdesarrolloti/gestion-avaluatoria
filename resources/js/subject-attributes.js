function formatAdjustment(value) {
    if (value === null || !Number.isFinite(value)) return 'pendiente';
    if (Math.abs(value) < 0.05) return '0%';
    return `${value > 0 ? '+' : ''}${String(value).replace('.', ',')}%`;
}

function scoreFromUnit(unit) {
    let sum = 0;
    let weightSum = 0;
    let count = 0;
    unit.querySelectorAll('[data-attribute-rating]').forEach((rating) => {
        const row = rating.closest('[data-attribute-row]');
        const toggle = row?.querySelector('[data-attribute-toggle]');
        if (rating.disabled || (toggle && !toggle.checked)) return;
        const weight = row?.querySelector('[data-attribute-weight]');
        const ratingValue = Number.parseInt(rating.value, 10);
        const weightValue = Number.parseInt(weight?.value || '', 10);
        if (!Number.isInteger(ratingValue) || !Number.isInteger(weightValue)) return;
        if (ratingValue < 1 || ratingValue > 5 || weightValue < 1 || weightValue > 3) return;
        sum += ratingValue * weightValue;
        weightSum += weightValue;
        count += 1;
    });
    if (weightSum === 0) return { adjustment: null, percent: null, count: 0 };
    const score = Math.round((sum / weightSum) * 100) / 100;
    const percent = Math.round((score / 5) * 100);
    const rawAdjustment = ((score - 3) / 2) * 10;
    const adjustment = Math.round(Math.max(-10, Math.min(10, rawAdjustment)) * 10) / 10;
    return { adjustment, percent, count };
}

export function subjectAttributes(initialUnit = '') {
    return {
        activeAttributes: initialUnit,
        busyAttributes: false,
        scores: {},
        selectionTick: 0,
        init() {
            this.refreshScores();
        },
        handleAttributeChange(event) {
            if (event.target?.matches?.('[data-attribute-toggle]')) this.selectionTick += 1;
            if (event.target?.matches?.('[data-attribute-rating]') && event.target.value) {
                const weight = event.target.closest('[data-attribute-row]')?.querySelector('[data-attribute-weight]');
                if (weight && !weight.value) {
                    weight.value = '2';
                    weight.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
            this.$nextTick(() => this.refreshScores());
        },
        refreshScores() {
            const next = {};
            this.$el.querySelectorAll('[data-attribute-unit]').forEach((unit) => {
                next[unit.dataset.unitId] = scoreFromUnit(unit);
            });
            this.scores = next;
        },
        unitAdjustment(unitId) {
            return formatAdjustment(this.scores[unitId]?.adjustment ?? null);
        },
        selectedCount(unitId) {
            const unit = this.$el.querySelector(`[data-unit-id="${unitId}"]`);
            return unit ? unit.querySelectorAll('[data-attribute-toggle]:checked').length : 0;
        },
        unitLimitText(unitId) {
            this.selectionTick;
            const count = this.selectedCount(unitId);
            return count <= 6 ? `${count} seleccionados · máximo 6` : `${count} seleccionados · máximo 6; revisa si todos inciden`;
        },
        unitSuggestionClass(unitId) {
            this.selectionTick;
            return this.selectedCount(unitId) > 6 ? 'bg-amber-100 text-amber-900' : 'bg-white text-amber-800';
        },
        unitScoreText(unitId) {
            const score = this.scores[unitId];
            if (!score || score.adjustment === null || !Number.isFinite(score.adjustment)) return 'Ajuste pendiente';
            return `Ajuste ${formatAdjustment(score.adjustment)} · índice ${score.percent}%`;
        },
    };
}
