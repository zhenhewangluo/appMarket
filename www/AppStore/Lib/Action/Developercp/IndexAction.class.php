<?php
/**
 * 用户公共类
 * @file	IndexAction.class.php
 * @name	IndexAction
 * @author	sunsir
 * @desc	开发者模块
 * @date	2012-7-10
 */
class IndexAction extends UcenterdevpBaseAction{
	protected	$_username = '';
	protected	$_userinfo = array();
	protected	$_userhead = array();	
	/* public function _initialize() {
		if (Cookie::is_set(team_id))
		{
			$this->assign('jumpUrl',"__APP__/Developer/Apps/appList");			
		}
		else {
			$this->assign('jumpUrl',"__APP__/Developer/index/login");
			$this->error("请先登录");
		}
	} */
	public function index(){		
		//$this->assign(myse,$myse);
		$this->redirect("login");
		//$this->display($this->_layout);
	}
	
	public function register(){	
		header("Content-Type:text/html; charset=utf-8");
		//import("ORG.Util.image");
		//$length="4";
		//$mode="5";
		//$type=;
		//Image::buildImageVerify();		
		$this->display($this->_layout);
	}
	public function registerAction(){
		$data["teamname"]=Filter::act(Req::get('teamname','post'));
		Dump($data["teamname"]);
		$data["password"]=md5(Filter::act(Req::get('password','post')));
		$repassword=md5(Filter::act(Req::get('repassword','post')));
		$Team=D("Team");
		if (empty($data["teamname"])) $this->error('用户名不能为空');
	    if ($this->tnameisexist($data["teamname"])) $this->error("该用户名已被注册");
		if ($data["password"]!=$repassword) $this->error("两次输入的密码不一致");	
		if ($_SESSION['verify'] != md5($_POST['verify'])) $this->error("验证码不正确");		
		
		//if (!$Team->autoCheckToken($_POST['teamname'])) $this->error("验证失败");
			if($teamReg=$Team->add($data)){
			$this->assign('jumpUrl',"__APP__/Developercp/Index/login");
			$this->success("注册成功");
			}
			else {
				$this->assign('jumpUrl',"__URL__/register");
				$this->success("注册失败");
			}
		//if ($_SESSION['verify'] != md5($_POST['verify'])) $this->error("验证码不正确");
		/*$Team= D("Team");
		if($Team->create()){			
			if ($developerteam= $Team->add())
			{
				$this->assign('jumpUrl',"__URL__/register");
				$this->success("注册成功");
			}
			else {
				$this->assign('jumpUrl',"__URL__/register");
				$this->error("注册失败");
			}
		}
		else {
			$this->assign('jumpUrl',"__URL__/register");
			$this->error($Team->getError());
		}*/
	}
	/*	 * *********************************************
	 * Function:      login
	* Description:    登录显示和方法，
	* Input:
	* Return:
	* Others:
	* Date：         2012-7-28
	* Author：       sunsir
	* ************************************************** */
	public function login(){
		 if (Cookie::is_set(team_id))
		{
			$this->assign('jumpUrl',"__APP__/Developercp/Apps/appList");			
			$this->success("您已经登陆");			
		}
		else{ 
		$this->display($this->_layout);
		}
	}
	
public function loginAction(){
		$data["teamname"]=Filter::act(Req::get('teamname','post'));		
		$data["password"]=md5(Filter::act(Req::get('password','post')));
		//Dump($data);
		$developer=M("Team");
		//Dump($developer);
		//$developer->create();
		$developerteam=$developer->where("teamname='{$data["teamname"]}'")->find();
		//Dump($developerteam);
		$userinfo=$developerteam;
		$usercookie = "{$userinfo['uid']}\t{$userinfo['username']}\t{$userinfo['password']}\t{$userinfo['email']}\t{$userinfo['phone']}";
		import('ORG.Crypt.Crypt');
		if (!empty($developerteam)){
			if($developerteam['password']==$data["password"]){
				Vendor('Ucenter.UcApi');  //载入UcApi扩展
				$synlogin = uc_user_synlogin($return['userinfo']['uid']);
				
				Cookie::set(team_id, $developerteam['id'],time()+3600*24);
				Cookie::set(team_teamname, $developerteam['teamname'],time()+3600*24);
				Cookie::set(C("COOKIE_NAME"), Crypt::encrypt($usercookie, C("DES_KEY"), 1));
				$this->assign('jumpUrl',"__APP__/Developercp/Apps/appList");
				$this->success("登陆成功");
			}
			else {
				$this->assign('jumpUrl',"__URL__/login");
				$this->error("密码错误，请重新登陆");
				$this->redirect('login');
			}
		}
		else {
			$this->error("用户名不存在");
			$this->redirect('login');
		}
	} 
	/*	 * *********************************************
	 * Function:     logoutAction
	* Description:    退出登录，
	* Input:
	* Return:
	* Others:
	* Date：         2012-7-28
	* Author：       sunsir
	* ************************************************** */
	public function logoutAction(){
		Cookie::delete(team_id);
		Cookie::delete(team_teamname);
		Cookie::delete(C('COOKIE_NAME'));
		$this->assign('jumpUrl',"__URL__/index");
		$this->success("退出成功");
		}
		/*	 * *********************************************
		 * Function:     devpavatar
		* Description:    资料编辑修改，
		* Input:
		* Return:
		* Others:
		* Date：         2012-7-28
		* Author：       sunsir
		* ************************************************** */
		public function devpavatar()
		{
			$teamid=Cookie::get(team_id);
			//dump("$teamid");
			$team=D("Team");
			$teamprofile=$team->where("id=".$teamid)->find();
			//dump("$teamprofile");
			$this->assign('teamprofile',$teamprofile);
			//$uc_avatarflash=__ROOT__."/Public/flash/camera.swf";
			$userobj = A('teamdevp');
			$flash = $userobj->showuploadAction();
			//var_dump($flash);
			$this->assign('flash', $flash);
		
			$this->display($this->_layout);
		}
		/*	 * *********************************************
		 * Function:     editprofile
		* Description:    资料编辑修改，
		* Input:
		* Return:
		* Others:
		* Date：         2012-7-28
		* Author：       sunsir
		* ************************************************** */
	public function editprofile()
		{	
		$teamid=Cookie::get(team_id);
		//dump("$teamid");
		$team=D("Team");
		$teamprofile=$team->where("id=".$teamid)->find();
		//dump("$teamprofile");
		$this->assign('teamprofile',$teamprofile);
		$userobj = A('teamdevp');
		$this->display($this->_layout);
	}
	public function updateprofile(){		
		$teamid=Cookie::get(team_id);
		$data["teamname"]=Filter::act(Req::get('teamname','post'));
		$data["idcard"]=Filter::act(Req::get('idcard','post'));
		$data["address"]=Filter::act(Req::get('address','post'));
		$data["phone"]=Filter::act(Req::get('phone','post'));
		$data["sparecontact"]=Filter::act(Req::get('sparecontact','post'));
		$data["sparephone"]=Filter::act(Req::get('sparephone','post'));
		$data["site"]=Filter::act(Req::get('site','post'));
		import("ORG.Net.UploadFile");
		$upload = new UploadFile(); // 实例化上传类
		$upload->maxSize  = 3145728 ; // 设置附件上传大小
		$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
		$upload->savePath =  '__PUBLIC__/Uploads/'; // 设置附件上传目录
		/*if(!$upload->upload()) { // 上传错误提示错误信息
			$this->error($upload->getErrorMsg());
		}else{ // 上传成功 获取上传文件信息
			$info =  $upload->getUploadFileInfo();
			Dump($info);
		}
		
		$data["avatar"]=$info[0][savepath].$info[0][savename];
		
		Dump($data["avatar"]); */
		$this->assign('userdata', $userdata);
		$this->assign('viewcontent', $this->_tplPrefix . 'userinfo');
		$team=new TeamModel();
		//$creteam=$team->Create();		
		$savteam=$team->where("id={$teamid}")->save($data);
			if($savteam !== false){
				$this->success("修改资料成功");				
			}
			else{
				$this->error('失败');
			}		
		}
	public function uploadapp() {
		$this->display($this->_layout);			
		}
	public function uploadappAction() {
				
		}	
	public function insert(){
		$this->display($this->_layout);
	}
	public function insertAction(){
		$Team=D("Team");
		if($_SESSION['verify'] != md5($_POST['verify'])){
			$Team->Create();
			$registerteam=$Team->add();
			if ($registerteam){
				Cookie::set(team_id, $developerteam[id],time()+3600*24);
				Cookie::set(team_teamname, $developerteam[teamname],time()+3600*24);
				$this->success('注册成功');
			}
			else{
				$this->error('注册失败');
			}
		}
		else {
			$this->error("验证码不正确");		}
		
	}
	/*	 * *********************************************
	 * Function:     modifypass
	* Description:    密码修改，
	* Input:
	* Return:
	* Others:
	* Date：         2012-7-28
	* Author：       sunsir
	* ************************************************** */
	public function modifypass() {
		$teamid=Cookie::get(team_id);
		$team=D("Team");
		$teampass=$team->field('teamname,password')->where("id=".$teamid)->select();
		//Dump($teampass);
		$this->assign('teampass',$teampass);
		$this->display($this->_layout);
	}
	/*	 * *********************************************
	 * Function:     modifypassAction
	* Description:    密码修改，
	* Input:
	* Return:
	* Others:
	* Date：         2012-7-28
	* Author：       sunsir
	* ************************************************** */
	public function modifypassAction() {
		$teamid=Cookie::get(team_id);		
		import("ORG.Util.Input");		
		$Input=new Input();
		$data['passwordold']=md5($Input->getVar($_POST['passwordold']));
		$data['password']=md5($Input->getVar($_POST['password']));
		$data['passwordcheck']=md5($Input->getVar($_POST['passwordcheck']));
		//var_dump($data['passwordold']);
		//var_dump($data['password']);
		//var_dump($data['passwordcheck']);
		$team=M("Team");
		$teampasscheck=$team->field('teamname,password')->where("id=".$teamid)->find();
		//var_dump($teampasscheck);
		if($data['passwordold']!==$teampasscheck['password']){
			$this->assign('jumpUrl',"__URL__/modifypass");
			$this->error("原密码错误");				
		}
		elseif ($data['passwordcheck']!==$data['password']){
			$this->assign('jumpUrl',"__URL__/modifypass");
			$this->error("两次密码输入不正确");
			
		}
		$teampass=$team->where("id=".$teamid)->data($data)->save();
		var_dump($teampass);
		if($teampass){
			$this->assign('jumpUrl',"__URL__/modifypass");
			$this->success("修改密码成功");			
		}			
	}
	/*	 * *********************************************
	 * Function:     agreement
	* Description:    用户协议，
	* Input:
	* Return:
	* Others:
	* Date：         2012-7-28
	* Author：       sunsir
	* ************************************************** */
	public function agreement(){
		$this->display($this->_layout);
	}
	private function tnameisexist($teamname){
		$team=M("Team");
		//$data["teamname"]=Filter::act(Req::get('teamname','post'));
		Dump($teamname);
		$teamexist=$team->where('teamname="{$teamname}"')->select();
		Dump($teamexist[teamname]);
// 	if (!$teamexist['teamname']) 
// 		{
// 			return false;
// 		}
// 		else {
// 			return true;
// 		}
	}
	private function IsMail($Argv){
		$RegExp='/^[a-z0-9][a-z\.0-9-_] @[a-z0-9_-] (?:\.[a-z]{0,3}\.[a-z]{0,2}|\.[a-z]{0,3}|\.[a-z]{0,2})$/i';
		return preg_match($RegExp,$Argv)?$Argv:false;
	}
	private function isValidAct($acttype)
	{
		if(!array_key_exists($acttype, $this->_acttypeArr))
		{
			$this->error('非法操作');die;
		}
	}
}
?>