<?php
$z[0]=Array("match","<div id=\"newbox\">[内容]<span id=\"Lb_laiyuan\" class=\"hs\">",4,87);
$z[1]=Array("trim","<style([^>]*)>(.*)</style>",91,151);
$z[1][4]['replace']="";
$z[2]=Array("trim","<div([^.]*)>",153,199);
$z[2][4]['replace']="";
$z[3]=Array("trim","</div>",201,241);
$z[3][4]['replace']="";
$z[4]=Array("function","",245,276);

?>