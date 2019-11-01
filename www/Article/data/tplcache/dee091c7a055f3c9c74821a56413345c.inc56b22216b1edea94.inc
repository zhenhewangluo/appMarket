<?php
$z[0]=Array("match","<div id=\"boxsm\">[内容]<div id=\"tabbox\">",4,70);
$z[1]=Array("trim","<a([^>]*)>(.*)</a>",74,126);
$z[1][4]['replace']="";
$z[2]=Array("trim","<script([^>]*)>(.*)</script>",128,190);
$z[2][4]['replace']="";
$z[3]=Array("trim","<!--(.*)-->",192,237);
$z[3][4]['replace']="";
$z[4]=Array("trim","<table([^>]*)>(.*)</table>",239,299);
$z[4][4]['replace']="";
$z[5]=Array("trim","<style([^>]*)>(.*)</style>",301,361);
$z[5][4]['replace']="";
$z[6]=Array("trim","<object([^>]*)>(.*)</object>",363,425);
$z[6][4]['replace']="";
$z[7]=Array("trim","<embed([^>]*)>(.*)</embed>",427,487);
$z[7][4]['replace']="";
$z[8]=Array("trim","<iframe([^>]*)>(.*)</iframe>",489,551);
$z[8][4]['replace']="";
$z[9]=Array("trim","<param([^>]*)>(.*)</param>",553,613);
$z[9][4]['replace']="";
$z[10]=Array("trim","<div([^.]*)>",615,661);
$z[10][4]['replace']="";
$z[11]=Array("trim","</div>",663,703);
$z[11][4]['replace']="";
$z[12]=Array("function","",707,738);

?>