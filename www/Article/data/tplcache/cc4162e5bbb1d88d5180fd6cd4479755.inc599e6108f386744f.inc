<?php
$z[0]=Array("match","<div id=\"newbox\">[内容]<span id=\"Lb_laiyuan\" class=\"hs\">",4,87);
$z[1]=Array("trim","<a([^>]*)>(.*)</a>",91,143);
$z[1][4]['replace']="";
$z[2]=Array("trim","<script([^>]*)>(.*)</script>",145,207);
$z[2][4]['replace']="";
$z[3]=Array("trim","<style([^>]*)>(.*)</style>",209,269);
$z[3][4]['replace']="";
$z[4]=Array("trim","<div([^.]*)>",271,317);
$z[4][4]['replace']="";
$z[5]=Array("trim","</div>",319,359);
$z[5][4]['replace']="";
$z[6]=Array("function","",363,394);

?>