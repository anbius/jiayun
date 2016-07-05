<?php
namespace Admin\Controller;
use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;

class CollectController extends AdminController{

    protected $CollectModel;
    function _initialize()
    {
        parent::_initialize();
        $this->model = D('Collect/Collect');      
    }
   
    public function index($page=1,$r=10,$Collect_name='')
    {

        $map['status'] = array('neq',-1);		
        $builder=new AdminListBuilder();

        if ($Collect_name != '') {
            $map['Collect_name'] = array('like', '%' . $Collect_name . '%');
        }

        //检测中心的列表
        $Collect = M('Collect');
        $list = $Collect -> where($map) -> select();

        foreach($list as &$value){
            if($value['cate']==1){
                $value['cate']='咨询';
            }else if($value['cate']==2){
                $value['cate']= '兽医';
            }else{
                $value['cate']= '检测中心';
            }

            if($value['status'] ==1){
                $value['status']='关注';
            }else{
                $value['status']='未关注';
            }

            $user = M('user')->field('user_name') ->where('id='.$value['uid'])->find();
            $value['uid'] = $user['user_name'];
        }


        $totalCount = $Collect -> where($map) -> count();
        $builder->title('收藏列表')
            ->data($list)
            ->setStatusUrl(U('Collect/setCollectStatus'))
           ->buttonDelete()
        //    ->setSelectPostUrl(U('Admin/Collect/index'))
            ->keyId()
            ->keyText('uid','用户名称')
            ->keyText('cate','类型')

            ->keyText('coll_id','收藏品')

            ->keyText('token','token')
            ->keyText('status','是否关注')
            ->keyCreateTime()

          //  ->keyDoActionEdit('Collect/editCollect?id=###')
            ->setSearchPostUrl(U('Admin/Collect/index'))
           // ->search(L('检测中心名称'), 'Collect_name')
           // ->search(L('_CONTENT_'), 'content')
            ->pagination($totalCount,$r)
            ->display();
    }

    public function setCollectStatus($ids,$status=1)
    {
        !is_array($ids)&&$ids=explode(',',$ids);
        $builder = new AdminListBuilder();
        $builder->doSetStatus('Collect', $ids, $status);
    }

    /**
     * 编辑
     */
    public function editCollect()
    {
        $aId=I('id',0,'intval');
        $title=$aId?"编辑":"新增";
        if(IS_POST){
            //新增加
            $aId&&$data['id']=$aId;
            $data['Collect_name']	 = I('post.Collect_name','','text');
            $data['cover']		 = I('post.cover',0,'intval');//不存在转为整型的0
			//$data['cate']		 = I('post.cate','','text');
			$data['cate']		 = I('post.cate');
            /*if($data['cate']=='自营'){
                $data['cate']= 1;
            }else{
                $data['cate']= 2;
            }*/
            $data['mobile']		 = I('post.mobile','','intval');
			$data['description'] = I('post.description','','text');
			$data['bus_line']    = I('post.bus_line','','text');
			/*$data['addr']    	 = I('post.addr','','text');
			$data['addr']   	 = I('post.addr','','text');*/
			$data['addr']    	 = I('post.addr','','text');
            $data['test_program']= I('post.test_program','','text');
            $data['sort']		 = I('post.sort',0,'intval');
            $data['status']		 = I('post.status',1,'intval');

            if(!mb_strlen($data['Collect_name'],'utf-8')){
                $this->error('名称不能为空！');
            }
            $result=$this->model->editData($data);
            if($result){
                $aId=$aId?$aId:$result;
                $this->success($title.'成功！',U('Collect/index'));
            }else{
                $this->error($title.'失败！',$this->model->getError());
            }
        }else{

            //编辑模式下
            if($aId){
                $data=$this->model->find($aId);
            }

            $builder=new AdminConfigBuilder();
            $builder->title($title.'检测中心')
                ->data($data)
                ->keyId()
                ->keyText('Collect_name','检测中心名称')
                ->keySelect('cate',"选择经营方式",'',array(1=>'自营',2=>'第三方'))
				->keySingleImage('cover','封面')
                ->keyTextArea('description','中心简介')
                ->keyTextArea('test_program','检测项目')
                ->keyText('mobile','手机号')
                ->keyText('bus_line','检测中心乘车路线')
                ->keyText('addr','检测中心的地址')
                ->keyInteger('sort','排序')
                ->keyDefault('sort',0)
                ->keyStatus()
                ->keyDefault('status',1)
                ->buttonSubmit()
                ->buttonBack()
                ->display();
        }
    }




} 