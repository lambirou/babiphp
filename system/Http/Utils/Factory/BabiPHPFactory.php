<?php
declare(strict_types = 1);

namespace BabiPHP\Http\Utils\Factory;

use \Psr\Http\Message\ResponseFactoryInterface;
use \Psr\Http\Message\ResponseInterface;
use \Psr\Http\Message\ServerRequestFactoryInterface;
use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\StreamFactoryInterface;
use \Psr\Http\Message\StreamInterface;
use \Psr\Http\Message\UriFactoryInterface;
use \Psr\Http\Message\UriInterface;
use \BabiPHP\Http\Response;
use \BabiPHP\Http\ServerRequest;
use \BabiPHP\Http\Stream;
use \BabiPHP\Http\Uri;

/**
 * Simple class to create response instances of PSR-7 classes.
 */
class BabiPHPFactory implements
    ResponseFactoryInterface,
    ServerRequestFactoryInterface,
    StreamFactoryInterface,
    UriFactoryInterface
{
    /**
     * Check whether BabiPHP is available
     */
    public static function isInstalled(): bool
    {
        return class_exists('BabiPHP\\Http\\Response')
            && class_exists('BabiPHP\\Http\\ServerRequest')
            && class_exists('BabiPHP\\Http\\Stream')
            && class_exists('BabiPHP\\Http\\Uri');
    }
    
    /**
     * @see ResponseFactoryInterface
     */
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return new Response($code, [], null, '1.1', $reasonPhrase);
    }

    /**
     * @see ServerRequestFactoryInterface
     * @param mixed $uri
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        return new ServerRequest($method, $uri, [], null, '1.1', $serverParams);
    }

    /**
     * @see StreamFactoryInterface
     */
    public function createStream(string $content = ''): StreamInterface
    {
        $stream = $this->createStreamFromFile('php://temp', 'r+');
        $stream->write($content);

        return $stream;
    }

    /**
     * @see StreamFactoryInterface
     */
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        return $this->createStreamFromResource(fopen($filename, $mode));
    }

    /**
     * @see StreamFactoryInterface
     * @param mixed $resource
     */
    public function createStreamFromResource($resource): StreamInterface
    {
        return new Stream($resource);
    }

    /**
     * @see UriFactoryInterface
     */
    public function createUri(string $uri = ''): UriInterface
    {
        return new Uri($uri);
    }
}