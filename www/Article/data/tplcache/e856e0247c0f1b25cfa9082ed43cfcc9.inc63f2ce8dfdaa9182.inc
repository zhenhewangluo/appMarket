<?php
$z[0]=Array("match","<div id=\"newstt\" class=\"tt nt\"><h1>[内容]</h1><span class=\"tt_right hs\">",4,103);
$z[1]=Array("trim","<a([^>]*)>(.*)</a>",107,159);
$z[1][4]['replace']="";
$z[2]=Array("trim","<!--(.*)-->",161,206);
$z[2][4]['replace']="";
$z[3]=Array("trim","<table([^>]*)>(.*)</table>",208,268);
$z[3][4]['replace']="";
$z[4]=Array("trim","<style([^>]*)>(.*)</style>",270,330);
$z[4][4]['replace']="";
$z[5]=Array("trim","<img([^>]*)>",332,378);
$z[5][4]['replace']="";
$z[6]=Array("trim","<div([^.]*)>",380,426);
$z[6][4]['replace']="";
$z[7]=Array("trim","</div>",428,468);
$z[7][4]['replace']="";
$z[8]=Array("function","",472,503);

?>