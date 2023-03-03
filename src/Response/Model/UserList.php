<?php

namespace InstagramNextAPI\Response\Model;

use InstagramNextAPI\AutoPropertyMapper;

/**
 * UserList.
 *
 * @method int getPosition()
 * @method User getUser()
 * @method bool isPosition()
 * @method bool isUser()
 * @method $this setPosition(int $value)
 * @method $this setUser(User $value)
 * @method $this unsetPosition()
 * @method $this unsetUser()
 */
class UserList extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'position' => 'int',
        'user'     => 'User',
    ];
}
