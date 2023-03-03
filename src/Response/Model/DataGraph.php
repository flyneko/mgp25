<?php

namespace InstagramNextAPI\Response\Model;

use InstagramNextAPI\AutoPropertyMapper;

/**
 * DataGraph.
 *
 * @method DataPoints[] getDataPoints()
 * @method string getName()
 * @method bool isDataPoints()
 * @method bool isName()
 * @method $this setDataPoints(DataPoints[] $value)
 * @method $this setName(string $value)
 * @method $this unsetDataPoints()
 * @method $this unsetName()
 */
class DataGraph extends AutoPropertyMapper
{
    const JSON_PROPERTY_MAP = [
        'name'         => 'string',
        'data_points'  => 'DataPoints[]',
    ];
}
