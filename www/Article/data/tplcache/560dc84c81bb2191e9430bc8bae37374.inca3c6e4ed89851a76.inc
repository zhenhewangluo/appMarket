<?php
$z[0]=Array("array","@me = (strpos(@me['litpic'],'defaultpic') ? \"\" : \"<a href='{@me['arcurl']}' class='imgview' target='_blank'><img src='{@me['litpic']}' align='left' alt='{@me['title']}' width='100' height='100'/></a>\"); ",30,273);
$z[0][4]['runphp']="yes";
$z[1]=Array("arcurl","",310,325);
$z[2]=Array("title","",350,392);
$z[2][4]['function']="html2text(@me)";
$z[3]=Array("title","",440,495);
$z[3][4]['function']="cn_substr(Html2text(@me),75)";
$z[4]=Array("typeurl","",523,539);
$z[5]=Array("typename","",558,575);
$z[6]=Array("stime","",627,641);
$z[7]=Array("description","",660,722);
$z[7][4]['function']="cn_substr(Html2text(@me),250)";
$z[8]=Array("arcurl","",735,750);
$z[9]=Array("click","",853,867);
$z[10]=Array("id","",908,941);
$z[10][4]['function']="GetTags(@me)";
$z[11]=Array("download_url","",1013,1034);
$z[12]=Array("twoindexcode","",1259,1280);

?>