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
      if (!this.applicable(group)) return 'Pendiente de revisión.';
      const findings = this.findings(group).length;
      const findingText = findings === 1 ? '1 factor con hallazgo' : `${findings} factores con hallazgo`;
      return `Resultado de apoyo interno: ${this.groupLevel(group)}; ${this.applicable(group)} factores revisados; ${findingText}.`;
    },
    groupSummary(code, group) {
      if (!this.applicable(group)) return `${code} · Pendiente`;
      const findings = this.findings(group).length;
      return `${code} · ${this.groupLevel(group)} · ${findings} hallazgo${findings === 1 ? '' : 's'}`;
    },
    groupTabResult(group) {
      if (!this.applicable(group)) return 'Pendiente';
      const findings = this.findings(group).length;
      if (findings === 0) return 'Sin hallazgos';
      return `${this.groupLevel(group)} · ${findings} hallazgo${findings === 1 ? '' : 's'}`;
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
      if (!this.globalApplicable()) return 'Resultado global: pendiente de revisión';
      const totalFindings = Object.keys(this.groupInfo).reduce((total, group) => total + this.findings(group).length, 0);
      if (totalFindings === 0) return 'Resultado global: sin hallazgos de obsolescencia';
      return `Resultado global: ${this.levelFrom(this.globalIeo()).toLowerCase()} · ${totalFindings} hallazgo${totalFindings === 1 ? '' : 's'}`;
    },
    deliverableText() {
      const parts = [];
      for (const group of Object.keys(this.groupInfo)) {
        parts.push(this.groupText(group));
      }
      parts.push(this.economicText());
      return parts.join('\n\n');
    },
    groupText(group) {
      const info = this.groupInfo[group] || { title: group, items: {}, no_finding: '' };
      const findings = this.findings(group);
      if (findings.length === 0) {
        return `${info.title}: ${info.no_finding}`;
      }
      const labels = findings.map((finding) => finding.label).join(', ');
      const supports = findings.map((finding) => `${finding.label}: ${finding.evidence}`).filter((text) => !text.endsWith(': '));
      const supportText = supports.length ? ` Soporte registrado: ${supports.join('; ')}.` : ' Requiere completar soporte breve antes de cerrar el informe.';
      return `${info.title}: se identifican hallazgos en ${labels}. La lectura técnica preliminar del bloque es ${this.groupLevel(group).toLowerCase()}.${supportText}`;
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
        return 'Incidencia valuatoria: con la información revisada no se advierte efecto económico material por obsolescencia y no se aplica descuento automático.';
      }
      if (Object.keys(this.groupInfo).some((group) => this.missing(group) > 0)) {
        return 'Incidencia valuatoria: queda pendiente hasta completar el soporte de los hallazgos relevantes o críticos.';
      }
      return 'Incidencia valuatoria: la calificación anterior es una ayuda interna de revisión y no equivale a depreciación automática; cualquier incidencia económica debe sustentarse aparte mediante mercado, costos, comparables o criterio técnico verificable, conforme al enfoque de valuación aplicado.';
    },
    format(value) {
      return value.toFixed(1).replace('.', ',');
    },
  };
}
