export function obsolescenceLive() {
  return {
    activeObs: 'fisica',
    allScores: {},
    allEvidence: {},
    scoreHelp: {},
    groupInfo: {},
    summaryText: '',
    init() {
      this.allScores = this.readJson('obsolescenceScores');
      this.allEvidence = this.readJson('obsolescenceEvidence');
      this.scoreHelp = this.readJson('obsolescenceScoreHelp');
      this.groupInfo = this.readJson('obsolescenceGroups');
      this.summaryText = this.$el.dataset.obsolescenceSummary || this.deliverableText();
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
    deliverableText() {
      const parts = ['Obsolescencias:'];
      for (const group of Object.keys(this.groupInfo)) {
        parts.push(this.groupText(group));
      }
      parts.push(this.economicText());
      return parts.join(' ');
    },
    groupText(group) {
      const info = this.groupInfo[group] || { title: group, items: {}, no_finding: '' };
      const findings = this.findings(group);
      if (findings.length === 0) {
        return `${info.title}: ${info.no_finding}`;
      }
      const labels = findings.map((finding) => finding.label).join(', ');
      const supports = findings.map((finding) => finding.evidence).filter(Boolean);
      const supportText = supports.length ? ` Soporte observado: ${supports.join('; ')}.` : ' Requiere completar soporte breve antes de cerrar el informe.';
      return `${info.title}: se identifican hallazgos de nivel ${this.groupLevel(group).toLowerCase()} en ${labels}.${supportText}`;
    },
    findings(group) {
      const labels = (this.groupInfo[group] || {}).items || {};
      return Object.entries(this.allScores[group] || {})
        .filter(([, score]) => ['1', '2', '3'].includes(score))
        .map(([item, score]) => ({
          item,
          score,
          label: labels[item] || item,
          evidence: this.evidence(group, item),
        }));
    },
    economicText() {
      if (this.globalIeo() === 0) {
        return 'Con la información revisada no se advierte efecto económico material por obsolescencia y no se aplica descuento automático.';
      }
      if (Object.keys(this.groupInfo).some((group) => this.missing(group) > 0)) {
        return 'La incidencia económica queda pendiente hasta completar el soporte de los hallazgos relevantes o críticos.';
      }
      return 'El IEO es un indicador diagnóstico y no equivale a depreciación automática; cualquier incidencia económica debe sustentarse aparte mediante mercado, costos, comparables o criterio técnico verificable.';
    },
    format(value) {
      return value.toFixed(1).replace('.', ',');
    },
  };
}
