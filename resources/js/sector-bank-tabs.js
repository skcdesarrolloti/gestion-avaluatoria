export function normalizeBankSection(value) {
    const match = String(value ?? '').match(/\d+/);
    return match ? match[0].padStart(2, '0') : '';
}

export function bankSectionFromHash(hash = window.location.hash) {
    const match = String(hash).match(/^#banco-(\d{1,2})$/);
    return match ? normalizeBankSection(match[1]) : '';
}

export function nextBankSection(codes, active) {
    const normalized = codes.map(normalizeBankSection).filter(Boolean);
    const index = normalized.indexOf(normalizeBankSection(active));
    return index >= 0 ? normalized[index + 1] || '' : '';
}

export function targetSectorFor(codes, active) {
    const next = nextBankSection(codes, active);
    return next ? `banco-${next}` : 'bien-sujeto';
}

export function sectorBankTabs(sectionCodes = []) {
    return {
        sectionCodes: sectionCodes.map(normalizeBankSection).filter(Boolean),
        activeBankSection: '',
        afterSectorSave: '',
        init() {
            this.selectSection(bankSectionFromHash() || this.sectionCodes[0] || '01', false);
            this.syncHash = () => {
                const section = bankSectionFromHash();
                if (section) this.selectSection(section, false);
            };
            window.addEventListener('hashchange', this.syncHash);
        },
        destroy() {
            window.removeEventListener('hashchange', this.syncHash);
        },
        selectSection(section, updateHash = true) {
            const code = normalizeBankSection(section);
            if (!this.sectionCodes.includes(code)) return;
            this.activeBankSection = code;
            this.afterSectorSave = '';
            if (updateHash) history.replaceState(null, '', `${location.pathname}${location.search}#banco-${code}`);
        },
        nextBankSection() {
            return nextBankSection(this.sectionCodes, this.activeBankSection);
        },
        targetSector() {
            return targetSectorFor(this.sectionCodes, this.activeBankSection);
        },
        advanceLabel() {
            const next = this.nextBankSection();
            return next ? `Guardar y pasar a 2.${parseInt(next, 10)}` : 'Guardar y pasar al numeral 3';
        },
        prepareSectorSave() {
            const hashSection = bankSectionFromHash();
            if (hashSection && this.sectionCodes.includes(hashSection)) this.activeBankSection = hashSection;
            this.afterSectorSave = this.targetSector() === 'bien-sujeto' ? 'bien-sujeto' : '';
            this.$refs.activeSector.value = `banco-${this.activeBankSection}`;
            this.$refs.targetSector.value = this.targetSector();
            this.$refs.afterSectorSave.value = this.afterSectorSave;
        },
    };
}
