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
	    
	    private $file_path = '/www/wwwroot/image/upload/';
	    
	    public function __construct()
	    {
	        //
	    }
	    
	    
	    //uniqid()函数基于以微秒计的当前时间，生成一个唯一的 ID。
	    public function uploadImage ( Request $request )
	    {
	    	$base64_str = $request->input('imgBase64');
	    	$userId = $request->input('userId');
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
	 
	    		if ( !file_exists($this->file_path.date('Ymd',time()).'/') )
	    		{
    				mkdir($this->file_path.date('Ymd',time()).'/', 0700);
	    		}
	    		
	    		$filepath = $this->file_path.date('Ymd',time()).'/'.$image_name;
	    		
	    		if ( file_put_contents($filepath, base64_decode(str_replace($result[1], '', $base64_str))) )
	    		{
	    			$id = DB::table('mantadia_image')->insertGetId([
	    				'filename' => $image_name
	    			]);
	    			if ( isset($id) )
	    			{
	    				$result = DB::update('update mantadia_user set `imageid`=? where 1=1 and `id`=?', [$id, $userId]);
	    				if ( isset($result) )
	    				{
	    					return $this->output(Response::SUCCESS, $image_name);
	    				}
	    				else
	    				{
	    					return $this->output(Response::WRONG_OPERATION);
	    				}
	    			}
	    			else
	    			{
	    				return $this->output(Response::WRONG_OPERATION);
	    			}
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
