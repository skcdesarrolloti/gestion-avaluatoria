<?php
declare(strict_types=1);
namespace App\Support;

final class MarketStatisticsAcademy
{
    public static function steps(): array
    {
        return [
            [
                'title'=>'1. Base del cálculo: qué dato entra al cálculo',
                'example'=>'Ejemplo didáctico: oferta de 500 millones COP, descuento de negociación sustentado del 10 % y área publicada de 50 m². El valor después de negociación es 450 millones; el cociente preliminar es 9 millones COP/m². Si el precio incluye un garaje o el área no es la privada pertinente, todavía falta depurar la base.',
                'meaning'=>'Una observación (dato de un inmueble) debe conservar sus fuentes. Dos anuncios del mismo inmueble no son dos comparables independientes. Un dato faltante tampoco equivale a cero: un descuento vacío queda pendiente, mientras un cero registrado significa que no se descontó negociación.',
                'next'=>'Revisa fuente, fecha, ubicación, precio, área, régimen y componentes incluidos. El cociente de esta pantalla usa el área publicada: verifica su correspondencia con la base requerida antes de adoptarlo.',
                'basis'=>'Arts. 16–17 y 19. En PH (propiedad horizontal), art. 19.2.a–b: área privada y tratamiento de componentes; para los casos del literal c y NPH (sin propiedad horizontal), revisar la ruta correspondiente. La división preliminar no acredita esa depuración.',
                'articles'=>[17,19],
            ],
            [
                'title'=>'2. Bloques y distribución: organizar sin cambiar los valores',
                'example'=>'Piensa en cajas rotuladas por franjas de precio. Frecuencia significa cuántos valores caen en cada caja; acumulada significa cuántos llevamos hasta esa caja. Una franja entre 8 y 10 millones tiene marca de clase (punto medio) de 9 millones, aunque sus inmuebles valgan 8,1 o 9,8 millones.',
                'meaning'=>'El histograma (gráfico de frecuencias por intervalos) permite ver concentración y extremos. La regla de Sturges (criterio para elegir cuántos intervalos dibujar) organiza este gráfico; no define mercados comparables. Los demás cálculos conservan los valores individuales, sin sustituirlos por marcas de clase.',
                'next'=>'Abre los bloques y revisa qué inmuebles contienen. Una franja con CV bajo puede ser sólo consecuencia de haber limitado los precios: sustenta cualquier grupo por características y evidencia del mercado, no por el porcentaje obtenido.',
                'basis'=>'Art. 20.c permite representaciones de forma y distribución; su uso es optativo y complementario. Art. 19 exige comparabilidad. Sturges y las marcas de clase son decisiones estadísticas del ejercicio, no umbrales impuestos por la Resolución.',
                'articles'=>[19,20],
            ],
            [
                'title'=>'3. Tendencia central: qué significa cada centro',
                'example'=>'Con 8, 10 y 12 millones COP/m², la media (suma dividida por cantidad) y la mediana (dato central ordenado) son 10. Si el último valor cambia a 30, la media sube a 16 y la mediana sigue en 10. Eso muestra distinta sensibilidad; no demuestra que 30 sea un error.',
                'meaning'=>'La moda (valor que más se repite) puede no existir si todos los valores son distintos. La media geométrica (promedio mediante logaritmos) requiere valores positivos. La media recortada (promedio que omite extremos sólo para ese cálculo) aquí toma 20 % por cada cola; no borra inmuebles de la matriz.',
                'next'=>'Compara los centros con las características de los inmuebles y sus fuentes. MAPE (error porcentual absoluto medio) compara cada centro con estos mismos valores; un MAPE menor no demuestra capacidad de predecir otros inmuebles.',
                'basis'=>'Art. 20.a contempla medidas de tendencia central. Art. 21 regula la adopción de la media; su parágrafo 1 se refiere expresamente a la elección distinta de la media como valor unitario de terreno. No convertir esa redacción en una aprobación automática de la mediana para cualquier activo.',
                'articles'=>[20,21],
            ],
            [
                'title'=>'4. Dispersión: cuánto se separan los valores',
                'example'=>'Con 8, 10 y 12 millones COP/m², la media es 10. Las diferencias son −2, 0 y 2; sus cuadrados suman 8. La varianza muestral divide 8 por n − 1 = 2 y da 4; su raíz es s = 2 millones COP/m². El CV es 100 × 2 / 10 = 20 %.',
                'meaning'=>'n es la cantidad de valores y s la desviación estándar muestral (separación de los valores respecto a la media). CV (coeficiente de variación) expresa esa dispersión como porcentaje. La varianza tiene unidades al cuadrado; s tiene la misma unidad que los valores. No confundir CV con el margen del intervalo de la media.',
                'next'=>'Si se pretende adoptar la media, contrasta el CV con el ámbito urbano o rural aplicable y verifica el mercado. Si supera el límite, no retires valores sólo para hacerlo bajar: revisa comparabilidad, fuentes y criterios alternativos sustentados conforme al alcance de la norma.',
                'basis'=>'Art. 20.b: dispersión. Art. 21: límites máximos para adoptar la media, 7,50 % urbano y 10,0 % rural; además exige interpretar tipología, mercado, calidad y uniformidad. Un CV bajo por sí solo no valida el avalúo.',
                'articles'=>[20,21],
            ],
            [
                'title'=>'5. Sensibilidad y consideraciones: una señal no es una exclusión',
                'example'=>'Si entre valores de 8, 10 y 12 aparece uno de 30 millones COP/m², revisa qué lo explica: ubicación, área, componentes, calidad, error de captura u otro mercado. El precio alto por sí solo no permite decidir cuál de esas explicaciones es correcta.',
                'meaning'=>'RIC (rango intercuartílico) mide la distancia entre Q1 y Q3 (posiciones del 25 % y 75 % de los datos ordenados). MAD (mediana de las desviaciones absolutas respecto a la mediana) describe dispersión robusta. Asimetría describe hacia qué lado se extiende la distribución; curtosis ayuda a estudiar su forma. Ninguna señal demuestra un error.',
                'next'=>'Abre Revisar inmueble, contrasta la evidencia y deja tu consideración. Los multiplicadores 1,5 del RIC y el umbral 3,5 del diagnóstico robusto son criterios exploratorios del cálculo actual; no son órdenes normativas de eliminar datos.',
                'basis'=>'Art. 20.c–d: forma y técnicas robustas, de uso optativo. Art. 21, parágrafo 1: análisis conjunto cuando aplica y sin inferir el valor por el signo de la asimetría. Las consideraciones de este recorrido sólo se conservan en la ficha abierta y la memoria descargada.',
                'articles'=>[20,21],
            ],
            [
                'title'=>'6. Precisión de la media: grupo, incertidumbre y remuestreo',
                'example'=>'Una media de 10 millones COP/m² y un margen de 0,8 millones producen límites de 9,2 y 10,8 millones. Ese intervalo estima la media del grupo bajo los supuestos del procedimiento; no describe todos los precios ni determina el valor de tu inmueble.',
                'meaning'=>'IC (intervalo de confianza), error estándar (variabilidad estimada de la media) y semiancho (margen a cada lado de ella) son distintos del CV. Bootstrap (remuestreo con reemplazo) repite valores de la muestra en diferentes composiciones; no cambia la media original ni agrega evidencia de mercado.',
                'next'=>'Abre la academia detallada de precisión para ver la bolsa de fichas y entender el botón de 10.000 repeticiones. Revisa representatividad e independencia antes de interpretar ambos procedimientos. Ninguno reemplaza la validación de una regresión.',
                'basis'=>'Art. 20 establece una lista enunciativa de herramientas complementarias y su parágrafo 1 exige pertinencia y suficiencia. No prescribe aquí t de Student, bootstrap ni 10.000 repeticiones. Son complementos estadísticos del módulo, no requisitos textuales ni pruebas de cumplimiento del art. 21.',
                'articles'=>[20,21],
            ],
            [
                'title'=>'7. Conclusión y memoria: explicar cómo llegamos al resultado',
                'example'=>'Una memoria útil permite seguir la cadena: fuente y dato original → revisión de área y componentes → decisión sobre comparabilidad → cálculo → interpretación → criterio y limitación. Decir sólo «el CV pasó» no explica por qué los datos representan al sujeto.',
                'meaning'=>'Trazabilidad significa poder reconstruir el origen y las operaciones. La memoria de este ejercicio conserva la ejecución descargada; no es todavía el informe final ni una declaración de cumplimiento de todas las normas.',
                'next'=>'Documenta la base, fecha, grupo usado, pendientes y razones. Conserva la descarga: las notas y esta conclusión no se autoguardan. Si falta información concluyente o el mercado es heterogéneo, revisa el art. 21, parágrafo 2, antes de concluir.',
                'basis'=>'Art. 21, parágrafos 1–2, según su alcance; anexo 2.1: datos trazables y tablas legibles. NTS S 03 (edición examinada 2009), 7.1.8 y 7.1.10, e IVS 106 (referencia de estudio 2025), 20 y 30: documentar datos, análisis, razones y límites, sin certificar conformidad por esta pantalla.',
                'articles'=>[21],
            ],
        ];
    }

