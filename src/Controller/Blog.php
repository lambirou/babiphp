<?php

namespace App\Controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Container\ContainerInterface;
use \App\Provider\Service;

class Blog
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function index(Request $request, Response $response, array $args)
    {
        $d['page'] = void_class();
        $d['page']->title = 'Welcome!';
        $d['page']->name = 'To ' . config('app.name') . ' ' . BP_VERSION;

        return $response->render('index', $d);
    }

    public function portfolio(Request $request, Response $response, array $args)
    {
        $d['page'] = void_class(['title' => 'Portfolio - ' . config('app.name')]);
        $d['username'] = $args['name'];

        return $response->render('portfolio', $d);
    }

    public function post(Request $request, Response $response, array $args)
    {
        $d['page'] = void_class(['title' => 'Post - ' . config('app.name')]);

        $d['post'] = [
            'title' => 'Li Europan lingues (en)',
            'content' => 'The European languages are members of the same family. Their separate existence is a myth. For science, music, sport, etc, Europe uses the same vocabulary. The languages only differ in their grammar,.',
            'date' => '2017-12-07 12:40:53'
        ];

        return $response->render('post', $d);
    }

    public function ajax(Request $request, Response $response, array $args)
    {
        $json = Service::getJsonResponse();

        $json['status'] = true;
        $json['response'] = ['name' => config('app.name'), 'type' => 'framework', 'version' => BP_VERSION];
        $json['message'] = 'Request successful !';

        return $response->renderJson($json);
    }
}
