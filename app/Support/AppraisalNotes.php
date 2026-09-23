<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalNotes
{
    public static function all(): array
    {
        $notes = [
            'tipo_derecho' => [
                'dominio_pleno' => self::note('Derecho completo de propiedad sobre el bien.', 'Cuando el certificado y títulos respaldan propiedad plena.', 'Base civil e inmobiliaria; verificar gravámenes y limitaciones.'),
                'nuda_propiedad' => self::note('Propiedad separada del goce o usufructo.', 'Cuando el uso económico pertenece temporalmente a otro.', 'Se valora el derecho limitado, no el inmueble completo.'),
                'usufructo' => self::note('Derecho de uso y disfrute sobre bien ajeno.', 'Cuando existe usufructuario vigente.', 'Separar duración, rentas, cargas y extinción del derecho.'),
                'posesion' => self::note('Tenencia material con ánimo de señor y dueño.', 'Cuando no hay dominio pleno inscrito o hay controversia.', 'Requiere advertir riesgo, realizabilidad y prueba disponible.'),
                'mejoras' => self::note('Derecho económico sobre obras o inversiones.', 'Cuando la construcción pertenece a quien no domina el suelo.', 'Valorar solo lo reconocible y soportado.'),
                'derecho_fiduciario' => self::note('Derecho derivado de una fiducia o patrimonio autónomo.', 'Proyectos, encargos fiduciarios o derechos económicos.', 'Leer contrato fiduciario antes de concluir valor.'),
                'arrendatario' => self::note('Posición económica de quien ocupa por contrato.', 'Arrendamientos, derechos de uso o ventajas contractuales.', 'Conecta con NIIF 16 si el encargo es contable.'),
                'otro' => self::note('Derecho no encajado en categorías base.', 'Casos especiales o figuras contractuales particulares.', 'Debe documentarse con fuente jurídica específica.'),
            ],
            'tipo_negocio' => [
                'venta' => self::note('El análisis se orienta a precio de transferencia o compraventa.', 'Cuando el encargo busca estimar cuánto podría pagarse por el bien.', 'Usar evidencia de mercado comparable y no mezclar cánones de renta como precio directo.'),
                'arriendo' => self::note('El análisis se orienta a canon o ingreso periódico.', 'Cuando el encargo busca estimar renta, alquiler o explotación económica.', 'Requiere consistencia entre renta, vacancia, gastos y capitalización si se deriva valor.'),
            ],
            'finalidad' => [
                'judicial' => self::note('Dictamen con finalidad probatoria.', 'Juzgados, procesos ejecutivos, pertenencia o controversias.', 'Prima trazabilidad, replicabilidad y soporte documental.'),
                'extrajudicial' => self::note('Informe para uso fuera de proceso judicial.', 'Negociaciones, consultas o acuerdos privados.', 'Dejar claro destinatario y uso permitido.'),
                'interna' => self::note('Análisis para gestión de la entidad solicitante.', 'Control, planeación, auditoría interna o inventario.', 'No se presenta como dictamen para terceros.'),
                'patrimonial' => self::note('Soporta lectura del patrimonio o activos propios.', 'Inventarios, sucesiones, sociedades o decisiones familiares.', 'Separar valor de mercado de valores contables.'),
                'negociacion' => self::note(
                    'Finalidad orientada a soportar una conversación económica entre partes.',
                    'Mesas de negociación, ofertas, contraofertas, renegociación de cánones o análisis de oportunidad.',
                    'El valor técnico no sustituye la decisión libre de las partes ni las condiciones particulares del cierre.',
                    'La finalidad del informe es servir como insumo técnico para negociación; sus conclusiones son referencia objetiva, no precio obligatorio de cierre.'
                ),
                'decision_junta' => self::note('Insumo para órgano directivo o societario.', 'Junta, comité, asamblea o aprobación interna.', 'Debe ser claro, comparable y trazable.'),
                'garantia' => self::note('Soporta respaldo económico de una obligación.', 'Crédito, cupo, fiducia o garantía real.', 'Revisar exigencias del acreedor y realizabilidad.'),
                'remate' => self::note('Apoya precio base o referencia de subasta.', 'Procesos de ejecución, liquidación o venta forzada.', 'Debe evidenciar restricciones y condición de venta.'),
            ],
            'base_valor' => [
                'mercado' => self::note('Precio probable en mercado abierto entre partes informadas.', 'Compraventa, garantía, conciliación o patrimonio.', 'Respaldo NTS S 03, NTS I 01 e IVS cuando aplique.', 'Definición del Valor de Mercado: la cuantía estimada por la que un bien podría intercambiarse en la fecha de valuación, entre un comprador dispuesto a comprar y un vendedor dispuesto a vender, en una transacción libre tras una comercialización adecuada, en la que las partes hayan actuado con información suficiente, de manera prudente y sin coacción.'),
                'razonable' => self::note('Medición contable basada en participantes de mercado.', 'Estados financieros, auditoría, deterioro o revelación.', 'Soporte principal NIIF 13; contrastar con IVS según alcance.', 'Definición del Valor Razonable: precio que sería recibido por vender un activo o pagado por transferir un pasivo en una transacción ordenada entre participantes de mercado en la fecha de medición. En NIIF, la lectura se expresa sobre el activo, su uso, mercado, estado, restricciones y datos observables disponibles.'),
                'depreciable' => self::note('Base para costo, vida útil, deterioro o reposición.', 'Activos construidos, maquinaria o seguros.', 'Conecta con NIC 16, NIC 36, seguros y método de costo.', 'Definición del Valor Depreciable: importe de un activo, o el importe que lo sustituya, que se distribuye sistemáticamente durante su vida útil. Relaciona costo atribuible, depreciación acumulada, deterioro, reposición o consumo de beneficios económicos según el alcance.'),
                'renta' => self::note('Valor derivado de ingresos o cánones esperados.', 'Arriendos, inversión y explotación económica.', 'NTS M 01 e IVS: requiere datos de renta, gastos, vacancia y riesgo.', 'Definición del Valor de Renta: estimación del ingreso periódico atribuible al derecho inmobiliario analizado, conforme a condiciones de mercado, uso permitido, estado del activo, gastos, vacancia, riesgo y demás variables aplicables.'),
                'catastral' => self::note('Valor usado para fines catastrales o administrativos.', 'Catastro, impuestos o consistencia oficial.', 'Marco catastral y administrativo aplicable.', 'Definición del Valor Catastral: valor determinado para fines catastrales, fiscales o administrativos conforme al marco técnico y legal aplicable por la autoridad competente; no reemplaza automáticamente el valor de mercado ni el valor razonable.'),
                'residual' => self::note('Valor estimado a partir de potencial de desarrollo.', 'Suelo, proyectos, renovación o expansión.', 'NTS M 01 e IVS: exige norma urbana, ventas esperadas, costos, tiempos y riesgo.', 'Definición del Valor Residual: estimación que parte del valor esperado del proyecto, desarrollo o aprovechamiento permitido del activo y descuenta costos directos e indirectos, tiempos, utilidad esperada, riesgo, financiación y restricciones normativas verificables.'),
            ],
            'aplica_niif' => [
                'no' => self::note('El encargo no se formula bajo medición contable.', 'Avalúos comerciales, judiciales o internos no financieros.', 'Usar NTS, marco jurídico e IVS según alcance.'),
                'si' => self::note('El encargo requiere lenguaje y soporte contable NIIF sobre el activo.', 'Estados financieros, auditoría, deterioro, valor razonable o revelación.', 'Revisar NIIF 13, NIC 16, NIC 36, NIC 38, NIC 40 o NIIF 16 según el activo y la finalidad.'),
            ],
        ];

        return AppraisalTypeNotes::all() + $notes + AppraisalPropertyNotes::all();
    }

    private static function note(string $what, string $when, string $basis, string $report = ''): array { return ['what' => $what, 'when' => $when, 'basis' => $basis, 'report' => $report]; }
}
