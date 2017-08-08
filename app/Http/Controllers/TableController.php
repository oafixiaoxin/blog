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
	    
	    
	    //搜索未在使用的桌子
	    public function searchEmptyTable ( $tableId )
	    {
	    	$result = DB::select('SELECT * FROM mantadia_tables WHERE 1=1 AND `status`=0 AND id=?', [$tableId]);
	    	if ( count($result) == 1 )
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
	    	$result = DB::select('SELECT ta.id,ta.name,ta.price,ta.note,tb.filename,tc.name AS menutypename FROM mantadia_menuitem ta
LEFT JOIN mantadia_image tb ON ta.imageid=tb.id 
LEFT JOIN mantadia_menutype tc ON ta.menutypeid=tc.id
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
	    
	    
	    //获取所有推荐的商品
	    public function getAllRecommand ()
	    {
	    	$result = DB::select('SELECT ta.id,ta.name,ta.price,ta.size,ta.`belong_who`,ta.`belong_area`,ta.`base_info`,tb.filename FROM mantadia_recommendation ta
left join mantadia_image tb on ta.`filename`=tb.id 
ORDER BY id ASC');
	    	if ( count($result) != 0 )
	    	{
	    		return $this->output(Response::SUCCESS, $result);
	    	}
	    	else
	    	{
	    		return $this->output(Response::NO_MORE_INFO);
	    	}
	    }
	    
	    
	    //获取推荐商品详情
	    public function getRecommandDetail ( $id )
	    {
	    	$result = DB::select('SELECT ta.id,ta.name,ta.price,ta.size,ta.`belong_who`,ta.`belong_area`,ta.`base_info`,tb.filename
FROM mantadia_recommendation ta
LEFT JOIN mantadia_image tb ON ta.`filename`=tb.`id`
WHERE 1=1 AND ta.`id`=?', [$id]);
	    	if ( count($result) != 0 )
	    	{
	    		return $this->output(Response::SUCCESS, $result);
	    	}
	    	else
	    	{
	    		return $this->output(Response::NO_MORE_INFO);
	    	}
	    }
	    
	    
	    //提交订单给厨房
	    public function sendOrder ( Request $request )
	    {
	    	$selectedMenu = $request->input('selectedMenu');
	    	$tableId = $request->input('tableId');
	    	$mealNumber = $request->input('mealNumber');
	    	$time = $request->input('time');
	    	
	    	DB::beginTransaction();
	    	DB::table('mantadia_tables')->where('id', $tableId)->update(['status' => 1]);
//	    	DB::commit();
			DB::rollback();
	    	
	    	return $this->output(Response::SUCCESS, 1);
	    	
	    }

	    
	}

