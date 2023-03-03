<?php

namespace InstagramNextAPI\Realtime\Subscription\GraphQl;

use InstagramNextAPI\Realtime\Subscription\GraphQlSubscription;
use InstagramNextAPI\Signatures;

class ZeroProvisionSubscription extends GraphQlSubscription
{
    const QUERY = '17913953740109069';
    const ID = 'zero_provision';

    /**
     * Constructor.
     *
     * @param string $deviceId
     */
    public function __construct(
        $deviceId)
    {
        parent::__construct(self::QUERY, [
            'client_subscription_id' => Signatures::generateUUID(),
            'device_id'              => $deviceId,
        ]);
    }

    /** {@inheritdoc} */
    public function getId()
    {
        return self::ID;
    }
}
