{dede:noteinfo notename="安卓哥资讯2" channelid="1" macthtype="string"
    refurl="http://www.apkge.com/news/page1.html" sourcelang="utf-8" cosort="asc"
  isref="no" exptime="10" usemore="0" /}
{dede:listrule sourcetype="batch" rssurl="http://" regxurl="http://www.apkge.com/news/page(*).html"
startid="2" endid="4" addv="1" urlrule="area" musthas=".html"
 nothas="<span([^>]*)>([^>]*)</span>" listpic="1" usemore="0"}
    {dede:addurls}{/dede:addurls}
    {dede:batchrule}{/dede:batchrule}
    {dede:regxrule}{/dede:regxrule}
    {dede:areastart}<div class="bg0">{/dede:areastart}
    {dede:areaend}<div id="pager" >{/dede:areaend}
{/dede:listrule}
