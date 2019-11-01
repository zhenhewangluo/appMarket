<?php
$z[0]=Array("typeurl","",48,64);
$z[1]=Array("typename","",83,100);
$z[2]=Array("arcurl","",121,136);
$z[3]=Array("title","",161,203);
$z[3][4]['function']="html2text(@me)";
$z[4]=Array("title","",205,260);
$z[4][4]['function']="cn_substr(Html2text(@me),75)";
$z[5]=Array("stime","",286,300);
$z[6]=Array("array","@me = (strpos(@me['litpic'],'defaultpic') ? \"\" : \"<a href='{@me['arcurl']}' class='imgview' target='_blank'><img src='{@me['litpic']}' align='right' alt='{@me['title']}' width='100' height='100'/></a>\"); ",318,562);
$z[6][4]['runphp']="yes";
$z[7]=Array("description","",563,625);
$z[7][4]['function']="cn_substr(Html2text(@me),250)";
$z[8]=Array("arcurl","",638,653);
$z[9]=Array("click","",737,751);
$z[10]=Array("id","",792,825);
$z[10][4]['function']="GetTags(@me)";

?>