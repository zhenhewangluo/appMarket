
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

