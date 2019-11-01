<?php
$z[0]=Array("typeurl","",49,65);
$z[1]=Array("typename","",84,101);
$z[2]=Array("arcurl","",122,137);
$z[3]=Array("title","",162,204);
$z[3][4]['function']="html2text(@me)";
$z[4]=Array("title","",222,277);
$z[4][4]['function']="cn_substr(Html2text(@me),75)";
$z[5]=Array("stime","",303,317);
$z[6]=Array("array","@me = (strpos(@me['litpic'],'defaultpic') ? \"\" : \"<a href='{@me['arcurl']}' class='imgview' target='_blank'><img src='{@me['litpic']}' align='right' alt='{@me['title']}' width='100' height='100'/></a>\"); ",336,580);
$z[6][4]['runphp']="yes";
$z[7]=Array("description","",581,643);
$z[7][4]['function']="cn_substr(Html2text(@me),250)";
$z[8]=Array("arcurl","",656,671);
$z[9]=Array("click","",749,763);
$z[10]=Array("id","",804,837);
$z[10][4]['function']="GetTags(@me)";

?>