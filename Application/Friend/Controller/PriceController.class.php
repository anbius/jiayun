<?php
namespace Api\Controller;
use Think\Controller;
header("Access-Control-Allow-Origin:*");
/* 价格管理 */
class PriceController extends ApiController {  
	//定义属性
	private $area = '';
	private $type = 0;
	public function __construct(){
		//获取地区
		$this->area  = I('area')?I('area'):'';//设置地区
		//$this->area  = clearProvice($this->area);
		$t_area      = speProvice($this->area); //过滤
		if($t_area)	$this->area = $t_area;
		
		//获取原料类型
		$this->type  = I('type')?I('type'):0;//设置原材料0为玉米、1为豆粕
	}
    public function index(){ 
		$region_mod = M('region','eg_','mysql://root:root@localhost/egg_admin'); 
		$region = $region_mod->where("parent_id = 1")->select();  
		$str = '';
		foreach($region as $v){
			$str .= ",'{$v['region_name']}':true";	
		}   
		echo $str;
    }
	/******************	鸡蛋价格 *********************/		
	//鸡蛋的当日价格
	public function getPrice(){
		if(!S('dayPrice')){				
			$zt    = date("Y-m-d",strtotime("-1 day"));//昨天的日期
			
			$dw = '元/斤';
			//当日价格
			$t = D('jm_eggprice')->getEggDayAvg('',$this->area);	
			if(is_null($t))$t = D('jm_eggprice')->getEggDayAvg($zt,$this->area);
			$data['egg']['d'] = sprintf("%.2f",$t).$dw;
			
			//周价格
			$t = D('jm_eggprice')->getWeekAvgPrice($this->area);
			$data['egg']['w'] = sprintf("%.2f",$t).$dw;
			
			//月价格
			$t = D('jm_eggprice')->getEggMouthAvgPrice($this->area);
			$data['egg']['m'] = sprintf("%.2f",$t).$dw;
			
			
			$dw = '元/吨';
			//玉米价格
			$t = D('jm_Feedstock')->getDayAvg('',$this->area);
			if(is_null($t))$t = D('jm_Feedstock')->getDayAvg($zt,$this->area);
			$data['corn']['d'] = sprintf("%.2f",$t).$dw;
			
			$t = D('jm_Feedstock')->getWeekAvg('',$this->area);
			$data['corn']['w'] = sprintf("%.2f",$t).$dw;
			
			$t = D('jm_Feedstock')->getMouthAvgPrice($this->area);
			$data['corn']['m'] = sprintf("%.2f",$t).$dw;
			
			//豆粕价格
			$t = D('jm_Feedstock')->getDayAvg('',$this->area,1);	
			if(is_null($t))$t = D('jm_Feedstock')->getDayAvg($zt,$this->area,1);
			$data['bean']['d'] = sprintf("%.2f",$t).$dw;
			
			$t = D('jm_Feedstock')->getWeekAvg('',$this->area,1);
			$data['bean']['w'] = sprintf("%.2f",$t).$dw;
			
			$t = D('jm_Feedstock')->getMouthAvgPrice($this->area,1);
			$data['bean']['m'] = sprintf("%.2f",$t).$dw;
			S('dayPrice',$data,3600);
		}
		
		\Org\Response::show(2000,'获取成功!',S('dayPrice'));			
	}
	
	//获取当日鸡蛋价格
	public function getDayEggPrice(){
		//当日价格
		$t = D('jm_eggprice')->getEggDayAvg('',$this->area);		
		if(is_null($t))$t = D('jm_eggprice')->getEggDayAvg(date('Y-m-d',strtotime('-1 day')),$this->area);
		$data = sprintf("%.2f",$t);
		\Org\Response::show(2000,'获取成功!',$data);	
	}
	
	//一周所有平均价
	public function eggWeekPrice(){			
		$data = D('jm_eggprice')->getEggWeekPrice($this->area);		
		\Org\Response::show(2000,'获取成功!',$data);	
	}
		
