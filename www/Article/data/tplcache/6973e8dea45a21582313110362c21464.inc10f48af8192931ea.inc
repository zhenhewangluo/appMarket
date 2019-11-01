<?php
$z[0]=Array("match","<div id=\"newbox\">[内容]<span id=\"Lb_laiyuan\" class=\"hs\">",5,88);
$z[1]=Array("trim","<style([^>]*)>(.*)</style>",93,153);
$z[1][4]['replace']="";
$z[2]=Array("trim","<div([^.]*)>",155,201);
$z[2][4]['replace']="";
$z[3]=Array("trim","</div>",203,243);
$z[3][4]['replace']="";
$z[4]=Array("function","",248,279);

?>