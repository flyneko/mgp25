<?php

namespace InstagramNextAPI\Response\Model;

use InstagramNextAPI\AutoPropertyMapper;

/**
 * Template.
 *
 * @method string getName()
 * @method mixed getParameters()
 * @method bool isName()
 * @method bool isParameters()
 * @method $this setName(string $value)
 * @method $this setParameters(mixed $value)
 * @method $this unsetName()
 * @method $this unsetParameters()
 */
class Template extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'name'       => 'string',
        'parameters' => '',
    ];
}
