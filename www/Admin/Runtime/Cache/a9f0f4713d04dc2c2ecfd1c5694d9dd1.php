<?php if (!defined('THINK_PATH')) exit();?><div class="headbar clearfix">
	<div class="position"><span>应用</span><span>></span><span>应用分类</span><span>></span><span>添加分类</span></div>
	<!--<ul class="tab" name="menu1">
		<li id="li_1" class="selected"><a href="javascript:void(0)" hidefocus="true" onclick="select_tab('1')">商品信息</a></li>
		<li id="li_2"><a href="javascript:void(0)" hidefocus="true" onclick="select_tab('2')">描述</a></li>
		<li id="li_3"><a href="javascript:void(0)" hidefocus="true" onclick="select_tab('3')">营销选项</a></li>
	</ul>-->
</div>
<script>
    //判断用户是否重复
   function validateCate(){
		var nameVal = $("#name").val();
        $.post("<?php echo U('AppStore://Ajax/validCateName');?>", { "name": nameVal},
        function(data){
             if(data.name == 1 && $.trim(nameVal) !== ''){
                //alert("此分类已经使用！");
				$('#name').removeClass('valid-text');
				$('#unique').removeClass('valid-msg');
				$('#name').addClass('invalid-text');
				$('#unique').addClass('invalid-msg');
				$('#unique').html('此分类已经使用！');
				return false;
            }              
        }, "json");
 
		return true;
   }
 
</script>
<div class="content_box">
	<div class="content form_content">
		<form name="myform" id="myform" action="<?php echo U('AppStore://Apps/appCateAddAction');?>" method="post">
			<table class="form_table">
				<col width="150px" />
				<col />
				<tr>
					<th>分类名称：</th>
					<td>
						<input class="normal" name="name" id ="name" type="text" value="" pattern="required" alt="分类名称不能为空"  onblur="validateCate()" />
						<label id="unique">*</label>
					</td>
				</tr>
				<tr>
					<th>所属分类：</th>
					<td style="position:relative;">
						<select class="normal" name="parent">
							<?php if(is_array($parentlist)): $i = 0; $__LIST__ = $parentlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><option value="<?php echo ($vo['id']); ?>" <?php if(parent_id == $vo['id']): ?>selected="selected"<?php endif; ?> ><?php echo ($vo['name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th>order：</th>
					<td>
						<input class="normal" name="order" type="text" pattern="int" value="" alt="请输入正确的排序号！" /><label></label>
					</td>
				</tr>				
				<tr>
					<th>分类介绍：</th>
					<td>
						<textarea name="cate_desc" class="input_box"></textarea>
					</td>
				</tr>
				<tr>
					<th>是否可见：</th>
					<td><label><input type="radio" name="visible" value="1" checked="true" />是</label>
						<label><input type="radio" name="visible" value="0" />否</label>
					</td>
				</tr>				
				<tr>
					<th></th>
					<td>
						<input type="submit"   value="确认" > <!-- onclick="add(0)"/>  -->
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>