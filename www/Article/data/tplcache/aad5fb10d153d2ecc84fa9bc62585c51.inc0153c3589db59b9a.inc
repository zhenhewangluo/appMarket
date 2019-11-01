<?php
$z[0]=Array("typeurl","",49,65);
$z[1]=Array("typename","",84,101);
$z[2]=Array("arcurl","",122,137);
$z[3]=Array("title","",162,204);
$z[3][4]['function']="html2text(@me)";
$z[4]=Array("title","",206,262);
$z[4][4]['function']="cn_substr(Html2text(@me),100)";
$z[5]=Array("stime","",288,302);
$z[6]=Array("array","@me = (strpos(@me['litpic'],'defaultpic') ? \"\" : \"<a href='{@me['arcurl']}' class='imgview' target='_blank'><img src='{@me['litpic']}' align='right' alt='{@me['title']}' width='100' height='100'/></a>\"); ",321,565);
$z[6][4]['runphp']="yes";
$z[7]=Array("description","",566,628);
$z[7][4]['function']="cn_substr(Html2text(@me),300)";
$z[8]=Array("arcurl","",641,656);
$z[9]=Array("click","",740,754);
$z[10]=Array("id","",795,828);
$z[10][4]['function']="GetTags(@me)";

?>