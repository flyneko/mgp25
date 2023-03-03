<?php

namespace InstagramNextAPI\Realtime\Parser;

use Fbns\Client\Thrift\Compact;
use Fbns\Client\Thrift\Reader;
use InstagramNextAPI\Realtime\Handler\RegionHintHandler;
use InstagramNextAPI\Realtime\Message;
use InstagramNextAPI\Realtime\ParserInterface;

class RegionHintParser implements ParserInterface
{
    const FIELD_TOPIC = 1;

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     * @throws \DomainException
     */
    public function parseMessage(
        $topic,
        $payload)
    {
        $region = null;
        new Reader($payload, function ($context, $field, $value, $type) use (&$region) {
            if ($type === Compact::TYPE_BINARY && $field === self::FIELD_TOPIC) {
                $region = $value;
            }
        });

        return [$this->_createMessage($region)];
    }

    /**
     * Create a message from given topic and payload.
     *
     * @param string $region
     *
     * @throws \RuntimeException
     * @throws \DomainException
     *
     * @return Message
     */
    protected function _createMessage(
        $region)
    {
        if ($region === null) {
            throw new \RuntimeException('Incomplete region hint message.');
        }

        return new Message(RegionHintHandler::MODULE, $region);
    }
}
