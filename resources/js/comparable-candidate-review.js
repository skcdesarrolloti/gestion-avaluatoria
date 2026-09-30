import { duplicateEvidence } from './comparable-duplicates.js';

export function candidateMatches(results, existing) {
    return results.map((item, index) => {
        const targets = existing.map((row, i) => ({ row, inMatrix: true, label: `Muestra ${i + 1} de la matriz` }));
        results.forEach((other, i) => {
            if (i !== index) targets.push({ row: other.row, label: `Aviso ${i + 1} de esta página` });
        });
        return targets.flatMap(target => {
            const evidence = duplicateEvidence(item.row, target.row);
            return evidence ? [{ ...target, ...evidence }] : [];
        });
    });
}

export function unresolvedCandidates(results, selected) {
    return results.filter(item => selected.includes(item.row.source_url) && !item.distinct
        && item.matches.some(match => match.inMatrix || selected.includes(match.row.source_url)));
}

export function matrixRows(form) {
    return [...form.querySelectorAll('tbody tr')].map(row => Object.fromEntries(
        [...row.querySelectorAll('[name]')].map(input => [input.name.match(/\[([^\]]+)\]$/)[1], input.value])
    ));
}

export function candidateSuggestions(results) {
    const keep = [];
    return results.map(item => {
        const registered = item.matches.find(match => match.inMatrix && match.exact);
        if (registered) return { tone: 'registered', suggested: false, label: `Ya incorporado: ${registered.label}` };
        const matrixMatch = item.matches.find(match => match.inMatrix);
        if (matrixMatch) return { tone: 'review', suggested: false, label: `Revisar coincidencia con ${matrixMatch.label}` };
        const representative = keep.find(other => duplicateEvidence(item.row, other.row));
        if (representative) return { tone: 'review', suggested: false, label: `Alternativa al aviso ${representative.number}: puedes dejar solo uno` };
        keep.push(item);
        return { tone: 'suggested', suggested: true, label: item.matches.length ? 'Sugerido para conservar entre los coincidentes' : 'Nuevo · sin coincidencias detectadas' };
    });
}
