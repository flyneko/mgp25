<?php

namespace InstagramNextAPI\Response\Model;

use InstagramNextAPI\AutoPropertyMapper;

/**
 * Param.
 *
 * @method mixed getName()
 * @method mixed getValue()
 * @method bool isName()
 * @method bool isValue()
 * @method $this setName(mixed $value)
 * @method $this setValue(mixed $value)
 * @method $this unsetName()
 * @method $this unsetValue()
 */
class Param extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'name'  => '',
        'value' => '',
    ];
}
