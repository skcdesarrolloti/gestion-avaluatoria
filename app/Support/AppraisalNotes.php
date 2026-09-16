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
                'mercado' => self::note('Precio probable en mercado abierto entre partes informadas.', 'Compraventa, garantía, conciliación o patrimonio.', 'Respaldo NTS/IGAC e IVS cuando aplique.'),
                'razonable' => self::note('Medición contable basada en participantes de mercado.', 'Estados financieros, auditoría o revelación.', 'Soporte principal NIIF 13.'),
                'depreciable' => self::note('Base para costo, vida útil, deterioro o reposición.', 'Activos construidos, maquinaria o seguros.', 'Conecta con NIC 16, NIC 36 y método de costo.'),
                'renta' => self::note('Valor derivado de ingresos o cánones esperados.', 'Arriendos, inversión y explotación económica.', 'No mezclar rentas del negocio con renta del inmueble.'),
                'catastral' => self::note('Valor usado para fines catastrales o administrativos.', 'Catastro, impuestos o consistencia oficial.', 'No reemplaza automáticamente el valor comercial.'),
                'residual' => self::note('Valor estimado a partir de potencial de desarrollo.', 'Suelo, proyectos, renovación o expansión.', 'Debe soportar usos, costos, tiempos y norma urbana.'),
            ],
            'aplica_niif' => [
                'no' => self::note('El encargo no se formula bajo medición contable.', 'Avalúos comerciales, judiciales o internos no financieros.', 'Usar NTS, marco jurídico e IVS según alcance.'),
                'si' => self::note('El encargo requiere lenguaje y soporte contable NIIF.', 'Estados financieros, auditoría, deterioro o valor razonable.', 'Revisar NIIF 13, NIC 16, NIC 36, NIC 38, NIC 40 o NIIF 16.'),
            ],
        ];

        return AppraisalTypeNotes::all() + $notes + AppraisalPropertyNotes::all();
    }

    private static function note(string $what, string $when, string $basis, string $report = ''): array { return ['what' => $what, 'when' => $when, 'basis' => $basis, 'report' => $report]; }
}
