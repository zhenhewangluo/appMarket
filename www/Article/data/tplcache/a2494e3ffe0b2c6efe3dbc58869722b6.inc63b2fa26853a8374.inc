<?php
$z[0]=Array("match","<div id=\"boxsm\">[内容]<div id=\"tabbox\">",5,71);
$z[1]=Array("trim","<a([^>]*)>(.*)</a>",76,128);
$z[1][4]['replace']="";
$z[2]=Array("trim","<script([^>]*)>(.*)</script>",130,192);
$z[2][4]['replace']="";
$z[3]=Array("trim","<!--(.*)-->",194,239);
$z[3][4]['replace']="";
$z[4]=Array("trim","<object([^>]*)>(.*)</object>",241,303);
$z[4][4]['replace']="";
$z[5]=Array("trim","<div([^.]*)>",305,351);
$z[5][4]['replace']="";
$z[6]=Array("trim","</div>",353,393);
$z[6][4]['replace']="";
$z[7]=Array("function","",398,429);

?>