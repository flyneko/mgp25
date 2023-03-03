<?php

namespace InstagramNextAPI\Realtime;

use InstagramNextAPI\Realtime\Handler\HandlerException;

interface HandlerInterface
{
    /**
     * Handle the message.
     *
     * @param Message $message
     *
     * @throws HandlerException
     */
    public function handleMessage(
        Message $message);
}
