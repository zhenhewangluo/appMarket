<?php
$z[0]=Array("listconfig","\r\n{dede:noteinfo notename=\"��������\" channelid=\"1\" macthtype=\"string\"\r\nrefurl=\"http://\" sourcelang=\"gb2312\" cosort=\"asc\" isref=\"no\" exptime=\"10\" usemore=\"0\" /}\r\n\r\n{dede:listrule sourcetype=\"batch\" rssurl=\"http://\" regxurl=\"\"\r\nstartid=\"1\" endid=\"\" addv=\"1\" urlrule=\"area\"\r\n musthas=\"news.sohu.com\" nothas=\"\" listpic=\"1\" usemore=\"0\"}\r\n	{dede:addurls}http://news.sohu.com/s2005/zixun.shtml{/dede:addurls}\r\n	{dede:batchrule}{/dede:batchrule}\r\n	{dede:regxrule}{/dede:regxrule}\r\n	{dede:areastart}<div class=\"f14list\">\r\n{/dede:areastart}\r\n	{dede:areaend}<div class=\"pages\">{/dede:areaend}\r\n{/dede:listrule}\r\n\r\n",0,638);
$z[1]=Array("itemconfig","\r\n{dede:sppage sptype='full'}{/dede:sppage}\r\n{dede:previewurl}http://news.sohu.com/20100421/n271657713.shtml{/dede:previewurl}\r\n{dede:keywordtrim}{/dede:keywordtrim}\r\n{dede:descriptiontrim}{/dede:descriptiontrim}\r\n{dede:item field='title' value='' isunit='' isdown=''}\r\n   {dede:match}<title>[����]-�Ѻ�����</title>{/dede:match}\r\n   \r\n   {dede:function}{/dede:function}\r\n{/dede:item}\r\n{dede:item field='writer' value='' isunit='' isdown=''}\r\n   {dede:match}{/dede:match}\r\n   \r\n   {dede:function}{/dede:function}\r\n{/dede:item}\r\n{dede:item field='source' value='' isunit='' isdown=''}\r\n   {dede:match}{/dede:match}\r\n   \r\n   {dede:function}{/dede:function}\r\n{/dede:item}\r\n{dede:item field='pubdate' value='' isunit='' isdown=''}\r\n   {dede:match}{/dede:match}\r\n   \r\n   {dede:function}@me=GetMkTime(@me);{/dede:function}\r\n{/dede:item}\r\n{dede:item field='body' value='' isunit='1' isdown='1'}\r\n   {dede:match}<!-- ���� st -->[����]<!-- ���� end -->{/dede:match}\r\n   {dede:trim replace=''}<a([^>]*)>(.*)</a>{/dede:trim}\r\n{dede:trim replace=''}<script([^>]*)>(.*)</script>{/dede:trim}\r\n{dede:trim replace=''}<!--(.*)-->{/dede:trim}\r\n{dede:trim replace=''}<table([^>]*)>(.*)</table>{/dede:trim}\r\n{dede:trim replace=''}<style([^>]*)>(.*)</style>{/dede:trim}\r\n{dede:trim replace=''}<object([^>]*)>(.*)</object>{/dede:trim}\r\n{dede:trim replace=''}<embed([^>]*)>(.*)</embed>{/dede:trim}\r\n{dede:trim replace=''}<iframe([^>]*)>(.*)</iframe>{/dede:trim}\r\n{dede:trim replace=''}<param([^>]*)>(.*)</param>{/dede:trim}\r\n{dede:trim replace=''}<div([^.]*)>{/dede:trim}\r\n{dede:trim replace=''}</div>{/dede:trim}\r\n   {dede:function}{/dede:function}\r\n{/dede:item}\r\n\r\n",642,2320);

?>