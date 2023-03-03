<?php

namespace InstagramNextAPI\Realtime\Handler;

use InstagramNextAPI\Realtime\HandlerInterface;
use InstagramNextAPI\Realtime\Message;
use InstagramNextAPI\Realtime\Payload\IrisSubscribeAck;

class IrisHandler extends AbstractHandler implements HandlerInterface
{
    const MODULE = 'iris';

    /** {@inheritdoc} */
    public function handleMessage(
        Message $message)
    {
        $iris = new IrisSubscribeAck($message->getData());
        if (!$iris->isSucceeded()) {
            throw new HandlerException(sprintf(
                'Failed to subscribe to Iris (%d): %s.',
                $iris->getErrorType(),
                $iris->getErrorMessage()
            ));
        }
        $this->_target->emit('iris-subscribed', [$iris]);
    }
}
