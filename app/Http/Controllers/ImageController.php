<?php
	namespace App\Http\Controllers;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Http\Request;
	use App\Response;
	
	class ImageController extends Controller
	{
	    /**
	     * Create a new controller instance.
	     *
	     * @return void
	     */
	    public function __construct()
	    {
	        //
	    }
	    
	    
	    //uniqid()函数基于以微秒计的当前时间，生成一个唯一的 ID。
	    public function uploadImage ( Request $request )
	    {
	    	$base64_str = $request->input('imgBase64');
//	    	$base64_image = str_replace('', '+', $base64_str);
	    	if ( preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_str, $result) )
	    	{
	    		//匹配成功 
	    		if ( $result[2] == 'jpeg' )
	    		{
	    			$image_name = date('YmdHis').time().'.jpg';
	    		}
	    		else
	    		{
	    			$image_name = date('YmdHis').time().'.'.$result[2];
	    		}
	 
	    		if ( !file_exists('../image/upload/') )
	    		{
	    			mkdir('../image/upload/', 0700);
	    		}
	    		$filepath = '../image/upload/'.$image_name;
//	    		$tempAry = [
//	    			"arg0" => $result[0],
//	    			"arg1" => $result[1],
//	    			"arg2" => $result[2],
//	    			"arg3" => $filepath
//	    		];
//	    		return $this->output(Response::SUCCESS, $tempAry);
	    		if ( file_put_contents($filepath, base64_decode(str_replace($result[1], '', $base64_str))) )
	    		{
	    			return $this->output(Response::SUCCESS, $image_name);
	    		}
	    		else
	    		{
	    			return $this->output(Response::FAILED);
	    		}
	    	}
	    	else
	    	{
	    		return $this->output(Response::WRONG_IMG_PATTERN);
	    	}
	    }
	
	}
