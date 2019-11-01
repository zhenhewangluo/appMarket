{dede:noteinfo notename="安卓哥新闻" channelid="1" macthtype="string"
refurl="http://www.apkge.com/page1.html" sourcelang="gb2312" cosort="asc" isref="yes" exptime="10" usemore="0" /}

{dede:listrule sourcetype="batch" rssurl="http://" regxurl="http://www.apkge.com/page(*).html"
startid="1" endid="2" addv="1" urlrule="area"
 musthas=".html" nothas="" listpic="1" usemore="0"}
    {dede:addurls}{/dede:addurls}
    {dede:batchrule}{/dede:batchrule}
    {dede:regxrule}{/dede:regxrule}
    {dede:areastart}<div class="bg0">{/dede:areastart}
    {dede:areaend}<div id="pager" >{/dede:areaend}
{/dede:listrule}
