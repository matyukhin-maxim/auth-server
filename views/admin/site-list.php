<div class="row">
	<div class="col-md-10 col-md-offset-1">
		<div class="panel panel-default">
			<div class="panel-heading strong clearfix">
				<i class="glyphicon glyphicon-globe"></i>
				Список доступных сайтов
				<div class="pull-right">
					<button class="btn btn-default btn-sm strong" data-toggle="modal" data-target="#universal" data-remote="/admin/siteEdit/">
						Новый сайт
					</button>
				</div>
			</div>
			<div class="panel-body">
				<ul class="list-group">
					<?= $list; ?>
				</ul>
			</div>
		</div>
	</div>
</div>