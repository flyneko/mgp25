<?php

namespace InstagramNextAPI\Response\Model;

use InstagramNextAPI\AutoPropertyMapper;

/**
 * QPExtraInfo.
 *
 * @method string getExtraInfo()
 * @method int getSurface()
 * @method bool isExtraInfo()
 * @method bool isSurface()
 * @method $this setExtraInfo(string $value)
 * @method $this setSurface(int $value)
 * @method $this unsetExtraInfo()
 * @method $this unsetSurface()
 */
class QPExtraInfo extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'surface'                 => 'int',
        'extra_info'              => 'string',
    ];
}
