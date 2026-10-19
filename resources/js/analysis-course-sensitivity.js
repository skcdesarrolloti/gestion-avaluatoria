import {courseCalculation} from './analysis-course-calculation.js';

// Derived only from the saved execution. No exclusions, new thresholds or writes.
export function courseSensitivity(result,fmt=v=>Number.isFinite(v)?v.toLocaleString('es-CO',{maximumFractionDigits:4}):'No estimable'){
    const c=courseCalculation(result);if(!c)return null;
    const s=result.summary,n=c.n,k=c.trim;
    const comparison=[
        {label:'Media',value:s.mean,operation:'Σ valores / n = '+fmt(c.meanSum)+' / '+n},
        {label:'Mediana',value:s.median,operation:c.medianLowPosition===c.medianHighPosition
            ?'Dato en posición '+c.medianLowPosition+' = '+fmt(c.medianLow.y)
            :'('+fmt(c.medianLow.y)+' + '+fmt(c.medianHigh.y)+') / 2 · posiciones '+c.medianLowPosition+' y '+c.medianHighPosition},
        {label:'Media recortada',value:c.trimmedValue,operation:fmt(c.trimmedSum)+' / '+c.trimmedCount+' · posiciones '+c.trimmedFirst+' a '+c.trimmedLast},
    ].map(v=>({...v,difference:Number.isFinite(v.value)?v.value-s.mean:null,
        percent:Number.isFinite(v.value)&&s.mean!==0?100*(v.value-s.mean)/Math.abs(s.mean):null}));
    const outside=result.valid.filter(v=>v.y<s.lower||v.y>s.upper),outsideBand=result.valid.filter(v=>v.y<s.q1||v.y>s.q3);
    const flagged=s.considerations.filter(v=>v.flags.length);
    const shape=Number.isFinite(s.skew)?s.skew>0?'La cola se orienta hacia valores altos.':s.skew<0?'La cola se orienta hacia valores bajos.':'La asimetría calculada es cero.':'La asimetría no es estimable.';
    const kurt=Number.isFinite(s.kurt)?s.kurt>0?'La curtosis supera la referencia normal: revisa la contribución de las desviaciones extremas.':s.kurt<0?'La curtosis queda por debajo de la referencia normal; no garantiza comparabilidad.':'La curtosis coincide numéricamente con la referencia normal; no demuestra normalidad.':'La curtosis no es estimable.';
    const differences=comparison.slice(1).map(v=>v.label+': diferencia '+fmt(v.difference)+' COP/m² ('+fmt(v.percent)+' %) frente a la media').join('; ');
    return {comparison,n,k,retained:c.trimmedCount,outside:outside.length,outsideBand:outsideBand.length,
        reading:'Con '+n+' valores, la media es '+fmt(s.mean)+' COP/m². '+differences+'. '+(flagged.length?'Señales exploratorias: '+flagged.map(v=>v.label).join(', ')+'.':'Sin señales exploratorias por RIC o distancia robusta.'),
        meaning:outsideBand.length+' muestras están fuera de Q1–Q3; esto es normal en una franja que representa aproximadamente el 50 % central. '+outside.length+' están fuera de los límites exploratorios de 1,5 RIC: son señales para revisión, no errores demostrados. La recortada deja '+k+' valores de cada cola fuera de su cálculo y usa '+c.trimmedCount+'; ese recorte no coincide necesariamente con las señales del RIC ni modifica el grupo. '+shape+' '+kurt,
        next:(flagged.length?'Revisa las muestras señaladas por RIC o distancia robusta y registra si sus datos y características son comparables.':'No hay muestras señaladas por estos criterios; revisa igualmente el soporte y la comparabilidad.')+' Las diferencias cuantifican cuánto cambia el centro al cambiar de estimador; no se ha fijado un umbral para clasificarlas. El CV del grupo completo permanece en '+fmt(s.cv)+' %: esta comparación no lo reduce ni acredita cumplimiento. No se adopta un valor ni se elimina una muestra.'+(result.simulated?' Hay datos simulados: el concepto corresponde al ejercicio, no acredita el mercado.':'')};
}
