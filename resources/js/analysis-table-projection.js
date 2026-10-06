import {portalTable} from './comparable-portal-table.js';
const keys=['id','published_attributes','listing_code','price_amount','area_m2','bathrooms','parking_spaces','contact_name','property_type','neighborhood','bedrooms','floor_level','admin_fee','source_name','source_url','listing_title','project_name'];
// Reuse projections until a source value changes; do not parse every ficha per cell.
export function tableProjection(manual=false) {
    let previous=[], table, lastRevision, lastRows;
    return (rows,revision)=>{
        if(revision!==undefined){if(table && revision===lastRevision && rows===lastRows)return table;lastRevision=revision;lastRows=rows;}
        const values=rows.flatMap(row=>[...keys.map(k=>row[k]),...(manual?[row.analysis_manual_factors]:[])]);
        if(!table || values.length!==previous.length || values.some((v,i)=>v!==previous[i])) {
            table=portalTable(rows,manual);previous=values;
        }
        return table;
    };
}
