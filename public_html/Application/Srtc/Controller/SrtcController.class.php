<?php
namespace Admin\Controller;
use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;

class SrtcController extends AdminController{

    protected $SrtcModel;
    function _initialize()
    {
        parent::_initialize();
        $this->model = D('Srtc/Srtc');      
    }
   
    public function index($page=1,$r=10)
    {
        $map['status'] = array('neq',-1);		
        $builder=new AdminListBuilder();

        //检测中心的列表
        $srtc = M('srtc');
        $list = $srtc -> where($map) -> select();

        foreach($list as &$value){
            if($value['cate']==1){
                $value['cate']='自营';
            }else{
                $value['cate']= '第三方';
            }
        }
        $totalCount = $srtc -> where($map) -> count();
        $builder->title('检测中心列表')
            ->data($list)
            ->buttonNew(U('Srtc/editSrtc'))
            ->setStatusUrl(U('Srtc/setSrtcStatus'))
            ->buttonEnable()->buttonDisable()->buttonDelete()
            ->setSelectPostUrl(U('Admin/Srtc/index'))            
            ->keyId()
            ->keyText('srtc_name','检测中心名称')
            ->keyText('cate','类型')
            ->keyImage('cover','封面')
            ->keyText('sort','排序')
            ->keyStatus()
            ->keyCreateTime()
            ->keyUpdateTime()
            ->keyDoActionEdit('Srtc/editSrtc?id=###')
            ->pagination($totalCount,$r)
            ->display();
    }

    public function setSrtcStatus($ids,$status=1)
    {
        !is_array($ids)&&$ids=explode(',',$ids);
        $builder = new AdminListBuilder();
        $builder->doSetStatus('Srtc', $ids, $status);
    }

    /**
     * 编辑
     */
    public function editSrtc()
    {
        $aId=I('id',0,'intval');
        $title=$aId?"编辑":"新增";
        if(IS_POST){
            //新增加
            $aId&&$data['id']=$aId;
            $data['srtc_name']	 = I('post.srtc_name','','text');
            $data['cover']		 = I('post.cover',0,'intval');//不存在转为整型的0
			$data['cate']		 = I('post.cate','','text');
            $data['mobile']		 = I('post.mobile','','intval');
			$data['description'] = I('post.description','','text');
			$data['bus_line']    = I('post.bus_line','','text');
			$data['addr']    	 = I('post.addr','','text');
			$data['addr']   	 = I('post.addr','','text');
			$data['addr']    	 = I('post.addr','','text');
            $data['sort']		 = I('post.sort',0,'intval');
            $data['status']		 = I('post.status',1,'intval');
            if(!mb_strlen($data['srtc_name'],'utf-8')){
                $this->error('名称不能为空！');
            }
            $result=$this->model->editData($data);
            if($result){
                $aId=$aId?$aId:$result;
                $this->success($title.'成功！',U('Srtc/index'));
            }else{
                $this->error($title.'失败！',$this->model->getError());
            }
        }else{

            //编辑模式下
            if($aId){
                $data=$this->model->find($aId);
            }
            $category = C('ACTIVE_ADDR');
            //$options  = array(0=>'无分类');
            foreach($category as $key=>$val){
                $options[$key] = $val;
            }			
            $builder=new AdminConfigBuilder();
            $builder->title($title.'检测中心')
                ->data($data)
                ->keyId()                
                ->keyText('srtc_name','名称')                
                ->keySelect('city','城市','',$options)				
				->keySingleImage('cover','封面')
				->keyText('Srtc','检测中心')
                ->keyInteger('sort','排序')->keyDefault('sort',0)
                ->keyStatus()->keyDefault('status',1)
                ->buttonSubmit()->buttonBack()
                ->display();
        }
    }
} 