<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 8/18/15
 * Time: 11:19 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NilPortugues\Laravel5\Json;

use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;

trait JsonResponseTrait
{
    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function addHeaders(\Psr\Http\Message\ResponseInterface $response)
    {
        return $response;
    }

    /**
     * @param string $json
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function errorResponse($json)
    {
        return (new HttpFoundationFactory())
            ->createResponse($this->addHeaders(new \NilPortugues\Api\Json\Http\Message\ErrorResponse($json)));
    }

    /**
     * @param string $json
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function resourceCreatedResponse($json)
    {
        return (new HttpFoundationFactory())
            ->createResponse($this->addHeaders(new \NilPortugues\Api\Json\Http\Message\ResourceCreatedResponse($json)));
    }

    /**
     * @param string $json
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function resourceDeletedResponse($json)
    {
        return (new HttpFoundationFactory())
            ->createResponse($this->addHeaders(new \NilPortugues\Api\Json\Http\Message\ResourceDeletedResponse($json)));
    }

    /**
     * @param string $json
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function resourceNotFoundResponse($json)
    {
        return (new HttpFoundationFactory())
            ->createResponse($this->addHeaders(new \NilPortugues\Api\Json\Http\Message\ResourceNotFoundResponse($json)));
    }

    /**
     * @param string $json
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function resourcePatchErrorResponse($json)
    {
        return (new HttpFoundationFactory())
            ->createResponse($this->addHeaders(new \NilPortugues\Api\Json\Http\Message\ResourcePatchErrorResponse($json)));
    }

    /**
     * @param string $json
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function resourcePostErrorResponse($json)
    {
        return (new HttpFoundationFactory())
            ->createResponse($this->addHeaders(new \NilPortugues\Api\Json\Http\Message\ResourcePostErrorResponse($json)));
    }

    /**
     * @param string $json
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function resourceProcessingResponse($json)
    {
        return (new HttpFoundationFactory())
            ->createResponse($this->addHeaders(new \NilPortugues\Api\Json\Http\Message\ResourceProcessingResponse($json)));
    }

    /**
     * @param string $json
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function resourceUpdatedResponse($json)
    {
        return (new HttpFoundationFactory())
            ->createResponse($this->addHeaders(new \NilPortugues\Api\Json\Http\Message\ResourceUpdatedResponse($json)));
    }

    /**
     * @param string $json
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function response($json)
    {
        return (new HttpFoundationFactory())
            ->createResponse($this->addHeaders(new \NilPortugues\Api\Json\Http\Message\Response($json)));
    }

    /**
     * @param string $json
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unsupportedActionResponse($json)
    {
        return (new HttpFoundationFactory())
            ->createResponse($this->addHeaders(new \NilPortugues\Api\Json\Http\Message\UnsupportedActionResponse($json)));
    }
}
