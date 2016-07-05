<?php
namespace Admin\Controller;
use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;

class VeterinaryController extends AdminController{

    protected $VeterinaryModel;
    function _initialize()
    {
        parent::_initialize();
        $this->model = D('Veterinary/Veterinary');
    }
   
    public function index($page=1,$r=10,$vete_name='')
    {

        $map['status'] = array('neq',-1);		
        $builder=new AdminListBuilder();

        if ($vete_name != '') {
            $map['vete_name'] = array('like', '%' . $vete_name . '%');
        }

        //医生信息的列表
        $veterinary = M('veterinary');
        $list = $veterinary -> where($map) -> select();
        $totalCount =$veterinary  -> where($map) -> count();
        $builder->title('兽医成员列表')
            ->data($list)
            ->buttonNew(U('Veterinary/editVeterinary'))
            ->setStatusUrl(U('Veterinary/setVeterinaryStatus'))
            ->buttonEnable()->buttonDisable()->buttonDelete()
            ->setSelectPostUrl(U('Admin/Veterinary/index'))
            ->keyId()
            ->keyText('vete_name','兽医姓名')
            ->keyImage('cover','兽医头像')
            ->keyText('level','级别')
            ->keyText('description','简介')
            ->keyText('info','介绍')
            ->KeyText('phone','手机号')
            ->keyText('vet_pwd','用户密码')
            ->keyText('speciality','擅长')
            ->keyText('sort','排序')
            ->keyStatus()
            ->keyCreateTime()
            ->keyUpdateTime()
            ->keyDoActionEdit('Veterinary/editVeterinary?id=###')
            ->setSearchPostUrl(U('Admin/Veterinary/index'))
            ->search(L('兽医姓名'), 'vete_name')
           // ->search(L('_CONTENT_'), 'content')
            ->pagination($totalCount,$r)
            ->display();
    }

    public function setVeterinaryStatus($ids,$status=1)
    {
        !is_array($ids)&&$ids=explode(',',$ids);
        $builder = new AdminListBuilder();
        $builder->doSetStatus('Veterinary', $ids, $status);
    }

    /**
     * 编辑
     */
    public function editVeterinary()
    {
        $aId=I('id',0,'intval');
        $title=$aId?"编辑":"新增";
        if(IS_POST){
            //新增加vete
            $aId&&$data['id']=$aId;
            $data['vete_name']	 = I('post.vete_name','','text');
            $data['cover']		 = I('post.cover',0,'intval');//不存在转为整型的0
			//$data['cate']		 = I('post.cate','','text');
			$data['level']		 = I('post.level');
            $data['phone']		 = I('post.phone','','intval');
			$data['description'] = I('post.description','','text');
			$data['info']    = I('post.info','','text');
			$data['vet_pwd']    	 = I('post.vet_pwd','','text');
            $data['speciality']= I('post.speciality','','text');
            $data['sort']		 = I('post.sort',0,'intval');
            $data['status']		 = I('post.status',1,'intval');

            if(!mb_strlen($data['vete_name'],'utf-8')){
                $this->error('名称不能为空！');
            }
            $result=$this->model->editData($data);
            if($result){
                $aId=$aId?$aId:$result;
                $this->success($title.'成功！',U('Veterinary/index'));
            }else{
                $this->error($title.'失败！',$this->model->getError());
            }
        }else{

            //编辑模式下
            if($aId){
                $data=$this->model->find($aId);
            }

            $builder=new AdminConfigBuilder();
            $builder->title($title.'兽医信息')
                ->data($data)
                ->keyId()
                ->keyText('vete_name','兽医姓名')
                ->keySingleImage('cover','兽医头像')
                ->keyText('level','级别')
                ->keyTextArea('description','简介')
              //  ->keyEditor('description','简介')
                ->keyTextArea('info','介绍')
               // ->keyEditor('info','介绍')
                ->KeyText('phone','手机号')
                ->keyText('vet_pwd','用户密码')
                ->keyText('speciality','擅长')
                ->keyText('sort','排序')
                ->keyDefault('sort',0)
                ->keyStatus()
                ->keyDefault('status',1)
                ->buttonSubmit()
                ->buttonBack()
                ->display();
        }
    }




} 