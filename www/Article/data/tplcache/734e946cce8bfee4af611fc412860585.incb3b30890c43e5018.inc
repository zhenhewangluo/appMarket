<?php
$z[0]=Array("match","<div class=\"content\">[内容]<div class=\"pagebreak\">",5,82);
$z[1]=Array("trim","<a([^>]*)>(.*)</a>",87,139);
$z[1][4]['replace']="";
$z[2]=Array("trim","<script([^>]*)>(.*)</script>",141,203);
$z[2][4]['replace']="";
$z[3]=Array("trim","<!--(.*)-->",205,250);
$z[3][4]['replace']="";
$z[4]=Array("trim","<object([^>]*)>(.*)</object>",252,314);
$z[4][4]['replace']="";
$z[5]=Array("trim","<div([^.]*)>",316,362);
$z[5][4]['replace']="";
$z[6]=Array("trim","</div>",364,404);
$z[6][4]['replace']="";
$z[7]=Array("function","",409,440);

?>