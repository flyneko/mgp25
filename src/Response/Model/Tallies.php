<?php

namespace InstagramNextAPI\Response\Model;

use InstagramNextAPI\AutoPropertyMapper;

/**
 * Tallies.
 *
 * @method int getCount()
 * @method string getText()
 * @method bool isCount()
 * @method bool isText()
 * @method $this setCount(int $value)
 * @method $this setText(string $value)
 * @method $this unsetCount()
 * @method $this unsetText()
 */
class Tallies extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'text'                 => 'string',
        'count'                => 'int',
    ];
}
