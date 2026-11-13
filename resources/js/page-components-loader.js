import { loadAnalysisTable } from './analysis-table-loader.js';
const revision=typeof __INCOME_INPUTS_REVISION__==='string'?__INCOME_INPUTS_REVISION__:'development';
const igacRevision=typeof __IGAC_SELECTOR_REVISION__==='string'?__IGAC_SELECTOR_REVISION__:'development';
let incomeLoaded=false,igacLoaded=false;
export async function loadPageComponents(root,alpine,importer=url=>import(url)) {
    await loadAnalysisTable(root,alpine,importer);
    if(!igacLoaded && root.querySelector('[x-data^="igacUnitSelector("]')) {
        const url=new URL('igac-unit-selector.js',import.meta.url);url.searchParams.set('v',igacRevision);
        const module=await importer(url.href);
        if(typeof module.igacUnitSelector!=='function') throw new Error('IGAC');
        alpine.data('igacUnitSelector',module.igacUnitSelector);igacLoaded=true;
    }
    if(incomeLoaded || !root.querySelector('[data-income-inputs]')) return;
    const url=new URL('income-inputs.js',import.meta.url); url.searchParams.set('v',revision);
    const module=await importer(url.href);
    if(typeof module.incomeInputs!=='function') throw new Error('Renta');
    alpine.data('incomeInputs',module.incomeInputs); incomeLoaded=true;
}
