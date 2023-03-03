<?php

namespace InstagramNextAPI\Response\Model;

use InstagramNextAPI\AutoPropertyMapper;

/**
 * Megaphone.
 *
 * @method GenericMegaphone getGenericMegaphone()
 * @method bool isGenericMegaphone()
 * @method $this setGenericMegaphone(GenericMegaphone $value)
 * @method $this unsetGenericMegaphone()
 */
class Megaphone extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'generic_megaphone' => 'GenericMegaphone',
    ];
}
