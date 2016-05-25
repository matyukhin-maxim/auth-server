<br><br>
<div class="row">
	<div class="col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
		<div class="login-panel panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title strong text-center">Смена пароля</h3>
			</div>
			<div class="panel-body">
				<form method="post" action="/login/password/" id="frm-change">
					<fieldset>
						<div class="form-group">
							<input class="form-control text-center" type="text" disabled value="<?= $username; ?>">
						</div>
						<div class="form-group">
							<label for="npwd">Придумайте новый пароль</label>
							<input id="pass" class="form-control" placeholder="Введите новый пароль" name="password"
							       type="password" value="" required autofocus>
							<label for="npwd">Подтверждение пароля</label>
							<input id="check" class="form-control" placeholder="Введите пароль еще раз" name="password"
							       type="password" value="" required>
						</div>
						<button class="btn  btn-primary btn-block" type="submit">Изменить</button>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
</div>
