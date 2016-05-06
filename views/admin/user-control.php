<div class="row">
	<div class="col-md-10 col-md-offset-1">
		<div class="well well-sm text-center strong h3">
			<?= $username; ?>
		</div>
	</div>
</div>

<div class="row">
	<div class="panel panel-default">
		<div class="panel-heading strong">Рдактирование профиля сотрудника</div>
		<div class="panel-body">
			<div class="clearfix">
				<div class="col-md-4 text-center">
					<div class="alert alert-info strong compact">Членство в группах</div>
					<ul class="list-group panel-response">
						<?= $userGroups;?>
					</ul>
				</div>
				<div class="col-md-8">
					<div class="well">
						<div class="row">
							<div class="col-sm-4 text-right control-label">Полное имя сотрудника</div>
							<div class="col-sm-8 strong">
								<input name="uname" type="text" class="form-control" value="<?= $username; ?>">
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4 text-right control-label">Табельный номер сотрудника</div>
							<div class="col-sm-8 strong">
								<input type="text" class="form-control" disabled value="<?= $tabNumber; ?>">
							</div>
						</div>
					</div>
					<br>
					<div class="alert alert-success strong compact text-center">
						Доступ к сайтам зависит от выбранных групп
						<br>
						<span class="text-muted italic small">Чтобы заблокировать сайт для пользователя, щелкните по нему</span>
					</div>
					<div class="well clearfix">
						<?= $siteList;?>
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer clearfix">
			<div class="pull-left">
				<div class="btn-group">
					<button class="btn btn-danger strong" type="button">Удалить пользовтаеля</button>
					<?= $buttonBlock;?>
					<button class="btn btn-default" type="button">Сбросить пароль</button>
				</div>
			</div>
			<div class="pull-right">
				<button class="btn btn-primary strong" type="submit">Сохранить</button>
				<a href="/admin/userlist/" class="btn btn-default">Закрыть</a>
			</div>
		</div>
	</div>
</div>