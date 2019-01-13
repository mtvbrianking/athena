<?php
declare(strict_types = 1);

namespace Athena\Factory;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use RuntimeException;

/**
 * Simple class to create instances of PSR-7 classes.
 */
abstract class Factory
{
    private static $strategies = [
        DiactorosFactory::class,
    ];

    private static $factory;

    private static $factories = [];

    /**
     * Create the PSR-17 factories or throw an exception
     *
     * @param string $type
     * @return mixed
     */
    private static function getFactory(string $type)
    {
        if (!empty(self::$factories[$type])) {
            return self::$factories[$type];
        }

        if (!empty(self::$factory)) {
            return self::$factories[$type] = self::$factory;
        }

        foreach (self::$strategies as $className) {
            if (is_array($className) && isset($className[$type])) {
                $className = $className[$type];

                if (class_exists($className)) {
                    return self::$factories[$type] = new $className();
                }

                continue;
            }

            if (!class_exists($className)) {
                continue;
            }

            if (strpos($className, __NAMESPACE__) === 0 && !$className::isInstalled()) {
                continue;
            }

            return self::$factories[$type] = self::$factory = new $className();
        }

        throw new RuntimeException('No PSR-7 library detected');
    }

    /**
     * Change the strategies
     *
     * @param array|null $strategies
     */
    public static function setStrategies(array $strategies = null)
    {
        self::$factory = null;
        self::$factories = [];
        self::$strategies = $strategies;
    }

    /**
     * Set a custom ResponseFactory.
     *
     * @param \Psr\Http\Message\ResponseFactoryInterface $responseFactory
     */
    public static function setResponseFactory(ResponseFactoryInterface $responseFactory)
    {
        self::$factories['response'] = $responseFactory;
    }

    /**
     * Get a ResponseFactory.
     */
    public static function getResponseFactory(): ResponseFactoryInterface
    {
        return self::getFactory('response');
    }

    /**
     * Set a custom StreamFactory.
     *
     * @param \Psr\Http\Message\StreamFactoryInterface $streamFactory
     */
    public static function setStreamFactory(StreamFactoryInterface $streamFactory)
    {
        self::$factories['stream'] = $streamFactory;
    }

    /**
     * Get a StreamFactory.
     */
    public static function getStreamFactory(): StreamFactoryInterface
    {
        return self::getFactory('stream');
    }

    /**
     * Set a custom UriFactory.
     *
     * @param \Psr\Http\Message\UriFactoryInterface $uriFactory
     */
    public static function setUriFactory(UriFactoryInterface $uriFactory)
    {
        self::$factories['uri'] = $uriFactory;
    }

    /**
     * Get a UriFactory.
     */
    public static function getUriFactory(): UriFactoryInterface
    {
        return self::getFactory('uri');
    }

    /**
     * Set a custom ServerRequestFactory.
     *
     * @param \Psr\Http\Message\ServerRequestFactoryInterface $serverRequestFactory
     */
    public static function setServerRequestFactory(ServerRequestFactoryInterface $serverRequestFactory)
    {
        self::$factories['serverRequest'] = $serverRequestFactory;
    }

    /**
     * Get a ServerRequestFactory.
     */
    public static function getServerRequestFactory(): ServerRequestFactoryInterface
    {
        return self::getFactory('serverRequest');
    }

    /**
     * Creates a Response instance.
     *
     * @param int $code
     * @param string $reasonPhrase
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return self::getResponseFactory()->createResponse($code, $reasonPhrase);
    }

    /**
     * Creates a Stream instance.
     *
     * @param string $content
     * @return \Psr\Http\Message\StreamInterface
     */
    public static function createStream(string $content = ''): StreamInterface
    {
        return self::getStreamFactory()->createStream($content);
    }

    /**
     * Creates an Uri instance.
     *
     * @param string $uri
     * @return \Psr\Http\Message\UriInterface
     */
    public static function createUri(string $uri = ''): UriInterface
    {
        return self::getUriFactory()->createUri($uri);
    }

    /**
     * Creates a ServerRequest instance.
     *
     * @param string $method
     * @param mixed $uri
     * @param array $serverParams
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public static function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        return self::getServerRequestFactory()->createServerRequest($method, $uri, $serverParams);
    }
}
