<?php

namespace app\index\controller;
use think\Request;
use think\Controller;
use think\Session;
use think\Validate;

class Index extends Controller{

    public function __construct(){  
        parent::__construct();

        if (Session::get('name') == false) {

            return $this->error('登陆后操作','user/login');
        }
    }  

	public function index(){

        $dirSize = 0;

        if(file_exists('./uploads/images/')){

            $dirSize = getDirSize('./uploads/images/');
                
        }

        $this->assign('dirSize',$dirSize);

		return $this->fetch();
	}

	public function up(){

		$request  = Request::instance();

        if ($request->isPost() == false) {

            return '';
        }


        $post = $request->param();

        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file')->validate(['ext'=>'jpg,png,gif'])->move('uploads/images');


        if ($file) {

            $url = $request->domain().'/'.str_replace('\\', '/', $file->getPathName());

            return  $url .'#'. $post['uuid'];

        }
 
        return '';
	}

    public function delfile($id = false){

        $id = trim($id,'/');

        if (Request::instance()->isAjax()) {

            if (unlink($id)) {

                return 1;
            
            }
        }

        if(file_exists($id) == false){

            return $this->error('文件不存在');
        }

        if (unlink($id)) {

            return $this->success('删除成功');
            
        }else {
            
            return $this->error('删除失败');
        }
    }

    public function deldir($id = false,$day = 16){

        $path = './uploads/images/';

        if (is_dir($path) == false) {
            
            return $this->error('文件不存在');
        }

        if ($id == false) {

            $time = time()-3600*24*$day;


            $files = scandir($path);

            foreach ($files as $val) {
                
                if ($val != '.' && $val != '..') {
                    
                    if(strtotime($val) < $time){

                        $this->delDirAndFile($path.$val,true) ;
                    }

                }
            }
            return $this->success('删除成功');

        }elseif (file_exists($path.$id)) {

             $this->delDirAndFile($path.$id,true) ;

            return $this->success('删除成功');
        }else{

            return $this->error('删除失败');
        }

    }

    public function views($id=''){


        $id   =  $id ? $id : date('Y').date('m').date('d');

        $path = './uploads/images/'. $id.'/';

        $files= [];

        if(file_exists($path)){

           $files = scandir($path);

        }

        array_shift($files);
        array_shift($files);

        $data = [];

        foreach ($files as $key => $val) {

            $info = getimagesize($path.$val);

            $data[$key]['img'] = trim($path.$val,'.');
            $data[$key]['size']= $info[0].'*'.$info[1];
            $data[$key]['uid'] = preg_replace('/\.(jpg|png|gif)/','',$val);
        }


        $this->assign('files', $data);

        return $this->fetch();

    }

    public function lists(){

        $path  = './uploads/images/';
        $files = [];

        if(file_exists($path)){

           $dirs = scandir($path,1);

           foreach ($dirs as $key => $val) {

                if (strstr($val,".") == false) {

                    $files[$key]['name'] = $val;

                    $files[$key]['file'] = count(scandir($path.$val))-2;
                } 
            }

        }

        $this->assign('files', $files);

        return $this->fetch();

    }

    private function delDirAndFile($path, $delDir = FALSE) {

        $handle = opendir($path);

        if ($handle) {

            while (false !== ( $item = readdir($handle) )) {

                if ($item != "." && $item != "..")

                    is_dir("$path/$item") ? delDirAndFile("$path/$item", $delDir) : unlink("$path/$item");
            }

            closedir($handle);

            if ($delDir){

                return @rmdir($path); 
            }

        }else {

            if (file_exists($path)) {

                return unlink($path);

            } else {

                return FALSE;
            }
        }
    } 

}

