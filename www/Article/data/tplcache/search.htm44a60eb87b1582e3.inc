<?php
$z[0]=Array("global","",255,284);
$z[0][4]['name']="keyword";
$z[1]=Array("global","",298,324);
$z[1][4]['name']="cfg_webname";
$z[2]=Array("global","",586,615);
$z[2][4]['name']="cfg_templeturl";
$z[3]=Array("include","",697,733);
$z[3][4]['filename']="head2.htm";
$z[4]=Array("global","",825,854);
$z[4][4]['name']="keyword";
$z[5]=Array("list","\n\n			<div class=\"g\">\n\n				<h2><span>分类：<a href=\"[field:typeurl/]\" target=\"_blank\">[[field:typename/]]</a></span><a href=\"[field:arcurl/]\" target=\"_blank\" title=\"[field:title  function='html2text(@me)' /]\">[field:title function=\"cn_substr(Html2text(@me),50)\" /]</a><em class=\"list_time\">[field:stime/]</em></h2>\n\n				<p>[field:array runphp='yes']@me = (strpos(@me['litpic'],'defaultpic') ? \"\" : \"<a href='{@me['arcurl']}' class='imgview' target='_blank'><img src='{@me['litpic']}' align='right' alt='{@me['title']}' width='100' height='100'/></a>\"); [/field:array] [field:description/]... <a href=\"[field:arcurl/]\" target=\"_blank\">阅读全文&gt;&gt;</a> <span class=\"data\"><em class=\"list_view\">[field:click/]围观</em><em class=\"list_tag\">标签：[field:id function=GetTags(@me)/] \n\n		   </em></span> </p>\n\n			</div>\n\n			",901,1784);
$z[5][4]['pagesize']="15";
$z[5][4]['titlelen']="120";
$z[6]=Array("pagelist","",1846,1917);
$z[6][4]['listitem']="info,index,end,pre,next,pageno";
$z[6][4]['listsize']="3";
$z[7]=Array("likewords","",2010,2035);
$z[7][4]['num']="8";
$z[8]=Array("include","",3021,3059);
$z[8][4]['filename']="footer2.htm";

?>