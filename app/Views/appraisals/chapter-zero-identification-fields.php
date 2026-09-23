<?php
// Campos del numeral 1.2. Hereda $field, $name y $catalog desde chapter-zero.php.
$supportText = static fn (string $text): string => '<span class="mt-1 block text-xs leading-5 text-slate-500">Soporte: ' . e($text) . '</span>';
?>
<label class="label md:col-span-2">Nombre del avalúo
    <input class="input" name="titulo" maxlength="160" value="<?= e($field('titulo')) ?>"
        placeholder="Ej. Avalúo comercial Lote Bruselas">
    <?= $supportText('control interno del expediente; ayuda a identificar el encargo y no reemplaza la identificación registral.') ?>
</label>
<label class="label">Cliente / contratante
    <input class="input" name="client_name" maxlength="160" value="<?= e($field('client_name')) ?>"
        placeholder="Persona o entidad que contrata el encargo">
    <?= $supportText('NTS S 03 y NTS I 01: identificación del solicitante o contratante del informe.') ?>
</label>
<label class="label">Solicitante
    <input class="input" name="requester_name" maxlength="160" value="<?= e($field('requester_name')) ?>"
        placeholder="Quien pide o radica el avalúo">
    <?= $supportText('NTS S 03: identificación de quien formula o canaliza la solicitud del avalúo.') ?>
</label>
<label class="label">Identificación del solicitante
    <input class="input" name="requester_identification" maxlength="80" value="<?= e($field('requester_identification')) ?>"
        placeholder="NIT, cédula o identificación reportada">
    <?= $supportText('NTS I 01: identificación suficiente del solicitante y trazabilidad del encargo.') ?>
</label>
<label class="label">Calidad/cargo en que solicita
    <input class="input" name="requester_capacity" maxlength="220" value="<?= e($field('requester_capacity')) ?>"
        placeholder="Ej. directora Oficina Cartagena; propietario; representante legal; interesado en compra">
    <span class="mt-1 block text-xs leading-5 text-slate-500">
        Si es empresa, registra cargo y dependencia. Si es particular, registra la calidad o interés con que pide el avalúo.
    </span>
    <?= $supportText('NTS S 03: permite precisar representación, cargo, interés o calidad con que se solicita la valuación.') ?>
</label>
<label class="label">Propietario del inmueble
    <input class="input" name="property_owner_name" maxlength="160" value="<?= e($field('property_owner_name')) ?>"
        placeholder="Nombre del propietario, si se conoce">
    <?= $supportText('NTS S 03: identifica los derechos o intereses relacionados con el activo objeto de estudio.') ?>
</label>
<label class="label">Destinatario del informe
    <input class="input" name="report_recipient" maxlength="160" value="<?= e($field('report_recipient')) ?>"
        placeholder="A quien va dirigido el entregable">
    <?= $supportText('NTS S 03: destinatario y uso autorizado del informe.') ?>
</label>
<?php $name = 'tipo'; require BASE_PATH . '/app/Views/appraisals/chapter-zero-select-field.php'; ?>
<?php $name = 'tipo_derecho'; require BASE_PATH . '/app/Views/appraisals/chapter-zero-select-field.php'; ?>
<?php $name = 'finalidad'; require BASE_PATH . '/app/Views/appraisals/chapter-zero-select-field.php'; ?>
<label class="label md:col-span-2">Uso previsto del informe
    <input class="input" name="intended_use" maxlength="220" value="<?= e($field('intended_use')) ?>"
        placeholder="Ej. negociación, garantía, conciliación, decisión interna o proceso judicial">
    <?= $supportText('NTS S 03 y NTS I 01: uso previsto de la valuación y propósito comunicado al lector.') ?>
</label>
<label class="label">Fecha de solicitud
    <input class="input" type="date" name="request_date" value="<?= e($field('request_date')) ?>">
    <span class="mt-1 block text-xs leading-5 text-slate-500">Fecha en que se recibió o formalizó el encargo.</span>
    <?= $supportText('NTS S 03: fechas del encargo y trazabilidad temporal del informe.') ?>
