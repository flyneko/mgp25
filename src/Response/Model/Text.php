<?php

namespace InstagramNextAPI\Response\Model;

use InstagramNextAPI\AutoPropertyMapper;

/**
 * Text.
 *
 * @method string getText()
 * @method bool isText()
 * @method $this setText(string $value)
 * @method $this unsetText()
 */
class Text extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'text' => 'string',
    ];
}
