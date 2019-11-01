{dede:listconfig}
{dede:noteinfo notename="金毛介绍" channelid="1" macthtype="string"
	refurl="http://" sourcelang="gb2312" cosort="asc"
  isref="no" exptime="10" usemore="0" /}
{dede:listrule sourcetype="batch" rssurl="http://" regxurl="http://www.jinmao.org/jieshao/2_(*).html"
startid="2" endid="15" addv="1" urlrule="area" musthas=""
 nothas="" listpic="1" usemore="0"}
	{dede:addurls}http://www.jinmao.org/jieshao/{/dede:addurls}
	{dede:batchrule}{/dede:batchrule}
	{dede:regxrule}{/dede:regxrule}
	{dede:areastart}内容列表{/dede:areastart}
	{dede:areaend}<ul class="pagelist">
{/dede:areaend}
{/dede:listrule}

{/dede:listconfig}

{dede:itemconfig}
{dede:sppage sptype='full'}{/dede:sppage}
{dede:previewurl}http://www.jinmao.org/762.html{/dede:previewurl}
{dede:keywordtrim}{/dede:keywordtrim}
{dede:descriptiontrim}{/dede:descriptiontrim}
{dede:item field='title' value='' isunit='' isdown=''}
   {dede:match}<title>[内容]</title>{/dede:match}
   
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
   {dede:match}点击: 次[内容]阅读了{/dede:match}
   {dede:trim replace=''}<a([^>]*)>(.*)</a>{/dede:trim}
{dede:trim replace=''}<script([^>]*)>(.*)</script>{/dede:trim}
{dede:trim replace=''}<!--(.*)-->{/dede:trim}
{dede:trim replace=''}<style([^>]*)>(.*)</style>{/dede:trim}
{dede:trim replace=''}<img([^>]*)>{/dede:trim}
{dede:trim replace=''}<object([^>]*)>(.*)</object>{/dede:trim}
{dede:trim replace=''}<embed([^>]*)>(.*)</embed>{/dede:trim}
{dede:trim replace=''}<iframe([^>]*)>(.*)</iframe>{/dede:trim}
{dede:trim replace=''}<param([^>]*)>(.*)</param>{/dede:trim}
   {dede:function}{/dede:function}
{/dede:item}
{/dede:itemconfig}