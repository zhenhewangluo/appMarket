<?php
$z[0]=Array("match","<div id=\"newbox\">[内容]</div>\r\n                  <div id=\"tabbox\"><strong>相关标签",5,118);
$z[1]=Array("trim","<a([^>]*)>(.*)</a>",123,175);
$z[1][4]['replace']="";
$z[2]=Array("trim","<script([^>]*)>(.*)</script>",177,239);
$z[2][4]['replace']="";
$z[3]=Array("trim","<!--(.*)-->",241,286);
$z[3][4]['replace']="";
$z[4]=Array("trim","<table([^>]*)>(.*)</table>",288,348);
$z[4][4]['replace']="";
$z[5]=Array("trim","<style([^>]*)>(.*)</style>",350,410);
$z[5][4]['replace']="";
$z[6]=Array("trim","<object([^>]*)>(.*)</object>",412,474);
$z[6][4]['replace']="";
$z[7]=Array("trim","<embed([^>]*)>(.*)</embed>",476,536);
$z[7][4]['replace']="";
$z[8]=Array("trim","<iframe([^>]*)>(.*)</iframe>",538,600);
$z[8][4]['replace']="";
$z[9]=Array("trim","<param([^>]*)>(.*)</param>",602,662);
$z[9][4]['replace']="";
$z[10]=Array("trim","<div([^.]*)>",664,710);
$z[10][4]['replace']="";
$z[11]=Array("trim","</div>",712,752);
$z[11][4]['replace']="";
$z[12]=Array("function","",757,788);

?>