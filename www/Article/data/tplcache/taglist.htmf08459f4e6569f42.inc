<?php
$z[0]=Array("global","",244,272);
$z[0][4]['name']="cfg_soft_lang";
$z[1]=Array("field","",290,309);
$z[1][4]['name']="title";
$z[2]=Array("pagelist","",312,385);
$z[2][4]['listsize']="0";
$z[2][4]['listitem']="pageno";
$z[2][4]['function']="html2text(@me)";
$z[3]=Array("global","",386,412);
$z[3][4]['name']="cfg_webname";
$z[4]=Array("global","",469,494);
$z[4][4]['name']="cfg_cmsurl";
$z[5]=Array("global","",561,586);
$z[5][4]['name']="cfg_cmsurl";
$z[6]=Array("global","",738,767);
$z[6][4]['name']="cfg_templeturl";
$z[7]=Array("include","",1321,1357);
$z[7][4]['filename']="head2.htm";
$z[8]=Array("global","",1475,1500);
$z[8][4]['name']="cfg_cmsurl";
$z[9]=Array("field","",1525,1544);
$z[9][4]['name']="title";
$z[10]=Array("field","",1646,1665);
$z[10][4]['name']="title";
$z[11]=Array("list","\r\n\r\n			<div class=\"g\">\r\n\r\n				<h2><span>分类：<a href=\"[field:typeurl/]\" target=\"_blank\">[[field:typename/]]</a></span><a href=\"[field:arcurl/]\" target=\"_blank\" title=\"[field:title  function='html2text(@me)' /]\">[field:title function=\"cn_substr(Html2text(@me),50)\" /]</a><em class=\"list_time\">[field:stime/]</em></h2>\r\n\r\n				<p>[field:array runphp='yes']@me = (strpos(@me['litpic'],'defaultpic') ? \"\" : \"<a href='{@me['arcurl']}' class='imgview' target='_blank'><img src='{@me['litpic']}' align='right' alt='{@me['title']}' width='160' height='120'/></a>\"); [/field:array] [field:description  function=\"cn_substr(Html2text(@me),250)\"/]... <a href=\"[field:arcurl/]\" target=\"_blank\">阅读全文&gt;&gt;</a> <span class=\"data\"><em class=\"list_view\">[field:click/]围观</em><em class=\"list_tag\">标签：[field:id function=GetTags(@me)/] \r\n\r\n		   </em></span> </p>\r\n\r\n			</div>\r\n\r\n			",1698,2654);
$z[11][4]['orderby']="sortrank";
$z[11][4]['pagesize']="10";
$z[11][4]['titlelen']="120";
$z[12]=Array("pagelist","",2727,2756);
$z[12][4]['listsize']="4";
$z[13]=Array("tag"," <a href='[field:link/]'>[field:tag /]</a> ",2868,2965);
$z[13][4]['row']="10";
$z[13][4]['sort']="month";
$z[13][4]['getall']="0";
$z[14]=Array("myad","",3058,3083);
$z[14][4]['name']="index";
$z[15]=Array("arclist","\r\n\r\n				<li><a href=\"[field:arcurl/]\" target=\"_blank\">[field:title/]</a></li>\r\n\r\n				",3220,3382);
$z[15][4]['flag']="c";
$z[15][4]['row']="10";
$z[15][4]['orderby']="pubdate";
$z[15][4]['titlelen']="35";
$z[16]=Array("include","",4100,4138);
$z[16][4]['filename']="footer2.htm";

?>