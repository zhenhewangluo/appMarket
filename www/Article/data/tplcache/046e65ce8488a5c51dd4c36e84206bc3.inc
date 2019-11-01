{dede:listconfig}
{dede:noteinfo notename="经济新闻" channelid="1" macthtype="string"
refurl="http://" sourcelang="gb2312" cosort="asc" isref="no" exptime="10" usemore="0" /}

{dede:listrule sourcetype="batch" rssurl="http://" regxurl=""
startid="1" endid="" addv="1" urlrule="area"
 musthas="news.sohu.com" nothas="" listpic="1" usemore="0"}
	{dede:addurls}http://news.sohu.com/s2005/zixun.shtml{/dede:addurls}
	{dede:batchrule}{/dede:batchrule}
	{dede:regxrule}{/dede:regxrule}
	{dede:areastart}<div class="f14list">
{/dede:areastart}
	{dede:areaend}<div class="pages">{/dede:areaend}
{/dede:listrule}

{/dede:listconfig}

{dede:itemconfig}
{dede:sppage sptype='full'}{/dede:sppage}
{dede:previewurl}http://news.sohu.com/20100421/n271657713.shtml{/dede:previewurl}
{dede:keywordtrim}{/dede:keywordtrim}
{dede:descriptiontrim}{/dede:descriptiontrim}
{dede:item field='title' value='' isunit='' isdown=''}
   {dede:match}<title>[内容]-搜狐新闻</title>{/dede:match}
   
   {dede:function}{/dede:function}
{/dede:item}
{dede:item field='writer' value='' isunit='' isdown=''}
   {dede:match}{/dede:match}
   
   {dede:function}{/dede:function}
{/dede:item}
{dede:item field='source' value='' isunit='' isdown=''}
   {dede:match}{/dede:match}
   
   {dede:function}{/dede:function}
{/dede:item}
{dede:item field='pubdate' value='' isunit='' isdown=''}
   {dede:match}{/dede:match}
   
   {dede:function}@me=GetMkTime(@me);{/dede:function}
{/dede:item}
{dede:item field='body' value='' isunit='1' isdown='1'}
   {dede:match}<!-- 正文 st -->[内容]<!-- 正文 end -->{/dede:match}
   {dede:trim replace=''}<a([^>]*)>(.*)</a>{/dede:trim}
{dede:trim replace=''}<script([^>]*)>(.*)</script>{/dede:trim}
{dede:trim replace=''}<!--(.*)-->{/dede:trim}
{dede:trim replace=''}<table([^>]*)>(.*)</table>{/dede:trim}
{dede:trim replace=''}<style([^>]*)>(.*)</style>{/dede:trim}
{dede:trim replace=''}<object([^>]*)>(.*)</object>{/dede:trim}
{dede:trim replace=''}<embed([^>]*)>(.*)</embed>{/dede:trim}
{dede:trim replace=''}<iframe([^>]*)>(.*)</iframe>{/dede:trim}
{dede:trim replace=''}<param([^>]*)>(.*)</param>{/dede:trim}
{dede:trim replace=''}<div([^.]*)>{/dede:trim}
{dede:trim replace=''}</div>{/dede:trim}
   {dede:function}{/dede:function}
{/dede:item}

{/dede:itemconfig}