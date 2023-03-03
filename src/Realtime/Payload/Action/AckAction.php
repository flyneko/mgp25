<?php

namespace InstagramNextAPI\Realtime\Payload\Action;

use InstagramNextAPI\Realtime\Payload\RealtimeAction;

/**
 * AckAction.
 *
 * @method string getAction()
 * @method \InstagramNextAPI\Response\Model\DirectSendItemPayload getPayload()
 * @method string getStatus()
 * @method mixed getStatusCode()
 * @method bool isAction()
 * @method bool isPayload()
 * @method bool isStatus()
 * @method bool isStatusCode()
 * @method $this setAction(string $value)
 * @method $this setPayload(\InstagramNextAPI\Response\Model\DirectSendItemPayload $value)
 * @method $this setStatus(string $value)
 * @method $this setStatusCode(mixed $value)
 * @method $this unsetAction()
 * @method $this unsetPayload()
 * @method $this unsetStatus()
 * @method $this unsetStatusCode()
 */
class AckAction extends RealtimeAction
{
    const JSON_PROPERTY_MAP = [
        'status_code' => '',
        'payload'     => '\InstagramNextAPI\Response\Model\DirectSendItemPayload',
    ];
}
