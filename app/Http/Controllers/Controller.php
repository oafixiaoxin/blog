<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Response;

class Controller extends BaseController
{
    public function output($code = Response::SUCCESS, $data = [])
    {
    	$result = [
    		'responseCode' => $code,
    		'responseMsg' => Response::getResponseMsg($code),
    	];
    	
    	if ($data)
    	{
    		//$result = array_merge($result, $data);
    		$result['responseBody'] = $data;
    	}
    	
    	return json_encode($result);
    }
}
