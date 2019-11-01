<?PHP

/*
 *   公钥配置信息
 *   bluesie  2010-5-17
 */
//rsa 算法公钥
require("config.inc.php");

define("RSA_PUBLIC_KEY",65537);   //public_key
define("RSA_PRIVATE_KEY","146113750824343670942128097542444399259964660941491643821942210186083077109537583878673644902341866788189279987166560705020110275786201076287222163397806550798955350352765927156473489080296241902430288870197651617563532347924061387350273794499879003822039725726836935830155947973311169627355758798481984876609");//private_key
define("RSA_MODULO",'153400244902201255327025649247712075392482122006320291252701310857447882617707366311941475489631855114980790121090203910753924246190571894409773146908273251771112984362707682509615031664173679643452775818193013369202795463751003187532512848212012614635729858507630181976767562646363293565072310969559913791943');

	//各种卡的支付渠道对应
	$CHANNEL_CARD_CONFIG_ARR = array();

	$CHANNEL_CARD_CONFIG_ARR[0] = array(
		'cardID'                => 1,
		'cardIcon'              => AM_SITE_URL."card/icon1.png",
		'channelID'             => '1',
		'cardName'              => '骏网一卡通',
		'cardNo'                => 'JUNNET'
	);

	$CHANNEL_CARD_CONFIG_ARR[1] = array(
		'cardID'                => 2,
		'cardIcon'              => AM_SITE_URL."card/icon2.png",
		'channelID'             => '1|2',
		'cardName'              => '盛大卡',
		'cardNo'                => 'SNDACARD|SD'
	);
	$CHANNEL_CARD_CONFIG_ARR[2] = array(
		'cardID'                => 3,
		'cardIcon'              => AM_SITE_URL."card/icon3.png",
		'channelID'             => '1|2|3',
		'cardName'              => '神州行',
		'cardNo'                => 'SZX|SZX2|SZX3'
	);
	$CHANNEL_CARD_CONFIG_ARR[3] = array(
		'cardID'                => 4,
		'cardIcon'              => AM_SITE_URL."card/icon4.png",
		'channelID'             => '1',
		'cardName'              => '征途卡',
		'cardNo'                => 'ZHENGTU'
	);
	$CHANNEL_CARD_CONFIG_ARR[4] = array(
		'cardID'                => 5,
		'cardIcon'              => AM_SITE_URL."card/icon5.png",
		'channelID'             => '1',
		'cardName'              => 'Q币卡',
		'cardNo'                => 'QQCARD'
	);
	$CHANNEL_CARD_CONFIG_ARR[5] = array(
		'cardID'                => 6,
		'cardIcon'              => AM_SITE_URL."card/icon6.png",
		'channelID'             => '1|2',
		'cardName'              => '联通卡',
		'cardNo'                => 'UNICOM|LTJFK',
		'cardChannel'           => '|LTJFK00020000'  //支付方式
	);
	$CHANNEL_CARD_CONFIG_ARR[6] = array(
		'cardID'                => 7,
		'cardIcon'              => AM_SITE_URL."card/icon7.png",
		'channelID'             => '1',
		'cardName'              => '久游卡',
		'cardNo'                => 'JIUYOU'
	);
	$CHANNEL_CARD_CONFIG_ARR[7] = array(
		'cardID'                => 8,
		'cardIcon'              => AM_SITE_URL."card/icon8.png",
		'channelID'             => '1',
		'cardName'              => '易宝e卡通',
		'cardNo'                => 'YPCARD'
	);
	$CHANNEL_CARD_CONFIG_ARR[8] = array(
		'cardID'                => 9,
		'cardIcon'              => AM_SITE_URL."card/icon9.png",
		'channelID'             => '1',
		'cardName'              => '网易卡',
		'cardNo'                => 'NETEASE'
	);
	$CHANNEL_CARD_CONFIG_ARR[9] = array(
		'cardID'                => 10,
		'cardIcon'              => AM_SITE_URL."card/icon10.png",
		'channelID'             => '1',
		'cardName'              => '完美卡',
		'cardNo'                => 'WANMEI'
	);
	$CHANNEL_CARD_CONFIG_ARR[10] = array(
		'cardID'                => 11,
		'cardIcon'              => AM_SITE_URL."card/icon11.png",
		'channelID'             => '1',
		'cardName'              => '搜狐卡',
		'cardNo'                => 'SOHU'
	);
	$CHANNEL_CARD_CONFIG_ARR[11] = array(
		'cardID'                => 12,
		'cardIcon'              => AM_SITE_URL."card/icon12.png",
		'channelID'             => '1|2',
		'cardName'              => '电信卡',
		'cardNo'                => 'TELECOM|DXJFK',
		'cardChannel'           => '|DXJFK00010001'  //支付方式
	);
	$CHANNEL_CARD_CONFIG_ARR[12] = array(
		'cardID'                => 13,
		'cardIcon'              => AM_SITE_URL."card/zgyd.png",
		'channelID'             => '1|2',
		'cardName'              => '全国移动充值卡',
		'cardNo'                => 'SZX|CMJFK',  //支付编码
		'cardChannel'           => '|CMJFK00010001'  //支付方式
	);
	$CHANNEL_CARD_CONFIG_ARR[13] = array(
		'cardID'                => 14,
		'cardIcon'              => AM_SITE_URL."card/zgyd.png",
		'channelID'             => '2',
		'cardName'              => '辽宁移动电话交费卡',
		'cardNo'                => 'CMJFK',
		'cardChannel'           => 'CMJFK00010102'  //支付方式
	
	);
	$CHANNEL_CARD_CONFIG_ARR[14] = array(
		'cardID'                => 15,
		'cardIcon'              => AM_SITE_URL."card/zgyd.png",
		'channelID'             => '2',
		'cardName'              => '江苏移动充值卡',
		'cardNo'                => 'CMJFK',
		'cardChannel'           => 'CMJFK00010111'  //支付方式
	
	);
	$CHANNEL_CARD_CONFIG_ARR[15] = array(
		'cardID'                => 16,
		'cardIcon'              => AM_SITE_URL."card/zgyd.png",
		'channelID'             => '2',
		'cardName'              => '浙江移动缴费券',
		'cardNo'                => 'CMJFK',
		'cardChannel'           => 'CMJFK00010112'  //支付方式
	
	);
	$CHANNEL_CARD_CONFIG_ARR[16] = array(
		'cardID'                => 17,
		'cardIcon'              => AM_SITE_URL."card/zgyd.png",
		'channelID'             => '2',
		'cardName'              => '福建移动呱呱通充值卡',
		'cardNo'                => 'CMJFK',
		'cardChannel'           => 'CMJFK00010014'  //支付方式
	
	);
	//支付渠道
	$CHANNEL_CONFIG_ARR[] = array(
		'channelID'             => 1,
		'channelName'           => 'yeepay',
		'channelCName'          => '易宝',
	);
	$CHANNEL_CONFIG_ARR[] = array(
		'channelID'             => 2,
		'channelName'           => '19pay',
		'channelCName'          => '19pay',
	);
	$CHANNEL_CONFIG_ARR[] = array(
		'channelID'             => 3,
		'channelName'           => 'tenpay',
		'channelCName'          => '财付通',
	);
	$CHANNEL_CONFIG_ARR[] = array(
		'channelID'             => 4,
		'channelName'           => 'shengda',
		'channelCName'          => '盛大',
	);
	$CHANNEL_CONFIG_ARR[] = array(
		'channelID'             => 5,
		'channelName'           => 'zhifubao',
		'channelCName'          => '支付宝',
	);
?>
