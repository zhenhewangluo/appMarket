<?php
if(!isset($GLOBALS['_vars'])) $GLOBALS['_vars'] = array(); 
$fields = array();
?><!-- //begin search -->
<div class="box" id="side_search">
	  <h2>文档搜索</h2>
	  <form action="../plus/search.php" method="post" target="_blank">
	  <input type="hidden" name="mid" value="<?php echo $GLOBALS['_vars']['userid']; ?>" />
	  <div class="text">
		<label>
		  <input class="ip" type="text" name="keyword" size="15" />
		  <input class="bt" name="submit" type="submit" value=" Go "/>
		</label>
	  </div>
	  </form>
</div>
<!-- //end search -->