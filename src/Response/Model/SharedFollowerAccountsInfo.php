<?php

namespace InstagramNextAPI\Response\Model;

use InstagramNextAPI\AutoPropertyMapper;

/**
 * SharedFollowerAccountsInfo.
 *
 * @method bool getHasSharedFollowerAccounts()
 * @method bool isHasSharedFollowerAccounts()
 * @method $this setHasSharedFollowerAccounts(bool $value)
 * @method $this unsetHasSharedFollowerAccounts()
 */
class SharedFollowerAccountsInfo extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'has_shared_follower_accounts' => 'bool',
    ];
}
