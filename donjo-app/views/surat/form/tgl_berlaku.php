							<div class="form-group">
							<input type="hidden" id="masa_berlaku" value="<?= $masa_berlaku['masa_berlaku'] ?>">
							<input type="hidden" id="satuan_masa_berlaku" value="<?= $masa_berlaku['satuan_masa_berlaku'] ?>">
								<label for="berlaku_dari"  class="col-sm-3 control-label">Berlaku Dari - Sampai</label>
								<div class="col-sm-3 col-lg-2">
									<div class="input-group input-group-sm date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input title="Pilih Tanggal" id="tgl_mulai" class="form-control input-sm required readonly-permohonan" name="berlaku_dari" type="text"/>
									</div>
								</div>
								<div class="col-sm-3 col-lg-2">
									<div class="input-group input-group-sm date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input title="Pilih Tanggal" id="tgl_akhir" class="form-control input-sm required readonly-permohonan" name="berlaku_sampai" type="text"/>
									</div>
								</div>
							</div>
