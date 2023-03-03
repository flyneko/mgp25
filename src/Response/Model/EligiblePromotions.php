<?php

namespace InstagramNextAPI\Response\Model;

use InstagramNextAPI\AutoPropertyMapper;

/**
 * EligiblePromotions.
 *
 * @method Edges[] getEdges()
 * @method bool isEdges()
 * @method $this setEdges(Edges[] $value)
 * @method $this unsetEdges()
 */
class EligiblePromotions extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'edges'   => 'Edges[]',
    ];
}
