<?php
/*
 * @Description �ױ�֧�������п�֧��רҵ��ӿڷ��� 
 * @V3.0
 * @Author yang.xu
 */

	include 'YeePayCommon.php';	

	#�̼������û�������Ʒ��֧����Ϣ.
	#�̻�������.�ύ�Ķ����ű����������˻�������Ψһ.
	$p2_Order			= $_POST['p2_Order'];

	#֧�������
	$p3_Amt				= $_POST['p3_Amt'];

	#�Ƿ���鶩�����
	$p4_verifyAmt		= $_POST['p4_verifyAmt'];

	#��Ʒ����
	$p5_Pid				= $_POST['p5_Pid'];

	#iconv("UTF-8","GBK//TRANSLIT",$_POST['p5_Pid']);
	#��Ʒ����
	$p6_Pcat			= $_POST['p6_Pcat'];

	#iconv("UTF-8","GBK//TRANSLIT",$_POST['p6_Pcat']);
	#��Ʒ����
	$p7_Pdesc			= $_POST['p7_Pdesc'];

	#iconv("UTF-8","GBK//TRANSLIT",$_POST['p7_Pdesc']);
	#�̻����ս��׽��֪ͨ�ĵ�ַ,�ױ�֧����������֧�����(��������Ե�ͨѶ).֪ͨ��ͨ��HTTPЭ����GET��ʽ���õ�ַ��.	
	$p8_Url				= $_POST['p8_Url'];

	#��ʱ��Ϣ
	$pa_MP				= $_POST['pa_MP'];

	#iconv("UTF-8","GB2312//TRANSLIT",$_POST['pa_MP']);
	#�����
	$pa7_cardAmt			= arrToStringDefault($_POST['pa7_cardAmt']);

	#֧�������к�.
	$pa8_cardNo			= arrToStringDefault($_POST['pa8_cardNo']);

	#֧��������.
	$pa9_cardPwd			= arrToStringDefault($_POST['pa9_cardPwd']);

	#֧��ͨ������
	$pd_FrpId			= $_POST['pd_FrpId'];

	#Ӧ�����
	$pr_NeedResponse		= $_POST['pr_NeedResponse'];

	#�û�Ψһ��ʶ
	$pz_userId			= $_POST['pz_userId'];

	#�û���ע��ʱ��
	$pz1_userRegTime		= $_POST['pz1_userRegTime'];

	
	#�����п�֧��רҵ�����ʱ���õķ������ڲ��Ի����µ���ͨ�����������ʽ����annulCard
	#���������������һ��������ֻ��Ҫ����������ΪannulCard����
	#����ͨ������ʽ����ʱ����ø÷���
	annulCard($p2_Order,$p3_Amt,$p4_verifyAmt,$p5_Pid,$p6_Pcat,$p7_Pdesc,$p8_Url,$pa_MP,$pa7_cardAmt,$pa8_cardNo,$pa9_cardPwd,$pd_FrpId,$pz_userId,$pz1_userRegTime);
?> 
