{dede:noteinfo notename="安卓哥" channelid="1" macthtype="string"
    refurl="http://" sourcelang="utf-8" cosort="asc"
  isref="no" exptime="10" usemore="0" /}
{dede:listrule sourcetype="batch" rssurl="http://" regxurl="http://www.apkge.com/news/page(*).html"
startid="108" endid="108" addv="1" urlrule="area" musthas="html"
 nothas="" listpic="1" usemore="0"}
    {dede:addurls}{/dede:addurls}
    {dede:batchrule}{/dede:batchrule}
    {dede:regxrule}{/dede:regxrule}
    {dede:areastart}<div id="left">{/dede:areastart}
    {dede:areaend}<div id="pager" >{/dede:areaend}
{/dede:listrule}
{dede:sppage sptype='full' srul='1' erul='5'}<div class="pagebreak">[内容]</div>{/dede:sppage}
{dede:previewurl}http://www.apkge.com/info/1816.html{/dede:previewurl}
{dede:keywordtrim}{/dede:keywordtrim}
{dede:descriptiontrim}{/dede:descriptiontrim}
{dede:item field='title' value='' isunit='' isdown=''}
   {dede:match}<h1>[内容]</h1>{/dede:match}
   
   {dede:function}{/dede:function}
{/dede:item}{dede:item field='writer' value='' isunit='' isdown=''}
   {dede:match}<span class="tt_right hs">[内容]<strong id="dqtj">{/dede:match}
   {dede:trim replace="鹏哥哥"}被推荐{/dede:trim}
   {dede:function}{/dede:function}
{/dede:item}{dede:item field='source' value='' isunit='' isdown=''}
   {dede:match}<span class="tt_right hs">[内容]<strong id="dqtj">{/dede:match}
   {dede:trim replace="鹏哥哥"}被推荐{/dede:trim}
   {dede:function}{/dede:function}
{/dede:item}{dede:item field='pubdate' value='' isunit='' isdown=''}
   {dede:match}<span id="lb_bj">daiwei 发布于[内容]|{/dede:match}
   
   {dede:function}{/dede:function}
{/dede:item}{dede:item field='body' value='' isunit='1' isdown='1'}
   {dede:match}<div id="boxsm">[内容]<div id="tabbox">{/dede:match}
   {dede:trim replace=""}<a([^>]*)>(.*)</a>{/dede:trim}
{dede:trim replace=""}<script([^>]*)>(.*)</script>{/dede:trim}
{dede:trim replace=""}<!--(.*)-->{/dede:trim}
{dede:trim replace=""}<object([^>]*)>(.*)</object>{/dede:trim}
{dede:trim replace=""}<div([^.]*)>{/dede:trim}
{dede:trim replace=""}</div>{/dede:trim}
   {dede:function}{/dede:function}
{/dede:item}{dede:item field='download_url' value='' isunit='' isdown=''}
   {dede:match}{/dede:match}
   
   {dede:function}{/dede:function}
{/dede:item}{dede:item field='app_id' value='' isunit='' isdown=''}
   {dede:match}{/dede:match}
   
   {dede:function}{/dede:function}
{/dede:item}