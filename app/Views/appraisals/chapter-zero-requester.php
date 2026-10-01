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
<label class="label">Fecha de solicitud
    <input class="input" type="date" name="request_date" value="<?= e($field('request_date')) ?>">
    <span class="mt-1 block text-xs text-slate-500">Fecha en que se recibió la solicitud.</span>
</label>
<label class="label" x-data="{ invalid: false }">Correo del solicitante
    <input class="input" type="email" name="requester_email" maxlength="254" value="<?= e($field('requester_email')) ?>" placeholder="nombre@dominio.com"
        @input="invalid = !$el.validity.valid" :aria-invalid="invalid" aria-describedby="requester-email-error">
    <span id="requester-email-error" class="text-sm text-red-700" x-show="invalid" x-cloak>Escribe un correo válido, por ejemplo nombre@dominio.com.</span>
</label>
<label class="label">Celular del solicitante
    <input class="input" type="tel" name="requester_phone" maxlength="40" value="<?= e($field('requester_phone')) ?>" placeholder="Ej. +57 300 123 4567">
</label>
<label class="label">Municipio del solicitante
    <input class="input" type="text" name="requester_municipality" maxlength="120" value="<?= e($field('requester_municipality')) ?>" placeholder="Ej. Cartagena de Indias, Bolívar">
</label>
