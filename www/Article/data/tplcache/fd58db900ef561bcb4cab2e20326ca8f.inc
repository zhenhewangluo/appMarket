{dede:noteinfo notename="安卓哥采集到新闻" channelid="1" macthtype="string"
    refurl="http://www.apkge.com/page1.html" sourcelang="utf-8" cosort="asc"
  isref="yes" exptime="60" usemore="0" /}
{dede:listrule sourcetype="batch" rssurl="http://" regxurl="http://www.apkge.com/page(*).html"
startid="80" endid="88" addv="1" urlrule="area" musthas=""
 nothas="" listpic="1" usemore="0"}
    {dede:addurls}{/dede:addurls}
    {dede:batchrule}{/dede:batchrule}
    {dede:regxrule}{/dede:regxrule}
    {dede:areastart}<div id="left"><div id="newstt" class="tt"><strong>最新安卓动态</strong></div><div class="bg0">{/dede:areastart}
    {dede:areaend}</div><div id="right">{/dede:areaend}
{/dede:listrule}