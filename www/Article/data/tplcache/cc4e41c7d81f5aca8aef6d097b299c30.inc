<div class="g">
            	[field:array runphp='yes']@me = (strpos(@me['litpic'],'defaultpic') ? "" : "<a href='{@me['arcurl']}' class='imgview' target='_blank'><img src='{@me['litpic']}' align='left' alt='{@me['title']}' width='100' height='100'/></a>"); [/field:array]
				<div class="text"><h2><a href="[field:arcurl/]" target="_blank" title="[field:title  function='html2text(@me)' /]" style="clear:both; width:96%; display:block;">[field:title function="cn_substr(Html2text(@me),75)" /]</a><span>分类：<a href="[field:typeurl/]" target="_blank">[[field:typename/]]</a></span>
                <em class="list_time">[field:stime/]</em></h2>
				<p>[field:description  function="cn_substr(Html2text(@me),250)"/]... <a href="[field:arcurl/]" target="_blank">阅读全文>></a> 
                <span class="data">	<em class="list_view">[field:click/]围观</em><em class="list_tag">标签：[field:id function=GetTags(@me)/] 
		   		</em></span> </p>
           <span class="download"><a href="[field:download_url/]" target="_blank"><img src='/dede/templets/hjapptempv/images/install.jpg' align='left' width='84'  height='37' alt='download' /></a></span>
           </div>
          		 <div class="code">           			
   					<img src='[field:twoindexcode/]' align='left' width='72'  height='72' alt='download' style="display:block;" />				 
 					
          		 </div>
          		 
			</div>