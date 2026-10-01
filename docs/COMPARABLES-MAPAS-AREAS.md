# Captura 8.3: ubicación, evidencia y áreas por régimen

La matriz sigue siendo una colección de muestras, con los mismos IDs y fotos.
«4. Mapas y evidencia» presenta las coordenadas, precisión, fuente, consulta,
referencia de evidencia y corroboración de esas mismas filas. No requiere borrar
ni reimportar. Los soportes de imagen usan el panel existente «Fotos y soporte».
La distribución de puntos es esquemática, sin cartografía ni escala de distancias;
cada punto ofrece apertura externa de sus coordenadas en Google Maps.

En Matriz → Campos a revisar → Áreas y componentes se activa la vista de fichas:
- No PH: terreno, construcción, descripción/áreas de anexos y cultivos si aplican.
- PH: privada construida, privada libre y detalle de garajes/depósitos/unidades.
- Condominio PH con características NPH: captura terreno/construcción según art. 19.2.c.
- Por verificar: no se presume régimen ni se convierte el área publicada en privada.

El área publicada permanece independiente. Cambiar de régimen oculta campos,
pero no elimina sus valores. La academia plegable explica la separación captura
8.3 / análisis 8.4 e incluye textos completos de arts. 18 y 19 ya verificados.
No se implementan aquí desagregación monetaria, negociación, estadísticos ni
adopción de valor; continúan pendientes para 8.4. No certifica cumplimiento total.

## Actualización y persistencia

Aplicar `202610010003_comparable_capture_details.php` mediante
`php bin/console.php migrate`, o AUTO_MIGRATE en primer acceso autenticado.
Agrega MEDIUMTEXT nullable `capture_details`, sin modificar coordenadas previas,
filas ni fotografías. Entrada HTTP admite solo campos permitidos; áreas sin
separadores de miles, hasta cuatro decimales; textos hasta 1600 caracteres.
Repositorio conserva campos nuevos omitidos por clientes anteriores dentro
de la transacción/versionado existente. Un vacío explícito sí borra ese campo.

## Verificación

- PHP: 398 verificaciones; JavaScript: 103 pruebas; lint de archivos cambiados.
- Build y tamaño: 57.0 KB gzip, bajo 80 KB.
- BD desechable ga_test_app/ga_test_auth, GA_TEST_PORT=33329: 44 verificaciones,
  migración repetida y conservación; cinco comprobaciones adicionales del recorrido
  entrada HTTP/repositorio tras corregir la lista de campos admitidos.
- Navegador local: cambio PH/no PH conserva áreas; coordenadas y fuente recuperadas
  al recargar; fotos accesibles desde Mapas; revisión visual de escritorio y DOM
  móvil (390 px, sin desbordamiento). La captura de imagen móvil agotó el tiempo
  de la herramienta; no se atribuye validación visual a esa pantalla.

No se escribió en la base de producción. Actualizar hosting es un paso separado.
