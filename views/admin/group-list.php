<div class="row">
	<div class="col-md-10 col-md-offset-1">
		<div class="panel panel-default">
			<div class="panel-heading strong">Группы доступа</div>
			<div class="panel-body panel-response">
				<div class="list-group">
					<?= $grouplist;?>
				</div>
			</div>
			<div class="panel-footer">
				<div class="input-group">
					<input type="text" id="g-name" class="form-control" placeholder="Название новой группы">
					<div class="input-group-btn">
						<button class="btn btn-primary" id="add" title="Добавить новую группу">
							<i class="glyphicon glyphicon-share-alt"></i>
							Добавить
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>