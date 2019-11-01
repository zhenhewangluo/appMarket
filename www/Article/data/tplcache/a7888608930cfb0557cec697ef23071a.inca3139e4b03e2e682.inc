<?php
$z[0]=Array("typeurl","",49,65);
$z[1]=Array("typename","",84,101);
$z[2]=Array("arcurl","",122,137);
$z[3]=Array("title","",162,204);
$z[3][4]['function']="html2text(@me)";
$z[4]=Array("title","",206,261);
$z[4][4]['function']="cn_substr(Html2text(@me),50)";
$z[5]=Array("stime","",287,301);
$z[6]=Array("array","@me = (strpos(@me['litpic'],'defaultpic') ? \"\" : \"<a href='{@me['arcurl']}' class='imgview' target='_blank'><img src='{@me['litpic']}' align='right' alt='{@me['title']}' width='100' height='100'/></a>\"); ",320,564);
$z[6][4]['runphp']="yes";
$z[7]=Array("description","",565,585);
$z[8]=Array("arcurl","",598,613);
$z[9]=Array("click","",697,711);
$z[10]=Array("id","",752,785);
$z[10][4]['function']="GetTags(@me)";

?>