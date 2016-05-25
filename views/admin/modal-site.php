<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true" title="Закрыть">&times;</button>
	<h4 class="modal-title">
		<?= $modal_title;?>
	</h4>
</div>
<form action="/admin/siteSave/" method="post">
	<div class="modal-body">
		<div class="form-group row">
			<div class="col-xs-4 text-right">
				<label class="control-label">Название сайта :</label>
			</div>
			<div class="col-xs-8">
				<input type="text" class="form-control" name="s_title" value="<?= $s_title;?>">
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4 text-right">
				<label class="control-label">Индекс сайта (бит) :</label>
			</div>
			<div class="col-xs-8">
				<input type="text" class="form-control" name="s_key" value="<?= $s_key;?>">
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4 text-right">
				<label class="control-label">Ссылка на сайт :</label>
			</div>
			<div class="col-xs-8">
				<input type="text" class="form-control" name="s_link" value="<?= $s_link;?>">
				<input type="hidden" name="sid" value="<?= $sid;?>">
			</div>
		</div>
		<div class="form--group row">
			<div class="col-xs-4 text-right">
				<label class="control-label">Ключ шифрования</label>
			</div>
			<div class="col-xs-8">
				<input type="text" class="form-control" name="passkey" readonly value="<?= $p_key;?>">
			</div>
		</div>
	</div>
	<div class="modal-footer clearfix compact">
		<div class="btn-group pull-right">
			<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
			<button type="submit" class="btn btn-primary">Сохранить</button>
		</div>
	</div>
</form>