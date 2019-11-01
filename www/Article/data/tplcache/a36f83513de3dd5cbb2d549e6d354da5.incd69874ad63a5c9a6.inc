<?php
$z[0]=Array("typeurl","",49,65);
$z[1]=Array("typename","",84,101);
$z[2]=Array("arcurl","",122,137);
$z[3]=Array("title","",162,204);
$z[3][4]['function']="html2text(@me)";
$z[4]=Array("title","",206,261);
$z[4][4]['function']="cn_substr(Html2text(@me),75)";
$z[5]=Array("stime","",287,301);
$z[6]=Array("array","@me = (strpos(@me['litpic'],'defaultpic') ? \"\" : \"<a href='{@me['arcurl']}' class='imgview' target='_blank'><img src='/article{@me['litpic']}' align='right' alt='{@me['title']}' width='100' height='100'/></a>\"); ",320,572);
$z[6][4]['runphp']="yes";
$z[7]=Array("description","",573,635);
$z[7][4]['function']="cn_substr(Html2text(@me),300)";
$z[8]=Array("arcurl","",648,663);
$z[9]=Array("click","",747,761);
$z[10]=Array("id","",802,835);
$z[10][4]['function']="GetTags(@me)";

?>