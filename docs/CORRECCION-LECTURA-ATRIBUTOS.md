# Lectura de baños y atributos · 2026-10-05

El listado puede copiar sus indicadores como `2 Baños395 m²`. El lector anterior
priorizaba el número posterior a «Baños» y registraba el área como conteo. Ahora
reconoce la cantidad delante de la etiqueta y rechaza números con unidades de
área; mantiene cero explícito y deja desconocido sin valor. El mismo criterio
se aplica a baños, habitaciones, parqueaderos y depósitos en JavaScript y PHP.

El lector HTML conserva toda la tarjeta delimitada de un inmueble, hasta antes
de otro enlace inmobiliario. No termina al encontrar precio/área en un bloque
interior. En FincaRaíz se detiene en listingCard. Los títulos vienen del encabezado,
no de la descripción; una frase sobre un edificio no se guarda como su nombre.

Se conservan como atributos publicados las menciones explícitas de vista,
acabados, servicios, estado, ascensor, accesos, balcón, recepción, vigilancia,
aire acondicionado, planta eléctrica, piscina, gimnasio y terraza. Las menciones
no asignan códigos de jerarquía ni distinguen por sí solas atributos privados
de copropiedad. La descripción original sigue disponible. Las ausencias no
publicadas permanecen pendientes. El selector admite equivalencia exacta
entre etiquetas como «Sí» y su valor existente `si`; no aproxima clasificaciones
de acabados ni valores que el catálogo no admite.

Referencia pública revisada:
[listado FincaRaíz Cartagena](https://www.fincaraiz.com.co/venta/oficinas/cartagena/bolivar)
y [oficina 193978243](https://www.fincaraiz.com.co/oficina-en-venta-en-manga-cartagena/193978243).
La prueba local incorporó dos avisos públicos en el expediente ficticio;
quedaron seis anuncios/cuatro inmuebles y los cuatro anuncios anteriores intactos.
La relectura complementó el ascensor sin crear filas nuevas; baños y atributos
publicados llegaron a la tabla, y el servidor confirmó el autoguardado.
No se editaron expedientes reales.

Los valores existentes se preservan: relectura llena vacíos y registra diferencias
para corregirlas en la ficha, sin reemplazar automáticamente un dato diligenciado.
Actualizar el programa no reescribe muestras; volver a pegar usa el lector nuevo.

Validación: 1420 PHP, 168 JavaScript, 212 BD local desechable en puerto 3395,
lint, build y 75,2 KB gzip. Navegador de escritorio y ancho móvil sin desbordamiento;
dos tarjetas señaladas muestran dos baños y cuatro parqueaderos cada una,
con el ascensor y atributos descritos conservados en la tabla.
