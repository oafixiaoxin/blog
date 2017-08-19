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
	    	$dateStr = date("Y-m");
	    	$result = DB::select('SELECT ta.id,ta.name,ta.price,ta.note,tb.filename,tc.name AS menutypename,IFNULL(td.month_sale, 0) AS month_sale FROM mantadia_menuitem ta
LEFT JOIN mantadia_image tb ON ta.imageid=tb.id 
LEFT JOIN mantadia_menutype tc ON ta.menutypeid=tc.id
LEFT JOIN (SELECT ta.*,SUM(ta.number) AS month_sale,tb.time FROM mantadia_orderitem ta
LEFT JOIN mantadia_orders tb ON ta.ordersid=tb.id
WHERE 1=1 AND tb.time LIKE "'.$dateStr.'%"
GROUP BY ta.menuitemid) td ON ta.id=td.menuitemid
WHERE 1=1 AND ta.menutypeid='.$menuTypeId.'
ORDER BY ta.id ASC');
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
	    	$selectedMenu = json_decode($request->input('selectedMenu'));
	    	$tableId = $request->input('tableId');
	    	$mealNumber = $request->input('mealNumber');
	    	$time = $request->input('time');
	    	
	    	DB::beginTransaction();
//	    	DB::table('mantadia_tables')->where('id', $tableId)->update(['status' => 1]);
			//开台
			$updateTableEffects = DB::update('update mantadia_tables set `status`=1 where 1=1 and `id`=?', [$tableId]);
			if ( isset($updateTableEffects) )
			{
				$id;
				//查看是否存在订单
				$orderId = DB::table('mantadia_orders')->where('tablesid', $tableId)->where('status', 0)->value('id');
				//不存在就新增订单,并获取新增的订单id
				if ( !isset($orderId) )
				{
					$id = DB::table('mantadia_orders')->insertGetId(
					[
						'time' => $time,
						'userid' => 0,
						'tablesid' => $tableId,
						'number' => $mealNumber,
						'status' => 0
					]);
				}
				//存在订单就获取订单Id进行接下来的操作
				else
				{
					$id = DB::table('mantadia_orders')->where('tablesid', $tableId)->where('status', '>=', '0')->where('status', '<>', '3')->value('id');
				}
				if ( isset($id) )
				{
					for ( $i = 0 ; $i < count($selectedMenu) ; $i++ )
					{
						$orderMenuItemId = DB::table('mantadia_orderitem')->where('ordersid', $id)->where('menuitemid', $selectedMenu[$i]->id)->value('id');
						//不存在就新增
						if ( !isset($orderMenuItemId) )
						{
							$id1 = DB::table('mantadia_orderitem')->insertGetId(
							[
								'ordersid' => $id,
								'menuitemid' => $selectedMenu[$i]->id,
								'number' => $selectedMenu[$i]->number,
								'status' => 0
							]);
							//新增失败就回滚
							if ( !isset($id1) )
							{
								DB::rollback();
								return $this->output(Response::WRONG_OPERATION);
							}
						}
						//存在就更新
						else
						{
							DB::table('mantadia_orderitem')->where('ordersid', $id)->where('menuitemid', $selectedMenu[$i]->id)->update(['number' => $selectedMenu[$i]->number]);
						}
					}
					DB::commit();
					return $this->output(Response::SUCCESS, $id);
				}
				else
				{
					DB::rollback();
					return $this->output(Response::WRONG_OPERATION);
				}
			}
			else
			{
				DB::rollback();
				return $this->output(Response::WRONG_OPERATION);
			}
	    }
	    
	    
	    //手机端登录
	    public function login ( $loginName, $loginPassword )
	    {
	    	$judgeLoginName = DB::select('select * from mantadia_user where 1=1 and `account`=?', [$loginName]);
	    	if ( isset($judgeLoginName) ) 
	    	{
	    		$userId = DB::table('mantadia_user')->where('account', $loginName)->where('password', $loginPassword)->value('id');
	    		if ( isset($userId) )
	    		{
	    			return $this->output(Response::SUCCESS, $userId);
	    		}
	    		else
	    		{
	    			return $this->output(Response::PASSWORD_INCORRECT);
	    		}
	    	}
	    	else
	    	{
	    		return $this->output(Response::USER_NOT_FOUND);
	    	}
	    }
	    
	    
	    //获取用户信息
	    public function getUserInfo ( $userId )
	    {
	    	$userInfo = DB::table('mantadia_user')->where('id', $userId)->first();
	    	if ( isset($userInfo) )
	    	{
	    		return $this->output(Response::SUCCESS, $userInfo);
	    	}
	    	else
	    	{
	    		return $this->output(Response::NO_MORE_INFO);
	    	}
	    }
	    
	    
	    //获取所有桌子信息
	    public function getAllTable( $type )
	    {
	    	if ( $type == 3 )
	    	{
	    		$result = DB::select('SELECT ta.*,IFNULL((SELECT number FROM mantadia_orders WHERE 1=1 AND `status`<>3 AND tablesid=ta.id), 0) AS meal_number FROM mantadia_tables ta');
	    	}
	    	else
	    	{
	    		$result = DB::select('SELECT ta.*,IFNULL((SELECT number FROM mantadia_orders WHERE 1=1 AND `status`<>3 AND tablesid=ta.id), 0) AS meal_number FROM mantadia_tables ta WHERE 1=1 AND ta.`type`=:type', ['type' => $type]);
	    	}
	    	
	    	if ( count($result) )
	    	{
	    		return $this->output(Response::SUCCESS, $result);
	    	}
	    	else
	    	{
	    		return $this->output(Response::NO_MORE_INFO);
	    	}
	    }
	    
	    
	    //搜索菜单 
	    public function searchMenuItem ( $regex )
	    {
//	    	return $this->output(Response::SUCCESS, urldecode($regex));
//	    	$result = DB::select('select * from mantadia_menuitem where 1=1 and `name` like "%'.urldecode($regex).'%"');
			$result = DB::table('mantadia_menuitem')->where('name', 'like', '%'.urldecode($regex).'%')->get();
	    	if ( count($result) != 0 )
	    	{
	    		return $this->output(Response::SUCCESS, $result);
	    	}
	    	else
	    	{
	    		return $this->output(Response::NO_MORE_INFO);
	    	}
	    }
	    
	    
	    //获取所有正在作用的餐桌，phone端
	    public function getUsingTable ( $type )
	    {
	    	if ( $type == 3 )
	    	{
	    		$result = DB::select('SELECT ta.*,tb.id AS orderId,tb.number AS meal_number,SUM(tc.number*td.price) AS orderTotalPrice
FROM mantadia_tables ta
LEFT JOIN mantadia_orders tb ON ta.id=tb.tablesid
LEFT JOIN mantadia_orderitem tc ON tb.id=tc.ordersid
LEFT JOIN mantadia_menuitem td ON tc.menuitemid=td.id
WHERE 1=1 AND ta.`status`=1 AND tb.status<>3
GROUP BY ta.id
ORDER BY ta.id ASC');	
	    	}
	    	else
	    	{
	    		$result = DB::select('SELECT ta.*,tb.id AS orderId,tb.number AS meal_number,SUM(tc.number*td.price) AS orderTotalPrice
FROM mantadia_tables ta
LEFT JOIN mantadia_orders tb ON ta.id=tb.tablesid
LEFT JOIN mantadia_orderitem tc ON tb.id=tc.ordersid
LEFT JOIN mantadia_menuitem td ON tc.menuitemid=td.id
WHERE 1=1 AND ta.`status`=1 AND tb.status<>3 AND ta.type=:type
GROUP BY ta.id
ORDER BY ta.id ASC', ['type' => $type]);
	    	}
	    	
	    	if ( count($result) != 0 )
	    	{
	    		return $this->output(Response::SUCCESS, $result);
	    	}
	    	else
	    	{
	    		return $this->output(Response::NO_MORE_INFO);
	    	}
	    }
	    
	    
	    //获取所有未在使用的桌子列表,phone端
	    public function getEmptyTablesByType ( $type )
	    {
	    	if ( $type == 3 )
	    	{
	    		$result = DB::select('SELECT * FROM mantadia_tables WHERE 1=1 AND `status`=0');
	    	}
	    	else
	    	{
	    		$result = DB::select('SELECT * FROM mantadia_tables WHERE 1=1 AND `status`=0 AND `type`=:type', ['type' => $type]);
	    	}
	    	if ( count($result) != 0 )
	    	{
	    		return $this->output(Response::SUCCESS, $result);
	    	}
	    	else
	    	{
	    		return $this->output(Response::NO_MORE_INFO);
	    	}
	    }
	    
	    
	    //换桌，phone端 
	    /*
	     * origin: 原桌号
	     * target: 目标桌号
	     * orderId: 订单id
	     */
	    public function changeTable ( Request $request )
	    {
	    	$origin = $request->input('origin');
	    	$target = $request->input('target');
	    	$orderId = $request->input('orderId');
	    	//开启事务
	    	DB::beginTransaction();
	    	$updateTableEffects = DB::update('UPDATE mantadia_tables SET `status`=0 WHERE 1=1 AND `id`=:origin', ['origin' => $origin]);
	    	if ( $updateTableEffects == 1 )
	    	{
	    		$updateTableEffects_1 = DB::update('UPDATE mantadia_tables SET `status`=1 WHERE 1=1 AND `id`=:target', ['target' => $target]);
	    		if ( $updateTableEffects_1 == 1 )
	    		{
	    			$updateTableEffects_2 = DB::update('UPDATE mantadia_orders SET `tablesid`=:target WHERE 1=1 AND `id`=:orderId', ['target' => $target, 'orderId' => $orderId]);
	    			if ( $updateTableEffects_2 == 1 )
	    			{
	    				DB::commit();
	    				return $this->output(Response::SUCCESS);
	    			}
	    			else
	    			{
	    				DB::rollback();
	    				return $this->output(Response::WRONG_OPERATION);
	    			}
	    		}
	    		else
	    		{
	    			DB::rollback();
	    			return $this->output(Response::WRONG_OPERATION);
	    		}
	    	}
	    	else
	    	{
	    		DB::rollback();
	    		return $this->output(Response::WRONG_OPERATION);
	    	}
	    }

	    
	}

