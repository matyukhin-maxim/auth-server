<form action="/report/query/" method="post" id="scales">

	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading strong">
					<i class="glyphicon glyphicon-cog"></i>&nbsp;
					Отчет по Конвейерным весам
				</div>
				<div class="panel-body">
					<div class="row form-group">
						<div class="col-sm-6">
							<div class="col-xs-4 control-label text-right">Дата с:</div>
							<div class="col-xs-8">
								<div class="input-group dpicker">
									<input id="bdate" type="text" class="form-control" name="bdate" readonly>
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-calendar"></i>
								</span>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="col-xs-4 control-label text-right">Дата по:</div>
							<div class="col-xs-8">
								<div class="input-group dpicker">
									<input id="edate" type="text" class="form-control" name="edate" readonly>
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-calendar"></i>
								</span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel-footer clearfix">
					<div class="pull-left col-xs-6">
						<div class="btn-group btn-group-justified" data-toggle="buttons">
							<label class="btn btn-default strong active">
								<input name="lineA" value="1" type="checkbox" autocomplete="off" checked> Нитка А
							</label>
							<label class="btn btn-default strong active">
								<input name="lineB" value="1" type="checkbox" autocomplete="off" checked> Нитка Б
							</label>
						</div>
					</div>
					<div class="pull-right">
						<div class="btn-group strong">
							<button type="submit" class="btn btn-primary strong">Сформировать</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</form>