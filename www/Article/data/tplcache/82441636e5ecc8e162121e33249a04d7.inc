{dede:noteinfo notename="安卓哥采集到新闻" channelid="1" macthtype="string"
    refurl="http://www.apkge.com/page1.html" sourcelang="utf-8" cosort="asc"
  isref="no" exptime="5" usemore="0" /}
{dede:listrule sourcetype="hand" rssurl="http://" regxurl="http://www.apkge.com/page(*).html"
startid="1" endid="2" addv="1" urlrule="area" musthas=".html"
 nothas="" listpic="1" usemore="0"}
    {dede:addurls}http://www.apkge.com/page1.html{/dede:addurls}
    {dede:batchrule}http://www.apkge.com/page1.html
http://www.apkge.com/page2.html{/dede:batchrule}
    {dede:regxrule}{/dede:regxrule}
    {dede:areastart}<div class="bg0">{/dede:areastart}
    {dede:areaend}<div id="pager" >{/dede:areaend}
{/dede:listrule}