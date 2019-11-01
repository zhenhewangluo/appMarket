<?php
$z[0]=Array("match","<div id=\"newbox\">[内容]</div>\r\n                  <div id=\"tabbox\"><strong>相关标签",4,117);
$z[1]=Array("trim","<a([^>]*)>(.*)</a>",121,173);
$z[1][4]['replace']="";
$z[2]=Array("trim","<script([^>]*)>(.*)</script>",175,237);
$z[2][4]['replace']="";
$z[3]=Array("trim","<!--(.*)-->",239,284);
$z[3][4]['replace']="";
$z[4]=Array("trim","<table([^>]*)>(.*)</table>",286,346);
$z[4][4]['replace']="";
$z[5]=Array("trim","<style([^>]*)>(.*)</style>",348,408);
$z[5][4]['replace']="";
$z[6]=Array("trim","<object([^>]*)>(.*)</object>",410,472);
$z[6][4]['replace']="";
$z[7]=Array("trim","<embed([^>]*)>(.*)</embed>",474,534);
$z[7][4]['replace']="";
$z[8]=Array("trim","<iframe([^>]*)>(.*)</iframe>",536,598);
$z[8][4]['replace']="";
$z[9]=Array("trim","<param([^>]*)>(.*)</param>",600,660);
$z[9][4]['replace']="";
$z[10]=Array("trim","<div([^.]*)>",662,708);
$z[10][4]['replace']="";
$z[11]=Array("trim","</div>",710,750);
$z[11][4]['replace']="";
$z[12]=Array("function","",754,785);

?>