    public static function reportSupport(): string
    {
        return 'Marco de consulta del recorrido: Resolución IGAC 0941 de 2026, arts. 16–19 (datos y comparabilidad), 20 (estadística optativa y complementaria, con pertinencia y suficiencia), 21 (adopción de la media: CV máximo urbano 7,50 % y rural 10,0 %, y alcance específico de sus parágrafos), anexo 2.1 (trazabilidad y depuración, no homogeneización automatizada). Revisar ámbito y régimen del encargo antes de afirmar obligatoriedad. NTS S 03, edición examinada 2009, 7.1.8 y 7.1.10; NTS M 01, edición examinada 2016, sección 8 y anexo B informativo: apoyo técnico cuya edición vigente y aplicabilidad al encargo deben confirmarse. No trasladar automáticamente el tratamiento por factores del anexo A. IVS 104, 30 y 50, e IVS 106, 20 y 30: referencia de estudio 2025 mediante traducción española preliminar aportada; cotejo con edición oficial en inglés pendiente antes de declarar conformidad. Las diapositivas, Sturges, recorte 20 %, señales robustas e intervalos t/bootstrap son apoyo académico o decisiones del ejercicio, no obligaciones textuales de la Resolución. Esta memoria no certifica cumplimiento ni adopta un valor.';
    }
}
