<?php

namespace InstagramNextAPI\Response\Model;

use InstagramNextAPI\AutoPropertyMapper;

/**
 * FelixShare.
 *
 * @method string getText()
 * @method Item getVideo()
 * @method bool isText()
 * @method bool isVideo()
 * @method $this setText(string $value)
 * @method $this setVideo(Item $value)
 * @method $this unsetText()
 * @method $this unsetVideo()
 */
class FelixShare extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'video' => 'Item',
        'text'  => 'string',
    ];
}
