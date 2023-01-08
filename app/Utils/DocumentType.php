<?php

namespace App\Utils;

class DocumentType
{
    const GUARANTOR_ID_CARD =  [
        'key' => 'guarantor_id_card',
        'name' => 'Guarantor ID Card'
    ];

    const GOODS_IN_TRANSIT_INSURANCE = [
        'key' => 'goods_in_transit_insurance',
        'name' => 'Goods In Transit Insurance'
    ];
    const FIDELITY_INSURANCE = [
        'key' => 'fidelity_insurance',
        'name' => 'Fidelity Insurance'
    ];

    const CAC_DOCUMENT = [
        'key' => 'cac_document',
        'name' => 'CAC Document'
    ];
}
