<?php

namespace tests;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use triagens\ArangoDb\UpdatePolicy;
use triagens\ArangoDb\ConnectionHandler;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    const GET = 'GET';
    
    const POST = 'POST';
    
    protected function getLogger() {
        
            $logger = new Logger('phpUnitTest');
            $logger->pushHandler(new StreamHandler("logs/phpunit.log", Logger::DEBUG));  
       
        
        return $logger;
    }
    
    public function setRequestMethod($method) {
        define("__REQUEST_METHOD", $method);
    }
    
    public function setURI($uri) {
        define('__URI', $uri);
        define("__REQUEST_URI", $uri . '/');
    }
    
    public function testBase() {
        
    }


    protected function getCredentials()
    {
        return array(
            // database name
            'dbName' => 'travel-sample',
            // server endpoint to connect to
            'host' => '192.168.0.190',
            // authorization type to use (currently supported: 'Basic')
            'username' => 'Administrator',
            // user for basic authorization
            'password' => 'isnothere'
        );
    }
}
