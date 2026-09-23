<?php
// Campos del numeral 1.2. Hereda $field y $selected desde chapter-zero.php.
$documentOptions = \App\Support\AppraisalCatalog::sourceDocumentOptions();
$selectedDocumentKeys = json_decode((string) $field('source_documents_json'), true);
if (!is_array($selectedDocumentKeys)) $selectedDocumentKeys = [];
$hasDocument = static fn (string $key): string => in_array($key, $selectedDocumentKeys, true) ? 'checked' : '';
$supportText = static fn (string $text): string => '<span class="mt-1 block text-xs leading-5 text-slate-500">Soporte: ' . e($text) . '</span>';
$sectionTitle = static fn (string $text): string => '<h3 class="md:col-span-2 mt-2 rounded-xl bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800">' . e($text) . '</h3>';
?>
<?= $sectionTitle('1.1 Solicitud del avalúo') ?>
<label class="label">Solicitante
    <input class="input" name="requester_name" maxlength="160" value="<?= e($field('requester_name')) ?>"
        placeholder="Ej. Dra. Martha Espinosa B.">
    <?= $supportText('NTS S 03: identificación de quien formula o canaliza la solicitud del avalúo.') ?>
</label>
<label class="label">Calidad/cargo en que solicita
    <input class="input" name="requester_capacity" maxlength="220" value="<?= e($field('requester_capacity')) ?>"
        placeholder="Ej. directora Oficina Cartagena; propietario; representante legal; interesado en compra">
    <span class="mt-1 block text-xs leading-5 text-slate-500">Si es empresa, registra cargo y dependencia. Si es particular, registra la calidad o interés con que pide el avalúo.</span>
    <?= $supportText('NTS S 03: permite precisar representación, cargo, interés o calidad con que se solicita la valuación.') ?>
</label>

<?= $sectionTitle('1.2 Razón social e identificación del solicitante') ?>
<label class="label">Cliente / contratante
    <input class="input" name="client_name" maxlength="160" value="<?= e($field('client_name')) ?>"
        placeholder="Persona o entidad que contrata el encargo">
    <?= $supportText('NTS S 03 y NTS I 01: identificación del solicitante o contratante del informe.') ?>
</label>
<label class="label">Identificación del solicitante
    <input class="input" name="requester_identification" maxlength="80" value="<?= e($field('requester_identification')) ?>"
        placeholder="NIT, cédula o identificación reportada">
    <?= $supportText('NTS I 01: identificación suficiente del solicitante y trazabilidad del encargo.') ?>
</label>

<?= $sectionTitle('1.3 Encargo valuatorio') ?>
<label class="label md:col-span-2">Descripción del encargo valuatorio
    <textarea class="input" name="assignment_description" rows="4" maxlength="2000"
        placeholder="Ej. realizar el avalúo para establecer el valor de mercado de un inmueble destinado a uso comercial."><?= e($field('assignment_description')) ?></textarea>
    <?= $supportText('NTS S 03: define qué encomienda recibió el valuador antes de precisar alcance y limitaciones.') ?>
</label>
<label class="label md:col-span-2">Nombre del avalúo
    <input class="input" name="titulo" maxlength="160" value="<?= e($field('titulo')) ?>"
        placeholder="Ej. Informe de valuación de un inmueble urbano oficina Chambacú">
    <?= $supportText('Control interno del expediente; ayuda a identificar el encargo y no reemplaza la identificación registral.') ?>
</label>

<?= $sectionTitle('1.3.1 Identificación del activo objeto del estudio') ?>
<label class="label md:col-span-2">Propietario del inmueble
    <input class="input" name="property_owner_name" maxlength="160" value="<?= e($field('property_owner_name')) ?>"
        placeholder="Nombre del propietario, si se conoce">
    <?= $supportText('NTS S 03: identifica los derechos o intereses relacionados con el activo objeto de estudio.') ?>
</label>

<?= $sectionTitle('1.3.2 Derechos o intereses objeto de valuación') ?>
<?php $name = 'tipo_derecho'; require BASE_PATH . '/app/Views/appraisals/chapter-zero-select-field.php'; ?>

<?= $sectionTitle('1.3.3 Uso que se pretende dar a la valuación') ?>
<?php $name = 'finalidad'; require BASE_PATH . '/app/Views/appraisals/chapter-zero-select-field.php'; ?>
<label class="label md:col-span-2">Uso previsto del informe
    <textarea class="input" name="intended_use" rows="5" maxlength="1200"
        placeholder="Ej. estimar el valor de mercado para actualizar libros contables, soportar negociación, garantía o decisión interna."><?= e($field('intended_use')) ?></textarea>
    <?= $supportText('NTS S 03 y NTS I 01: uso previsto de la valuación y propósito comunicado al lector.') ?>
</label>

<?= $sectionTitle('1.3.4 Definición de la base o tipo de valor') ?>
<?php $name = 'base_valor'; require BASE_PATH . '/app/Views/appraisals/chapter-zero-select-field.php'; ?>

<?= $sectionTitle('1.3.5 Fecha de aplicación de la estimación del valor') ?>
<label class="label">Fecha de valor
    <input class="input" type="date" name="value_date" value="<?= e($field('value_date')) ?>">
    <?= $supportText('NTS S 03 e IVS: fecha efectiva de valoración o fecha de aplicación del valor.') ?>
</label>

<?= $sectionTitle('1.3.6 Ámbito o amplitud de la valuación y del informe') ?>
<label class="label md:col-span-2">Alcance o amplitud del informe
    <textarea class="input" name="assignment_scope" rows="4" maxlength="2000"
        placeholder="Define qué cubre el avalúo, fuentes consultadas, unidad de análisis y uso autorizado."><?= e($field('assignment_scope')) ?></textarea>
    <?= $supportText('NTS S 03: ámbito de uso, cobertura técnica e información considerada en el informe.') ?>
