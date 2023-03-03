<?php

namespace InstagramNextAPI\Realtime\Payload;

use InstagramNextAPI\AutoPropertyMapper;

/**
 * ThreadAction.
 *
 * @method \InstagramNextAPI\Response\Model\ActionLog getActionLog()
 * @method string getUserId()
 * @method bool isActionLog()
 * @method bool isUserId()
 * @method $this setActionLog(\InstagramNextAPI\Response\Model\ActionLog $value)
 * @method $this setUserId(string $value)
 * @method $this unsetActionLog()
 * @method $this unsetUserId()
 */
class ThreadAction extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'user_id'    => 'string',
        'action_log' => '\InstagramNextAPI\Response\Model\ActionLog',
    ];
}
