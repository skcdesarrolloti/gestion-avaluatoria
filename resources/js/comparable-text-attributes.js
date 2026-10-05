// Keep units and word boundaries: "2 Baños395 m²" is two bathrooms, never 395.
export function explicitCount(text, label) {
    const patterns=[
        new RegExp(`(?:^|[^\\d.,])([0-9]{1,3})\\s*(?:${label})(?=$|[\\s\\d:;,.)])`, 'i'),
        new RegExp(`(?:^|\\b)(?:${label})[\\s:：=-]+([0-9]{1,3})(?![\\d.,]|\\s*(?:m[2²]|mt[s]?\\.?[2²]|metros))`, 'i'),
    ];
    for (const pattern of patterns) { const match=String(text || '').match(pattern); if (match) return match[1]; }
    return '';
}
export function descriptionAttributes(text) {
    const raw=String(text || '').replace(/\s+/g,' ').trim(), facts={};
    const add=(label,regex) => { const phrase=raw.match(regex)?.[0]; if (phrase) facts[label]=phrase.trim().slice(0,240); };
    add('Vista descrita',/vista\s+(?:panor[aá]mica|al mar|frontal al mar|paisaj[ií]stica|exterior|interior)[^.;\n]{0,65}/i);
    add('Acabados descritos',/(?:acabados(?: de las oficinas)?\s*:[^.;]{1,160}|(?:excelentes|buenos|modernos) acabados)/i);
    add('Servicios descritos',/(?:servicios (?:b[aá]sicos|p[uú]blicos)(?: de)?[^.;]{0,100}|agua y electricidad)/i);
    add('Estado descrito',/estado del inmueble\s*:[^.;-]{1,65}/i);
    for (const [label,attribute] of Object.entries({Ascensor:'ascensor(?:es)?', 'Acceso para discapacitados':'acceso para discapacitados',
        'Acceso pavimentado':'acceso pavimentado',Balcón:'balc[oó]n',Recepción:'recepci[oó]n',Vigilancia:'vigilancia',
        'Aire acondicionado':'aire acondicionado', 'Planta eléctrica descrita':'planta el[eé]ctrica',
        'Piscina descrita':'piscina', 'Gimnasio descrito':'gimnasio', 'Terraza descrita':'terraza'})) {
        const negative=new RegExp(`(?:sin|no (?:tiene|cuenta con|dispone de))\\s+(?:${attribute})\\b`,'i').test(raw);
        const positive=new RegExp(`\\b(?:${attribute})\\b`,'i').test(raw);
        if (positive) facts[label]=negative ? 'No · descrito en el anuncio' : 'Mencionado en la descripción · verificar alcance';
    }
    return facts;
}
