// Portal capture first; preserve every exported field and its import metadata.
const portalFields = [
    'source_name', 'source_url', 'market_city', 'neighborhood', 'project_name',
    'property_type', 'operation', 'price_amount', 'price_unit', 'area_m2', 'area_basis',
    'parking_spaces', 'ph_parking_presence', 'ph_parking_in_price',
    'ph_deposit_presence', 'ph_deposit_count', 'ph_deposit_in_price',
    'contact_name', 'contact_phone', 'admin_fee', 'floor_level', 'consulted_at',
    'listing_code', 'listing_date', 'stratum', 'age_years', 'bedrooms', 'bathrooms',
];
const priceFields = [
    'negotiation_discount', 'negotiated_amount', 'negotiation_percent',
    'unit_area_m2', 'unit_area_label', 'offer_per_m2', 'negotiated_per_m2',
    'unit_price_currency', 'unit_price_status',
];
const technicalFields = ['capture_pending', 'id'];
export function orderExcelColumns(columns) {
    const priority = [...portalFields, ...priceFields];
    const score = key => priority.includes(key) ? priority.indexOf(key)
        : technicalFields.includes(key) ? priority.length + 1 + technicalFields.indexOf(key)
        : priority.length;
    return [...columns].sort((a, b) => score(a.key) - score(b.key));
}
