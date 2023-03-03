<?php

namespace InstagramNextAPI\Response\Model;

use InstagramNextAPI\AutoPropertyMapper;

/**
 * MediaShare.
 *
 * @method Item getMedia()
 * @method string getText()
 * @method bool isMedia()
 * @method bool isText()
 * @method $this setMedia(Item $value)
 * @method $this setText(string $value)
 * @method $this unsetMedia()
 * @method $this unsetText()
 */
class MediaShare extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'media' => 'Item',
        'text'  => 'string',
    ];
}
