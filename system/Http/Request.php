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

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use BabiPHP\Collection\DataCollection;
use BabiPHP\Collection\HeaderDataCollection;
use BabiPHP\Collection\ServerDataCollection;
use BabiPHP\Http\ServerRequest;
use BabiPHP\Http\Uri;
use BabiPHP\Config\Config;

use function BabiPHP\Http\stream_for;

class Request implements RequestInterface
{
    use MessageTrait;

    /**
     * @var string Unique identifier for the request
     */
    protected $id;

    /**
     * @var DataCollection GET (query) parameters
     */
    protected $get;

    /**
     * @var DataCollection POST parameters
     */
    protected $post;

    /**
     * @var DataCollection Client cookie data
     */
    protected $cookie;

    /**
     * @var ServerDataCollection Server created attributes
     */
    protected $server;

    /**
     * @var HeaderDataCollection Server headers
     */
    protected $headerCollection;

    /**
     * @var DataCollection Uploaded temporary files
     */
    protected $files;

    /**
     * @var string
     */
    protected $base_path;

    /**
     * @var string
     */
    protected $base_url;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var string
     */
    protected $route_name;

    /**
     * Known handled content types
     *
     * @var array
     */
    protected static $knownContentTypes = [
        'application/json',
        'application/xml',
        'text/xml',
        'text/html',
    ];

    /** 
     * @var string
     */
    protected $method;

    /** 
     * @var null|string
     */
    protected $requestTarget;

    /** 
     * @var UriInterface 
     */
    protected $uri;

    /**
     * @param string                               $method  HTTP method
     * @param string|UriInterface                  $uri     URI
     * @param array                                $headers Request headers
     * @param string|null|resource|StreamInterface $body    Request body
     * @param string                               $version Protocol version
     */
    public function __construct(
        $method,
        $uri,
        array $headers = [],
        $body = null,
        $version = '1.1'
    ) {
        if (!($uri instanceof UriInterface)) {
            $uri = new Uri($uri);
        }

        $this->method = strtoupper($method);
        $this->uri = $uri;
        $this->setHeaders($headers);
        $this->protocol = $version;
        $this->server = new ServerDataCollection($_SERVER);
        $this->headerCollection = new HeaderDataCollection($this->server->getHeaders());

        if (!$this->hasHeader('Host')) {
            $this->updateHostFromUri();
        }

        if ($body !== '' && $body !== null) {
            $this->stream = stream_for($body);
        }

        if (Config::get('app.base_path') === false) {
            $server_root = str_replace('\\', '/', $this->server->get('DOCUMENT_ROOT'));
            $base_path = str_replace($server_root, '', ROOT);
        } else {
            $base_path = Config::get('app.base_path');
        }

        $path = trim($base_path, '/');
        $host = $this->uri->getHost();
        $query = $this->uri->getQuery();

        if($this->uri->getPort()) {
            $host = $host.':'.$this->uri->getPort();
        }

        $base_uri = ($path) ? $host.'/'.$path : $host;
        $request_uri = $this->uri->getPath();
        $query_params = $this->parseQuery($query);
        $www = 'www.';

        if (Config::get('system.redirect.www') && substr($base_uri, 0, 4) !== $www) {
            $base_uri = $www.$base_uri;
        }

        if ($query) {
            $request_uri = $request_uri.'?'.$query;
        }

        $this->base_path = $base_path;
        $this->base_url = $this->uri->getScheme().'://'.$base_uri;
        $this->url = $this->uri->getScheme().'://'.$host.$request_uri;

        $this->get = new DataCollection($query_params);
        $this->post = new DataCollection($_POST);
        $this->cookie = new DataCollection($_COOKIE);
        $this->files = new DataCollection(ServerRequest::normalizeFiles($_FILES));

        define('FUNC_BASE_URL', $this->base_url);
    }

    public function withRequestTarget($requestTarget)
    {
        if (preg_match('#\s#', $requestTarget)) {
            throw new InvalidArgumentException(
                'Invalid request target provided; cannot contain whitespace'
            );
        }

        $new = clone $this;
        $new->requestTarget = $requestTarget;
        return $new;
    }

    public function withMethod($method)
    {
        $new = clone $this;
        $new->method = strtoupper($method);
        return $new;
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        if ($uri === $this->uri) {
            return $this;
        }

        $new = clone $this;
        $new->uri = $uri;

        if (!$preserveHost) {
            $new->updateHostFromUri();
        }

        return $new;
    }

    private function updateHostFromUri()
    {
        $host = $this->uri->getHost();

        if ($host == '') {
            return;
        }

        if (($port = $this->uri->getPort()) !== null) {
            $host .= ':' . $port;
        }

        if (isset($this->headerNames['host'])) {
            $header = $this->headerNames['host'];
        } else {
            $header = 'Host';
            $this->headerNames['host'] = 'Host';
        }
        // Ensure Host is the first header.
        // See: http://tools.ietf.org/html/rfc7230#section-5.4
        $this->headers = [$header => [$host]] + $this->headers;
    }

