# Apartado 3: academia de regresión

Se amplía la explicación del modelo existente, sin cambiar selección, captura,
codificación, fórmula, mínimos operativos ni persistencia. Academia en acordeones:
1–5 para preparación y cálculo; 6–9 para gráficos y diagnósticos. Ejemplo de
ecuación inventado y rotulado; no se añade a la matriz. Términos con explicación
española. La descarga HTML agrega fuentes y alcance como texto escapado.

## Fuentes examinadas y límites

- Resolución IGAC 0941 de 2026, arts. 19–21 y anexo 2.1. Art. 20 §2 contrastado
  con página PDF 22 del original aportado; consulta oficial:
  https://www.igac.gov.co/node/53595. Los enlaces de artículos reutilizan el lector
  existente. CV para adoptar la media no equivale a R² ni error residual.
- NTS M 01 aportada, edición 12/02/2016, anexo B **informativo**: B.3–B.4 PDF
  35–36; B.5–B.7 PDF 37–38; B.8–B.13 PDF 39 y siguientes. No se certifica edición
  vigente ni obligatoriedad universal. No incorpora tratamiento por factores del
  anexo A. NTS S 03, edición 2009, 7.1.8/7.1.10, documentación.
- Traducción española **preliminar** IVS 2025 aportada: IVS 104, 30/50; IVS 105,
  10/30/40/50, PDF 59–61; IVS 106, 20/30. Cotejo inglés oficial pendiente antes
  de declarar conformidad. NTS I 01 no recibe numerales no verificados.
- NIST, mínimos cuadrados: https://www.itl.nist.gov/div898/handbook/pmd/section1/pmd141.htm.
  Apoyo académico; no obligación jurídica colombiana.

Error residual conserva √[SSE/(n−k−1)]; no se presenta como RMSE con divisor n.
Los mínimos 3 factores/30 filas/10 por factor y señales existentes son criterios
del ejercicio. El modelo no baja automáticamente el CV de los valores originales.

## Pendientes explícitos

Regresión simple operativa; generación automática de indicadoras nominales;
ANOVA, t/F, valores p e intervalos de coeficientes; validación fuera de muestra;
estimación del sujeto e intervalo. No se prometen en esta entrega ni se adopta
valor. La academia permite entenderlos antes de acordar su implementación.

No cambia BD: no requiere migración ni pruebas de persistencia nuevas.
Respaldo de código previo: output/antes-regresion-academia-fd0da31.zip.
