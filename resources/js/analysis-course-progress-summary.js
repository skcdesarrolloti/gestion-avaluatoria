import {preferredCourseEstimators} from './analysis-course-math.js';
import {outsideCourseLimits} from './analysis-course-dispersion-plot.js';
const fmt=v=>Number.isFinite(v)?v.toLocaleString('es-CO',{maximumFractionDigits:2}):'no estimable';
export function courseProgressSummary(r){
    const s=r.summary,selected=preferredCourseEstimators(s.estimators),outside=r.valid.filter(v=>outsideCourseLimits(v,s));
    return {
        approximation:selected.length?'La referencia descriptiva por menor MAPE es '+selected.map(e=>e.label+' = '+fmt(e.value)+' COP/m² (MAPE '+fmt(e.mape)+' %)').join('; ')+(selected.length>1?'. Existe empate; no se elige uno arbitrariamente.':'.'):'No hay un centro con MAPE estimable para proponer una referencia descriptiva.',
        distribution:r.valid.length+' valores disponibles. Media: '+fmt(s.mean)+' COP/m²; mediana: '+fmt(s.median)+' COP/m². El tramo central Q1–Q3 va de '+fmt(s.q1)+' a '+fmt(s.q3)+' COP/m² y reúne aproximadamente el 50 % central de las posiciones ordenadas. Es una franja descriptiva, no un intervalo de valoración del inmueble.',
        median:'La mediana aparece porque Q2 es, por definición, el corte central de los cuartiles. Se usa para describir la distribución y depende menos de la magnitud de los extremos. No sustituye al centro seleccionado por MAPE. La nube y el CV siguen utilizando la media aritmética.',
        next:'Siguiente paso: Sensibilidad y consideraciones. '+outside.length+' inmuebles quedan fuera de los límites exploratorios. Revisaremos su fuente y comparabilidad, y contrastaremos media, mediana y media recortada para conocer la influencia de los extremos. Una señal no autoriza su eliminación.',
        scope:(r.simulated?'La ejecución contiene datos simulados; esta aproximación ilustra el análisis y no acredita un valor de mercado. ':'')+'Menor MAPE significa mejor resultado entre los centros comparados con estos mismos datos; no demuestra que sea el mejor indicador para el avalúo. La elección para el sujeto requiere completar la revisión y su sustento.'
    };
}
