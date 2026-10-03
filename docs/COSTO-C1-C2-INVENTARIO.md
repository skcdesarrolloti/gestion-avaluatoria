# Costo C1 y C2 · inventario de InversKC · 2026-10-02

## Entrega implementada
C1 consulta por unidad descripción, cantidades/unidad, área adoptada/fuente, edad,
vida útil, conservación y alcance C2. Semáforo de presencia: no acredita suficiencia
normativa ni precios. Academia de directos/indirectos, fuentes, SISPAC, ejecución,
retiro y Ross–Heideck; consulta artículos 27–30 de Resolución 941.
C2 guarda objeto (construcción/remanente/retiro), reposición/reproducción/no aplica,
fuente prevista y textos de directos, indirectos, retiro, depreciación e integración.
Consulta explícita C1 sin modificar método de Mercado; C2 sólo persiste alcance para
Costo. Sigue versión global de metodología y propietario/componente activo.
C3–C5 conservan navegación; no se implementa aún presupuesto ni motor Ross.

## Inventario del original, sin modificación ni importación automática
- config/costos_directos_insumos_base.json: 833 insumos.
- config/apu_presupuesto_base.json: 772 APU, 6090 componentes.
- config/presupuesto_capitulos_base.json: 17 capítulos, 76 subcapítulos, 792 partidas.
  772 partidas enlazan APU, 20 no. Normalización de códigos coma/punto sólo para
  diagnóstico: conservar código original y correspondencia explícita al migrar.
- config/sispac_capitulos.json: 12 modelos de 18 capítulos.
- config/sispac_personalizados.json: 10 modelos estimados de 18 capítulos.
  No tratarlos como tabla oficial de publicación. Comercial suma 100,5: validar y
  guardar peso original y ajuste aprobado, sin normalizar silenciosamente.
- models/ProyectoCatalogo.php: 11 rubros indirectos y 8 financieros separados.
- views/proyectos/form.php y models/Proyecto.php: edición de presupuesto, insumos,
  APU, cantidades, desperdicio y consolidación de costos por naturaleza.
- views/valuatoria/definicion_sujeto_comparables.php: academia/observaciones SISPAC.
- config/valuatoria_urbana_schema.json y views/valuatoria/avaluos_urbanos.php:
  Fitto/Corvini histórico; no trasladar motor ni ponderación automática mercado/costo.

## Correcciones necesarias al trasladar
1. Pesos SISPAC: no reducir denominador porque una partida no esté ejecutada.
   19,35% por ejecución 50% aporta 9,675 puntos al modelo completo; no confundir
   participación con área ni con valor recuperable. Vacío no equivale a cero.
2. Mantener edición, región, página, unidad y fecha del precio. Fecha de extracción
   o edición del catálogo no prueba vigencia del precio. Publicado/estimado separados.
3. Indirectos: registrar rubro, cantidad/porcentaje, base, fuente e inclusión previa;
   evitar duplicar AIU. Comercialización/financiación no se incorporan automáticamente.
4. Retiro: eliminar atajo área lote × tarifa fija. Presupuesto por partidas propias,
   protecciones, cargue, transporte y disposición; recuperables sustentados aparte.
   Pérdida física ocurrida y desembolso futuro son conceptos diferentes. No descontar
   dos veces daño ni aplicar depreciación de construcción al presupuesto de retiro.
5. Ross–Heideck continuo con edad/vida útil/conservación y excepciones sustentadas.
   Preservar Fitto histórico, sin ofrecerlo como cálculo nuevo. No fusionar automáticamente
   detrimento por capítulo y depreciación global.
6. Versionar modelos y observaciones por expediente/unidad; evitar respaldo global
   localStorage compartido y archivos maestros sobrescritos sin historial.

## Ruta siguiente
C3: importación controlada de catálogos, simulación de relaciones y faltantes; presupuesto
editable de directos, indirectos y retiro, fuentes/fechas y cantidades por unidad.
C4: revisar ejecución/remanente, aplicar Ross continuo y controlar integración PH/terreno,
recuperables y doble conteo. Casos de prueba manualmente resueltos antes del motor.
C5: memoria con fuente, parámetros, fórmula, cantidades y resultados trazables.
No se importaron expedientes, fotografías, precios como vigentes ni datos reales.

## Conservación y validación
Respaldo de los cinco JSON originales con SHA256 en manifest.json, entregado en
InversKC-catalogos-costo-respaldo.zip. Original InversKC intacto.
PHP 655; JS 131; BD MariaDB desechable 3365: 156. Build y size 66,3 KB gzip.
Prueba Chrome local confirma C2 autoguardado/recarga y semáforo C1 por componente.
Persistencia JSON existente; ninguna migración. Publicación Git no confirma hosting.

Fuentes normativas/editor:
https://camacol.co/sites/default/files/descargables/IGAC-Resolucion-2026-N0000941_20260731_Diario_Oficial-N053573_20260801.pdf
https://www.sispac.com.co/empresa
