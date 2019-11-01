<?php
$z[0]=Array("match","<span id=\"lb_bj\">daiwei 发布于[内容] | 111 次阅读</span>",4,93);
$z[1]=Array("trim","<a([^>]*)>(.*)</a>",97,149);
$z[1][4]['replace']="";
$z[2]=Array("trim","<!--(.*)-->",151,196);
$z[2][4]['replace']="";
$z[3]=Array("trim","<style([^>]*)>(.*)</style>",198,258);
$z[3][4]['replace']="";
$z[4]=Array("trim","<div([^.]*)>",260,306);
$z[4][4]['replace']="";
$z[5]=Array("trim","</div>",308,348);
$z[5][4]['replace']="";
$z[6]=Array("function","",352,383);

?>