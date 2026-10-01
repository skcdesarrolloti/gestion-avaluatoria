<?php
declare(strict_types=1);
namespace App\Services;

/** Revisión operativa; no acredita diligenciamiento ni ejecuta cálculos. */
final class Resolution941MethodReview
{
    public static function for(string $method): array
    {
        return match ($method) {
            'renta' => [
                'reference' => 'Arts. 22–26 · anexo 2.2 (pp. 20–21)', 'page' => 20,
                'available' => 'Guía, selección motivada del método y antecedentes económicos. No hay todavía una liquidación completa de capitalización ni FCD.',
                'checks' => [
                    'Contratos y mercado (arts. 22–24): identificar canon, uso, fecha, periodicidad y fuente; contrastar los contratos con arriendos comparables. Excluir ingresos de marcas, negocio y otros intangibles.',
                    'Capitalización directa (art. 23): renta bruta con tasa bruta o renta neta con tasa neta. Identificar deducciones del propietario y sustentar la tasa preferiblemente con cánones y ventas comparables; justificar su construcción cuando no haya observación directa.',
                    'Vivienda urbana (art. 24.e): verificar el máximo legal del canon capitalizable. No extender automáticamente ese tope a oficinas, locales ni otros usos.',
                    'FCD (arts. 25–26): registrar ingresos y gastos por periodo, horizonte, ocupación, vacancia y valor continuo; distinguir tasa de descuento y tasa terminal de capitalización, con fuentes y riesgo.',
                    'Gastos mínimos (art. 26): predial, seguros, mantenimiento/reparaciones menores, administración, servicios cuando apliquen, imprevistos/otros y comisión inmobiliaria. Reconocer los flujos cuando se generen.',
                ],
                'pending' => 'Faltan tablas de contratos/cánones y egresos, soporte y periodicidad de las tasas, flujo por periodo, valor terminal y memoria reproducible del resultado. Los ejemplos del anexo no son tasas del expediente.',
            ],
            'costo' => [
                'reference' => 'Arts. 27–30 · anexo 2.3 (pp. 22–39)', 'page' => 22,
                'available' => 'Existen caracterización, áreas, tipologías y módulo de conservación. Esos insumos no equivalen a un presupuesto ni a la liquidación final del método.',
                'checks' => [
                    'Componentes (art. 27): identificar terreno, construcciones y anexos incluidos en el encargo. VC = (CT − D) + VT; determinar el terreno por un método técnicamente viable y no depreciarlo como construcción.',
                    'Costo a nuevo (art. 28): escoger y justificar reposición o reproducción; documentar cantidades, unidades, especificaciones, precios, fecha, localización y costos directos e indirectos. Evitar partidas duplicadas.',
                    'Información limitada (art. 28, parágrafos): solicitar planos, memorias y cantidades cuando se requieran. Si no existen o son insuficientes, justificar tipologías, modelos, bases de datos o fuentes técnicas verificables. Registrar materiales, edad y conservación de los anexos.',
                    'Vida útil (art. 29 y anexo 2.3): sustentar edad, referencia, conservación y vida remanente. Cuando la edad supere la referencia, documentar la vida útil prolongada como edad más remanente, conforme al anexo; no renovarla automáticamente.',
                    'Condiciones VUP (anexo 2.3.2): puede evaluarse desde el 90% de la vida útil de referencia con justificación técnica. Solo admite estados 2,5 a 4,5; excluye 1,0 a 2,0 y 5,0 y no aplica a BIC. Conservar evidencia de la visita y de las intervenciones.',
                    'Depreciación (art. 30): emplear Ross–Heideck continuo por edad y conservación. Diferenciar coeficiente de conservación, factor y valor monetario descontado; cotejar las expresiones del artículo y del anexo antes de calcular.',
                    'Excepción patrimonial (art. 30, parágrafo): los BIC y los demás bienes allí descritos no se deprecian por edad. Sí se analiza conservación; las intervenciones especializadas pueden sustentar el descuento mediante presupuestos o fuentes idóneas.',
                ],
                'pending' => 'Faltan presupuesto por componente, trazabilidad de precios, cálculo verificable de CT, D y VT, tratamiento de excepciones y conciliación del resultado. El catálogo no reemplaza el análisis técnico ni autoriza descuentos adicionales automáticos.',
            ],
            'residual' => [
                'reference' => 'Arts. 31–34 · anexo 2.4 (p. 40)', 'page' => 40,
                'available' => 'Hay guía y antecedentes del sujeto y de la norma urbana. Aún no existe un modelo financiero completo de proyecto residual.',
                'checks' => [
                    'Factibilidad (art. 31): justificar mayor y mejor uso con viabilidad técnica, jurídica y comercial. El máximo permitido por la norma no demuestra demanda ni posibilidad real de venta.',
                    'Técnica (art. 32): justificar estático o dinámico según calidad y disponibilidad de información. El dinámico requiere etapas, ritmo de ventas y obra, precios constantes o corrientes y tasa sustentada; no basta un crecimiento simplificado.',
                    'Producto y costos (art. 33.1–5): áreas netas, útiles y vendibles, ventas por mercado, urbanismo y mitigación, cargas, construcción directa, indirectos, financiación, gerencia y comercialización. No duplicar indirectos de urbanismo.',
                    'Utilidad y riesgo (art. 33.6–7): análisis financiero sustentado, TIR sectorial, costo de oportunidad y VPN al menos cero; documentar fuentes para cada dato, cronograma y supuestos.',
                    'Resultado (art. 33.8–9 y parágrafo): no sumar nuevamente la construcción al resultado total. Si se requiere desagregar, estimarla por costo y descontarla; analizar por separado la excepción del inmueble desarrollado bajo su mayor y mejor uso.',
                    'Terreno en bruto (art. 34): justificar primero que no procede comparación directa ni residual. Distinguir área útil de índice de ocupación, documentar afectaciones/cesiones/vías y separar ganancia de urbanizar de costos de urbanismo por m² útil.',
                ],
                'pending' => 'Faltan cuadro de áreas/productos, ventas sustentadas, presupuesto sin duplicidades, flujos/cronograma, utilidad y tasa documentadas, memoria del residual y ruta excepcional VTB con unidades verificadas.',
            ],
            default => [
                'reference' => 'Arts. 16–21 · anexo 2.1 (pp. 17–20)', 'page' => 17,
                'available' => 'Captura 8.3 con muestras, fuentes, áreas por régimen, coordenadas y evidencia en Mapas. El análisis de esa misma matriz corresponde a 8.4.',
                'checks' => [
                    'Comparabilidad (arts. 16–19): corroborar uso, ubicación, fecha, precio, áreas y régimen PH/NPH. Separar área publicada de privada verificada y documentar contactos y soportes.',
                    'Negociación (art. 17): distinguir valor pedido, negociado o transado, porcentaje y justificación. La coincidencia de precio y área no confirma identidad de inmuebles.',
                    'Estadística (arts. 20–21): las herramientas del artículo 20 son optativas y complementarias; los límites del CV aplican a adoptar la media. Documentar criterio alternativo, descartes y modelos auditables cuando se empleen.',
                    'Anexo 2.1: no homologar ofertas mediante factores; distinguir los procedimientos expresamente regulados. El número de muestras no demuestra por sí solo suficiencia.',
                ],
                'pending' => 'Faltan negociación desglosada, desagregación monetaria cuando proceda, depuración y cálculos reproducibles en 8.4, justificación del valor e integración del informe.',
            ],
        };
    }
}
