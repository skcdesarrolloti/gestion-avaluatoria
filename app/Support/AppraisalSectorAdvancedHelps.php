<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalSectorAdvancedHelps
{
    public static function all(): array
    {
        return [
            'microsector' => 'Escribe la zona precisa que estás analizando dentro del barrio. Ejemplo: residencial y servicios aeroportuarios.',
            'mapa_barrio_url' => 'Enlace de apoyo para abrir el barrio en mapa. No reemplaza la delimitación oficial ni la visita.',
            'fuente_base_delimitacion' => 'Coloca de dónde sale el límite del barrio: Datos Abiertos, Geoportal, POT/MIDAS, plano oficial, Google Maps provisional o visita de campo.',
            'fuente_base_satelital' => 'Indica la fuente visual usada para mirar el sector desde arriba: Google Maps, Google Earth, MIDAS, geoportal o captura propia.',
            'latitud_centro' => 'Coordenada aproximada del centro del barrio o del microsector. Úsala como referencia, no como lindero jurídico.',
            'longitud_centro' => 'Coordenada aproximada del centro del barrio o del microsector. Debe revisarse con mapa o fuente geográfica.',
            'area_hectareas' => 'Área del barrio o polígono de estudio en hectáreas. Si viene de MIDAS, conserva la fuente y fecha.',
            'perimetro_metros' => 'Perímetro del barrio o polígono de estudio en metros. Si no está oficial, marca pendiente de validación.',
            'norte' => 'Referencia territorial al norte del barrio o microsector: vía, barrio vecino, cuerpo de agua o hito.',
            'sur' => 'Referencia territorial al sur del barrio o microsector: vía, barrio vecino, cuerpo de agua o hito.',
            'este' => 'Referencia territorial al este del barrio o microsector: vía, barrio vecino, cuerpo de agua o hito.',
            'oeste' => 'Referencia territorial al oeste del barrio o microsector: vía, barrio vecino, cuerpo de agua o hito.',
            'observacion_localizacion' => 'Redacta en lenguaje de informe: dónde queda el barrio, qué lo rodea y qué falta confirmar en campo o cartografía.',
            'mapa_delimitacion_url' => 'Pega el enlace al mapa que permita abrir o revisar el límite usado para la caracterización.',
            'imagen_satelital_url' => 'Pega el enlace o describe la imagen satelital que soporta la lectura espacial.',
            'medicion_source' => 'Indica quién da el área o perímetro. Si no hay dato oficial, escribe medición manual pendiente de validar.',
            'fuente_servicios' => 'Indica si la información viene de observación de campo, empresa de servicios, ficha interna o consulta pública.',
            'acueducto_detalle' => 'Registra empresa, cobertura o soporte usado para confirmar acueducto. En Cartagena suele verificarse con Aguas de Cartagena.',
            'alcantarillado_detalle' => 'Registra empresa, cobertura o soporte usado para confirmar alcantarillado. Valida con fuente oficial o recibo.',
            'energia_detalle' => 'Registra empresa o soporte de energía. En Cartagena suele verificarse con Afinia.',
            'gas_detalle' => 'Registra empresa o soporte de gas natural. En Cartagena suele verificarse con Surtigas.',
            'aseo_detalle' => 'Registra empresa de aseo, frecuencia, horario o microrruta. Verifica por barrio con Pacaribe/Veolia y visita.',
            'descripcion_general_sector' => 'Describe el carácter del barrio: residencial, comercial, mixto, turístico, institucional o de transición.',
            'fuente_normativa' => 'Indica la fuente normativa consultada: POT, MIDAS, Secretaría de Planeación, norma especial o pendiente de consulta.',
            'midas_lectura_manual' => 'Resume lo que encontraste en MIDAS/POT: tratamiento, uso permitido, restricciones o pendientes.',
            'vias_detalle' => 'Describe accesos, estado vial, señalización, jerarquía de vías y facilidad de llegada al inmueble.',
            'comentario_vias_senalizacion' => 'Convierte la revisión vial en un párrafo técnico listo para el informe.',
            'fuente_estratificacion' => 'Indica de dónde sale el estrato: recibo, consulta pública, visita, ficha del inmueble o dato pendiente.',
            'observacion_externalidades' => 'Registra factores externos que suben o bajan valor: parques, comercio, ruido, tráfico, riesgo o deterioro.',
            'soportes_fotograficos_plan' => 'Define qué evidencias vas a subir: mapa, satelital, vías, entorno inmediato, equipamientos y externalidades.',
            'comentario_conclusion_sectorial' => 'Cierra con criterio profesional: cómo el sector incide en el valor y qué debe validar el analista.',
        ];
    }
}
