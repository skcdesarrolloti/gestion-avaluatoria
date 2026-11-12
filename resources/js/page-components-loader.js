import { loadAnalysisTable } from './analysis-table-loader.js';
const revision=typeof __INCOME_INPUTS_REVISION__==='string'?__INCOME_INPUTS_REVISION__:'development';
let incomeLoaded=false;
export async function loadPageComponents(root,alpine,importer=url=>import(url)) {
    await loadAnalysisTable(root,alpine,importer);
    if(incomeLoaded || !root.querySelector('[data-income-inputs]')) return;
    const url=new URL('income-inputs.js',import.meta.url); url.searchParams.set('v',revision);
    const module=await importer(url.href);
    if(typeof module.incomeInputs!=='function') throw new Error('No se pudo cargar Renta.');
    alpine.data('incomeInputs',module.incomeInputs); incomeLoaded=true;
}
