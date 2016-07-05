<?php
const PRE = '[greenhouse].[dbo].';//数据表前缀常量

//获取当前时间的季度
function getQuarterByMonth($date){
    $month = substr($date,-2);
    $Q = ceil($month/3);
    return $Q;
}
//获取一个月的开始和结束
function getMouth() 
{ 
	$time = time();
	$firstday = date('Y-m-01',$time); 
	$lastday  = date('Y-m-d H:i:s', strtotime("$firstday +1 month") - 1); 
	return array($firstday, $lastday); 
} 

//获取本周的开始和结束
function getWeek(){
	$date		= date('Y-m-d');  //当前日期
	$first 	  	= 1; //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期		
	$w			= date('w',strtotime($date));  //获取当前周的第几天 周日是 0 周一到周六是 1 - 6		
	$now_start 	= date('Y-m-d H:i:s',strtotime("$date -".($w ? $w - $first : 6).' days')); //获取本周开始日期，如果$w是0，则表示周日，减去 6 天		
	$now_end 	= date('Y-m-d H:i:s',strtotime("$now_start +7 days")-1);  //本周结束日期	
	return array($now_start, $now_end); 
}
//获取一周的所有日期
function getWeekAllDate($week = ''){ 
	if(empty($week))$week = date('Y-m-d',time());//当前日期 
	$whichD=date('w',strtotime($week));
	$weeks=array();
	for($i=0;$i<7;$i++){
		if($i<$whichD){
			$date=strtotime($week)-($whichD-$i)*24*3600;
		}else{
			$date=strtotime($week)+($i-$whichD)*24*3600;
		}
		$weeks[$i]=date('Y-m-d',$date);	 
	}
	return $weeks;	
}

//获取当前日期往前七天
function getSevenDate($time = ''){ 
	if(empty($time))$time = time();//当前日期 	
	$weeks=array();
	for($i=0;$i<7;$i++){
		if($i>0)$time -= 86400;
		$weeks[$i]=date('Y-m-d',$time);	 
	}
	return array_reverse($weeks);	
}

//获取月初到当前的日期  
function getMouthGoCurrent(){
	$time   = time();
	$m	    = date('Y-m',$time); 
	$mouths = array();	
	$i = 1;
	$d = date('d',$time);
	while($i <= $d){
		$mouths[] = $m.'-'.$i;
		$i++;
	}
	return $mouths;
}

function getMouthGoCurrent1(){
	
	for($i=0;$i<30;$i++){
		
		$mouths[$i]=date('Y-m-d',strtotime("-{$i} day"));	
		
	}
	return $mouths;
}

//清除省市自治区
function clearProvice($area){
	$area = str_replace('省','',$area);
	$area = str_replace('市','',$area);
	$area = str_replace('自治区','',$area);
	$area = str_replace('回族','',$area);
	$area = str_replace('维吾尔族','',$area);	
	$area = str_replace('壮族','',$area);
	$area = str_replace('特别行政区','',$area);
	return $area;	
}

//特殊省市
function speProvice($provice){	
	$arr = array(
			'宁夏回族自治区'=>'宁夏省',
			'西藏自治区'=>'西藏省',
			'新疆维吾尔自治区'=>'新疆维吾尔族自治区',			
			);	
	return $arr[$provice];
}


//清除style属性
/*function clearStyle($html){
	$html = preg_replace("/ style=\".*?\"/", '', $html); //过滤style标签
	$html = preg_replace('#src="/#is', 'src="http://www.danjiguanjia.com/', $html);  ;	
	//$html = preg_replace("/ width=\".*?\"/", '', $html); //过滤width标签	
	return $html;
}
*/

/**
 * 对查询结果集进行排序
 * http://www.onethink.cn
 * /Application/Common/Common/function.php
 *
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param string $sortby 排序类型 （asc正向排序 desc逆向排序 nat自然排序）
 * @return array
 */

/*function list_sort_by($list, $field, $sortby = 'asc')
{
	if (is_array($list))
	{
		//dump($list);
		$refer = $resultSet = array();
		foreach ($list as $i => $data)
		{
			$refer[$i] = &$data[$field];
		}
		switch ($sortby)
		{
			case 'asc': // 正向排序
				asort($refer);
				break;
			case 'desc': // 逆向排序
				arsort($refer);
				break;
			case 'nat': // 自然排序
				natcasesort($refer);
				break;
		}
		foreach ($refer as $key => $val)
		{
			$resultSet[] = &$list[$key];
		}
		return $resultSet;
	}
	return false;
}
*/
//处理数据
function handleData($data,$field1,$field2 = ''){
	foreach($data as $k=>$v){
		if($v['NoteDate']){
			$data[$k]['NoteDate'] = handleDate($v['NoteDate']);			
		}
		$data[$k][$field1] = sprintf("%.2f",$v[$field1]);
		if($field2)$data[$k][$field2] = sprintf("%.2f",$v[$field2]);
	}	
	return $data;
}

//处理日期
function handleDate($date){				
	if(is_object($date)){				
		$bj = object2array($date);
		$date = $bj['date'];
	}			
	$date = explode(' ',$date);			
	return $date[0];	
}

//base64转图片
function base64_to_img( $base64_string, $output_file ) {
   $ifp = fopen( $output_file, "wb" ); 
   fwrite( $ifp, base64_decode( $base64_string) ); 
   fclose( $ifp ); 
   return( $output_file ); 
}


//上传图片
function upload($base64_string){
	$savename = uniqid().'.jpeg';		 
	$savepath = 'Uploads/Picture/'.date('Y-m-d'); 
	$filename = $savepath.'/'.$savename;
	if(!is_dir($savepath))mkdir($savepath, 0777, true);//目录不存在则创建			 
	$base64_string = explode(',',$base64_string);			 
	$image = base64_to_img( $base64_string[1], $filename );	
	return $filename;
}



