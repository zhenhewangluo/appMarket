<?php
if(!isset($GLOBALS['_vars'])) $GLOBALS['_vars'] = array(); 
$fields = array();
?><!-- //最近访客 -->
<div class="box" id="side_search">
	 <h2>最近访客</h2>
	 <div class="text">
		<?php
$atts = array();
$atts['tagname'] = 'newvisitor';
$atts['row'] = '6';
$blockValue = MakePublicTag($atts,$this->refObj,$fields); 
if(is_array($blockValue) && count($blockValue) > 0){
foreach( $blockValue as $key=>$fields )
{
?>			<div class='usericobox'>
				<a href="<?php echo $fields['url']; ?>" target="_blank"><img src="<?php echo $fields['face']; ?>" style="width:45px;height:45px;"/></a>
				<br /><a href="<?php echo $fields['url']; ?>" target="_blank"><?php echo $fields['uname']; ?></a>
			</div>
		<?php
}
}
?>
	 </div>
</div>