</label>

<?= $sectionTitle('1.3.7 Condiciones contingentes o restrictivas del valor') ?>
<label class="label md:col-span-2">Limitaciones y salvedades
    <textarea class="input" name="assignment_limitations" rows="4" maxlength="2000"
        placeholder="Registra documentos faltantes, restricciones de acceso o información no verificada."><?= e($field('assignment_limitations')) ?></textarea>
    <?= $supportText('NTS S 03 y NTS S 04: condiciones contingentes o restrictivas y salvedades del valor.') ?>
</label>
<label class="label md:col-span-2">Hipótesis de trabajo
    <textarea class="input" name="assignment_hypotheses" rows="3" maxlength="2000"
        placeholder="Supuestos razonables usados para producir el informe, si aplican."><?= e($field('assignment_hypotheses')) ?></textarea>
    <?= $supportText('NTS S 03 e IVS: hipótesis, supuestos especiales o condiciones de trabajo comunicadas al usuario.') ?>
</label>

<?= $sectionTitle('1.4 Localización y dirección del inmueble') ?>
<label class="label md:col-span-2">Localización para el entregable
    <textarea class="input" name="location_description" rows="5" maxlength="2000"
        placeholder="Describe dirección, edificio, piso, entorno inmediato, referencias urbanas y ubicación de unidades o anexos."><?= e($field('location_description')) ?></textarea>
    <?= $supportText('Decreto 1420 de 1998: localización, dirección y contexto físico del inmueble objeto de avalúo.') ?>
</label>
<label class="label md:col-span-2">Foto, mapa o soporte visual de localización
    <textarea class="input" name="location_image_reference" rows="3" maxlength="1000"
        placeholder="Ej. usar foto/mapa cargado en 3.7; captura satelital de localización; foto de fachada o acceso principal."><?= e($field('location_image_reference')) ?></textarea>
    <?= $supportText('Referencia editorial para insertar o ubicar la imagen de localización en el entregable; el archivo puede cargarse en el módulo de fotos.') ?>
</label>

<?= $sectionTitle('1.5 Objeto del avalúo') ?>
<div class="md:col-span-2 rounded-2xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950">
    El objeto del avalúo se construye automáticamente con la base o tipo de valor seleccionada, el activo identificado y la finalidad del encargo.
</div>

<?= $sectionTitle('1.6 Destinatario de la valuación') ?>
<label class="label">Destinatario del informe
    <input class="input" name="report_recipient" maxlength="160" value="<?= e($field('report_recipient')) ?>"
        placeholder="A quien va dirigido el entregable">
    <?= $supportText('NTS S 03: destinatario y uso autorizado del informe.') ?>
</label>

<?= $sectionTitle('1.7 Tipo de avalúo') ?>
<?php $name = 'tipo'; require BASE_PATH . '/app/Views/appraisals/chapter-zero-select-field.php'; ?>

<?= $sectionTitle('1.8 Tipo de derecho, mueble o inmueble que representa al activo') ?>
<div class="md:col-span-2 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm leading-6 text-slate-700">
    El derecho o interés se captura en el numeral 1.3.2 y la tipología física se toma de la configuración del activo. Esta sección se arma en el entregable con esos dos insumos para evitar doble digitación.
</div>

<?= $sectionTitle('1.9 Destinación actual del inmueble') ?>
<?php $name = 'destinacion'; require BASE_PATH . '/app/Views/appraisals/chapter-zero-select-field.php'; ?>

<?= $sectionTitle('1.10 Fechas') ?>
<label class="label">Fecha de solicitud
    <input class="input" type="date" name="request_date" value="<?= e($field('request_date')) ?>">
    <?= $supportText('NTS S 03: fecha en que se recibió o formalizó el encargo.') ?>
</label>
<label class="label">Fecha de visita
    <input class="input" type="date" name="visit_date" value="<?= e($field('visit_date')) ?>">
    <?= $supportText('NTS I 01: fecha de visita o verificación del bien, cuando aplique.') ?>
</label>
<label class="label">Fecha del informe
    <input class="input" type="date" name="report_date" value="<?= e($field('report_date')) ?>">
    <?= $supportText('NTS S 03: fecha de emisión del informe.') ?>
</label>

<?= $sectionTitle('1.11 Documentos aportados o insumos') ?>
<div class="md:col-span-2 rounded-2xl border border-slate-200 bg-white p-4">
    <p class="text-sm font-semibold text-slate-800">Checklist documental</p>
    <p class="mt-1 text-xs leading-5 text-slate-500">Marca el documento recibido. La ampliación técnica se desarrolla en el capítulo correspondiente.</p>
    <div class="mt-4 grid gap-3 md:grid-cols-2">
        <?php foreach ($documentOptions as $key => $label): ?>
            <label class="flex min-h-11 items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3 text-sm font-medium text-slate-700">
                <input class="mt-1 size-4" type="checkbox" name="source_documents_selected[]" value="<?= e($key) ?>" <?= $hasDocument($key) ?>>
                <span><?= e($label) ?></span>
            </label>
        <?php endforeach; ?>
    </div>
</div>
<label class="label md:col-span-2">Observaciones sobre documentos e insumos
    <textarea class="input" name="source_documents" rows="5" maxlength="3000"
        placeholder="Ej. Escritura Pública No. 259 del 20/02/2017 de la Notaría Quinta de Cartagena; CTL; predial; RUT; fotografías."><?= e($field('source_documents')) ?></textarea>
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
