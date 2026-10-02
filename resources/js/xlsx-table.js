// Small OOXML export for the app: inline text never becomes an executable formula.
const encoder = new TextEncoder();
const xml = value => String(value ?? '').replace(/[\x00-\x08\x0b\x0c\x0e-\x1f]/g, '')
    .replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;');
export function columnName(index) {
    let name = '';
    for (index++; index; index = Math.floor((index - 1) / 26)) name = String.fromCharCode(65 + (index - 1) % 26) + name;
    return name;
}
function crc32(bytes) {
    let crc = 0xffffffff;
    for (const byte of bytes) {
        crc ^= byte;
        for (let i = 0; i < 8; i++) crc = (crc >>> 1) ^ ((crc & 1) ? 0xedb88320 : 0);
    }
    return (crc ^ 0xffffffff) >>> 0;
}
function zip(files) {
    const chunks = [], directory = []; let offset = 0;
    for (const [path, content] of Object.entries(files)) {
        const name = encoder.encode(path), data = encoder.encode(content), crc = crc32(data);
        const header = new Uint8Array(30 + name.length), view = new DataView(header.buffer);
        view.setUint32(0, 0x04034b50, true); view.setUint16(4, 20, true); view.setUint16(6, 0x800, true);
        view.setUint32(14, crc, true); view.setUint32(18, data.length, true); view.setUint32(22, data.length, true);
        view.setUint16(26, name.length, true); header.set(name, 30);
        const central = new Uint8Array(46 + name.length), cv = new DataView(central.buffer);
        cv.setUint32(0, 0x02014b50, true); cv.setUint16(4, 20, true); cv.setUint16(6, 20, true); cv.setUint16(8, 0x800, true);
        cv.setUint32(16, crc, true); cv.setUint32(20, data.length, true); cv.setUint32(24, data.length, true);
        cv.setUint16(28, name.length, true); cv.setUint32(42, offset, true); central.set(name, 46);
        chunks.push(header, data); directory.push(central); offset += header.length + data.length;
    }
    const size = directory.reduce((sum, chunk) => sum + chunk.length, 0), end = new Uint8Array(22), ev = new DataView(end.buffer);
    ev.setUint32(0, 0x06054b50, true); ev.setUint16(8, directory.length, true); ev.setUint16(10, directory.length, true);
    ev.setUint32(12, size, true); ev.setUint32(16, offset, true);
    const result = new Uint8Array(offset + size + end.length); let position = 0;
    for (const chunk of [...chunks, ...directory, end]) { result.set(chunk, position); position += chunk.length; }
    return result;
}
export function tableWorkbook(columns, rows, title = 'Comparables') {
    const ns = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
    const rel = 'http://schemas.openxmlformats.org/package/2006/relationships';
    const range = `A1:${columnName(columns.length - 1)}${rows.length + 1}`;
    const cell = (ref, value, style) => typeof value === 'number' && Number.isFinite(value)
        ? `<c r="${ref}" s="${style}"><v>${value}</v></c>`
        : `<c r="${ref}" s="${style}" t="inlineStr"><is><t xml:space="preserve">${xml(value)}</t></is></c>`;
    const header = columns.map((column, i) => cell(`${columnName(i)}1`, column.label, 1)).join('');
    const body = rows.map((row, r) => `<row r="${r + 2}">${columns.map((column, c) => {
        const item = row[column.key] ?? {value: '', pending: true};
        const offerIndex = columns.findIndex(item => item.key === 'price_amount'), discountIndex = columns.findIndex(item => item.key === 'negotiation_discount');
        if (['negotiated_amount','negotiation_percent'].includes(column.key) && offerIndex >= 0 && discountIndex >= 0) {
            const offer = `${columnName(offerIndex)}${r+2}`, discount = `${columnName(discountIndex)}${r+2}`;
            const expression = column.key === 'negotiated_amount' ? `${offer}-${discount}` : `IF(${offer}=0,0,${discount}/${offer}*100)`;
            const formula = `IF(AND(ISNUMBER(${offer}),ISNUMBER(${discount}),${discount}>=0,${discount}<=${offer}),${expression},"")`;
            return `<c r="${columnName(c)}${r+2}" s="${item.pending ? 3 : 2}"${typeof item.value === 'number' ? '' : ' t="str"'}><f>${xml(formula)}</f><v>${xml(item.value)}</v></c>`;
        }
        return cell(`${columnName(c)}${r + 2}`, item.value, item.pending ? 3 : column.numeric ? 2 : 0);
    }).join('')}</row>`).join('');
    const types = '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/>';
    const style = `<styleSheet xmlns="${ns}"><fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><color rgb="FFFFFFFF"/><sz val="11"/><name val="Calibri"/></font></fonts><fills count="4"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF0F766E"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFFEF3C7"/></patternFill></fill></fills><borders count="1"><border/></borders><cellStyleXfs count="1"><xf/></cellStyleXfs><cellXfs count="4"><xf fontId="0" fillId="0" borderId="0" xfId="0"><alignment vertical="top" wrapText="1"/></xf><xf fontId="1" fillId="2" borderId="0" xfId="0" applyFill="1"><alignment wrapText="1"/></xf><xf numFmtId="4" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/><xf fontId="0" fillId="3" borderId="0" xfId="0" applyFill="1"><alignment wrapText="1"/></xf></cellXfs></styleSheet>`;
    const files = {
        '[Content_Types].xml': types + [['/xl/workbook.xml','sheet.main'], ['/xl/worksheets/sheet1.xml','worksheet'], ['/xl/styles.xml','styles'], ['/xl/tables/table1.xml','table']].map(([path,type]) => `<Override PartName="${path}" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.${type}+xml"/>`).join('') + '</Types>',
        '_rels/.rels': `<Relationships xmlns="${rel}"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>`,
        'xl/workbook.xml': `<workbook xmlns="${ns}" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="${xml(title)}" sheetId="1" r:id="rId1"/></sheets></workbook>`,
        'xl/_rels/workbook.xml.rels': `<Relationships xmlns="${rel}"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>`,
        'xl/styles.xml': style.replace('</styleSheet>', '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles></styleSheet>'),
        'xl/worksheets/sheet1.xml': `<worksheet xmlns="${ns}" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheetViews><sheetView workbookViewId="0"><pane ySplit="1" xSplit="1" topLeftCell="B2" activePane="bottomRight" state="frozen"/></sheetView></sheetViews><cols>${columns.map((_,i) => `<col min="${i+1}" max="${i+1}" width="26" customWidth="1"/>`).join('')}</cols><sheetData><row r="1" ht="65" customHeight="1">${header}</row>${body}</sheetData><tableParts count="1"><tablePart r:id="rId1"/></tableParts></worksheet>`,
        'xl/worksheets/_rels/sheet1.xml.rels': `<Relationships xmlns="${rel}"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/table" Target="../tables/table1.xml"/></Relationships>`,
        'xl/tables/table1.xml': `<table xmlns="${ns}" id="1" name="Muestras" displayName="Muestras" ref="${range}" totalsRowShown="0"><autoFilter ref="${range}"/><tableColumns count="${columns.length}">${columns.map((column,i) => `<tableColumn id="${i+1}" name="${xml(column.label)}"/>`).join('')}</tableColumns><tableStyleInfo name="TableStyleMedium2" showFirstColumn="0" showLastColumn="0" showRowStripes="1" showColumnStripes="0"/></table>`,
    };
    return zip(files);
}