    public function getRequestTarget()
    {
        if ($this->requestTarget !== null) {
            return $this->requestTarget;
        }

        $target = $this->uri->getPath();
        if ($target == '') {
            $target = '/';
        }
        if ($this->uri->getQuery() != '') {
            $target .= '?' . $this->uri->getQuery();
        }

        return $target;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Determine & return which content type we know about is wanted using Accept header
     *
     * @return string
     */
    public function getContentType()
    {
        $acceptHeader = $this->getHeaderLine('Accept');
        $selectedContentTypes = array_intersect(explode(',', $acceptHeader), self::$knownContentTypes);

        if (count($selectedContentTypes)) {
            return current($selectedContentTypes);
        }

        // handle +json and +xml specially
        if (preg_match('/\+(json|xml)/', $acceptHeader, $matches)) {
            $mediaType = 'application/' . $matches[1];
            if (in_array($mediaType, self::$knownContentTypes)) {
                return $mediaType;
            }
        }

        return 'text/html';
    }

    /**
     * Gets a unique ID for the request
     * Generates one on the first call
     *
     * @param boolean $hash     Whether or not to hash the ID on creation
     * @return string
     */
    public function id($hash = true)
    {
        if (null === $this->id) {
            $this->id = uniqid();

            if ($hash) {
                $this->id = sha1($this->id);
            }
        }

        return $this->id;
    }

    /**
     * Returns the server collection
     *
     * @return BabiPHP\Http\Collection\DataCollection
     */
    public function server()
    {
        return $this->server;
    }

    /**
     * Returns the headers collection
     *
     * @return BabiPHP\Http\Collection\HeaderDataCollection
     */
    public function headers()
    {
        return $this->headerCollection;
    }

    /**
     * Permet de définir le nom de route
     *
     * @param string $name
     * @return void
     */
    public function setRouteName(string $name)
    {
        $this->route_name = $name;

        return $this;
    }

    /**
     * Permet de définir le controller approprié à la requête
     *
     * @param string $controller
     * @return Psr\Http\Message\ServerRequestInterface
     */
    public function setController(string $controller)
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * Permet de définir l'action de la requête
     *
     * @param string $action
     * @return Psr\Http\Message\ServerRequestInterface
     */
    public function setAction(string $action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Permet de définir l'ur de base
     *
     * @param string $base_url
     * @return Psr\Http\Message\ServerRequestInterface
     */
    public function setBaseUrl(string $base_url)
    {
        $this->base_url = $base_url;

        return $this;
    }

    /**
     * Return the base path
     * 
     * @return string
     */
    public function getBasePath()
    {
        return $this->base_path;
    }

    /**
     * Return the request base url
     * 
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->base_url;
    }

    /**
     * Return the request url
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Return the request route name
     * 
     * @return string
     */
    public function getRouteName()
    {
        return $this->route_name;
    }

    /**
     * Return the request current controller
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Return the request current action
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Gets the request domain
     *
     * @return string
     */
    public function getDomain($length = 1)
    {
        $segments = explode('.', $this->uri->getHost());
        $domain = array_slice($segments, -1 * ($length + 1));

        return implode('.', $domain);
    }

    /**
     * Gets the request subdomain
     *
     * @return string
     */
    public function getSubDomains($length = 1)
    {
        $segments = explode('.', $this->uri->getHost());

        return array_slice($segments, 0, -1 * ($length + 1));
    }

    /**
     * Gets the request IP address
     *
     * @return string
     */
    public function getIp()
    {
        if ($this->server->exists('HTTP_CLIENT_IP')) {
            return $this->server->get('HTTP_CLIENT_IP');
        } elseif ($this->server->exists('HTTP_X_FORWARDED_FOR')) {
            return $this->server->get('HTTP_X_FORWARDED_FOR');
        } else {
            return $this->server->get('REMOTE_ADDR');
        }
    }

    /**
     * Gets the request user agent
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->headerCollection->get('USER_AGENT');
    }

    /**
     * Retourne toutes les entêtes de la requête
     *
     * @return array
     */
    public function getallheaders()
    { 
        $headers = [];

        foreach ($_SERVER as $name => $value) 
        { 
            if (substr($name, 0, 5) == 'HTTP_') 
            { 
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value; 
            } 
        } 
        
        return $headers;
    }

    /**
     * Return $_POST collection
     * 
     * @return BabiPHP\Http\Collection\DataCollection
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Return $_GET collection
     * 
     * @return BabiPHP\Http\Collection\DataCollection
     */
    public function getQuery()
    {
        return $this->get;
    }

    /**
     * Returns the files collection
     *
     * @return BabiPHP\Http\Collection\DataCollection
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Returns the cookie collection
     *
     * @return BabiPHP\Http\Collection\DataCollection
     */
    public function getCookie()
    {
        return $this->cookie;
    }

    /**
     * Is the request secure?
     *
     * @return boolean
     */
    public function isSecure()
    {
        return ($this->server->get('HTTPS') == true);
    }

    /**
     * Permet de vérifier si des données ont été été envoyées par la méthode GET
     * 
     * @return boolean
     */
    public function isGet()
    {
        return ($this->get->isEmpty()) ? false : true;
    }
    
    /**
     * Permet de vérifier si des données ont été été envoyées par la méthode POST
     * 
     * @return boolean
     */
    public function isPost()
    {
        return ($this->post->isEmpty()) ? false : true;
    }
    
    /**
     * Test si la requête courante est une requête de type xmlhttprequest
     *
     * @return boolean
     */
    public function isXhr()
    {
        return ($this->server->exists('HTTP_X_REQUESTED_WITH') && strtolower($this->server->get('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest') ? true : false;
    }

    /**
     * Test si la requête courante est une requête de type xmlhttprequest
     *
     * @return boolean
     */
    public function isAjax()
    {
        return $this->isXhr();
    }

    /**
     * Permet de parser les paramètres de la requête
     *
     * @param string $query
     * @return array
     */
    private function parseQuery(string $query)
    {
        $params = [];

        if ($query) {
            foreach (explode('&', $query) as $k => $v) {
                $param = explode('=', $v);
                $params[$param[0]] = $param[1];
            }
        }

        return $params;
    }
}