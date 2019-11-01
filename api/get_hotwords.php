<?php
	header("Content-Type:text/html;charset=utf-8");
	require("./inc/init.php");	
	
    	if($AM_CURRENT_REQUEST["PROTO"] != 22){
		echo error2json("E002");
		die;
	}

	$count = 0;
	if(isset($_POST['count']) && !empty($_POST['count'])){
		$count = intval(__getPost('count'));
	}
	if($count <= 0){
		$count = 20;
	}
	
	$paging = true;
	if(isset($_POST["has_paging"])){
		$paging = (__getPost("has_paging") == 'yes') ? true : false;
	}
	
	$page_size = AM_DEFAULT_PAGE_SIZE;
	if(isset($_POST["num_per_page"])){
		$page_size = intval(__getPost("num_per_page"));
		if($page_size <= 0){
			$page_size = AM_DEFAULT_PAGE_SIZE;
		}
	}

	//current page number
	$page_no = 1;
	if(isset($_POST["pageno"])){
		$page_no = intval(__getPost("pageno"));
		if($page_no < 1){
			$page_no = 1;	
		}
	}
	//connect to database	
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}

	//get records count for paging 
	$sql = "select count(*) from am_hotwords";
	$rs1 = mysql_query($sql, $conn);
	if($rs1 === FALSE){
		log_message($sql, 'S');
		echo error2json("S002");
		die;
	}	
	$words_count = mysql_result($rs1, 0, 0);
	if($words_count == 0){
		echo error2json("E193");
		die;
	}
	/*
	if($words_count > $count){
		$words_count = $count;
	}
	*/
	//Caculate total number of pages and current page num.
	$total_page = 1;
	if($paging){
		//$total_page = ceil($words_count / $page_size);
		$total_page = floor($words_count / $page_size);
		//echo $page_no."----".$total_page;
		if($page_no > $total_page){
			
			//$page_no = $total_page;
			$pageR = $page_no%$total_page;
			if($pageR == 0) 
				$page_no = $total_page;
			else
				$page_no = $pageR;
		}
	}

	$sql = "select * from am_hotwords order by `order`, count desc";
	
	//add paging info to sql
	if($paging){
		$offset = ($page_no - 1) * $page_size;
		$sql .= ' limit '. $offset . ',' . $page_size;
	}
	//echo $sql."<br />";
	$rs = mysql_query($sql, $conn);
	if($rs === FALSE){
		log_message($sql, 'S');
		echo error2json("S002");
		die;
	}
	$words = array();
	while($row = mysql_fetch_assoc($rs)){
		$words[] = array(
			"name"  => $row["keyword"],
			"count" => $row["count"],
		);	
	}
	//print_r($words);
	//exit();
	echo array2json(array(
		"proto" => 22,
	    	"reqsuccess" =>  AM_REQUEST_SUCCESS,
		"list"  => $words,
	));
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>

