export function acknowledgeCompositionMethods(form, result) {
    if (!Number.isInteger(result.composition_method_version)) return;
    const version = form.querySelector('[name="composition_method_version"]');
    if (version) version.value = String(result.composition_method_version);
    form.querySelectorAll('[data-original-method]').forEach(input => {
        const key = input.dataset.originalMethod;
        if (Object.hasOwn(result.composition_methods ?? {}, key)) input.value = result.composition_methods[key];
    });
}
