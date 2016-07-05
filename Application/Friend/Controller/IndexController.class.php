<?php
namespace Api\Controller;
use Think\Controller;
class IndexController extends Controller {
	
	//获取物联网数据
    public function getIOT(){
		$data['account']  = '北京伟嘉';
		$data['Password'] = md5('151111');
		
		$url = 'http://wlwhkq.loongk.com:8080/langrh/mobile/mobile!login.action';	
		$xml  = curl_file_post_contents($url,$data);
		$rs   = xmltoarray($xml);
		//dump($rs);	
		/*//获取ticket及舍栏编号gatewayid
		$jData['ticket'] = $rs["Ticket"];
		//$t = preg_match_all('/<Gateway\Id="mydiary([0-9]+)"\s>/',$str,$arr);
		preg_match_all('/<\s*Gateway\s+[^>]*?Id\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i',$rs["Gateways"],$arr); 
		$ids = $arr[2];*/
		
		//获取ticket及gatewayid
		$jData['ticket'] 	= $rs["Ticket"];
		$jData['gatewayid'] = '243B4431B250B0D6E050740AC6737897';
		
		$url = 'http://wlwhkq.loongk.com:8080/langrh/mobile/mobile!getShackStatusAndDatas.action';	
		$xml  = curl_file_post_contents($url,$jData);
		$rs   = xmltoarray($xml);
		
		
		//获取温度等数据
		preg_match_all('/<\s*Sensor\s+[^>]*?Val\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i',$rs["SensorDatas"],$arr); 
		$data = $arr[2];
		
		$iot   = '';//物联网数据
		$iot['TEMPERATURE'] = $data[0];//温度
		$iot['HUMIDITY'] 	= $data[2];//湿度
		$iot['BRIGHTENESS'] = $data[3];//光照
		$iot['AMMONIA'] 	= $data[4];//氨气		
		\Org\Response::show(2000,'获取成功!',$iot);
    }		
	
	//获取物联网数据
    public function getIOTtes(){
		$data['account']  = '北京伟嘉';
		$data['Password'] = md5('151111');
		
		$url = 'http://wlwhkq.loongk.com:8080/langrh/mobile/mobile!login.action';	
		$xml  = curl_file_post_contents($url,$data);
		$rs   = xmltoarray($xml);
		dump($rs);
		//dump($rs);	
		//获取ticket及舍栏编号gatewayid
		$jData['ticket'] = $rs["Ticket"];
		$jData['gatewayid'] = '243B3B9DD513C223E050740AC67372EF';
		//$t = preg_match_all('/<Gateway\Id="mydiary([0-9]+)"\s>/',$str,$arr);
		/*preg_match_all('/<\s*Gateway\s+[^>]*?Id\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i',$rs["Gateways"],$arr); 
		$ids = $arr[2];*/
		
		$url = 'http://wlwhkq.loongk.com:8080/langrh/mobile/mobile!config.action';	
		$xml  = curl_file_post_contents($url,$jData);
		$rs   = xmltoarray($xml);
		dump($rs);
		
		
		$url = 'http://wlwhkq.loongk.com:8080/langrh/mobile/mobile!getShackStatusAndDatas.action';	
		$xml  = curl_file_post_contents($url,$jData);
		$rs   = xmltoarray($xml);
		dump($rs);
		//获取ticket及gatewayid
		/*$jData['ticket'] 	= $rs["Ticket"];
		$jData['gatewayid'] = '243B4431B250B0D6E050740AC6737897';
		
		$url = 'http://wlwhkq.loongk.com:8080/langrh/mobile/mobile!getShackStatusAndDatas.action';	
		$xml  = curl_file_post_contents($url,$jData);
		$rs   = xmltoarray($xml);
		
		
		//获取温度等数据
		preg_match_all('/<\s*Sensor\s+[^>]*?Val\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i',$rs["SensorDatas"],$arr); 
		$data = $arr[2];
		
		$iot   = '';//物联网数据
		$iot['TEMPERATURE'] = $data[0];//温度
		$iot['HUMIDITY'] 	= $data[2];//湿度
		$iot['BRIGHTENESS'] = $data[3];//光照
		$iot['AMMONIA'] 	= $data[4];//氨气	
		dump($iot);	*/
		\Org\Response::show(2000,'获取成功!',$iot);
    }	
	
