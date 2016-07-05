<?php
namespace Admin\Controller;
use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminTreeListBuilder;

class SrtcController extends AdminController{

    protected $SrtcModel;
	protected $TestitemModel;
	protected $DetectionModel;
	protected $SampletypeModel;
    function _initialize()
    {
        parent::_initialize();
        $this->model = D('Srtc/Srtc'); 
		$this->TestitemModel = D('Srtc/Testitem'); 
		$this->DetectionModel = D('Srtc/Detection'); 
		$this->SampletypeModel = D('Srtc/Sampletype');   
    }
   
    public function index($page=1,$r=10,$srtc_name='')
    {
        $map['status'] = array('neq',-1);		
        $builder=new AdminListBuilder();

        if ($srtc_name != '') {
            $map['srtc_name'] = array('like', '%' . $srtc_name . '%');
        }

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
            ->keyText('description','简介')
            ->KeyText('mobile','手机号')
            ->keyText('bus_line','乘车路线')
            ->keyText('addr','地址')
            ->keyText('test_program','检测项目')
            ->keyText('sort','排序')
            ->keyStatus()
            ->keyCreateTime()
            ->keyUpdateTime()
            ->keyDoActionEdit('Srtc/editSrtc?id=###')
            ->setSearchPostUrl(U('Admin/rtc/index'))
            ->search(L('检测中心名称'), 'srtc_name')
           // ->search(L('_CONTENT_'), 'content')
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

            $builder=new AdminConfigBuilder();
            $builder->title($title.'检测中心')
                ->data($data)
                ->keyId()                
                ->keyText('srtc_name','检测中心名称')
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
	/*
	 *
	 */
	public function testing(){
		$map['status'] = array('eq',0);		
        $builder=new AdminListBuilder();
		$list = M('testing') -> where($map) -> order('id desc') -> select();
        $totalCount = M('testing') -> where($map) -> count();
		foreach($list as $key=>$value){
			$user = M('user')->where("id=".$value['uid'])->field('user_name')->find();
			$list[$key]['uid']=$user['user_name'];
			$srtc = M('srtc')->where("id=".$value['srtc_id'])->field('srtc_name')->find();
			$list[$key]['srtc_id'] = $srtc['srtc_name'];
			$detection = $value['detection'];
			$detection_id = explode(',',$detection);
			$detection_title = array();
			foreach($detection_id as $kk=>$vall){
				$title = M("detection")->where("id=".$vall)->field("title,id")->find();
				$detection_title[$kk] = $title['titile'];
				
			}
			$list[$key]['detection'] = implode(',',$detection_title);
		}
        $builder->title('检测表单列表')
            ->data($list)
            ->setStatusUrl(U('Srtc/settestingStatus'))
            ->buttonDelete()
            ->setSelectPostUrl(U('Admin/Srtc/testing'))            
            ->keyId()
            ->keyText('uid','用户名')
            ->keyText('forpeople','送检人')
            ->keyImage('manager','技术厂长')
            ->keyText('farmname','养殖场户名')
            ->KeyText('mobile_phone','联系电话')
            ->keyText('wx_code','微信号')
            ->keyText('addr','养殖场地址')
            ->keyText('srtc_id','送检中心')
            ->keyText('veterinary_id','健康管理顾问')
            ->keyText('animal_id','动物种类')
            ->keyText('day_old','日龄')
            ->keyImage('total','养殖总量')
            ->keyText('test_population','检测群体量')
            ->KeyText('sampling_time','采样时间')
            ->keyText('submission_time','送检时间')
            ->keyText('sample_type','样品种类')
            ->keyText('sample_quantity','样品数量')
            ->keyText('detection','检测目的')
            ->keyText('spirit','精神状态')
            ->keyImage('respiratory','呼吸道')
            ->keyText('feed_intake','采食量')
            ->KeyText('faeces','粪便状态')
            ->keyText('diarrhea','腹泻状况')
            ->keyText('addr','养殖场地址')
            ->keyText('mortality','死亡率')
            ->keyText('clinical_symptom','临床症状')
            ->keyText('check_symptom','剖检症状')
            ->keyText('check_picture','剖检图片')
            ->keyImage('immunization','免疫程序')
            ->keyText('disease','该地区流行病情况')
            ->KeyText('test_item','检测项目')
            ->keyText('create_time','提交时间')
            ->keyStatus()
            ->keyDoActionEdit('Srtc/editSrtc?id=###')
            ->setSearchPostUrl(U('Admin/Srtc/testing'))
            ->pagination($totalCount,$r)
            ->display();
		
	}
	
	
	/*
	 *	检测项目分类
	 */
	public function testitem() {

		$builder = new AdminTreeListBuilder();

		$type = $this -> TestitemModel -> getTree(0, 'id,pid,title,status');
		$builder -> title('技术管理') -> buttonNew(U('Srtc/add')) -> data($type) -> display();

	}
	public function add($id = 0, $pid = 0) {

		$title = $id ? "编辑" : "新增";
        if (IS_POST) {
            if ($this->TestitemModel->editData()) {
                S('SHOW_EDIT_BUTTON',null);
                $this->success($title.L('_SUCCESS_'), U('Srtc/testitem'));
            } else {
                $this->error($title.L('_FAIL_').$this->TestitemModel->getError());
            }
        } else {
            $builder = new AdminConfigBuilder();

            if ($id != 0) {
                $data = $this->TestitemModel->find($id);
            } else {
                $father_category_pid=$this->TestitemModel->where(array('id'=>$pid))->getField('pid');
                if($father_category_pid!=0){
                    $this->error(L('_ERROR_CATEGORY_HIERARCHY_'));
                }
            }
            if($pid!=0){
                $categorys = $this->TestitemModel->where(array('pid'=>0,'status'=>array('egt',0)))->select();
            }
            $opt = array();
            foreach ($categorys as $category) {
                $opt[$category['id']] = $category['title'];
            }

			$builder -> title($title . '分类') -> data($data) -> keyId() -> keyText('title', '标题') -> keySelect('pid', '父分类', '选择父级分类', array('0' => '顶级分类') + $opt) -> keyDefault('pid', $pid) -> keyStatus() -> keyDefault('status', 1) -> buttonSubmit(U('Srtc/add')) -> buttonBack() -> display();

		}

	}
	public function setStatus($ids, $status) {
		$builder = new AdminListBuilder();

		$builder -> doSetStatus('testitem', $ids, $status);

	}
	
	
	/*
	 *	检测目的类别
	 */
	public function detection($page=1,$r=10) {
	
        $builder=new AdminListBuilder();
        $list = $this -> DetectionModel -> getList();
        $builder->title('检测目的列表')
            ->data($list)
            ->buttonNew(U('Srtc/editdetection'))
            ->setStatusUrl(U('Srtc/setdetectionStatus'))
            ->buttonDelete()
            ->setSelectPostUrl(U('Admin/Srtc/detection'))            
            ->keyId()
            ->keyText('title','检测目的名称')
            ->keyStatus()
            ->keyCreateTime()
            ->keyUpdateTime()
            ->keyDoActionEdit('Srtc/editdetection?id=###')
            ->setSearchPostUrl(U('Admin/Srtc/detection'))
            ->display();

	}
	 public function editdetection()
    {
        $aId=I('id',0,'intval');
        $title=$aId?"编辑":"新增";
        if(IS_POST){
            //新增加vete
            $aId&&$data['id']=$aId;
            $data['title']	 = I('post.title','','text');
            $data['status']		 = I('post.status',1,'intval');

            if(!mb_strlen($data['title'],'utf-8')){
                $this->error('名称不能为空！');
            }
            $result=$this->DetectionModel->editData($data);
            if($result){
                $aId=$aId?$aId:$result;
                $this->success($title.'成功！',U('Srtc/detection'));
            }else{
                $this->error($title.'失败！',$this->model->getError());
            }
        }else{

            //编辑模式下
            if($aId){
                $data=$this->DetectionModel->find($aId);
            }

            $builder=new AdminConfigBuilder();
            $builder->title($title.'检测目的类别')
                ->data($data)
                ->keyId()
                ->keyText('title','检测目的名称')
                ->keyStatus()
                ->keyDefault('status',1)
                ->buttonSubmit()
                ->buttonBack()
                ->display();
        }
    }
	public function setdetectionStatus($ids,$status=1)
    {
        !is_array($ids)&&$ids=explode(',',$ids);
        $builder = new AdminListBuilder();
        $builder->doSetStatus('detection', $ids, $status);
    }

	/*
	 *	样品种类类别
	 */
	public function sampletype($page=1,$r=10) {
	
        $builder=new AdminListBuilder();
        $list = $this -> SampletypeModel -> getList();
        $builder->title('样品种类列表')
            ->data($list)
            ->buttonNew(U('Srtc/editsampletype'))
            ->setStatusUrl(U('Srtc/setsampletypeStatus'))
            ->buttonDelete()
            ->setSelectPostUrl(U('Admin/Srtc/sampletype'))            
            ->keyId()
            ->keyText('title','样品种类的名称')
            ->keyStatus()
            ->keyCreateTime()
            ->keyUpdateTime()
            ->keyDoActionEdit('Srtc/editsampletype?id=###')
            ->setSearchPostUrl(U('Admin/Srtc/sampletype'))
            ->display();

	}
	 public function editsampletype()
    {
        $aId=I('id',0,'intval');
        $title=$aId?"编辑":"新增";
        if(IS_POST){
            //新增加vete
            $aId&&$data['id']=$aId;
            $data['title']	 = I('post.title','','text');
            $data['status']		 = I('post.status',1,'intval');

            if(!mb_strlen($data['title'],'utf-8')){
                $this->error('名称不能为空！');
            }
            $result=$this->SampletypeModel->editData($data);
            if($result){
                $aId=$aId?$aId:$result;
                $this->success($title.'成功！',U('Srtc/sampletype'));
            }else{
                $this->error($title.'失败！',$this->model->getError());
            }
        }else{

            //编辑模式下
            if($aId){
                $data=$this->SampletypeModel->find($aId);
            }

            $builder=new AdminConfigBuilder();
            $builder->title($title.'样品种类类别')
                ->data($data)
                ->keyId()
                ->keyText('title','样品种类的名称')
                ->keyStatus()
                ->keyDefault('status',1)
                ->buttonSubmit()
                ->buttonBack()
                ->display();
        }
    }
	public function setsampletypeStatus($ids,$status=1)
    {
        !is_array($ids)&&$ids=explode(',',$ids);
        $builder = new AdminListBuilder();
        $builder->doSetStatus('sampletype', $ids, $status);
    }

} 