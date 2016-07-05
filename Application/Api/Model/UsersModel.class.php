<?php
namespace Api\Model;
use Think\Model;
use User\Api\UserApi;

/**
 * 会员模型文档
 */
class UsersModel extends Model{

    /* 会员模型自动完成 */
    protected $_auto = array (          
         array('password', 'sys_md5',1, 'function'),//添加密码的同时进行加密
         array('reg_time',"time",1,'function') , // 新增的时候默认插入当前时间
         array('last_login_time','time',2,'function'), // 对last_login_time字段在更新的时候写入当前时间戳
     );
    /* 会员模型自动验证 */
    protected $_validate = array(
        
        /* 验证用户名 */
      //  array('user_name','require',4001), //默认情况下用正则进行验证   用户名不能为空！
      //  array('user_name','',4002,0,'unique',1), // 在新增的时候验证name字段是否唯一   用户名已经存在！     
        

        /* 验证密码 */
        array('user_pwd', '6,16',4003, self::MUST_VALIDATE, 'length'), //密码长度不合法

        /* 验证手机号码 */
        array('user_name','require',4004), //手机号不能为空
        array('user_name', 'mobile', 4005, self::MUST_VALIDATE), //手机格式不正确   
        array('user_name', '', 4006, self::MUST_VALIDATE, 'unique'), //手机号被占用
     
        //array('real_name','require',4007), //默认情况下用正则进行验证   真实姓名不能为空！
        
    );

}
