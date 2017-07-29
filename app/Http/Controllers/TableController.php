<?php
	namespace App\Http\Controllers;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Http\Request;
	use App\Response;
	
	class TableController extends Controller
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
	    
	    //获取所有未在使用的桌子列表
	    public function getEmptyTables ()
	    {
	    	$result = DB::select('SELECT * FROM mantadia_tables WHERE 1=1 AND `status`=0');
	    	if ( count($result) != 0 )
	    	{
	    		return $this->output(Response::SUCCESS, $result);
	    	}
	    	else
	    	{
	    		return $this->output(Response::NO_MORE_INFO);
	    	}
	    }


	    //获取所有的menu_type
	    public function getAllMenuType ()
	    {
			$result = DB::select('SELECT * FROM mantadia_menutype');
			if ( count($result) != 0 )
			{
				return $this->output(Response::SUCCESS, $result);
			}
			else
			{
				return $this->output(Response::NO_MORE_INFO);
			}
	    }
	    
	    
	    //根据menu_type_id获取所有menu_item
	    public function getMenuItem( $menuTypeId )
	    {
//	    	$menu_type_id = $request->input('menuTypeId');
	    	$result = DB::select('SELECT ta.id,ta.name,ta.price,ta.note,tb.filename FROM mantadia_menuitem ta
LEFT JOIN mantadia_image tb ON ta.imageid=tb.id 
WHERE 1=1 AND ta.menutypeid=?
ORDER BY ta.id ASC', [$menuTypeId]);
			if ( count($result) != 0 || !isset($result) )
			{
				return $this->output(Response::SUCCESS, $result);
			}
			else
			{
				return $this->output(Response::NO_MORE_INFO);
			}
	    }

	    
	}

