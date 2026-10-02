// Hidden context identifies the exported collection and columns after Excel saves it.
const escape = value => String(value).replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;');
export function addWorkbookContext(files, context, columns, title) {
    if (!context) return files;
    const ns = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
    const metadata = {...context, format:'sucasa-comparables-2', columns, sheet:title};
    files['[Content_Types].xml'] = files['[Content_Types].xml'].replace('</Types>', '<Override PartName="/xl/worksheets/sheet2.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>');
    files['xl/workbook.xml'] = files['xl/workbook.xml'].replace('</sheets>', '<sheet name="_SuCasa" sheetId="2" state="veryHidden" r:id="rId3"/></sheets>');
    files['xl/_rels/workbook.xml.rels'] = files['xl/_rels/workbook.xml.rels'].replace('</Relationships>', '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet2.xml"/></Relationships>');
    files['xl/worksheets/sheet2.xml'] = `<worksheet xmlns="${ns}"><sheetData><row r="1"><c r="A1" t="inlineStr"><is><t>${escape(JSON.stringify(metadata))}</t></is></c></row></sheetData></worksheet>`;
    return files;
}
