<?php
class MyuploadAction extends Action{
	public function index()
	{
		$myupload=M('Myupload');

		$myuploadslist=$myupload->order('id DESC')->limit(3)->select();
		//var_dump($myuploadslist);
		$this->assign('myuploadslist',$myuploadslist);
		$this->display();
	}
	public function upload(){
		var_dump($_FILES);
		//exit();
		import("ORG.Net.UploadFile");
		$upload=new UploadFile();
		$upload->maxSize  = 3145728 ; // 设置附件上传大小
		
		$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
		$time=date("Ymd");
		$upload->savePath = "Myuploads/".$time."/"; // 设置附件上传目录
		//var_dump($QQ);
		$upload->saveRule = uniqid;//保存文件的命名规则，这里以时间戳为文件名
		//var_dump($aa);
		$result = $upload->upload();//保存上传文件，获取上传信息
		if(!$result) { // 上传错误提示错误信息
		
			$this->error($upload->getErrorMsg());
		
		}else{ // 上传成功 获取上传文件信息
		
			$info =  $upload->getUploadFileInfo();
			//$savename = $info[0]['savename'];//获取保存的文件名
					
		}
		$myupload=M('Myupload');
		$myupload->Create();
		$num=count($info[0][name]);
		Dump($num);	
		$myupload->photo =$time.'/'.$info[0]["savename"]; //将图片路径写入到数据库
		
		//$data["photo"]=$time.'/'.$info[]["savename"];
		//Dump($data["photo"]);
		
		$myupload->add();
		$this->success("上传成功");
		
	}
}
?>