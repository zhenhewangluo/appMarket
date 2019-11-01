<div class="chNews">

				<h3><strong><a href="{dede:field name='typeurl'/}">{dede:field name='typename'/}</a></strong><span><a href="{dede:field name='typeurl'/}">更多>></a></span></h3>

				{dede:arclist row=1 orderby=pubdate type='image.' imgwidth='120' imgheight='95' titlelen='44' infolen='70'}

				<div class="category_dis">

					<a href="[field:arcurl/]" title="[field:title/]" target="_blank">[field:image/]</a><h4><a href="[field:arcurl/]" target="_blank" title="[field:title/]">[field:title/]</a></h4><p>[field:info/]...</p>

				</div>

				{/dede:arclist}

				<ul>

					{dede:arclist titlelen='45' limit='1,4'}

					<li><a href="[field:arcurl /]" target="_blank">[field:title /]</a></li>

					{/dede:arclist}

				</ul>

			</div>