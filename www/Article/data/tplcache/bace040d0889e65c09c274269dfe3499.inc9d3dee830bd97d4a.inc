<?php
$z[0]=Array("match","<div class=\"content\">[内容]<div class=\"pagebreak\">",4,81);
$z[1]=Array("trim","<a([^>]*)>(.*)</a>",85,137);
$z[1][4]['replace']="";
$z[2]=Array("trim","<script([^>]*)>(.*)</script>",139,201);
$z[2][4]['replace']="";
$z[3]=Array("trim","<!--(.*)-->",203,248);
$z[3][4]['replace']="";
$z[4]=Array("trim","<object([^>]*)>(.*)</object>",250,312);
$z[4][4]['replace']="";
$z[5]=Array("trim","<div([^.]*)>",314,360);
$z[5][4]['replace']="";
$z[6]=Array("trim","</div>",362,402);
$z[6][4]['replace']="";
$z[7]=Array("function","",406,437);

?>