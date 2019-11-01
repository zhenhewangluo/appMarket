{dede:noteinfo notename="安卓哥新闻" channelid="1" macthtype="regex"
refurl="http://www.apkge.com/page1.html" sourcelang="utf-8" cosort="asc" isref="yes" exptime="20" usemore="0" /}

{dede:listrule sourcetype="batch" rssurl="http://" regxurl="http://www.apkge.com/page(*).html"
startid="3" endid="5" addv="1" urlrule="area"
 musthas=".html" nothas="" listpic="1" usemore="0"}
    {dede:addurls}{/dede:addurls}
    {dede:batchrule}{/dede:batchrule}
    {dede:regxrule}{/dede:regxrule}
    {dede:areastart}<div class="bg0">{/dede:areastart}
    {dede:areaend}</div><div id="pager" >{/dede:areaend}
{/dede:listrule}
