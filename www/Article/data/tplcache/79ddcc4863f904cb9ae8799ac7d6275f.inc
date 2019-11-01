
   {dede:match}<div id="newstt" class="tt nt"><h1>[内容]</h1><span class="tt_right hs">{/dede:match}
   {dede:trim replace=''}<a([^>]*)>(.*)</a>{/dede:trim}
{dede:trim replace=''}<!--(.*)-->{/dede:trim}
{dede:trim replace=''}<table([^>]*)>(.*)</table>{/dede:trim}
{dede:trim replace=''}<style([^>]*)>(.*)</style>{/dede:trim}
{dede:trim replace=''}<img([^>]*)>{/dede:trim}
{dede:trim replace=''}<div([^.]*)>{/dede:trim}
{dede:trim replace=''}</div>{/dede:trim}
   {dede:function}{/dede:function}
