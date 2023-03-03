<?php

namespace InstagramNextAPI\Response\Model;

use InstagramNextAPI\AutoPropertyMapper;

/**
 * Prefill.
 *
 * @method mixed getCandidates()
 * @method string getUsage()
 * @method bool isCandidates()
 * @method bool isUsage()
 * @method $this setCandidates(mixed $value)
 * @method $this setUsage(string $value)
 * @method $this unsetCandidates()
 * @method $this unsetUsage()
 */
class Prefill extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'usage'                            => 'string',
        'candidates'                       => '',
    ];
}
