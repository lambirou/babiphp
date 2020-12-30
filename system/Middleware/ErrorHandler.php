<?php
/**
 * BabiPHP : The flexible PHP Framework
 *
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) BabiPHP.
 * @link          https://github.com/lambirou/babiphp BabiPHP Project
 * @license       MIT
 *
 * Not edit this file
 */

namespace BabiPHP\Middleware;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;
use \Psr\Http\Server\MiddlewareInterface;
use \Psr\Http\Server\RequestHandlerInterface;
use BabiPHP\Http\Utils\Factory;
use BabiPHP\Error\Run;
use BabiPHP\Error\Util\SystemFacade;
use BabiPHP\Error\Handler\PrettyPageHandler;
use BabiPHP\Error\Handler\PlainTextHandler;
use BabiPHP\Error\Handler\JsonResponseHandler;
use BabiPHP\Error\Handler\XmlResponseHandler;

class ErrorHandler implements MiddlewareInterface
{
    /**
     * @var Run|null
     */
    private $error_handler;

    /**
     * @var SystemFacade|null
     */
    private $system;

    /**
     * @var bool Whether catch errors or not
     */
    private $catchErrors = true;

    /**
     * Set the error_handler instance.
     *
     * @param Run|null $error_handler
     * @param SystemFacade|null $systemFacade
     */
    public function __construct(Run $error_handler = null, SystemFacade $system = null)
    {
        $this->error_handler = $error_handler;
        $this->system = $system;
    }

    /**
     * Whether catch errors or not.
     *
     * @param bool $catchErrors
     *
     * @return self
     */
    public function catchErrors($catchErrors = true)
    {
        $this->catchErrors = (bool) $catchErrors;

        return $this;
    }

    /**
     * Process a request and return a response.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        ob_start();
        $level = ob_get_level();

        $method = Run::EXCEPTION_HANDLER;
        $error_handler = $this->error_handler ?: $this->geterror_handlerInstance($request);

        $error_handler->allowQuit(false);
        $error_handler->writeToOutput(false);
        $error_handler->sendHttpCode(false);

        //Catch errors means register error_handler globally
        if ($this->catchErrors) {
            $error_handler->register();

            $shutdown = function () use ($error_handler) {
                $error_handler->allowQuit(true);
                $error_handler->writeToOutput(true);
                $error_handler->sendHttpCode(true);

                $method = Run::SHUTDOWN_HANDLER;
                $error_handler->$method();
            };

            if ($this->system) {
                $this->system->registerShutdownFunction($shutdown);
            } else {
                register_shutdown_function($shutdown);
            }
        }

        try {
            $response = $handler->handle($request);
        } catch (\Throwable $exception) {
            $response = Factory::createResponse(500);
            $response->getBody()->write($error_handler->$method($exception));
            $response = self::updateResponseContentType($response, $error_handler);
        } catch (\Exception $exception) {
            $response = Factory::createResponse(500);
            $response->getBody()->write($error_handler->$method($exception));
            $response = self::updateResponseContentType($response, $error_handler);
        } finally {
            while (ob_get_level() >= $level) {
                ob_end_clean();
            }
        }

        if ($this->catchErrors) {
            $error_handler->unregister();
        }

        return $response;
    }

    /**
     * Returns the error_handler instance or create one.
     *
     * @param ServerRequestInterface $request
     *
     * @return Run
     */
    private function geterror_handlerInstance(ServerRequestInterface $request)
    {
        if (!$this->system) {
            $this->system = new SystemFacade();
        }

        $error_handler = new Run($this->system);

        switch (self::getPreferredFormat($request)) {
            case 'json':
                $handler = new JsonResponseHandler();
                $handler->addTraceToOutput(true);
                break;

            case 'xml':
                $handler = new XmlResponseHandler();
                $handler->addTraceToOutput(true);
                break;

            case 'plain':
                $handler = new PlainTextHandler();
                $handler->addTraceToOutput(true);
                break;

            default:
                $handler = new PrettyPageHandler();
                break;
        }

        $error_handler->pushHandler($handler);

        return $error_handler;
    }

    /**
     * Returns the preferred format used by error_handler.
     *
     * @param ServerRequestInterface $request
     *
     * @return string|null
     */
    private static function getPreferredFormat($request)
    {
        if (php_sapi_name() === 'cli') {
            return 'plain';
        }

        $formats = [
            'json' => ['application/json'],
            'html' => ['text/html'],
            'xml' => ['text/xml'],
            'plain' => ['text/plain', 'text/css', 'text/javascript'],
        ];

        $accept = $request->getHeaderLine('Accept');

        foreach ($formats as $format => $mimes) {
            foreach ($mimes as $mime) {
                if (stripos($accept, $mime) !== false) {
                    return $format;
                }
            }
        }
    }

    /**
     * Returns the content-type for the error_handler instance
     *
     * @param ResponseInterface $response
     * @param Run $error_handler
     *
     * @return ResponseInterface
     */
    private static function updateResponseContentType(ResponseInterface $response, Run $error_handler)
    {
        if (1 !== count($error_handler->getHandlers())) {
            return $response;
        }

        $handler = current($error_handler->getHandlers());

        if ($handler instanceof PrettyPageHandler) {
            return $response->withHeader('Content-Type', 'text/html');
        }

        if ($handler instanceof JsonResponseHandler) {
            return $response->withHeader('Content-Type', 'application/json');
        }

        if ($handler instanceof XmlResponseHandler) {
            return $response->withHeader('Content-Type', 'text/xml');
        }

        if ($handler instanceof PlainTextHandler) {
            return $response->withHeader('Content-Type', 'text/plain');
        }

        return $response;
    }
}