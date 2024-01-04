<?php

namespace responses;

// PHP 7
// use \Slim\Http\Response as Response;

// PHP 8
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Factory\AppFactory;

class GenericResponse
{

    private $data;
    private $message;
    private $code;
    private $token;
	private $statusCode;

    function __construct($statusCode)
    {
        $this->statusCode = $statusCode;
        $this->data = null;
        $this->message = null;
        $this->code = null;
        $this->token = null;
    }

    public function SetData($data) {
        $this->data = $data;
    }

    public function SetMessage($message) {
        $this->message = $message;
    }

    public function SetCode($code) {
        $this->code = $code;
    }

    public function SetToken($token) {
        $this->token = $token;
    }

    public function GetResponse()
    {
        if ($this->data == null) {
            //$data = array();
            //$data = array("data"=>$this->data);
            $data["message"] = $this->message;
            $data["code"] = $this->code;
        } else {
            $data = array("data"=>$this->data);
            $data["message"] = $this->message;
            $data["code"] = $this->code;
        }

		// PHP 7
        // $response = new Response();
		
		// PHP 8
		$app = AppFactory::create();
		$responseFactory = $app->getResponseFactory();
		$response = $responseFactory->createResponse();
		
        if ($this->token) {
            return $response->withStatus($this->statusCode)
                ->withHeader('Access-Control-Allow-Credentials', 'true')
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->withHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization')
                ->withHeader('Access-Control-Request-Headers', 'Authorization')
                ->withHeader('Access-Control-Expose-Headers', 'Authorization')
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Authorization', $this->token)
                ->write(json_encode($data));
        } else {
            return $response->withStatus($this->statusCode)
                ->withHeader('Access-Control-Allow-Credentials', 'true')
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->withHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization')
                ->withHeader('Access-Control-Request-Headers', 'Authorization')
                ->withHeader('Access-Control-Expose-Headers', 'Authorization')
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($data));
        }
    }

}