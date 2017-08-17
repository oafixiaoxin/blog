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
	    
	    
	    public function uploadImage ( Request $request )
	    {
	    	$base64_str = $request->input('imgBase64');
	    	return $base64_str;
//	    	$base64_image = str_replace('', '+', $base64_str);
//	    	if ( preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image, $result) )
//	    	{
//	    		//匹配成功 
//	    		if ( $result[2] == 'jpeg' )
//	    		{
//	    			$image_name = uniqid().'.jpg';
//	    		}
//	    		else
//	    		{
//	    			$image_name = uniqid().'.'.$result[2];
//	    		}
//	    		return $image_name;
//	    	}
//	    	else
//	    	{
//	    		return false;
//	    	}
	    }
	
	}
