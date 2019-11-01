<?php
$z[0]=Array("match","<div id=\"newbox\">[内容<span id=\"Lb_laiyuan\" class=\"hs\">",4,86);
$z[1]=Array("trim","<a([^>]*)>(.*)</a>",90,142);
$z[1][4]['replace']="";
$z[2]=Array("trim","<script([^>]*)>(.*)</script>",144,206);
$z[2][4]['replace']="";
$z[3]=Array("trim","<style([^>]*)>(.*)</style>",208,268);
$z[3][4]['replace']="";
$z[4]=Array("trim","<div([^.]*)>",270,316);
$z[4][4]['replace']="";
$z[5]=Array("trim","</div>",318,358);
$z[5][4]['replace']="";
$z[6]=Array("function","",362,393);

?>