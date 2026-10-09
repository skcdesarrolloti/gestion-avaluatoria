import {distribution,quantile} from './analysis-distribution.js';
export function orderCourseEstimators(estimators){return [...estimators].sort((a,b)=>(Number.isFinite(a.mape)?a.mape:Infinity)-(Number.isFinite(b.mape)?b.mape:Infinity));}
export function preferredCourseEstimators(estimators){const valid=orderCourseEstimators(estimators).filter(e=>Number.isFinite(e.mape)&&Number.isFinite(e.value));return valid.filter(e=>e.mape===valid[0]?.mape);}
export function courseSelectionConclusion(estimators,format){const selected=preferredCourseEstimators(estimators);if(!selected.length)return 'No se selecciona un centro de referencia: no hay resultados MAPE estimables.';
    const values=selected.map(e=>e.label+': '+format(e.value)+' COP/m²').join(' · ');return (selected.length===1?'Centro seleccionado para este análisis: ':'Centros con igual menor MAPE: ')+values+'. MAPE mínimo: '+format(selected[0].mape)+' %. '+(selected.length===1?'Se selecciona porque presenta la menor diferencia porcentual absoluta media frente a los valores de esta muestra.':'El empate no permite elegir un único centro mediante este criterio.')+' La selección es descriptiva y requiere continuar el análisis; no constituye el valor adoptado del avalúo.';}
// Regularized incomplete beta and inverse Student t, without a normal approximation.
function logGamma(z){const c=[676.5203681218851,-1259.1392167224028,771.32342877765313,-176.61502916214059,12.507343278686905,-.13857109526572012,9.984369578019572e-6,1.5056327351493116e-7];
    if(z<.5)return Math.log(Math.PI)-Math.log(Math.sin(Math.PI*z))-logGamma(1-z);z--;let x=.99999999999980993;c.forEach((v,i)=>x+=v/(z+i+1));const t=z+c.length-.5;return .5*Math.log(2*Math.PI)+(z+.5)*Math.log(t)-t+Math.log(x);}
function betaFraction(a,b,x){const tiny=1e-30,q=a+b;let c=1,d=1-q*x/(a+1);if(Math.abs(d)<tiny)d=tiny;d=1/d;let h=d;
    for(let m=1;m<=200;m++){let aa=m*(b-m)*x/((a+2*m-1)*(a+2*m));
        d=1+aa*d;if(Math.abs(d)<tiny)d=tiny;c=1+aa/c;if(Math.abs(c)<tiny)c=tiny;d=1/d;h*=d*c;
        aa=-(a+m)*(q+m)*x/((a+2*m)*(a+2*m+1));d=1+aa*d;if(Math.abs(d)<tiny)d=tiny;c=1+aa/c;if(Math.abs(c)<tiny)c=tiny;d=1/d;const delta=d*c;h*=delta;if(Math.abs(delta-1)<3e-14)break;
    }return h;}
function betaI(x,a,b){if(x<=0)return 0;if(x>=1)return 1;const bt=Math.exp(logGamma(a+b)-logGamma(a)-logGamma(b)+a*Math.log(x)+b*Math.log1p(-x));
    return x<(a+1)/(a+b+2)?bt*betaFraction(a,b,x)/a:1-bt*betaFraction(b,a,1-x)/b;}
export function studentCDF(t,df){const tail=.5*betaI(df/(df+t*t),df/2,.5);return t<0?tail:1-tail;}
export function studentQuantile(p,df){if(!(p>0&&p<1&&df>0))return null;if(p===.5)return 0;if(p<.5)return -studentQuantile(1-p,df);let lo=0,hi=1;while(studentCDF(hi,df)<p)hi*=2;
    for(let i=0;i<80;i++){const mid=(lo+hi)/2;if(studentCDF(mid,df)<p)lo=mid;else hi=mid;}return (lo+hi)/2;}
export function courseSummary(rows,confidence=.95){const values=rows.map(r=>r.y),s=distribution(values),sorted=[...values].sort((a,b)=>a-b),n=values.length;
    const counts=new Map();values.forEach(v=>counts.set(v,(counts.get(v)||0)+1));const most=Math.max(...counts.values());
    const mad=quantile(values.map(v=>Math.abs(v-s.median)).sort((a,b)=>a-b),.5),t=n>1?studentQuantile((1+confidence)/2,n-1):null,se=n>1?s.sd/Math.sqrt(n):null,margin=t!==null?t*se:null;
    const classes=frequencyClasses(rows),considerations=rows.map(r=>({...r,modifiedZ:mad>0?.6744897501960817*(r.y-s.median)/mad:null,
        flags:[...(r.y<s.lower||r.y>s.upper?['Fuera de 1,5 RIC']:[]),...(mad>0&&Math.abs(.6744897501960817*(r.y-s.median)/mad)>3.5?['Distancia robusta > 3,5']:[])]}));
    return {...s,min:sorted[0],max:sorted[n-1],range:sorted[n-1]-sorted[0],variance:s.sd===null?null:s.sd**2,modes:most>1?[...counts].filter(v=>v[1]===most).map(v=>v[0]):[],mad,
        confidence,t,se,margin,lowerCI:margin===null?null:s.mean-margin,upperCI:margin===null?null:s.mean+margin,relativeMargin:s.mean!==0&&margin!==null?100*margin/Math.abs(s.mean):null,classes,considerations};}
export function frequencyClasses(rows){const values=rows.map(r=>r.y),min=Math.min(...values),max=Math.max(...values),k=min===max?1:Math.ceil(Math.log2(rows.length)+1),width=(max-min)/k;
    return Array.from({length:k},(_,i)=>{const lower=min+i*width,upper=i===k-1?max:min+(i+1)*width,members=rows.filter(r=>r.y>=lower&&(i===k-1?r.y<=upper:r.y<upper));
        const stats=members.length?distribution(members.map(r=>r.y)):null;
        return {n:i+1,lower,upper,mark:(lower+upper)/2,closed:i===k-1,members,frequency:members.length,percent:100*members.length/rows.length,cumulative:rows.filter(r=>i===k-1?r.y<=upper:r.y<upper).length,mean:stats?.mean??null,cv:stats?.cv??null};});}
export async function courseBootstrap(values,confidence=.95,progress=()=>{},repetitions=10000,seed=2026){let state=seed>>>0;const random=()=>{state+=0x6D2B79F5;let t=state;t=Math.imul(t^t>>>15,t|1);t^=t+Math.imul(t^t>>>7,t|61);return ((t^t>>>14)>>>0)/4294967296;};
    const means=[],medians=[],cvs=[],n=values.length;
    for(let i=0;i<repetitions;i++){const sample=Array.from({length:n},()=>values[Math.floor(random()*n)]).sort((a,b)=>a-b),mean=sample.reduce((s,v)=>s+v,0)/n,sd=Math.sqrt(sample.reduce((s,v)=>s+(v-mean)**2,0)/(n-1));
        means.push(mean);medians.push(quantile(sample,.5));if(mean!==0)cvs.push(100*sd/Math.abs(mean));
        if((i+1)%250===0){progress(i+1);await new Promise(resolve=>setTimeout(resolve,0));}}
    const interval=a=>{a.sort((a,b)=>a-b);return a.length?[quantile(a,(1-confidence)/2),quantile(a,(1+confidence)/2)]:[null,null];};
    return {seed,repetitions,confidence,mean:interval(means),median:interval(medians),cv:interval(cvs),cvAvailable:cvs.length};}
