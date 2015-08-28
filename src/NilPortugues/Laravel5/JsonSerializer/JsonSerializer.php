<?php

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 8/16/15
 * Time: 4:43 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NilPortugues\Laravel5\JsonSerializer;

use NilPortugues\Serializer\DeepCopySerializer;
use NilPortugues\Api\Json\JsonTransformer;

/**
 * Class JsonSerializer.
 */
class JsonSerializer extends DeepCopySerializer
{
    /**
     * @param HalJsonTransformer $halJsonTransformer
     */
    public function __construct(JsonTransformer $jsonTransformer)
    {
        parent::__construct($jsonTransformer);
    }
}
