<?php

namespace InstagramNextAPI\Realtime\Payload;

use InstagramNextAPI\AutoPropertyMapper;

/**
 * StoryScreenshot.
 *
 * @method \InstagramNextAPI\Response\Model\User getActionUserDict()
 * @method int getMediaType()
 * @method bool isActionUserDict()
 * @method bool isMediaType()
 * @method $this setActionUserDict(\InstagramNextAPI\Response\Model\User $value)
 * @method $this setMediaType(int $value)
 * @method $this unsetActionUserDict()
 * @method $this unsetMediaType()
 */
class StoryScreenshot extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'action_user_dict' => '\InstagramNextAPI\Response\Model\User',
        /*
         * A number describing what type of media this is.
         */
        'media_type'       => 'int',
    ];
}
