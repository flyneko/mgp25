<?php

namespace InstagramNextAPI\Response\Model;

use InstagramNextAPI\AutoPropertyMapper;

/**
 * DirectSeenItemPayload.
 *
 * @method mixed getCount()
 * @method string getTimestamp()
 * @method bool isCount()
 * @method bool isTimestamp()
 * @method $this setCount(mixed $value)
 * @method $this setTimestamp(string $value)
 * @method $this unsetCount()
 * @method $this unsetTimestamp()
 */
class DirectSeenItemPayload extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'count'     => '',
        'timestamp' => 'string',
    ];
}
