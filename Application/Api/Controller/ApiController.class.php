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
        $listRows           = I("pages")?I("pages"):15;//每页数 
        $page               = new \Think\Page($total, $listRows);
        $limit              = $page->firstRow.','.$page->listRows;
		$data = $model->field($field)->where($where)->limit($limit)->order($order)->select();

		$this->total =  $total;		
        return $data;
    }
	
	
	
	
	/* 
	 * 上传图片
	 * @return id(图片id)
	 */
	public function uploadImage(){
		$base64_string = I("base64_string");
		if(empty($base64_string))json(4000,'参数缺失!');			
		$data['md5']  		  = md5($base64_string);
		$id = M('picture')->where($data)->getField('id');
		if($id)return $id;//图片存在则返回相同id		
		$filename = uploadImg($base64_string);
		$data['path']		  = '/'.$filename; 
		$data['create_time']  = time();
		$data['type']  		  = "app";
		$data['status']  	  = 1;
		$res = M('picture')->add($data);
		return $res;
	}
	

	
	
	
	
	
	
	

}