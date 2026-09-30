// One JSON field avoids PHP max_input_vars truncating large comparable matrices.
export function packComparableRows(body) {
    const rows = new Map();
    for (const [name, value] of [...body.entries()]) {
        const match = name.match(/^comparables\[(\d+)\]\[([^\]]+)\]$/);
        if (!match) continue;
        const row = rows.get(match[1]) || {};
        row[match[2]] = value;
        rows.set(match[1], row);
        body.delete(name);
    }
    body.set('comparable_rows_json', JSON.stringify([...rows.values()]));
}