	//月所有平均价
	/*public function eggMouthPrice(){				
		$data = D('jm_eggprice')->getEggMouthPrice($this->area);
		\Org\Response::show(2000,'获取成功!',$data);	
	}	*/
	public function eggMouthPrice(){				
		$data = D('jm_eggprice')->getEggMouthPrice($this->area);
		\Org\Response::show(2000,'获取成功!',$data);	
	}	
	//当前年平均价
	public function eggYearPrice(){	
		$m = date('m');
		/*if($m == 1){
			$data = D('jm_eggprice')->getEggMouthPrice($this->area);	
		}elseif($m < 4){
			$data = D('jm_eggprice')->getQuarterEggAvg($this->area);	
		}else{
			$data = D('jm_eggprice')->getMouthEggAvg();		
		}	*/		
			$data = D('jm_eggprice')->getMouthEggAvg($this->area);		
		\Org\Response::show(2000,'获取成功!',$data);
	}
	//当前季度平均价
	public function eggQuarterPrice(){		
		$data = D('jm_eggprice')->getQuarterEggAvg($this->area);	
		\Org\Response::show(2000,'获取成功!',$data);
	}	
		
	
	////////////////////////////////////////////////////
	//蛋价地图
	public function eggAreaPrice(){				
		$data = D('jm_eggprice')->getEggAreaPrice($this->area);	
		if($this->area == ''){
			foreach($data as $key=>$vo){
				$data[$key]['name'] = clearProvice($vo['name']);	
			}
		}
		
		$data = '{"retcode":2000,"data":'.json_encode($data).'}';		
		
		echo $data;			
	}
	public function eggAreaPriceDjcy(){				
		$data = D('jm_eggprice')->getEggAreaPrices($this->area);	
		if($this->area == ''){
			foreach($data as $key=>$vo){
				$data[$key]['name'] = clearProvice($vo['name']);	
			}
		}
		
		$data = '{"retcode":2000,"data":'.json_encode($data).'}';		
		
		echo $data;			
	}
	//
	public function eggAreaTrPrice(){	
		$data = D('jm_eggprice')->getEggAreaTrPrice($this->area);		
		$data = handleData($data,'value');
		$data = '{"retcode":2000,"data":'.json_encode($data).'}';
		echo $data;		
	}
	
	//省市蛋价排序
	public function eggSortPrice(){	
		$province = D('jm_eggprice')->getEggAreaPrice();
		if(empty($province))\Org\Response::show(4000,'',array());
		$province = list_sort_by($province, 'value', 'desc');		
		$data['province']['data'] = $province;//所有省份		
		$data['province']['first'] = $province[0];
		$data['province']['end'] = end($province);
		
		$city = D('jm_eggprice')->getPriceCity();
		
					
		$city = list_sort_by($city, 'value', 'desc');
		
					
		$data['city']['first'] = $city[0];
		$data['city']['end'] = end($city);
		
		\Org\Response::show(2000,'获取成功!',$data);
	}
	
	
	
	/******************	玉米和豆粕价格 *********************/	
	//一周所有平均价
	public function weekPrice(){		
		$data = D('jm_Feedstock')->getWeekPrice($this->area,$this->type);
		\Org\Response::show(2000,'获取成功!',$data);
	}	
	
	//月平均价
	public function mouthPrice(){	
		$data = D('jm_Feedstock')->getMouthPrice($this->area,$this->type);
		\Org\Response::show(2000,'获取成功!',$data);	
	}
	
	//当前年平均价
	public function yearPrice(){		
		//获取月时间
		$data = D('jm_Feedstock')->getMouthAvg('',$this->area,$this->type);			
		\Org\Response::show(2000,'获取成功!',$data);	
	}
	//当前季度平均价
	public function quarterPrice(){		
		//获取月时间
		$data = D('jm_Feedstock')->getQuarterAvg($this->area,'',$this->type);	
		\Org\Response::show(2000,'获取成功!',$data);	
	}
	
	/******************	提交报价 *********************/	
	//鸡蛋价格
	public function submitEggPrice(){	
		$uid = I('uid');
		$id  = I('id');//鸡蛋ID
		if(empty($uid))\Org\Response::show(4000,'参数缺失!');	
		$data['EggSalePrice'] = I('price');//添加时间
		if($data['EggSalePrice'] > 20 || $data['EggSalePrice'] < 0)\Org\Response::show(4002,'价格参数不合法!');	
		$data['NoteDate'] 	  = I('note_date');//选定时间
		$data['DayContent']   = I('note');
		$area = explode(',',I('area'));//地区
		$data['Province'] = $area[0];
		$data['City'] 	  = $area[1];
		$data['County']   = $area[2];
						
		$map['id'] = array('eq',$uid);
		$data['BaseNum']	  = D('dt_manager')->where($map)->getField('user_name');
		if($id){			
			$rs = D('jm_eggprice')->where("id = $id")->save($data);			
		}else{
			$rs = D('jm_eggprice')->add($data);		
		}		
		if($rs){
			\Org\Response::show(2000,'编辑成功!');	
		}else{
			\Org\Response::show(4001,'失败!');
		}				
	}
	//获取报价
	public function getEggPrice(){
		$id  = I('id');//鸡蛋ID	
		$map['Id'] = array('eq',$id);
		$rs = D('jm_eggprice')->where($map)->find();		
		$rs['NoteDate']	 = handleDate($rs['NoteDate']);
		\Org\Response::show(2000,'',$rs);	
	}
	