</label>
<label class="label">Fecha de visita
    <input class="input" type="date" name="visit_date" value="<?= e($field('visit_date')) ?>">
    <?= $supportText('NTS I 01: fecha de visita o verificación del bien, cuando aplique.') ?>
</label>
<label class="label">Fecha de valor
    <input class="input" type="date" name="value_date" value="<?= e($field('value_date')) ?>">
    <?= $supportText('NTS S 03 e IVS: fecha efectiva de valoración o fecha de aplicación del valor.') ?>
</label>
<label class="label">Fecha del informe
    <input class="input" type="date" name="report_date" value="<?= e($field('report_date')) ?>">
    <?= $supportText('NTS S 03: fecha de emisión del informe.') ?>
</label>
<label class="label md:col-span-2">Descripción del encargo valuatorio
    <textarea class="input" name="assignment_description" rows="3" maxlength="2000"
        placeholder="Ej. realizar el avalúo para establecer el valor de mercado de un inmueble destinado a uso comercial."><?= e($field('assignment_description')) ?></textarea>
    <?= $supportText('NTS S 03: define qué encomienda recibió el valuador antes de precisar alcance y limitaciones.') ?>
</label>
<label class="label md:col-span-2">Alcance o amplitud del informe
    <textarea class="input" name="assignment_scope" rows="3" maxlength="2000"
        placeholder="Define qué cubre el avalúo, fuentes consultadas y unidad de análisis."><?= e($field('assignment_scope')) ?></textarea>
    <?= $supportText('NTS S 03: ámbito de uso, cobertura técnica e información considerada en el informe.') ?>
</label>
<label class="label md:col-span-2">Limitaciones y salvedades
    <textarea class="input" name="assignment_limitations" rows="3" maxlength="2000"
        placeholder="Registra documentos faltantes, restricciones de acceso o información no verificada."><?= e($field('assignment_limitations')) ?></textarea>
    <?= $supportText('NTS S 03 y NTS S 04: condiciones contingentes o restrictivas y salvedades del valor.') ?>
</label>
<label class="label md:col-span-2">Hipótesis de trabajo
    <textarea class="input" name="assignment_hypotheses" rows="3" maxlength="2000"
        placeholder="Supuestos razonables usados para producir el informe, si aplican."><?= e($field('assignment_hypotheses')) ?></textarea>
    <?= $supportText('NTS S 03 e IVS: hipótesis, supuestos especiales o condiciones de trabajo comunicadas al usuario.') ?>
</label>
<label class="label md:col-span-2">Documentos aportados o insumos
    <textarea class="input" name="source_documents" rows="4" maxlength="3000"
        placeholder="Ej. Escritura pública, certificado de tradición, predial, RUT, reglamento PH, fotografías o soportes del encargo."><?= e($field('source_documents')) ?></textarea>
    <?= $supportText('NTS I 01: documentos, insumos e información examinada para soportar el informe.') ?>
</label>
<label class="label md:col-span-2">Texto adicional para memoria descriptiva
    <textarea class="input" name="assignment_report_text" rows="4" maxlength="5000"
        placeholder="Ajustes narrativos del capítulo 1 que no estén cubiertos por los campos anteriores."><?= e($field('assignment_report_text')) ?></textarea>
    <?= $supportText('Campo editorial interno: permite complementar el capítulo sin modificar datos estructurados.') ?>
</label>
<label class="label md:col-span-2">Observaciones generales
    <textarea class="input" name="observaciones" rows="4" maxlength="4000"
        placeholder="Notas generales del encargo que deban quedar disponibles para el informe."><?= e($field('observaciones')) ?></textarea>
    <?= $supportText('Trazabilidad interna: observaciones del expediente; pasan al informe solo si el analista lo decide.') ?>
</label>
