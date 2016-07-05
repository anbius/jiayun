<?php

namespace Api\Controller;
use Think\Controller;
class ApiController extends Controller {
	public $page  = 0;
	public $total = 0;
    /**
     * 通用分页列表数据集获取方法
     * @param sting|Model  $model   模型名或模型实例
     * @param array        $where   where查询条件(优先级: $where>$_REQUEST>模型设定)
     * @param array|string $order   排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
     *                              请求参数中如果指定了_order和_field则据此排序(优先级第二);
     *                              否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
     * @param boolean      $field   单表模型用不到该参数,要用在多表join时为field()方法指定参数 
     * @return array|false
     * 返回数据集
     */
    protected function lists ($model,$where="",$order='',$field=""){			
        $total              = $model->where($where)->count();//获得总数
        $listRows           = I("pages")?I("pages"):10;//每页数 
        $page               = new \Think\Page($total, $listRows);
        $limit              = $page->firstRow.','.$page->listRows;
		
		$data = $model->field($field)->where($where)->limit($limit)->order($order)->select();		
		$this->total =  $total;
		//dump($data);
        return $data;
    }
	
	/**
     * 通用分页列表数据集获取方法
     *
     *  可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
     *  可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
     *  可以通过url参数r指定每页数据条数,例如: index.html?r=5
     *
     * @param sting|Model  $model   模型名或模型实例
     * @param array        $where   where查询条件(优先级: $where>$_REQUEST>模型设定)
     * @param array|string $order   排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
     *                              请求参数中如果指定了_order和_field则据此排序(优先级第二);
     *                              否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
     *
     * @param boolean      $field   单表模型用不到该参数,要用在多表join时为field()方法指定参数     
     *
     * @return array|false
     * 返回数据集
     */
    protected function lista ($model,$where=array(),$order='',$field=true){
        $options    =   array();
        $REQUEST    =   (array)I('request.');
        if(is_string($model)){
            $model  =   M($model);
        }

        if($order===null){
            //order置空
        }else if ( isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']),array('desc','asc')) ) {
            $options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
        }elseif( $order==='' && empty($options['order']) && !empty($pk) ){
            $options['order'] = $pk.' desc';
        }elseif($order){
            $options['order'] = $order;
        }
        unset($REQUEST['_order'],$REQUEST['_field']);

        if(empty($where)){
            $where  =   array('status'=>array('egt',0));
        }
        if( !empty($where)){
            $options['where']   =   $where;
        }
       
        $total        =   $model->where($options['where'])->count();

        if( isset($REQUEST['r']) ){
            $listRows = (int)$REQUEST['r'];
        }else{
            $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        }
        $page = new \Think\Page($total, $listRows, $REQUEST);
        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $p = $page->show();		
        $this->page  =  $p;
        $this->total =  $total;
		
        $options['limit'] = $page->firstRow.','.$page->listRows;

        $model->setProperty('options',$options);
		$rs = $model->field($field)->select();		
        return $rs;
    }

    /*发送验证短信*/
    public function sendVerCode(){
        $mobile  = I("mobile");//手机号
        if(empty($mobile))\Org\Response::show(4000,'手机号为空！');
        $rs = D("sms")->where("mobile = '{$mobile}'")->find();

        $user          = getC('SMS_USER');//获取用户名
        $key           = getC('SMS_KEY');//获取key
        $data["code"]  = mt_rand(1000,9999);//生成随机的四位数
        $content       = getC('SMS_CONTENT');
        $content       = str_replace("{CODE}", $data["code"], $content);//获取短信内容

        $data["send_time"] = time();//短信发送时间
        $is_sucess = false;
        if($rs){//当前手机号存在短信表里
            //if(time() < ($rs["send_time"]+60))\Org\Response::show(4001,'60秒内不能重复发送!');// 60秒不能重复发送
            $is_sucess = D("sms")->where("mobile = '{$mobile}'")->save($data);
        }else{//当前手机号不存在短信表里
            $data['mobile'] = $mobile;
            $is_sucess = D("sms")->add($data);
        }
        if($is_sucess){ 
			$url   = "http://106.ihuyi.cn/webservice/sms.php?method=Submit&account={$user}&password={$key}&mobile={$mobile}&content={$content}";
            curl_file_get_contents($url);//获取短信返回值          
            \Org\Response::show(2000,'发送成功!', array('code' => $data["code"]));
        }else{
            \Org\Response::show(4002,'发送失败!');
        }
    }   
     /*验证验证码*/
     public function verifiCode(){
        $code             = I("code");//验证码  
        $data['mobile']   = I("mobile");//手机号
        $is_mobile = D('driver')->where("mobile = '".$data['mobile']."'")->getField("did");        
        if($is_mobile)\Org\Response::show(4000,'该手机号已经被注册!');
        //验证验证码
        $virify = D("sms")->field("code,send_time")->where("mobile = '{$data['mobile']}'")->find();        
        if($virify){
            if($virify["code"] != $code){
                \Org\Response::show(4001,'验证码错误！');
            }else{
                if(time() > ($virify["send_time"]+getC("OVERTIME"))){
                    \Org\Response::show(4002,'验证码已过期请您重新发送!');// 180过后就过期
                }else{
                    \Org\Response::show(2000,'验证通过!');// 180过后就过期
                }
            }
        }else{
            \Org\Response::show(4003,'您还没有点击发送验证码！');
        }
    }	
}