	//获取鸡蛋价格
	public function getAllEggPrice(){
		$uid = I('id');
		if(empty($uid))\Org\Response::show(4000,'参数缺失!');
		$umap['id'] = array('eq',$uid);
		$user  = D('dt_manager')->where($umap)->getField('user_name');
		
		$stime = I('starttime');
		$etime = I('endtime');
		$map['BaseNum']  = array('eq',$user);
		//$map['BaseNum']  = array('eq',$user);
		if($stime && $etime){
			$map['NoteDate'] = array('between',array($stime,$etime));
		}elseif($stime && empty($etime)){			
			$map  = "datediff(day,[AddTime],'{$stime}' )= 0";
		}elseif(empty($stime) && $etime){			
			$map  = "datediff(day,[AddTime],'{$etime}' )= 0";	
		}
		$data['data']  = $this->lists(D('jmEggprice'),$map,'id desc','id,NoteDate,Province,EggSalePrice');		
		$data['data'] = handleData($data['data'],'eggsaleprice');
		
		$data['total'] = $this->total;
		\Org\Response::show(2000,'获取成功!',$data);			
	}
	

	//删除价格
	public function deleteEgg(){
		$ids = rtrim(I('ids'),',');
		if(empty($ids))\Org\Response::show(4000,'参数缺失!');
		$rs = D('jmEggprice')->where("id in ({$ids})")->delete();
		\Org\Response::show(2000,'删除成功!',$rs);			
	}
	
	
	//原料价格
	public function submitMatePrice(){	
		$uid = I('uid');
		$id  = I('id');
		if(empty($uid))\Org\Response::show(4000,'参数缺失!');	
		$data['CornPrice'] = I('price1');//价格
		$data['BeanPrice'] = I('price2');//价格
		$data['NoteDate'] 	  = I('note_date');//选定时间
		$data['DayContent']   = I('note');
		$area = explode(',',I('area'));//地区
		$data['Province'] = $area[0];
		$data['City'] 	  = $area[1];
		$data['County']   = $area[2];
						
		$map['id'] = array('eq',$uid);
		$data['BaseNum']	  = D('dt_manager')->where($map)->getField('user_name');
		if($id){			
			$rs = D('jmFeedstock')->where("id = $id")->save($data);			
		}else{
			$rs = D('jmFeedstock')->add($data);		
		}
		
		if($rs){
			\Org\Response::show(2000,'成功!');	
		}else{
			\Org\Response::show(4001,'失败!');
		}		
	}
	
	//获取报价
	public function getMatePrice(){
		$id  = I('id');	
		$map['Id'] = array('eq',$id);
		$rs = D('jmFeedstock')->where($map)->find();		
		$rs['NoteDate']	 = handleDate($rs['NoteDate']);
		\Org\Response::show(2000,'',$rs);	
	}
	
	//获取原料价格
	public function getAllMatePrice(){
		$uid = I('id');
		if(empty($uid))\Org\Response::show(4000,'参数缺失!');
		$umap['id'] = array('eq',$uid);
		$user  = D('dt_manager')->where($umap)->getField('user_name');	
		
		$stime = I('starttime');
		$etime = I('endtime');
		$map['BaseNum']  = array('eq',$user);
		if($stime && $etime)$map['NoteDate'] = array('between',array($stime,$etime));
		$data['data']  = $this->lista(D('jmFeedstock'),$map,'id desc','id,NoteDate,Province,CornPrice,BeanPrice');		
		$data['data'] = handleData($data['data'],'CornPrice','BeanPrice');
		$data['total'] = $this->total;
		\Org\Response::show(2000,'获取成功!',$data);			
	}
	//删除价格
	public function deleteMate(){
		$ids = rtrim(I('ids'),',');
		if(empty($ids))\Org\Response::show(4000,'参数缺失!');
		$rs = D('jmFeedstock')->where("id in ({$ids})")->delete();
		\Org\Response::show(2000,'删除成功!',$rs);			
	}
	
	
}