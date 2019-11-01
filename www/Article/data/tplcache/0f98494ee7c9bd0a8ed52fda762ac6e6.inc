{dede:noteinfo notename="安卓哥采集到新闻" channelid="1" macthtype="string"
    refurl="http://www.apkge.com/page1.html" sourcelang="utf-8" cosort="asc"
  isref="yes" exptime="5" usemore="0" /}
{dede:listrule sourcetype="batch" rssurl="http://" regxurl="http://www.apkge.com/page(*).html"
startid="80" endid="88" addv="1" urlrule="area" musthas=""
 nothas="" listpic="0" usemore="0"}
    {dede:addurls}{/dede:addurls}
    {dede:batchrule}{/dede:batchrule}
    {dede:regxrule}{/dede:regxrule}
    {dede:areastart}<div id="left"><div id="newstt" class="tt"><strong>最新安卓动态</strong></div><div class="bg0">{/dede:areastart}
    {dede:areaend}</div><div id="right">{/dede:areaend}
{/dede:listrule}
{dede:sppage sptype='full' srul='1' erul='5'}{/dede:sppage}
{dede:previewurl}http://www.apkge.com/info/1533.html{/dede:previewurl}
{dede:keywordtrim}{/dede:keywordtrim}
{dede:descriptiontrim}{/dede:descriptiontrim}
{dede:item field='title' value='' isunit='' isdown=''}
   {dede:match}<div id="newstt" class="tt nt"><h1>[内容]</h1><span class="tt_right hs">{/dede:match}
   
   {dede:function}{/dede:function}
{/dede:item}{dede:item field='writer' value='' isunit='' isdown=''}
   {dede:match}[鹏哥哥]{/dede:match}
   
   {dede:function}{/dede:function}
{/dede:item}{dede:item field='source' value='' isunit='' isdown=''}
   {dede:match}[安卓哥]{/dede:match}
   
   {dede:function}{/dede:function}
{/dede:item}{dede:item field='pubdate' value='' isunit='' isdown=''}
   {dede:match}<span id="lb_bj">daiwei 发布于[内容] | 111 次阅读</span>{/dede:match}
   
   {dede:function}{/dede:function}
{/dede:item}{dede:item field='body' value='' isunit='' isdown='1'}
   {dede:match}<div id="boxsm">[内容]</p></div>
                 </div>{/dede:match}
   
   {dede:function}{/dede:function}
{/dede:item}{dede:item field='download_url' value='' isunit='' isdown=''}
   {dede:match}{/dede:match}
   
   {dede:function}{/dede:function}
{/dede:item}{dede:item field='app_id' value='' isunit='' isdown=''}
   {dede:match}{/dede:match}
   
   {dede:function}{/dede:function}
{/dede:item}