<?php

namespace InstagramNextAPI\Realtime\Subscription\Skywalker;

use InstagramNextAPI\Realtime\Subscription\SkywalkerSubscription;

class LiveSubscription extends SkywalkerSubscription
{
    const ID = 'live';
    const TEMPLATE = 'ig/live_notification_subscribe/%s';

    /** {@inheritdoc} */
    public function getId()
    {
        return self::ID;
    }

    /** {@inheritdoc} */
    public function __toString()
    {
        return sprintf(self::TEMPLATE, $this->_accountId);
    }
}
