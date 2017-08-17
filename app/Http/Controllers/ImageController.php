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
//	    	return $this->output(Response::SUCCESS, $base64_str);
//	    	$base64_image = str_replace('', '+', $base64_str);
	    	if ( preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_str, $result) )
	    	{
	    		//匹配成功 
	    		if ( $result[2] == 'jpeg' )
	    		{
	    			$image_name = time().'.jpg';
	    		}
	    		else
	    		{
	    			$image_name = time().'.'.$result[2];
	    		}
	    		return $this->output(Response::SUCCESS, $image_name);
	    	}
	    	else
	    	{
	    		return $this->output(Response::NO_MORE_INFO);
	    	}
	    }
	
	}
