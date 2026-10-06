const revision=typeof __ANALYSIS_TABLE_REVISION__==='string'?__ANALYSIS_TABLE_REVISION__:'development';
let loaded=false;
export async function loadAnalysisTable(root,alpine,importer=url=>import(url)) {
    if(loaded || !root.querySelector('[data-market-analysis]')) return;
    const url=new URL('market-analysis-table.js',import.meta.url);url.searchParams.set('v',revision);
    const module=await importer(url.href);
    if(typeof module.marketAnalysisTable!=='function') throw new Error('No se pudo cargar el análisis. Recarga la página.');
    alpine.data('marketAnalysisTable',module.marketAnalysisTable);loaded=true;
}
