<div class="">
	<div class="well well-sm">
		<form method="post" action="/admin/changegroup/">
			<div class="input-group">
				<span class="input-group-addon control-label">
					Название группы
				</span>
				<input type="text" class="form-control" placeholder="Название группы" autocomplete="off" required name="group-name"
				       value="<?= $group_name; ?>">
				<input type="hidden" name="group-id" value="<?= $group_id;?>" id="gid">
				<div class="input-group-btn">
					<button class="btn btn-primary" type="submit" title="Сохранить">
						<i class="glyphicon glyphicon-ok"></i>
					</button>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="row">
	<div class="col-md-5">
		<div class="panel panel-default">
			<div class="panel-heading strong">
				<div class="badge pull-right bg-info" id="cnt-user"></div>
				Сотрудники состоящие в группе
			</div>
			<div class="panel-body panel-response">
				<ul class="list-group">
					<?= $plist; ?>
				</ul>
			</div>
		</div>
		<div class="well well-sm clearfix compact">
			<a href="delete/" class="btn btn-danger btn-block strong">Удалить группу</a>
		</div>
	</div>
	<div class="col-md-7">
		<div class="alert alert-warning strong" style="margin-bottom: 1px">
			Подбор сотрудников для группы
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="input-group">
                    <span class="input-group-addon white-bg">
                        <i class="glyphicon glyphicon-search"></i>
                    </span>
					<input type="text" autofocus class="form-control"
					       placeholder="Фамилия или табельный номер..." id="selection"/>
				</div>
			</div>
			<div class="panel-body panel-response" id="select-response"></div>
		</div>
	</div>
</div>