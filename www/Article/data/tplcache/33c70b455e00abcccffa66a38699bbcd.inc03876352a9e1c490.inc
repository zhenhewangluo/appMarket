<?php
$z[0]=Array("match","<div id=\"boxsm\">[内容]<div id=\"tabbox\">",4,70);
$z[1]=Array("trim","<a([^>]*)>(.*)</a>",74,126);
$z[1][4]['replace']="";
$z[2]=Array("trim","<script([^>]*)>(.*)</script>",128,190);
$z[2][4]['replace']="";
$z[3]=Array("trim","<!--(.*)-->",192,237);
$z[3][4]['replace']="";
$z[4]=Array("trim","<object([^>]*)>(.*)</object>",239,301);
$z[4][4]['replace']="";
$z[5]=Array("trim","<div([^.]*)>",303,349);
$z[5][4]['replace']="";
$z[6]=Array("trim","</div>",351,391);
$z[6][4]['replace']="";
$z[7]=Array("function","",395,426);

?>