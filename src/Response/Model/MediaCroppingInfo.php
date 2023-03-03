<?php

namespace InstagramNextAPI\Response\Model;

use InstagramNextAPI\AutoPropertyMapper;

/**
 * MediaCroppingInfo.
 *
 * @method mixed getProfileGridCrop()
 * @method bool isProfileGridCrop()
 * @method $this setProfileGridCrop(mixed $value)
 * @method $this unsetProfileGridCrop()
 */
class MediaCroppingInfo extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'profile_grid_crop'                            => '',
    ];
}
