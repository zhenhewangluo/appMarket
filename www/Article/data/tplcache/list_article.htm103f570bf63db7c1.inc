<?php
$z[0]=Array("global","",236,264);
$z[0][4]['name']="cfg_soft_lang";
$z[1]=Array("field","",278,297);
$z[1][4]['name']="title";
$z[2]=Array("global","",298,324);
$z[2][4]['name']="cfg_webname";
$z[3]=Array("field","",365,394);
$z[3][4]['name']="keywords";
$z[4]=Array("field","",435,454);
$z[4][4]['name']="title";
$z[5]=Array("pagelist","",460,533);
$z[5][4]['listsize']="0";
$z[5][4]['listitem']="pageno";
$z[5][4]['function']="html2text(@me)";
$z[6]=Array("field","",537,595);
$z[6][4]['name']="description";
$z[6][4]['function']="html2text(@me)";
$z[7]=Array("global","",646,671);
$z[7][4]['name']="cfg_cmsurl";
$z[8]=Array("global","",736,761);
$z[8][4]['name']="cfg_cmsurl";
$z[9]=Array("global","",907,936);
$z[9][4]['name']="cfg_templeturl";
$z[10]=Array("include","",1478,1514);
$z[10][4]['filename']="head2.htm";
$z[11]=Array("field","",1597,1626);
$z[11][4]['name']="position";
$z[12]=Array("list","\r\n			<div class=\"g\">\r\n				<h2><span>分类：<a href=\"[field:typeurl/]\" target=\"_blank\">[[field:typename/]]</a></span><a href=\"[field:arcurl/]\" target=\"_blank\" title=\"[field:title  function='html2text(@me)' /]\" id=\"list_title\">[field:title function=\"cn_substr(Html2text(@me),75)\" /]</a><em class=\"list_time\">[field:stime/]</em></h2>\r\n				<p>[field:array runphp='yes']@me = (strpos(@me['litpic'],'defaultpic') ? \"\" : \"<a href='{@me['arcurl']}' class='imgview' target='_blank'><img src='{@me['litpic']}' align='right' alt='{@me['title']}' width='100' height='100'/></a>\"); [/field:array] [field:description  function=\"cn_substr(Html2text(@me),250)\"/]... <a href=\"[field:arcurl/]\" target=\"_blank\">阅读全文>></a> <span class=\"data\"><em class=\"list_view\">[field:click/]围观</em><em class=\"list_tag\">标签：[field:id function=GetTags(@me)/] \r\n		   </em></span> </p>\r\n			</div>\r\n			",1682,2617);
$z[12][4]['pagesize']="15";
$z[12][4]['titlelen']="120";
$z[13]=Array("pagelist","",2682,2753);
$z[13][4]['listitem']="info,index,end,pre,next,pageno";
$z[13][4]['listsize']="4";
$z[14]=Array("global","",2863,2888);
$z[14][4]['name']="cfg_cmsurl";
$z[15]=Array("arclist","\r\n				  <li><a href=\"[field:arcurl/]\" target=\"_blank\">[field:title/]</a></li>\r\n				",3073,3232);
$z[15][4]['flag']="c";
$z[15][4]['row']="8";
$z[15][4]['orderby']="pubdate";
$z[15][4]['titlelen']="45";
$z[16]=Array("arclist","\r\n				<li>\r\n					<div class=\"diggNum\"><a href=\"[field:arcurl/]\" target=\"_blank\">[field:click/]</a></div>\r\n					<div class=\"diggLink\"><a href=\"[field:arcurl/]\" target=\"_blank\">[field:title/]</a></div>\r\n				</li>\r\n				",3738,4038);
$z[16][4]['row']="5";
$z[16][4]['titlelen']="80";
$z[16][4]['noflag']="h";
$z[16][4]['orderby']="hot";
$z[16][4]['subday']="3";
$z[17]=Array("arclist","\r\n				<li>\r\n					<div class=\"diggNum\"><a href=\"[field:arcurl/]\" target=\"_blank\">[field:click/]</a></div>\r\n					<div class=\"diggLink\"><a href=\"[field:arcurl/]\" target=\"_blank\">[field:title/]</a></div>\r\n				</li>\r\n				",4116,4416);
$z[17][4]['row']="5";
$z[17][4]['titlelen']="80";
$z[17][4]['noflag']="h";
$z[17][4]['orderby']="hot";
$z[17][4]['subday']="7";
$z[18]=Array("arclist","\r\n				<li>\r\n					<div class=\"diggNum\"><a href=\"[field:arcurl/]\" target=\"_blank\">[field:click/]</a></div>\r\n					<div class=\"diggLink\"><a href=\"[field:arcurl/]\" target=\"_blank\">[field:title/]</a></div>\r\n				</li>\r\n				",4494,4795);
$z[18][4]['row']="5";
$z[18][4]['titlelen']="80";
$z[18][4]['noflag']="h";
$z[18][4]['orderby']="hot";
$z[18][4]['subday']="30";
$z[19]=Array("tag"," <a href='[field:link/]' target=\"_blank\" style=\"[field:total runphp=yes]@me=getTagStyle();[/field:total]\">[field:tag /]</a> ",4984,5160);
$z[19][4]['row']="20";
$z[19][4]['sort']="new";
$z[19][4]['getall']="0";
$z[20]=Array("include","",5855,5893);
$z[20][4]['filename']="footer2.htm";

?>