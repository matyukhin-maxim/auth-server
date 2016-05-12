<li class="list-group-item" style="padding-right: 0">
	<div class="pull-right col-xs-3">
		<div class="btn-group btn-xs btn-group-justified">
			<a href="<?= $siteLink;?>" class="btn btn-default" rel="nofollow" target="_blank" title="Перейти на сайт">
				<i class="glyphicon glyphicon-share-alt"></i>
			</a>
			<a href="<?= $editLink;?>" data-toggle="modal" data-target="#universal" class="btn btn-default" title="Редактировать">
				<i class="glyphicon glyphicon-pencil"></i>
			</a>
			<a href="<?= $deleteLink;?>" class="btn btn-default" title="Удалить ссылку" onclick="return confirm('Уверены ???');">
				<i class="glyphicon glyphicon-remove"></i>
			</a>
		</div>
	</div>

	<span class="h3 strong" data-placement="right" data-toggle="popover" data-content="<?= $siteLink;?>" data-trigger="hover">
		<?= $s_title;?>
	</span>

</li>
