<?php
$z[0]=Array("array","@me = (strpos(@me['litpic'],'defaultpic') ? \"\" : \"<a href='{@me['arcurl']}' class='imgview' target='_blank'><img src='{@me['litpic']}' align='left' alt='{@me['title']}' width='100' height='100'/></a>\"); ",29,272);
$z[0][4]['runphp']="yes";
$z[1]=Array("arcurl","",308,323);
$z[2]=Array("title","",348,390);
$z[2][4]['function']="html2text(@me)";
$z[3]=Array("title","",438,493);
$z[3][4]['function']="cn_substr(Html2text(@me),75)";
$z[4]=Array("typeurl","",521,537);
$z[5]=Array("typename","",556,573);
$z[6]=Array("stime","",624,638);
$z[7]=Array("description","",656,718);
$z[7][4]['function']="cn_substr(Html2text(@me),250)";
$z[8]=Array("arcurl","",731,746);
$z[9]=Array("click","",842,856);
$z[10]=Array("id","",897,930);
$z[10][4]['function']="GetTags(@me)";
$z[11]=Array("download_url","",1000,1021);

?>