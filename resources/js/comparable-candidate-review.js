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
