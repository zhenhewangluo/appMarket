<?php
$z[0]=Array("typeurl","",51,67);
$z[1]=Array("typename","",86,103);
$z[2]=Array("arcurl","",124,139);
$z[3]=Array("title","",164,206);
$z[3][4]['function']="html2text(@me)";
$z[4]=Array("title","",208,263);
$z[4][4]['function']="cn_substr(Html2text(@me),50)";
$z[5]=Array("stime","",289,303);
$z[6]=Array("array","@me = (strpos(@me['litpic'],'defaultpic') ? \"\" : \"<a href='{@me['arcurl']}' class='imgview' target='_blank'><img src='{@me['litpic']}' align='right' alt='{@me['title']}' width='100' height='100'/></a>\"); ",324,568);
$z[6][4]['runphp']="yes";
$z[7]=Array("description","",569,589);
$z[8]=Array("arcurl","",602,617);
$z[9]=Array("click","",701,715);
$z[10]=Array("id","",756,789);
$z[10][4]['function']="GetTags(@me)";

?>