	/*反馈意见*/
	public function feedback(){
		//header("Access-Control-Allow-Origin: *");
		$data['title'] = I('title'); 
		$data['content'] = I('content'); 
		if(empty($data['title']))\Org\Response::show(4001,'标题不能为空!');
		if(empty($data['content']))\Org\Response::show(4002,'内容不能为空!');
		$rs = D('dt_feedback')->add($data);
		if($rs){
			\Org\Response::show(2000,'提交成功!');	
		}else{
			\Org\Response::show(4000,'提交失败!');		
		}
	}
	
	/*反馈意见*/
	public function aboutus(){
		$data = array(
					 'url'=>'http://www.danjiguanjia.com',
					 'content'=>'蛋鸡管家是全国蛋鸡产业信息化服务平台，是一款蛋鸡场网络化生产管理软件平台。它以数据指导生产，以数据聚向管理，以提升管理水平，从而实现蛋鸡效益提升。利用该平台，用户便可随时随地查询、监督、提升本企业的生产经营状况；该平台将形成蛋鸡行业的大数据，利用大数据，可提供行业分析周报，进行市场价格预测，实施数据增值服务。系统主要包括：蛋鸡头条、嘉云检测、行业会议、微日报、蛋鸡地图、鸡蛋报价、蛋价曲线、原料价格、原料曲线、查询统计等。',
					 'icon'=>'http://jianong.com/single/download/images/icon.png'
						);
		\Org\Response::show(2000,'获取成功!',$data);
	}
	
	/*******投票********************/
	public function vote(){
		$map['cms_id'] = array('eq',1);
		$vote = D("vote")->where($map)->find();
		
		//投票是否结束
		$end_time = strtotime($vote["cms_expireddate"]);		
		if($end_time<time())\Org\Response::show(4000,'投票已结束!');	
		
		//获取投票的子集
		$smap['voteid'] = array('eq',$vote['cms_id']);
		$vote['s'] = D('voteitem')->where($smap)->select();
		
		\Org\Response::show(2000,'获取成功!',$vote);	
	}
	
	//更改数据
	public function voteUpdate(){
		$id = I('id');
		if(empty($id))\Org\Response::show(4000,'数据缺失!');
		$map['id'] = array('eq',$id);
		$rs   = D('voteitem')->where($map)->setInc('voteScore');		
		if($rs){
			$smap['voteid'] = array('eq',1);
			$vote = D('voteitem')->field('voteScore')->where($smap)->select();
			
			\Org\Response::show(2000,'增加成功!',$vote);	
		}else{
			\Org\Response::show(4001,'增加失败!');
		}			
	}
	
	//获取嘉云检测
	public function submitVetins(){
		header("Access-Control-Allow-Origin: *");
		//$str = $_POST;
		$Vetins = M('vetins','dj_','mysql://root:root@localhost/danjiguanjia');
		$Vetins->create($_POST);
		$Vetins->add();
		\Org\Response::show(2000,'提交成功!');
	}
	
	//获取保险
	public function submitInsurance(){
		//header("Access-Control-Allow-Origin: *");
		//$str = $_POST;
		//logDebug(json_encode($_POST));
		$insurance = M('insurance','dj_','mysql://root:root@localhost/danjiguanjia');
		$data = $insurance->create($_POST);
		if($data['identity_card_img'])$data['identity_card_img'] = upload($data['identity_card_img']);
		if($data['house_door_img'])$data['house_door_img'] = upload($data['house_door_img']);
		if($data['house_img'])$data['house_img'] = upload($data['house_img']);
		if($data['house_inner_img'])$data['house_inner_img'] = upload($data['house_inner_img']);
		if($data['house_flat_img'])$data['house_flat_img'] = upload($data['house_flat_img']);
		$data['add_time'] = time();
		logDebug($data);
		$insurance->add($data);
		\Org\Response::show(2000,'提交成功!');
	}
	
	//获取版本号
	public function getVersion(){
		$device = I('device')?I('device'):'android';
		$sign   = I('sign')?I('sign'):'jianong';
		$data = array(
					'versionName'=>'1.9.0',//版本号
					'versionCode'=>90,
					'download'=>'http://www.jianong.com:81/download/',//下载地址
					'content'=>'1、增加即时通讯 2、UI调整 3、修复bug',
					'size'=>'19.3M',
					'length'=>20208949,
					'must'=>true//是否必须更新
				);
		switch($device){
			case 'ios':
				/*$data['version'] = '';*/			
				$data['download'] = 'https://itunes.apple.com/cn/app/dan-ji-guan-jia/id1078970003';				
				break;	
			case 'android':
				/*$data['version'] = '';
				$data['download'] = '';*/
				
				break;
			case 'web':
				/*$data['version'] = '';
				$data['download'] = '';*/
				break;
		}
		
		\Org\Response::show(2000,'',$data);	
	}
	
}