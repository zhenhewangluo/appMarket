<?php
$z[0]=Array("noteinfo","",0,202);
$z[0][4]['notename']="安卓哥采集到新闻";
$z[0][4]['channelid']="1";
$z[0][4]['macthtype']="string";
$z[0][4]['refurl']="http://www.apkge.com/page1.html";
$z[0][4]['sourcelang']="utf-8";
$z[0][4]['cosort']="asc";
$z[0][4]['isref']="yes";
$z[0][4]['exptime']="20";
$z[0][4]['usemore']="0";
$z[1]=Array("listrule","\n    {dede:addurls}{/dede:addurls}\n    {dede:batchrule}http://www.apkge.com/page1.html\r\nhttp://www.apkge.com/page2.html{/dede:batchrule}\n    {dede:regxrule}{/dede:regxrule}\n    {dede:areastart}<div class=\"bg0\">{/dede:areastart}\n    {dede:areaend}<div id=\"pager\" >{/dede:areaend}\n",203,692);
$z[1][4]['sourcetype']="batch";
$z[1][4]['rssurl']="http://";
$z[1][4]['regxurl']="http://www.apkge.com/page(*).html";
$z[1][4]['startid']="80";
$z[1][4]['endid']="88";
$z[1][4]['addv']="1";
$z[1][4]['urlrule']="area";
$z[1][4]['musthas']=".html";
$z[1][4]['nothas']="";
$z[1][4]['listpic']="1";
$z[1][4]['usemore']="0";
$z[2]=Array("sppage","",694,753);
$z[2][4]['sptype']="full";
$z[2][4]['srul']="1";
$z[2][4]['erul']="5";
$z[3]=Array("previewurl","http://www.apkge.com/info/169.html",755,824);
$z[4]=Array("keywordtrim","",826,863);
$z[5]=Array("descriptiontrim","",865,910);
$z[6]=Array("item","\n   {dede:match}<h1>[内容]</h1>{/dede:match}\n   \n   {dede:function}{/dede:function}\n",912,1064);
$z[6][4]['field']="title";
$z[6][4]['value']="";
$z[6][4]['isunit']="";
$z[6][4]['isdown']="";
$z[7]=Array("item","\n   {dede:match}<div id=\"newhead\">\r\n                    <span id=\"lb_bj\">[内容]发布于{/dede:match}\n   {dede:trim replace='鹏哥哥'}[内容]{/dede:trim}\n   {dede:function}{/dede:function}\n",1064,1325);
$z[7][4]['field']="writer";
$z[7][4]['value']="";
$z[7][4]['isunit']="";
$z[7][4]['isdown']="";
$z[8]=Array("item","\n   {dede:match}<div id=\"newhead\">\r\n                    <span id=\"lb_bj\">[内容]发布于{/dede:match}\n   {dede:trim replace='安卓哥'}[内容]{/dede:trim}\n   {dede:function}{/dede:function}\n",1325,1586);
$z[8][4]['field']="source";
$z[8][4]['value']="";
$z[8][4]['isunit']="";
$z[8][4]['isdown']="";
$z[9]=Array("item","\n   {dede:match}发布于[内容]|{/dede:match}\n   \n   {dede:function}{/dede:function}\n",1586,1741);
$z[9][4]['field']="pubdate";
$z[9][4]['value']="";
$z[9][4]['isunit']="";
$z[9][4]['isdown']="";
$z[10]=Array("item","\n   {dede:match}<div id=\"newbox\">[内容]<span id=\"Lb_laiyuan\" class=\"hs\">{/dede:match}\n   {dede:trim replace=\"\"}<style([^>]*)>(.*)</style>{/dede:trim}\r\n{dede:trim replace=\"\"}<div([^.]*)>{/dede:trim}\r\n{dede:trim replace=\"\"}</div>{/dede:trim}\n   {dede:function}{/dede:function}\n",1741,2085);
$z[10][4]['field']="body";
$z[10][4]['value']="";
$z[10][4]['isunit']="1";
$z[10][4]['isdown']="1";
$z[11]=Array("item","\n   {dede:match}{/dede:match}\n   \n   {dede:function}{/dede:function}\n",2085,2227);
$z[11][4]['field']="download_url";
$z[11][4]['value']="";
$z[11][4]['isunit']="";
$z[11][4]['isdown']="";
$z[12]=Array("item","\n   {dede:match}{/dede:match}\n   \n   {dede:function}{/dede:function}\n",2227,2363);
$z[12][4]['field']="app_id";
$z[12][4]['value']="";
$z[12][4]['isunit']="";
$z[12][4]['isdown']="";

?>