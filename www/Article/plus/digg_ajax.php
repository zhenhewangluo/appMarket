<?php
/**
 *
 * 文档digg处理ajax文件
 *
 * @version        $Id: digg_ajax.php 2 13:00 2011/11/25 tianya $
 * @package        DedeCMS.Plus
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/../include/common.inc.php");
$action = isset($action) ? trim($action) : '';
$id = empty($id)? 0 : intval(preg_replace("/[^\d]/",'', $id));

helper('cache');

if($id < 1)
{
    exit();
}

$maintable = '#@__archives';

$prefix = 'diggCache';
$key = 'aid-'.$id;
$row = GetCache($prefix, $key);

if(!is_array($row) || $cfg_digg_update==0)
{
  $row = $dsql->GetOne("SELECT likepost,goodpost,passingpost,badpost,cheatpost,scores FROM `$maintable` WHERE id='$id' ");
    if($cfg_digg_update == 0)
    {
		if($action == 'good')
		{
			$row['goodpost'] = $row['goodpost'] + 1;
			$dsql->ExecuteNoneQuery("UPDATE `$maintable` SET scores = scores + {$cfg_caicai_add},goodpost=goodpost+1,lastpost=".time()." WHERE id='$id'");
		}
		else if($action == 'like')
		{
			$row['likepost'] = $row['likepost'] + 1;
			$dsql->ExecuteNoneQuery("UPDATE `$maintable` SET scores = scores + {$cfg_caicai_add},likepost=likepost+1,lastpost=".time()." WHERE id='$id'");
		}
		else if($action=='passing')
		{
			$row['passingpost'] = $row['passingpost'] + 1;
			$dsql->ExecuteNoneQuery("UPDATE `$maintable` SET scores = scores - {$cfg_caicai_sub},passingpost=passingpost+1,lastpost=".time()." WHERE id='$id'");
		}
		else if($action=='bad')
		{
			$row['badpost'] = $row['badpost'] + 1;
			$dsql->ExecuteNoneQuery("UPDATE `$maintable` SET scores = scores - {$cfg_caicai_sub},badpost=badpost+1,lastpost=".time()." WHERE id='$id'");
		}
		else if($action=='cheat')
		{
			$row['cheatpost'] = $row['cheatpost'] + 1;
			$dsql->ExecuteNoneQuery("UPDATE `$maintable` SET scores = scores - {$cfg_caicai_sub},cheatpost=cheatpost+1,lastpost=".time()." WHERE id='$id'");
		}
		DelCache($prefix, $key);
    }
  SetCache($prefix, $key, $row, 0);
} else {
	if($action == 'good')
	{
	    $row['goodpost'] = $row['goodpost'] + 1;
	    $row['scores'] = $row['scores'] + $cfg_caicai_sub;
	    if($row['goodpost'] % $cfg_digg_update == 0)
	    {
			$add_caicai_sub = $cfg_digg_update * $cfg_caicai_sub;
		    $dsql->ExecuteNoneQuery("UPDATE `$maintable` SET scores = scores + {$add_caicai_sub},goodpost=goodpost+{$cfg_digg_update} WHERE id='$id'");
		    DelCache($prefix, $key);
	    }
	}
	 else if($action == 'like')
	{
	    $row['likepost'] = $row['likepost'] + 1;
		$row['scores'] = $row['scores'] + $cfg_caicai_sub;
	    if($row['likepost'] % $cfg_digg_update == 0)
	    {
			$add_caicai_sub = $cfg_digg_update * $cfg_caicai_sub;
		    $dsql->ExecuteNoneQuery("UPDATE `$maintable` SET scores = scores - {$add_caicai_sub},likepost=likepost+{$cfg_digg_update} WHERE id='$id'");
		    DelCache($prefix, $key);
	    }
	}
	else if($action == 'passing')
	{
	    $row['passingpost'] = $row['passingpost'] + 1;
		$row['scores'] = $row['scores']+ $cfg_caicai_sub;
	    if($row['passingpost'] % $cfg_digg_update == 0)
	    {
			$add_caicai_sub = $cfg_digg_update * $cfg_caicai_sub;
		    $dsql->ExecuteNoneQuery("UPDATE `$maintable` SET scores = scores - {$add_caicai_sub},passingpost=passingpost+{$cfg_digg_update} WHERE id='$id'");
		    DelCache($prefix, $key);
	    }
	}
	else if($action == 'bad')
	{
	    $row['badpost'] = $row['badpost'] + 1;
		$row['scores'] = $row['scores'] - $cfg_caicai_sub;
	    if($row['badpost'] % $cfg_digg_update == 0)
	    {
			$add_caicai_sub = $cfg_digg_update * $cfg_caicai_sub;
		    $dsql->ExecuteNoneQuery("UPDATE `$maintable` SET scores = scores - {$add_caicai_sub},badpost=badpost+{$cfg_digg_update} WHERE id='$id'");
		    DelCache($prefix, $key);
	    }
	}
	else if($action == 'cheat')
	{
		$row['cheatpost'] = $row['cheatpost'] + 1;
		$row['scores'] = $row['scores'] - $cfg_caicai_sub;
		if($row['cheatpost'] % $cfg_digg_update == 0)
		{
			$add_caicai_sub = $cfg_digg_update * $cfg_caicai_sub;
			$dsql->ExecuteNoneQuery("UPDATE `$maintable` SET scores = scores - {$add_caicai_sub},cheatpost=cheatpost+{$cfg_digg_update} WHERE id='$id'");
			DelCache($prefix, $key);
		}
	}
	SetCache($prefix, $key, $row, 0);
}

$digg = '';
if(!is_array($row)) exit();

if($row['goodpost'] + $row['likepost']+ $row['passingpost'] + $row['badpost'] + $row['cheatpost'] == 0)
{
    $row['goodper'] = $row['likepost'] = $row['passingper'] = $row['badper'] =$row['cheatpost'] = 0;
}
else
{
    $row['goodper'] = number_format($row['goodpost'] / ($row['goodpost'] + $row['likepost']+ $row['passingpost'] + $row['badpost'] + $row['cheatpost']), 3) * 100;
    $row['likeper'] = number_format($row['likepost'] / ($row['goodpost'] + $row['likepost']+ $row['passingpost'] + $row['badpost'] + $row['cheatpost']), 3) * 100;
    $row['badper'] = number_format($row['badpost'] / ($row['goodpost'] + $row['likepost']+ $row['passingpost'] + $row['badpost'] + $row['cheatpost']), 3) * 100;
    $row['passingper'] = number_format($row['passingpost'] / ($row['goodpost'] + $row['likepost']+ $row['passingpost'] + $row['badpost'] + $row['cheatpost']), 3) * 100;
    $row['cheatper'] = number_format($row['cheatpost'] / ($row['goodpost'] + $row['likepost']+ $row['passingpost'] + $row['badpost'] + $row['cheatpost']), 3) * 100;
}

if(empty($formurl)) $formurl = '';
if($formurl=='caicai')
{
    if($action == 'good') $digg = $row['goodpost'];
    if($action == 'like') $digg  = $row['likepost'];
    if($action == 'passing') $digg  = $row['passingpost'];
    if($action == 'bad') $digg  = $row['badpost'];
    if($action == 'cheat') $digg  = $row['cheatpost'];
}
else
{
    $row['goodper'] = trim(sprintf("%4.2f", $row['goodper']));
    $row['likeper'] = trim(sprintf("%4.2f", $row['likeper']));
    $row['passingper'] = trim(sprintf("%4.2f", $row['passingper']));
    $row['badper'] = trim(sprintf("%4.2f", $row['badper']));
    $row['cheatper'] = trim(sprintf("%4.2f", $row['cheatper']));
    $row['goodpertop'] = 100 - $row['goodper'];
    $row['likepertop'] = 100-$row['likeper'];
    $digg = '<div class="diggbox digg_like" onmousemove="this.style.backgroundPosition=\'left bottom\';" onmouseout="this.style.backgroundPosition=\'left top\';" onclick="postDigg(\'like\','.$id.')">
            
            <div class="digg_percent">
                <div class="digg_percent_bar"><div class="digg_num">'.$row['likepost'].'</div><span style="height:'.$row['likeper'].'px;"></span></div>                
            </div>
               <div class="digg_actimg"><img src="/Article/images/hjapp_like.gif" width="60" height="60" /></div> 
             <div class="digg_act">喜欢</div>   		
        </div>
    		<div class="diggbox digg_good" onmousemove="this.style.backgroundPosition=\'left bottom\';" onmouseout="this.style.backgroundPosition=\'left top\';" onclick="postDigg(\'good\','.$id.')">
                        <div class="digg_percent">
                <div class="digg_percent_bar"><div class="digg_num">'.$row['goodpost'].'</div><span style="height:'.$row['goodper'].'px"></span></div>                
            </div>
                <div class="digg_actimg"><img src="/Article/images/hjapp_good.gif" width="60" height="60" /></div> 		
             <div class="digg_act">不错</div>   		
        </div>
		<div class="diggbox digg_passing" onmousemove="this.style.backgroundPosition=\'left bottom\';" onmouseout="this.style.backgroundPosition=\'left top\';" onclick="postDigg(\'passing\','.$id.')">
              <div class="digg_percent">
                <div class="digg_percent_bar"><div class="digg_num">'.$row['passingpost'].'</div><span style="height:'.$row['passingper'].'px"></span></div>
             </div>
               <div class="digg_actimg"><img src="/Article/images/hjapp_passing.gif" width="60" height="60" /></div> 
             <div class="digg_act">路过</div>   		
        </div>
        <div class="diggbox digg_bad" onmousemove="this.style.backgroundPosition=\'left bottom\';" onmouseout="this.style.backgroundPosition=\'left top\';" onclick="postDigg(\'bad\','.$id.')">
            
            <div class="digg_percent">
                <div class="digg_percent_bar"><div class="digg_num">'.$row['badpost'].'</div><span style="height:'.$row['badper'].'px"></span></div>
             </div>
              <div class="digg_actimg"><img src="/Article/images/hjapp_bad.gif" width="60" height="60" /></div> 
             <div class="digg_act">不给力</div>   		
        </div>
         <div class="diggbox digg_cheat" onmousemove="this.style.backgroundPosition=\'left bottom\';" onmouseout="this.style.backgroundPosition=\'left top\';" onclick="postDigg(\'cheat\','.$id.')">
            
            <div class="digg_percent">
                <div class="digg_percent_bar"><div class="digg_num">'.$row['cheatpost'].'</div><span style="height:'.$row['cheatper'].'px"></span></div>
             </div>
              <div class="digg_actimg"><img src="/Article/images/hjapp_cheat.gif" width="60" height="60" /></div> 
             <div class="digg_act">坑爹</div>   		
        </div>';
}
AjaxHead();
echo $digg;
exit();