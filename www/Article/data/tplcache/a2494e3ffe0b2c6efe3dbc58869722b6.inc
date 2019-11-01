
   {dede:match}<div id="boxsm">[内容]<div id="tabbox">{/dede:match}
   {dede:trim replace=""}<a([^>]*)>(.*)</a>{/dede:trim}
{dede:trim replace=""}<script([^>]*)>(.*)</script>{/dede:trim}
{dede:trim replace=""}<!--(.*)-->{/dede:trim}
{dede:trim replace=""}<object([^>]*)>(.*)</object>{/dede:trim}
{dede:trim replace=""}<div([^.]*)>{/dede:trim}
{dede:trim replace=""}</div>{/dede:trim}
   {dede:function}{/dede:function}
