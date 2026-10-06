import {amount} from './comparable-negotiation.js';
export function modelArea(columns) {
    return columns.find(c=>/^published:area privada(?: construida)?(?:\s*[·-]?\s*m[²2])?$/.test(c.key)) || {key:'area_m2',label:'Área publicada · m²'};
}
export function modelFactorCount(columns) {return columns.length+(modelArea(columns).key==='area_m2'?1:0);}
export function positiveModelArea(value) {return amount(String(value??'').replace(/m[²2]|metros? cuadrados?/gi,''))>0;}
