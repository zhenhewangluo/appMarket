<?php
$z[0]=Array("typeurl","",48,64);
$z[1]=Array("typename","",83,100);
$z[2]=Array("arcurl","",121,136);
$z[3]=Array("title","",161,203);
$z[3][4]['function']="html2text(@me)";
$z[4]=Array("title","",221,276);
$z[4][4]['function']="cn_substr(Html2text(@me),75)";
$z[5]=Array("stime","",302,316);
$z[6]=Array("array","@me = (strpos(@me['litpic'],'defaultpic') ? \"\" : \"<a href='{@me['arcurl']}' class='imgview' target='_blank'><img src='{@me['litpic']}' align='right' alt='{@me['title']}' width='100' height='100'/></a>\"); ",334,578);
$z[6][4]['runphp']="yes";
$z[7]=Array("description","",579,641);
$z[7][4]['function']="cn_substr(Html2text(@me),250)";
$z[8]=Array("arcurl","",654,669);
$z[9]=Array("click","",747,761);
$z[10]=Array("id","",802,835);
$z[10][4]['function']="GetTags(@me)";

?>