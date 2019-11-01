{dede:listconfig}
{dede:noteinfo notename="³±Á÷½ÚÅÄ" channelid="1" macthtype="string"
	refurl="http://" sourcelang="gb2312" cosort="asc"
  isref="no" exptime="10" usemore="0" /}
{dede:listrule sourcetype="batch" rssurl="http://" regxurl="http://woman.39.net/nxsh/gjgw/jtcl/index_(*).html"
startid="2" endid="3" addv="1" urlrule="area" musthas="woman.39.net/"
 nothas="" listpic="0" usemore="0"}
	{dede:addurls}http://woman.39.net/nxsh/gjgw/jtcl/{/dede:addurls}
	{dede:batchrule}{/dede:batchrule}
	{dede:regxrule}{/dede:regxrule}
	{dede:areastart}<div class="newslist">
{/dede:areastart}
	{dede:areaend}<div class="page">
{/dede:areaend}
{/dede:listrule}

{/dede:listconfig}

{dede:itemconfig}
{dede:sppage sptype='full'}{/dede:sppage}
{dede:previewurl}http://fitness.39.net/jbjf/104/19/1231272.html{/dede:previewurl}
{dede:keywordtrim}{/dede:keywordtrim}
{dede:descriptiontrim}{/dede:descriptiontrim}
{dede:item field='title' value='' isunit='' isdown=''}
   {dede:match}<title>[ÄÚÈÝ]_39½¡¿µÍø</title>{/dede:match}
   
   {dede:function}{/dede:function}
{/dede:item}{dede:item field='writer' value='' isunit='' isdown=''}
   {dede:match}{/dede:match}
   
   {dede:function}{/dede:function}
{/dede:item}{dede:item field='source' value='' isunit='' isdown=''}
   {dede:match}{/dede:match}
   
   {dede:function}{/dede:function}
{/dede:item}{dede:item field='pubdate' value='' isunit='' isdown=''}
   {dede:match}{/dede:match}
   
   {dede:function}{/dede:function}
{/dede:item}{dede:item field='body' value='' isunit='1' isdown='1'}
   {dede:match}<div class="article">[ÄÚÈÝ]<div class="page"{/dede:match}
   {dede:trim replace=""}<a([^>]*)>(.*)</a>{/dede:trim}
{dede:trim replace=""}<script([^>]*)>(.*)</script>{/dede:trim}
{dede:trim replace=""}<!--(.*)-->{/dede:trim}
{dede:trim replace=""}<table([^>]*)>(.*)</table>{/dede:trim}
{dede:trim replace=""}<style([^>]*)>(.*)</style>{/dede:trim}
{dede:trim replace=""}<object([^>]*)>(.*)</object>{/dede:trim}
{dede:trim replace=""}<embed([^>]*)>(.*)</embed>{/dede:trim}
{dede:trim replace=""}<iframe([^>]*)>(.*)</iframe>{/dede:trim}
{dede:trim replace=""}<param([^>]*)>(.*)</param>{/dede:trim}
{dede:trim replace=""}<div([^.]*)>{/dede:trim}
{dede:trim replace=""}</div>{/dede:trim}
   {dede:function}{/dede:function}
{/dede:item}
{/dede:itemconfig}