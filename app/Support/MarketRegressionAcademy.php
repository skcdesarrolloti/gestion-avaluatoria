<?php
declare(strict_types=1);
namespace App\Support;

final class MarketRegressionAcademy
{
    public static function topics(): array
    {
        return [
            ['application','1. Del promedio a las características',
                'La media entrega un centro del grupo. La regresión relaciona diferencias de valor con características observadas: área, antigüedad y otros factores pertinentes. Dos oficinas pueden tener distinto valor por m² sin que ninguna esté mal capturada.',
                'Regresión simple (un factor explicativo) y múltiple (varios factores explicativos) comparten esta idea. Esta pantalla opera el modelo múltiple existente. Los factores requieren una explicación del mercado; asociación no demuestra causalidad.',
                'NTS M 01, anexo B informativo, B.3; Resolución 0941, art. 20, parágrafo 2.'],
            ['application','2. Preparar Y y X: qué estamos comparando',
                'Y (variable que queremos explicar) es aquí el valor por m². Oferta de 500 millones y área publicada de 50 m²: Y = 10 millones COP/m². Con descuento registrado de 10 %: Y = 9 millones. Debemos declarar cuál base usamos.',
                'X (variables explicativas) son los factores aplicados. Revisar áreas, régimen, componentes, fechas y fuentes. Oferta no equivale a transacción. En PH (propiedad horizontal), dividir por área publicada no acredita la depuración requerida sobre área privada y componentes. Las filas incompletas quedan pendientes: no se convierten en ceros ni se borran.',
                'Resolución 0941, arts. 19 y 20; IVS 104, 30 y 50, referencia preliminar de estudio.'],
            ['application','3. Categorías: una palabra no se convierte en medida',
                'Antigüedad de 1–8 años → código 1; 9–15 → código 2. El paso representa un cambio de categoría, no un año. Al usarlo linealmente suponemos el mismo cambio estimado por escalón, aunque los intervalos tengan distinta amplitud.',
                'Ordinal (categoría con orden) exige justificar orden y escala. Nominal (categoría sin orden), como nombres de barrios, no debe numerarse como si fueran distancias. Variables indicadoras (columnas 0/1 para categorías) son otro tratamiento: su generación automática todavía no está implementada. Confirmar la codificación no prueba que sea correcta.',
                'NTS M 01, anexo B informativo, B.8–B.11; confirmar edición y aplicabilidad.'],
            ['application','4. La ecuación con plastilina: leer coeficientes',
                'Ejemplo inventado sólo para enseñar: ŷ = 5 + 0,02 × área − 0,30 × código de antigüedad + 0,50 × indicador de ascensor. Con 50 m², código 2 y ascensor 1: 5 + 1 − 0,6 + 0,5 = 5,9 millones COP/m². Estos coeficientes no proceden de tus muestras ni se utilizan en el cálculo.',
                'ŷ (valor estimado); intercepto (constante inicial); coeficiente (cambio asociado a una unidad de un factor manteniendo los demás constantes). En el ejemplo, 0,02 significa 0,02 millones COP/m² por m² adicional; −0,30 corresponde a un escalón del código. El intercepto no representa automáticamente un inmueble real con todo en cero. La pantalla presenta coeficientes en COP/m² y unidades originales de los factores.',
                'NTS M 01, anexo B informativo, B.3 y B.12; NIST, mínimos cuadrados lineales.'],
            ['application','5. Ajustar: cómo busca la ecuación',
                'Observado 10 millones, estimado 9,6: residuo (observado menos estimado) = +0,4 millones. Estimado 10,3: residuo = −0,3. Mínimos cuadrados (ajuste que minimiza la suma de residuos al cuadrado) busca coeficientes que reduzcan conjuntamente esas diferencias.',
                'Se usan todas las filas numéricas completas y sus valores individuales. Se conserva el mínimo del ejercicio: 3 factores, 30 filas completas y 10 filas por factor. No es un umbral legal universal ni garantiza representatividad. Factores constantes o dependencia lineal exacta impiden este cálculo. QR (descomposición matricial para resolver el ajuste) mejora estabilidad numérica; no valida datos ni supuestos.',
                'NIST, mínimos cuadrados lineales; NTS M 01, anexo B informativo, B.4–B.5. Mínimos operativos: criterio del ejercicio.'],
            ['diagnostics','6. R², error y CV: preguntas diferentes',
                'R² = 0,90 indica que el ajuste explica 90 % de la variación observada en esta muestra respecto a su media. No significa 90 % de exactitud del avalúo. Si el CV original es 23,94 %, hacer una regresión no cambia ese porcentaje de los mismos valores originales.',
                'R² (coeficiente de determinación) describe ajuste; R² ajustado considera la cantidad de parámetros. Error residual = √[suma de residuos² / (n − k − 1)], en COP/m²: no es el RMSE (raíz del error cuadrático medio) calculado dividiendo entre n. MAPE (error porcentual absoluto medio) aquí evalúa los mismos datos del ajuste. Ninguno equivale al CV (coeficiente de variación) ni demuestra desempeño en inmuebles nuevos. El 7,5 % urbano del art. 21 corresponde a adoptar la media; no se convierte en requisito de R² o MAPE.',
                'Resolución 0941, arts. 20–21; NTS M 01, anexo B informativo, B.7.'],
            ['diagnostics','7. Gráficos: qué mirar antes de creer la ecuación',
                'Observado frente a estimado: cercanía a la diagonal indica cercanía del ajuste. Residuos frente a estimados: una curva sugiere una relación no recogida; un abanico sugiere distinta dispersión del error. Son pistas para investigar, no veredictos.',
                'Linealidad (relación adecuadamente representada); homocedasticidad (varianza del error aproximadamente constante); independencia (errores sin dependencia relevante). Revisar anuncios duplicados, proximidad espacial y fechas relacionadas. Q-Q (comparación de cuantiles con una distribución normal) e histograma de residuos ayudan a estudiar normalidad de errores para inferencias; no exigen precio normal. Los gráficos actuales no ejecutan pruebas formales de estos supuestos.',
                'NTS M 01, anexo B informativo, B.4 y B.5.1–B.5.4.'],
            ['diagnostics','8. VIF y señales: revisar sin retirar inmuebles',
                'Si dos factores cuentan casi lo mismo, cuesta separar sus asociaciones con el valor. VIF (factor de inflación de la varianza) estudia esa redundancia. Un inmueble extremo en sus factores puede tirar de la ecuación sin tener un precio equivocado.',
                'Colinealidad (dependencia entre factores); apalancamiento h (posición del inmueble en el espacio de factores); Cook (influencia sobre el ajuste); residuo studentizado (residuo relativo a su variabilidad estimada). Se conservan señales exploratorias: |studentizado| > 2, h > 2p/n y Cook > 4/n, p = k + 1. RIC (rango intercuartílico) orienta revisión de extremos de Y. No son mandatos de exclusión: verificar evidencia y conservar motivos antes de corregir o excluir en la preparación.',
                'NTS M 01, anexo B informativo, B.5.5–B.5.6; NIST, revisión de atípicos.'],
            ['diagnostics','9. Validación y sujeto: qué falta para adoptar un valor',
                'Estudiar un examen con las mismas preguntas puede dar una nota alta. Resolver preguntas nuevas muestra otra capacidad. Ajustar y evaluar con los mismos inmuebles mide ajuste interno; validar con evidencia no usada para ajustar estudia capacidad de generalización.',
                'Pendientes: ANOVA (análisis de varianza), pruebas t/F (significancia de coeficientes y del conjunto), valores p (probabilidad bajo una hipótesis estadística), intervalos de coeficientes, validación fuera de muestra y estimación del sujeto con su intervalo. Tampoco hay ruta operativa de regresión simple. Extrapolación (aplicar fuera del ámbito observado) exige revisar rangos, categorías y mercado. La descarga conserva esta ejecución y límites; no adopta valor ni constituye el dictamen final.',
                'Resolución 0941, art. 20, parágrafo 2; NTS M 01, anexo B informativo, B.6 y B.8–B.13; IVS 105, 10, 30, 40 y 50, referencia preliminar.'],
        ];
    }

    public static function reportSupport(): string
    {
        return 'Fuentes y alcance: Resolución IGAC 0941 de 2026, arts. 19, 20 (parágrafo 2: datos, supuestos, límites, métricas y validación profesional) y 21 (CV para adoptar la media, distinto de R² y error del modelo), anexo 2.1. Verificar ámbito del encargo. NTS M 01, edición examinada 12/02/2016, anexo B informativo B.3–B.13: apoyo técnico, no obligación general de regresión; confirmar edición y aplicabilidad. NTS S 03, edición examinada 2009, 7.1.8 y 7.1.10: datos, análisis y razones. IVS 104 (30 y 50), IVS 105 (10, 30, 40 y 50) e IVS 106 (20 y 30), referencia de estudio 2025 mediante traducción española preliminar: cotejo con original oficial inglés pendiente. NIST: apoyo académico. Mínimos 3 factores/30 filas/10 por factor y señales de revisión son criterios del ejercicio, no umbrales legales universales. Error residual = raíz de SSE/(n-k-1). Evaluación interna: sin validación fuera de muestra, pruebas t/F ni intervalos del sujeto. Esta ejecución no certifica conformidad ni adopta un valor.';
    }
}
