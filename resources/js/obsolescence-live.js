export function obsolescenceLive() {
  return {
    activeObs: 'fisica',
    allScores: {},
    allEvidence: {},
    scoreHelp: {},
    init() {
      this.allScores = this.readJson('obsolescenceScores');
      this.allEvidence = this.readJson('obsolescenceEvidence');
      this.scoreHelp = this.readJson('obsolescenceScoreHelp');
    },
    readJson(name) {
      try {
        return JSON.parse(this.$el.dataset[name] || '{}');
      } catch {
        return {};
      }
    },
    value(v) {
      return ['0', '1', '2', '3'].includes(v) ? Number(v) : null;
    },
    values(group) {
      return Object.values(this.allScores[group] || {});
    },
    applicable(group) {
      return this.values(group).filter((v) => this.value(v) !== null).length;
    },
    sum(group) {
      return this.values(group).reduce((total, v) => total + (this.value(v) ?? 0), 0);
    },
    max(group) {
      return this.applicable(group) * 3;
    },
    ieo(group) {
      return this.max(group) ? (this.sum(group) / this.max(group)) * 100 : 0;
    },
    levelFrom(ieo) {
      if (ieo === 0) return 'Sin hallazgos';
      if (ieo <= 33.33) return 'Leve';
      if (ieo <= 66.67) return 'Relevante';
      return 'Crítica';
    },
    groupLevel(group) {
      return this.levelFrom(this.ieo(group));
    },
    groupCalc(group) {
      if (!this.applicable(group)) return 'Sin factores revisados';
      return `${this.sum(group)} de ${this.max(group)} puntos posibles = ${this.format(this.ieo(group))} %`;
    },
    groupSummary(code, group) {
      if (!this.applicable(group)) return `${code} · Sin factores revisados`;
      return `${code} · ${this.format(this.ieo(group))} % · ${this.groupLevel(group)}`;
    },
    evidence(group, item) {
      return ((this.allEvidence[group] || {})[item] || '').trim();
    },
    needsSupport(group, item) {
      const score = (this.allScores[group] || {})[item];
      return ['2', '3'].includes(score) && this.evidence(group, item) === '';
    },
    missing(group) {
      return Object.keys(this.allScores[group] || {}).filter((item) => this.needsSupport(group, item)).length;
    },
    state(group) {
      if (this.missing(group) > 0) return 'warn';
      return this.applicable(group) > 0 ? 'ok' : 'optional';
    },
    stateText(group) {
      if (this.state(group) === 'warn') return 'Revisar';
      return this.state(group) === 'ok' ? 'Completo' : 'Opcional';
    },
    stateClass(group) {
      if (this.state(group) === 'warn') return 'bg-amber-100 text-amber-800';
      return this.state(group) === 'ok' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700';
    },
    actionText(group) {
      if (this.missing(group) > 0) return 'Agregar soporte breve en hallazgos relevantes o críticos.';
      return this.applicable(group) > 0 ? 'Listo para lectura.' : 'Pendiente; marque 0 si ya revisó y no encontró hallazgos.';
    },
    scoreText(group, item) {
      const score = (this.allScores[group] || {})[item] || '';
      return this.scoreHelp[score] || this.scoreHelp[''] || '';
    },
    globalValues() {
      return Object.values(this.allScores).flatMap((group) => Object.values(group));
    },
    globalApplicable() {
      return this.globalValues().filter((v) => this.value(v) !== null).length;
    },
    globalSum() {
      return this.globalValues().reduce((total, v) => total + (this.value(v) ?? 0), 0);
    },
    globalIeo() {
      return this.globalApplicable() ? (this.globalSum() / (this.globalApplicable() * 3)) * 100 : 0;
    },
    globalLabel() {
      return `IEO diagnóstico global: ${this.format(this.globalIeo())} % · ${this.levelFrom(this.globalIeo())}`;
    },
    format(value) {
      return value.toFixed(1).replace('.', ',');
    },
  };
}
