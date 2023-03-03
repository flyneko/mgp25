<?php

namespace InstagramNextAPI\Realtime\Handler;

use InstagramNextAPI\Realtime\HandlerInterface;
use InstagramNextAPI\Realtime\Message;
use InstagramNextAPI\Realtime\Subscription\GraphQl\AppPresenceSubscription;
use InstagramNextAPI\Response\Model\UserPresence;

class PresenceHandler extends AbstractHandler implements HandlerInterface
{
    const MODULE = AppPresenceSubscription::ID;

    /** {@inheritdoc} */
    public function handleMessage(
        Message $message)
    {
        $data = $message->getData();
        if (!isset($data['presence_event']) || !is_array($data['presence_event'])) {
            throw new HandlerException('Invalid presence (event data is missing).');
        }
        $presence = new UserPresence($data['presence_event']);
        $this->_target->emit('presence', [$presence]);
    }
}
