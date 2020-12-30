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

namespace BabiPHP\Http;

use \Psr\Http\Message\ResponseInterface;
use \Psr\Http\Message\StreamInterface;
use \BabiPHP\Http\ServerRequest;
use \BabiPHP\Http\Utils\Factory;
use \BabiPHP\Config\Config;
use \BabiPHP\Exception\BpException;
use \BabiPHP\Core\Renderer;
use function \BabiPHP\Http\send;

/**
 * PSR-7 response implementation.
 */
class Response implements ResponseInterface
{
    use MessageTrait;

    /** 
     * @var array Map of standard HTTP status code/reason phrases
     */
    private static $phrases = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];

    /** 
     * @var string 
     */
    private $reasonPhrase = '';

    /** 
     * @var string 
     */
    private $base_url;

    /** 
     * @var int 
     */
    private $statusCode = 200;

    /**
     * @param int                                  $status  Status code
     * @param array                                $headers Response headers
     * @param string|null|resource|StreamInterface $body    Response body
     * @param string                               $version Protocol version
     * @param string|null                          $reason  Reason phrase (when empty a default will be used based on the status code)
     */
    public function __construct(
        $status = 200,
        array $headers = [],
        $body = null,
        $version = '1.1',
        $reason = null
    ) {
        $this->statusCode = (int) $status;

        if ($body !== '' && $body !== null) {
            $this->stream = stream_for($body);
        }

        $this->setHeaders($headers);
        if ($reason == '' && isset(self::$phrases[$this->statusCode])) {
            $this->reasonPhrase = self::$phrases[$this->statusCode];
        } else {
            $this->reasonPhrase = (string) $reason;
        }

        $this->protocol = $version;
        $this->base_url = (ServerRequest::getInstance())->getBaseUrl();
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        $new = clone $this;
        $new->statusCode = (int) $code;
        if ($reasonPhrase == '' && isset(self::$phrases[$new->statusCode])) {
            $reasonPhrase = self::$phrases[$new->statusCode];
        }
        $new->reasonPhrase = $reasonPhrase;
        return $new;
    }

	/**
	 * Redirects the request to another URL
	 *
	 * @param string $url   The URL to redirect to
	 * @param bool $extern   if url is external (http://anotherdomain.co)
	 * @param int $code     The HTTP status code to use for redirection
	 * @return Psr\Http\Message\ResponseInterface
	 */
	public function redirect($url = null, bool $external = false, int $code = 302)
	{
        if(!$external) {
			$url = (is_null($url)) ? $this->base_url : $this->base_url.'/'.trim($url, '/');
		}
            
        return Factory::createResponse($code)->withHeader('Location', $url);
	}

    public function notAllowed($allows)
    {
        $contentType = ServerRequest::getInstance()->getContentType();

        switch ($contentType) {
            case 'application/json':
                $output = $this->renderJsonNotAllowedMessage($allows);
            break;
            case 'text/xml':
            case 'application/xml':
                $output = $this->renderXmlNotAllowedMessage($allows);
            break;
            case 'text/html':
                $output = $this->renderHtmlNotAllowedMessage($allows);
            break;
            default:
                $output = $this->renderJsonNotAllowedMessage($allows);
        }

        $response = Factory::createResponse(405)
            ->withHeader('Allow', implode(', ', $allows))
            ->withHeader('Content-type', $contentType);
        $response->getBody()->write($output);
        
        return $response;
    }

    /**
     * Permet de renvoyer une page d'erreur
     *
     * @param string|null $message
     * @param int $statusCode
     */
    public function sendError($statusCode = 410, $message = null)
    {
        if (!isset(self::$phrases[$statusCode])) {
            $statusCode = 410;
        }

        $page = new \stdClass;
        $page->title = self::$phrases[$statusCode];
        $page->status_code = $statusCode;

        if (!$message) {
            $message = $page->title;
        }

        $error = Config::get('custom_error.template');
        $template = $error['template'];

        if (isset($error['view'][$statusCode])) {
            $view = $error['view'][$statusCode];
        } else {
            $view = $error['view'][410];
        }

        $output = Renderer::make($view, ['page'=>$page, 'message'=>$message], $template);

        $response = $this->withStatus($statusCode);
        $response->getBody()->write($output);
        
        return $response;
    }

    /**
    * Forbidden
    */
    public function forbidden($message = null)
    {
        return $this->sendError(403, $message);
    }

    /**
    * NotFound
    * @param string $message
    */
    public function notFound($message = null)
    {
        if (Config::get('custom_error.enable')) {
            $response = $this->sendError(404, $message);
        } else {
            $response = $this->defaultNotFound();
        }

        return $response;
    }

    /**
     * Permet de générer la page demandée
     *
     * @param string $view
     * @param array $data
     * @return ResponseInterface
     */
    public function render(string $view, array $data = [])
    {
        $output = Renderer::make($view, $data);
        $response = $this->withHeader('Content-type', 'text/html');
        $response->getBody()->write($output);
        
        return $response;
    }

    /**
     * Permet retourner une réponse de type JSON
     * 
     * @param array $data
     * @return ResponseInterface
     */
    public function renderJson(array $data)
    {
        $response = $this->withHeader('Content-type', 'application/json');
        $response->getBody()->write(json_encode($data));
        
        return $response;
    }

    private function defaultNotFound()
    {
        $contentType = (ServerRequest::getInstance())->getContentType();

        switch ($contentType) {
            case 'application/json':
                $output = $this->renderJsonNotFound();
            break;
            case 'text/xml':
            case 'application/xml':
                $output = $this->renderXmlNotFound();
            break;
            case 'text/html':
                $output = $this->renderHtmlNotFound();
            break;
            default:
                throw new BpException('Cannot render unknown content type '.$contentType);
        }

        $response = Factory::createResponse(404)->withHeader('Content-type', $contentType);
        $response->getBody()->write($output);
        
        return $response;
    }

    private function renderHtmlNotFound()
    {
        $title = 'Page Not Found';
        $html = '<p>The page you are looking for could not be found. Check the address bar to ensure your URL is spelled correctly. If all else fails, you can visit our home page at the link below.</p>';
        $url = '<a href="'.$this->base_url.'">Visit the Home Page</a>';

        $output = sprintf(
            "<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
            "<title>%s</title><style>body{margin:0;padding:30px;font:12px/1.5 Helvetica,Arial,Verdana," .
            "sans-serif;}h1{margin:0;font-size:48px;font-weight:normal;line-height:48px;}strong{" .
            "display:inline-block;width:65px;}</style></head><body><h1>%s</h1>%s<p>%s</p></body></html>",
            $title,
            $title,
            $html,
            $url
        );

        return $output;
    }

    private function renderJsonNotFound()
    {
        $error = [
            'error' => voidClass([
                'title' => 'Page Not Found',
                'message' => 'The page you are looking for could not be found.'
            ])
        ];

        return json_encode($error, JSON_PRETTY_PRINT);
    }

    private function renderXmlNotFound()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= "<error>\n";
        $xml .= "	<title>Page Not Found</title>\n";
        $xml .= "	<message>The page you are looking for could not be found.</message>\n";
        $xml .= "</error>";

        return $xml;
    }

    private function renderHtmlNotAllowedMessage($methods)
    {
        $title = 'Method not allowed';
        $html = '<p>Method not allowed. Must be one of: <strong>'.implode(', ', $methods).'</strong></p>';

        $output = sprintf(
            "<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
            "<title>%s</title><style>body{margin:0;padding:30px;font:12px/1.5 Helvetica,Arial,Verdana," .
            "sans-serif;}h1{margin:0;font-size:48px;font-weight:normal;line-height:48px;}strong{" .
            "display:inline-block;width:65px;}</style></head><body><h1>%s</h1>%s</body></html>",
            $title,
            $title,
            $html
        );

        return $output;
    }

    private function renderJsonNotAllowedMessage($methods)
    {
        $error = [
            'error' => voidClass([
                'type' => 'RouterException',
                'title' => 'Method not allowed',
                'message' => 'Method not allowed. Must be one of: '.implode(', ', $methods)
            ])
        ];

        return json_encode($error, JSON_PRETTY_PRINT);
    }

    private function renderXmlNotAllowedMessage($methods)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= "<error>\n";
        $xml .= "  <type>RouterException</type>\n";
        $xml .= "  <title>Method not allowed</title>\n";
        $xml .= "  <message>Method not allowed. Must be one of: ".implode(', ', $methods)."</message>\n";
        $xml .= "</error>";

        return $xml;
    }
}