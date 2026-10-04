export function validResearchScale(factor, policy) {
    if (!policy?.policy_kind) return factor.scale_valid !== false;
    return factor.kind === policy.policy_kind && (!(factor.kind === 'ordinal' || policy.policy_fixed) || factor.categories === policy.policy_categories);
}
