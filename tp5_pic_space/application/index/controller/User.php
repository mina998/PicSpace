<?php

namespace app\index\controller;
use think\Controller;
use think\Session;

class User extends Controller{

    public function login(){
        
        if (Session::get('name') == true) {

            return $this->redirect('index/index');
        }
        
        if (request()->isPost()) {
            
            $pwd = '88888';

            $name= 'admin';

            $data = request()->param();

            if ($data['user'] == $name && $data['pwd'] == $pwd) {

                Session('name',$name);

                
                return $this->success('登陆成功','index/index');
            }

            return $this->error('帐号密码有误');

        }

        return $this->fetch();
    }
}

