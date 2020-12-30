<?php

namespace App\Provider;

use \BabiPHP\Core\Renderer;
use \BabiPHP\Helper\Utils;

class Service
{
    public static function getJsonResponse()
    {
        /**
         * (200, 'Request Successfuly')
         * (400, 'Invalid Request')
         * (401, 'Invalid Credentials)
         * (403, 'Access Denied')
         * (404, 'Not Found')
         * (500, 'Could Not Execute Request')
         * (503, 'Service Unavailable')
         */
        return [
            'status' => true, 
            'code' => 200, 
            'message' => '', 
            'payload' => null
        ];
    }
}