
<?php $__env->startSection('Contents'); ?>
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<?php if (request()->user()->hasRole('hr_access') && $status == 0) {?>
						<a href="<?php echo e($site); ?>/Contract/<?php echo e($department); ?>/<?php echo e($periode); ?>/<?php echo e($tipe); ?>" type="button"
							class="btn btn-default btn-xs"><i class="fa fa-angle-double-left"></i> &nbsp;Back</a>
						<?php }?>
						<div class="pull-left">
							<?php $__currentLoopData = $tb_salary_component; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<?php
								if ($dt->component == 'upah_pokok')
									$upah_pokok_a = $dt->amount;
								if ($dt->component == 'tunjangan_makan')
									$tunjangan_makan_a = $dt->amount;
								if ($dt->component == 'tunjangan_transport')
									$tunjangan_transport_a = $dt->amount;
								if ($dt->component == 'tunjangan_shift')
									$tunjangan_shift_a = $dt->amount;
								if ($dt->component == 'koperasi')
									$koperasi_a = $dt->amount;
								if ($dt->component == 'serikat')
									$serikat_a = $dt->amount;
								if ($dt->component == 'bpjs_kesehatan')
									$bpjs_kesehatan_l = $dt->amount;
								if ($dt->component == 'jaminan_hari_tua')
									$jaminan_hari_tua_p = $dt->presentase;
								if ($dt->component == 'jaminan_hari_tua_perusahaan')
									$jaminan_hari_tua_perusahaan_p = $dt->presentase;
								if ($dt->component == 'jaminan_pensiun') {
									$jaminan_pensiun_p = $dt->presentase;
									$jaminan_pensiun_l = $dt->amount;
								}
								if ($dt->component == 'jaminan_kecelakaan_kerja')
									$jaminan_kecelakaan_kerja_p = $dt->presentase;
								if ($dt->component == 'jaminan_kecelakaan_kerja_dasar')
									$jaminan_kecelakaan_kerja_dasar_p = $dt->presentase;
								if ($dt->component == 'jaminan_kematian')
									$jaminan_kematian_p = $dt->presentase;
								if ($dt->component == 'jaminan_kematian_dasar')
									$jaminan_kematian_dasar_a = $dt->amount;
								if ($dt->component == 'prosentase_bpjs_sai')
									$prosentase_bpjs_sai_p = $dt->presentase;
								if ($dt->component == 'prosentase_bpjs_magang')
									$prosentase_bpjs_magang_p = $dt->presentase;
								if ($dt->component == 'upah_dasar')
									$upah_dasar_a = $dt->amount;
																															?>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php if ($status == 1) {?>
							<button type="button" class="btn btn-primary btn-md edit-modal" data-idsalarycontract=""
								data-tanggalkontrak="<?php echo e($default); ?>" data-idkaryawan="" data-nik="" data-namakaryawan=""
								data-tipekontrak="<?php echo e($tipe); ?>" data-upahpokok="0" data-prosentaseupah="0" data-upahharian="0"
								data-tunjanganharian="0" data-tunjanganjabatan="0" data-tunjanganskill="0"
								data-tunjanganprestasitahunan="0" data-tunjanganmakan="1" data-tunjangantransport="0"
								data-tunjanganshift="0" data-koperasi="0" data-serikat="0" data-totalsalary="" data-slpj=""
								data-bpjstambahan="0" data-bpjskesehatan="" data-kesehatan="" data-jaminanharitua=""
								data-jaminanharituaperusahaan="" data-jaminanpensiun="" data-jaminankecelakaankerja=""
								data-jaminankematian="" data-statuspajak="1" data-prosentasebpjs="" data-npwp=""
								data-rekening="" data-statusbpjs="" data-akses="tambah"><i class="fa fa-user"></i> &nbsp;New
								Contract</button>
							<?php if (request()->user()->hasRole('hr_access')) {?>
							<a href="<?php echo e($site); ?>/Archive/Contract/<?php echo e($department); ?>/<?php echo e($periode); ?>/<?php echo e($tipe); ?>" type="button"
								class="btn btn-warning btn-md"><i class="fa fa-clipboard"></i> &nbsp;Archive</a>
							<a href="<?php echo e($site); ?>/ContractDtl/<?php echo e($department); ?>/<?php echo e($periode); ?>/<?php echo e($tipe); ?>" type="button"
								class="btn btn-info btn-md"><i class="fa fa-folder-o"></i> &nbsp;Detail</a>
							<?php }?>
							<?php }?>
						</div>
					</div>
					<div class="box-body" style="overflow-x: auto;">
						<table id="tables" class="table table-bordered" style="min-width: 100%;">
							<thead>
								<tr>
									<th>ACTIONS</th>
									<th>NO</th>
									<th>NIK</th>
									<th>EMPLOYEE_NAME</th>
									<th>JOIN_DATE</th>
									<th>DEPT</th>
									<th>COST_CENTER</th>
									<th>POSITION</th>
									<th>CONTRACT</th>
									<th>END_CONTRACT</th>
									<th>KTP</th>
									<th>REKENING</th>
									<th>TAX</th>
									<th>KOPERASI</th>
									<th>UNION</th>
									<th>BPJS</th>
									<th>JHT</th>
									<th>JP</th>
									<th>GROSS_SALARY</th>
								</tr>
							</thead>
							<tbody>
								<?php $no = 0;
	$item = '';?>
								<?php $__currentLoopData = $tb_salary_contract; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<tr>
																<td style="width:120px;">
																	<div class="pull-left">
																		<?php if ($status == 1) {?>
																		<button type="button" class="btn btn-primary btn-xs edit-modal"
																			data-idsalarycontract="<?php echo e($dt->id); ?>"
																			data-contractname="<?php echo e($dt->contract_name); ?>"
																			data-tanggalkontrak="<?php echo e($dt->tanggal_kontrak); ?>"
																			data-tanggalexpired="<?php echo e($dt->tanggal_expired); ?>"
																			data-idkaryawan="<?php echo e($dt->id_employee); ?>" data-nik="<?php echo e($dt->NIK); ?>"
																			data-namakaryawan="<?php echo e($dt->nama_karyawan); ?>"
																			data-tipekontrak="<?php echo e($dt->tipe_kontrak); ?>" data-subtipe="<?php echo e($dt->sub_tipe); ?>"
																			data-upahpokok="<?php echo number_format($dt->upah_pokok, 0, ",", ".");?>"
																			data-prosentaseupah="<?php echo number_format($dt->prosentase_upah, 0, ",", ".");?>"
																			data-upahharian="<?php echo number_format($dt->upah_harian, 0, ",", ".");?>"
																			data-tunjanganharian="<?php echo number_format($dt->tunjangan_harian, 0, ",", ".");?>"
																			data-tunjanganjabatan="<?php echo number_format($dt->tunjangan_jabatan, 0, ",", ".");?>"
																			data-tunjanganskill="<?php echo number_format($dt->tunjangan_skill, 0, ",", ".");?>"
																			data-tunjanganprestasitahunan="<?php echo number_format($dt->tunjangan_prestasi_tahunan, 0, ",", ".");?>"
																			data-tunjanganmakan="<?php echo number_format($dt->tunjangan_makan, 0, ",", ".");?>"
																			data-tunjangantransport="<?php echo number_format($dt->tunjangan_transport, 0, ",", ".");?>"
																			data-tunjanganshift="<?php echo number_format($dt->tunjangan_shift, 0, ",", ".");?>"
																			data-workingday="<?php echo e($dt->working_day); ?>"
																			data-koperasi="<?php echo number_format($dt->koperasi, 0, ",", ".");?>"
																			data-serikat="<?php echo number_format($dt->serikat, 0, ",", ".");?>"
																			data-totalsalary="<?php echo number_format($dt->total_salary, 0, ",", ".");?>"
																			data-bpjssalary="<?php echo number_format($dt->bpjs_salary, 0, ",", ".");?>"
																			data-slpj="<?php echo number_format($dt->slpj, 0, ",", ".");?>"
																			data-bpjstambahan="<?php echo number_format($dt->bpjs_tambahan, 0, ",", ".");?>"
																			data-bpjskesehatan="<?php echo number_format($dt->bpjs_kesehatan, 0, ",", ".");?>"
																			data-kesehatan="<?php echo number_format($dt->kesehatan, 0, ",", ".");?>"
																			data-jaminanharitua="<?php echo number_format($dt->jaminan_hari_tua, 0, ",", ".");?>"
																			data-jaminanharituaperusahaan="<?php echo number_format($dt->jaminan_hari_tua_perusahaan, 0, ",", ".");?>"
																			data-jaminanpensiun="<?php echo number_format($dt->jaminan_pensiun, 0, ",", ".");?>"
																			data-jaminankecelakaankerja="<?php echo number_format($dt->jaminan_kecelakaan_kerja, 0, ",", ".");?>"
																			data-jaminankematian="<?php echo number_format($dt->jaminan_kematian, 0, ",", ".");?>"
																			data-statuspajak="<?php echo number_format($dt->status_pajak, 0, ",", ".");?>"
																			data-prosentasebpjs="<?php echo number_format($dt->prosentase_bpjs, 0, ",", ".");?>"
																			data-ktp="<?php echo e($dt->nomor_ktp); ?>" data-npwp="<?php echo e($dt->nomor_npwp); ?>"
																			data-rekening="<?php echo e($dt->nomor_rekening); ?>"
																			data-statusbpjs="<?php echo e($dt->status_bpjs); ?>" data-akses="edit"><i
																				class="fa fa-edit"></i></button>
																		<?php }?>
																		<button title="Delete" type="button" class="delete-modal btn btn-danger btn-xs"
																			data-delid="<?php echo e($dt->id); ?>" data-delname="<?php echo e($dt->nama_karyawan); ?>" data-akses="<?php if ($status == 1)
										echo 'Delete';
									else
										echo 'Recreate';?>"><i class="<?php if ($status == 1)
										echo 'fa fa-trash';
									else
										echo 'fa fa-upload';?>"></i></button>
																		<?php if($dt->tanggal_expired != '' && $dt->status == 1): ?>
																			<button title="New" type="button" class="new-modal btn btn-success btn-xs"
																				data-newid="<?php echo e($dt->id); ?>" data-newname="<?php echo e($dt->nama_karyawan); ?>"><i
																					class="fa fa-plus"></i></button>
																		<?php endif; ?>
																	</div>
																</td>
																<td>
																	<?php $no++;
									echo $no;?>
																</td>
																<td><?php echo e($dt->NIK); ?></td>
																<td><?php echo e($dt->nama_karyawan); ?></td>
																<td><?php echo e(date('d-M-Y', strtotime($dt->tanggal_kontrak))); ?></td>
																<td><?php echo e($dt->department); ?></td>
																<td><?php echo e($dt->cc_code); ?></td>
																<td><?php echo e($dt->jabatan); ?></td>
																<td><?php echo e($dt->contract_name); ?></td>
																<td>
																	<?php if($dt->contract_name == 'Permanen'): ?>
																		&nbsp;
																	<?php elseif($dt->finish_contract == ''): ?>
																		<?php echo e(date('d-M-Y', strtotime($dt->start_contract))); ?>

																	<?php else: ?>
																		<?php echo e(date('d-M-Y', strtotime($dt->finish_contract))); ?>

																	<?php endif; ?>
																</td>
																<td><?php echo e($dt->nomor_ktp); ?></td>
																<td><?php echo e($dt->nomor_rekening); ?></td>
																<td><?php echo e($dt->statusPajak); ?></td>
																<td><?php if ($dt->koperasi == 1)
										echo number_format($data['koperasi'], 0);
									else
										echo "0";?>
																</td>
																<td><?php if ($dt->serikat == 1)
										echo number_format($data['serikat'], 0);
									else
										echo "0";?>
																</td>
																<td><?php if ($dt->status_bpjs == 1)
										echo number_format($dt->bpjs_kesehatan, 0);
									else
										echo "0";?>
																</td>
																<td><?php echo number_format($dt->jaminan_hari_tua, 0);?></td>
																<td><?php echo number_format($dt->jaminan_pensiun, 0);?></td>
																<td style="text-align:right;"><?php echo number_format($dt->total_salary, 0);?></td>
															</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>

						</table>
					</div>
					<!-- /.box-body -->
				</div>

			</div>
		</div>
	</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Modals'); ?>
	<div class="modal fade" id="modal-edit">
		<div class="modal-dialog" style="width:1000px;">
			<div class="modal-content">
				<form>
					<input type="hidden" id="idSalaryContract">
					<?php echo e(csrf_field()); ?>

					<div class="modal-body">
						<div class="box box-primary box-solid" style="border:0px;">
							<div class="box-header" style="padding:5px 10px;font-size:20px;">
								<label id="Judul"></label>
								<div class="pull-right">
									Efective
									<input type="date" style="color:#000;" id="tanggalKontrak">
									Expired
									<input type="date" style="color:#000;" id="tanggalExpired">
								</div>
							</div>
							<div class="row" style="padding-top:20px;">
								<div class="col-lg-3 col-md-6 col-xs-12" style="border-right:1px solid #EEE;">
									<div class="box-body" style="padding:10px 0px;">
										<div class="form-group">
											<label>Employee</label>
											<select id="idKaryawan" class="form-control selectpicker"
												data-live-search="true">
												<!-- <select id="idKaryawan" class="form-control"> -->
												<option value=""></option>
												<?php $__currentLoopData = $tb_employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($dt2->id); ?>">
														<?php echo strtoupper($dt2->nama_karyawan) . " (" . $dt2->NIK . " " . strtoupper($dt2->position_name) . " " . strtoupper($dt2->dept_code) . ")";?>
													</option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

											</select>
										</div>
										<div class="form-group">
											<label>Status Karyawan</label>
											<select id="tipeKontrak" class="form-control">
												<option value=""></option>
												<?php if ($tipe == 'SAI' || $tipe == 'TKA') {?>
												<option value="SAI">SAI</option>
												<option value="TKA">TKA</option>
												<?php } elseif ($tipe == 'MAGANG') {?>
												<option value="MAGANG">MAGANG</option>
												<?php } elseif ($tipe == 'MANAGEMENT') {?>
												<option value="MANAGEMENT">MANAGEMENT</option>
												<?php }?>
											</select>
										</div>
										<div class="form-group">
											<label>Tax Status</label>
											<select id="statusPajak" class="form-control">
												<option value=""></option>
												<?php $__currentLoopData = $tb_pph21_status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($dt2->id); ?>"><?php echo e($dt2->status_pajak); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</select>
										</div>
										<div class="form-group">
											<label>Base Salary</label>
											<input type="text" id="upahPokok" class="form-control" style="text-align:right;"
												onclick="select();">
											<input type="hidden" id="upahHarian" class="form-control"
												style="text-align:right;" onclick="select();">
										</div>
										<!-- <div class="form-group">
														<label>Nomor KTP</label>
														<input type="text" id="nomorktp" class="form-control" style="text-align:right;">
													</div> -->
										<!-- <div class="form-group">
														<label>Nomor NPWP</label>
														<input type="text" id="nomornpwp" class="form-control" style="text-align:right;">
														<input type="hidden" id="upahHarian" class="form-control" style="text-align:right;" onclick="select();">
													</div> -->
										<div class="form-group">
											<label>Nomor Rekening</label>
											<input type="text" id="nomorrekening" class="form-control"
												style="text-align:right;">
											<input type="hidden" id="tunjanganHarian" class="form-control"
												style="text-align:right;" onclick="select();">
										</div>
									</div>
								</div>
								<div class="col-lg-3 col-md-6 col-xs-12" style="border-right:1px solid #EEE;">
									<div class="box-body" style="padding:10px 0px;">
										<div class="form-group">
											<label>Positional Allowance</label>
											<input type="text" id="tunjanganJabatan" class="form-control"
												style="text-align:right;" onclick="select();">
										</div>
										<div class="form-group">
											<label>Skill Allowance</label>
											<input type="text" id="tunjanganSkill" class="form-control"
												style="text-align:right;" onclick="select();">
										</div>
										<div class="form-group">
											<label>Performance Allowance</label>
											<input type="text" id="tunjanganPrestasiTahunan" class="form-control"
												style="text-align:right;" onclick="select();">
										</div>
										<div class="form-group">
											<label>Total Salary</label>
											<input type="text" id="totalSalary" class="form-control" disabled
												style="text-align:right;">
											<!-- <input type="hidden" id="tunjanganTransport" value="1"> -->
											<input type="hidden" id="tunjanganShift" value="1">
										</div>
										<div class="form-group">
											<label>Transport Allowance</label>
											<select id="tunjanganTransport" class="form-control">
												<option value="0">No</option>
												<option value="1">Yes</option>
											</select>
										</div>
										<div class="form-group">
											<label>Working Day</label>
											<select id="workingDay" class="form-control">
												<option value="5">5</option>
												<option value="6">6</option>
											</select>
										</div>
									</div>
								</div>
								<div class="col-lg-3 col-md-6 col-xs-12" style="border-right:1px solid #EEE;">
									<div class="box-body" style="padding:10px 0px;">
										<div class="form-group">
											<label>Koperasi</label>
											<select id="koperasi" class="form-control">
												<option value="0">0</option>
												<option value="1"><?php echo e($data['koperasi']); ?></option>
											</select>
										</div>
										<div class="form-group">
											<label>Union</label>
											<select id="serikat" class="form-control">
												<option value="0">0</option>
												<option value="1"><?php echo e($serikat_a); ?></option>
											</select>
										</div>
										<div class="form-group">
											<label>BPJS Status</label>
											<select id="statusbpjs" class="form-control">
												<option value="0">0</option>
												<option value="1">1</option>
											</select>
											<input type="hidden" id="tunjanganMakan" class="form-control"
												style="text-align:right;" value="1">
										</div>
										<div class="form-group">
											<label>Salary for BPJS</label>
											<input type="text" id="bpjsSalary" class="form-control"
												style="text-align:right;" onclick="select();">
											<input type="hidden" id="prosentaseUpah" class="form-control" disabled>
										</div>
										<div class="form-group">
											<label>BPJS Additional</label>
											<input type="number" id="bpjsTambahan" class="form-control" onclick="select();">
										</div>
										<div class="form-group">
											<label>SLPJ</label>
											<div class="pull-right">(Salary/173)</div>
											<input type="text" id="slpj" class="form-control" disabled
												style="text-align:right;">
										</div>
									</div>
								</div>
								<div class="col-lg-3 col-md-6 col-xs-12" style="border-right:1px solid #EEE;">
									<div class="box-body" style="padding:10px 0px;">
										<label>BPJS Employee</label>
										<div
											style="background:#f9fafc;padding:3px 5px 0px;border:1px solid #CCC;border-radius:5px;">
											<div class="form-group">
												<label>BPJS Kes</label>
												<small id="bpjsKesehatanText"
													style="color:#CCC;"><?php echo "( 0% )";?></small>
												<input type="text" id="bpjsKesehatan" class="form-control" disabled
													style="text-align:right;">
												<input type="hidden" id="prosentaseBpjs">
											</div>
											<div class="form-group">
												<label>JHT</label>
												<small id="jaminanHariTuaText"
													style="color:#CCC;"><?php echo "( 0% )";?></small>
												<input type="text" id="jaminanHariTua" class="form-control"
													style="text-align:right;">
											</div>
											<div class="form-group">
												<label>JP</label>
												<small id="jaminanPensiunText"
													style="color:#CCC;"><?php echo "( 0% )";?></small>
												<input type="text" id="jaminanPensiun" class="form-control"
													style="text-align:right;" onclick="select();">
											</div>
										</div><br>
										<div
											style="background:#f9fafc;padding:3px 5px;border:1px solid #CCC;border-radius:5px;">
											<div class="form-group">
												<label>BPJS Company</label>
												<small id="kesehatanText" style="color:#CCC;"><?php echo "( 0% )";?></small>
												<input type="text" id="kesehatan" class="form-control" disabled
													style="text-align:right;">
											</div>
											<div class="form-group">
												<label>JKK</label>
												<small id="jaminanKecelakaanKerjaText"
													style="color:#CCC;"><?php echo "( 0% )";?></small>
												<input type="text" id="jaminanKecelakaanKerja" class="form-control"
													style="text-align:right;">
											</div>
											<div class="form-group">
												<label>JKM</label>
												<small id="jaminanKematianText"
													style="color:#CCC;"><?php echo "( 0% )";?></small>
												<input type="text" id="jaminanKematian" class="form-control"
													style="text-align:right;">
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="box-footer">
								<button type="button" class="btn btn-success pull-right" id="simpan">Save</button>
								<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="modal fade" id="modal-delete">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Confirmation</h4>
				</div>
				<div class="modal-body">
					<b id="aksesText">Delete </b> <b id="delname"></b> ?
					<input type="hidden" id="delid">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-left delete" data-dismiss="modal">Yes, Delete</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>

			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<div class="modal fade" id="modal-new">
		<div class="modal-dialog box box-success" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Confirmation</h4>
				</div>
				<div class="modal-body">
					<b id="aksesText">Add New </b> <b id="newname"></b> ?
					<input type="hidden" id="newid">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success pull-left new" data-dismiss="modal">Yes, Add</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>

			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
	<!-- page script Tabel-->
	<script type="text/javascript">
		$(document).ready(function () {
			$('#idDepartment').val(<?php echo e($department); ?>);
		});
		$('body').on("change", "#periode", function () {
			var department = document.getElementById('idDepartment').value;
			<?php if ($status == 1) {?>
			window.location.href = "<?php echo e($site); ?>/Contract/" + department;
			<?php } else {?>
			window.location.href = "<?php echo e($site); ?>/Archive/Contract/" + department;
			<?php }?>
		});
		$('body').on("change", "#idDepartment", function () {
			var department = document.getElementById('idDepartment').value;
			var tipe = "<?php echo e($tipe); ?>";
			<?php if ($status == 1) {?>
			window.location.href = "<?php echo e($site); ?>/Contract/" + department + "/0/" + tipe;
			<?php } else {?>
			window.location.href = "<?php echo e($site); ?>/Archive/Contract/" + department + "/0/" + tipe;
			<?php }?>
		});
		$(document).on('click', '.edit-modal', function () {
			var tipe = $(this).data('tipekontrak');
			var subtipe = $(this).data('subtipe');
			$('#idSalaryContract').val($(this).data('idsalarycontract'));
			$('#tipeKontrak').val($(this).data('tipekontrak'));
			$('#tanggalKontrak').val($(this).data('tanggalkontrak'));
			$('#tanggalExpired').val($(this).data('tanggalexpired'));
			$('#idKaryawan').val($(this).data('idkaryawan'));
			$('#upahPokok').val($(this).data('upahpokok'));
			$('#prosentaseUpah').val($(this).data('prosentaseupah'));
			$('#upahHarian').val($(this).data('upahharian'));
			$('#tunjanganHarian').val($(this).data('tunjanganharian'));
			$('#tunjanganJabatan').val($(this).data('tunjanganjabatan'));
			$('#tunjanganSkill').val($(this).data('tunjanganskill'));
			$('#tunjanganPrestasiTahunan').val($(this).data('tunjanganprestasitahunan'));
			$('#tunjanganMakan').val($(this).data('tunjanganmakan'));
			$('#tunjanganTransport').val($(this).data('tunjangantransport'));
			$('#tunjanganShift').val($(this).data('tunjanganshift'));
			$('#workingDay').val($(this).data('workingday'));
			$('#koperasi').val($(this).data('koperasi'));
			$('#serikat').val($(this).data('serikat'));
			$('#totalSalary').val($(this).data('totalsalary'));
			$('#bpjsSalary').val($(this).data('bpjssalary'));
			$('#slpj').val($(this).data('slpj'));
			$('#bpjsTambahan').val($(this).data('bpjstambahan'));
			$('#bpjsKesehatan').val($(this).data('bpjskesehatan'));
			$('#kesehatan').val($(this).data('kesehatan'));
			$('#prosentaseBpjs').val($(this).data('prosentasebpjs'));
			if (tipe == 'MAGANG') {
				$('#jaminanHariTua').val($(this).data('jaminanharituaperusahaan'));
			} else {
				$('#jaminanHariTua').val($(this).data('jaminanharitua'));
			}
			$('#jaminanPensiun').val($(this).data('jaminanpensiun'));
			$('#jaminanKecelakaanKerja').val($(this).data('jaminankecelakaankerja'));
			$('#jaminanKematian').val($(this).data('jaminankematian'));
			$('#statusPajak').val($(this).data('statuspajak'));
			if ($(this).data('nik') != '') {
				$('#Judul').text($(this).data('nik') + ': ' + $(this).data('namakaryawan'));
				document.getElementById("idKaryawan").disabled = true;
			} else {
				$('#Judul').text('New Contract');
				document.getElementById("idKaryawan").disabled = false;
			}
			var akses = $(this).data('akses');
			//alert(akses);
			if (akses == 'tambah') {
				// document.getElementById("upahPokok").disabled = true;
				// document.getElementById("upahHarian").disabled = true;
				// document.getElementById("tunjanganHarian").disabled = true;
				// document.getElementById("tunjanganJabatan").disabled = true;
				// document.getElementById("tunjanganSkill").disabled = true;
				// document.getElementById("tunjanganPrestasiTahunan").disabled = true;
				// document.getElementById("tunjanganMakan").disabled = true;
				// document.getElementById("tunjanganTransport").disabled = true;
				// document.getElementById("tunjanganShift").disabled = true;
				// document.getElementById("workingDay").disabled = true;
				// document.getElementById("koperasi").disabled = true;
				// document.getElementById("serikat").disabled = true;
				// document.getElementById("statusPajak").disabled = true;
				// document.getElementById("bpjsTambahan").disabled = true;
				// document.getElementById("idKaryawan").disabled = false;
				// document.getElementById("bpjsSalary").disabled = true;
				// document.getElementById("jaminanPensiun").disabled = true;
				$('#tipeKontrak').val(tipe);
				$('#prosentaseUpah').val(0);
			} else {
				document.getElementById("idKaryawan").disabled = true;
			}
			// $('#nomornpwp').val($(this).data('npwp'));
			// $('#nomorktp').val($(this).data('ktp'));
			$('#nomorrekening').val($(this).data('rekening'));
			$('#statusbpjs').val($(this).data('statusbpjs'));
			//pilihTipe();
			//calculate();

			//for update label
			var tipe = $('#tipeKontrak').val();
			var upahPokok = $('#upahPokok').val().replace(/\./g, '');
			let tunjanganJabatan = $('#tunjanganJabatan').val().replace(/\./g, '');
			let tunjanganSkill = $('#tunjanganSkill').val().replace(/\./g, '');
			let tunjanganPrestasiTahunan = $('#tunjanganPrestasiTahunan').val().replace(/\./g, '');
			var tunjanganDasar = tunjanganJabatan * 1 + tunjanganSkill * 1 + tunjanganPrestasiTahunan * 1;

			var umk = upahPokok * 100 / $('#prosentaseUpah').val();
			var salaryUmk = umk * 1 + tunjanganDasar;
			var salary = upahPokok * 1 + tunjanganDasar;

			var limitBpjs = '<?php echo $bpjs_kesehatan_l;?>';
			if (salaryUmk > limitBpjs) {
				var salaryBpjs = limitBpjs;
			} else if (salaryUmk > 0) {
				var salaryBpjs = salaryUmk;
			} else {
				var salaryBpjs = 0;
			}

			var limitPensiun = '<?php echo $jaminan_pensiun_l;?>';
			if (tipe == 'SAI' || tipe == 'TKA' || tipe == 'MANAGEMENT') {
				if (salaryUmk > limitPensiun) {
					var salaryPensiun = limitPensiun;
				} else {
					var salaryPensiun = salaryUmk;
				}
			} else {
				var salaryPensiun = 0;
			}

			var upahDasar = '<?php echo $upah_dasar_a;?>';
			if (salary > 0 && salary < upahDasar) {
				salaryDasar = upahDasar;
			} else {
				salaryDasar = salary;
			}

			if (tipe == 'SAI' || tipe == 'TKA' || tipe == 'MANAGEMENT') {
				var prosentaseBpjs = '<?php echo number_format($prosentase_bpjs_sai_p, 0);?>';
				var prosentaseBpjsPerusahaan = 5 - prosentaseBpjs;
				var prosentaseJht = '<?php echo number_format($jaminan_hari_tua_p, 0);?>';
				var prosentaseJp = '<?php echo number_format($jaminan_pensiun_p, 0);?>';
				var prosentaseJkk = '<?php echo number_format($jaminan_kecelakaan_kerja_p, 2);?>';

				var prosentaseJkm = '<?php echo number_format($jaminan_kematian_p, 2);?>';
				$('#jaminanKematianText').text('( ' + prosentaseJkm + '%, ' + salaryDasar + ' )');
				var jaminanKematian = Math.round(salaryDasar * prosentaseJkm / 100);
			} else if (tipe == 'MAGANG') {
				var prosentaseBpjs = '<?php echo number_format($prosentase_bpjs_magang_p, 0);?>';
				var prosentaseBpjsPerusahaan = 5 - prosentaseBpjs;
				var prosentaseJht = '<?php echo number_format($jaminan_hari_tua_perusahaan_p, 0);?>';
				//var prosentaseJht=0;
				var prosentaseJp = 0;
				var prosentaseJkk = '<?php echo number_format($jaminan_kecelakaan_kerja_dasar_p, 2);?>';

				var jaminanKematian = '<?php echo number_format($jaminan_kematian_dasar_a, 0, ",", ".");?>';
				$('#jaminanKematianText').text('( ' + jaminanKematian + ' )');
			} else {
				var prosentaseBpjs = 0;
				var prosentaseBpjsPerusahaan = 0;
				var prosentaseJht = 0;
				var prosentaseJp = 0;
				var prosentaseJkk = 0;

				var jaminanKematian = 0;
				$('#jaminanKematianText').text('( 0% )');
			}

			$('#prosentaseBpjs').val(prosentaseBpjs);
			var bpjsTambahan = $('#bpjsTambahan').val();
			var prosentaseBpjsShow = prosentaseBpjs * 1 + bpjsTambahan * 1;
			$('#bpjsKesehatanText').text('( ' + prosentaseBpjsShow + '%, ' + salaryBpjs + ' )');
			$('#kesehatanText').text('( ' + prosentaseBpjsPerusahaan + '%, ' + salaryBpjs + ' )');
			$('#jaminanHariTuaText').text('( ' + prosentaseJht + '%, ' + salaryDasar + ' )');
			$('#jaminanPensiunText').text('( ' + prosentaseJp + '%, ' + salaryPensiun + ' )');
			$('#jaminanKecelakaanKerjaText').text('( ' + prosentaseJkk + '%, ' + salaryDasar + ' )');
			pilihTipes();
			//End Label
			$('#modal-edit').modal('show');
		});
		$(document).on('change', '#tipeKontrak', function () {
			resetTipe();
		});
		$(document).on('keyup', '#upahPokok', function () {
			var biaya = document.getElementById('upahPokok');
			biaya.addEventListener('keyup', function (e) {
				biaya.value = formatRupiah(this.value, '');
			});
			calculate();
			let bpjsSalary = Math.round($('#totalSalary').val().replace(/\./g, '') * (100 / $('#prosentaseUpah').val()));
			$('#bpjsSalary').val(bpjsSalary);
			var angka = $('#bpjsSalary').val();
			formatRupiahS(angka, 'bpjsSalary');
		});
		$(document).on('keyup', '#bpjsSalary', function () {
			var biaya = document.getElementById('bpjsSalary');
			biaya.addEventListener('keyup', function (e) {
				biaya.value = formatRupiah(this.value, '');
			});
			calculateBPJS();
		});
		$(document).on('keyup', '#upahHarian', function () {
			var biaya = document.getElementById('upahHarian');
			biaya.addEventListener('keyup', function (e) {
				biaya.value = formatRupiah(this.value, '');
			});
			calculate();
		});
		$(document).on('keyup', '#tunjanganHarian', function () {
			var biaya = document.getElementById('tunjanganHarian');
			biaya.addEventListener('keyup', function (e) {
				biaya.value = formatRupiah(this.value, '');
			});
			calculate();
		});
		$(document).on('keyup', '#tunjanganJabatan', function () {
			var biaya = document.getElementById('tunjanganJabatan');
			biaya.addEventListener('keyup', function (e) {
				biaya.value = formatRupiah(this.value, '');
			});
			calculate();
			let bpjsSalary = Math.round($('#totalSalary').val().replace(/\./g, '') * (100 / $('#prosentaseUpah').val()));
			$('#bpjsSalary').val(bpjsSalary);
			var angka = $('#bpjsSalary').val();
			formatRupiahS(angka, 'bpjsSalary');
		});
		$(document).on('keyup', '#tunjanganSkill', function () {
			var biaya = document.getElementById('tunjanganSkill');
			biaya.addEventListener('keyup', function (e) {
				biaya.value = formatRupiah(this.value, '');
			});
			calculate();
			let bpjsSalary = Math.round($('#totalSalary').val().replace(/\./g, '') * (100 / $('#prosentaseUpah').val()));
			$('#bpjsSalary').val(bpjsSalary);
			var angka = $('#bpjsSalary').val();
			formatRupiahS(angka, 'bpjsSalary');
		});
		$(document).on('keyup', '#tunjanganPrestasiTahunan', function () {
			var biaya = document.getElementById('tunjanganPrestasiTahunan');
			biaya.addEventListener('keyup', function (e) {
				biaya.value = formatRupiah(this.value, '');
			});
			calculate();
			let bpjsSalary = Math.round($('#totalSalary').val().replace(/\./g, '') * (100 / $('#prosentaseUpah').val()));
			$('#bpjsSalary').val(bpjsSalary);
			var angka = $('#bpjsSalary').val();
			formatRupiahS(angka, 'bpjsSalary');
		});
		$(document).on('keyup', '#bpjsTambahan', function () {
			calculateBPJS();
		});
		$(document).on('change', '#bpjsTambahan', function () {
			calculateBPJS();
		});
	</script>
	<script>
		function pilihTipes() {
			var tipe = $('#tipeKontrak').val();
			if (tipe == 'SAI' || tipe == 'TKA' || tipe == 'MANAGEMENT') {
				document.getElementById("upahPokok").disabled = false;
				document.getElementById("upahHarian").disabled = true;
				document.getElementById("tunjanganHarian").disabled = true;
				document.getElementById("tunjanganJabatan").disabled = false;
				document.getElementById("tunjanganSkill").disabled = false;
				document.getElementById("tunjanganPrestasiTahunan").disabled = false;
				document.getElementById("tunjanganMakan").disabled = false;
				document.getElementById("tunjanganTransport").disabled = false;
				document.getElementById("tunjanganShift").disabled = false;
				document.getElementById("workingDay").disabled = false;
				document.getElementById("koperasi").disabled = false;
				document.getElementById("serikat").disabled = false;
				document.getElementById("statusPajak").disabled = false;
				document.getElementById("bpjsTambahan").disabled = false;
				document.getElementById("bpjsSalary").disabled = false;
				if (tipe == 'TKA') {
					$('#jaminanPensiun').val('0');
					//document.getElementById("jaminanPensiun").disabled = false;
				} else {
					if (tipe == 'MANAGEMENT') {
						//document.getElementById("jaminanPensiun").disabled = false;
					} else {
						//document.getElementById("jaminanPensiun").disabled = true;
					}
				}
				$('#prosentaseUpah').val(100);
			} else if (tipe == 'MAGANG') {
				document.getElementById("upahPokok").disabled = false;
				document.getElementById("upahHarian").disabled = true;
				document.getElementById("tunjanganHarian").disabled = true;
				document.getElementById("tunjanganJabatan").disabled = true;
				document.getElementById("tunjanganSkill").disabled = true;
				document.getElementById("tunjanganPrestasiTahunan").disabled = true;
				document.getElementById("tunjanganMakan").disabled = false;
				document.getElementById("tunjanganTransport").disabled = false;
				document.getElementById("workingDay").disabled = false;
				document.getElementById("tunjanganShift").disabled = false;
				document.getElementById("koperasi").disabled = true;
				document.getElementById("serikat").disabled = true;
				document.getElementById("statusPajak").disabled = false;
				document.getElementById("bpjsTambahan").disabled = false;
				document.getElementById("bpjsSalary").disabled = false;
				document.getElementById("jaminanPensiun").disabled = true;
				$('#prosentaseUpah').val(80);
			} else if (tipe == 'PSAB') {
				document.getElementById("upahPokok").disabled = true;
				document.getElementById("upahHarian").disabled = false;
				document.getElementById("tunjanganHarian").disabled = true;
				document.getElementById("tunjanganJabatan").disabled = true;
				document.getElementById("tunjanganSkill").disabled = true;
				document.getElementById("tunjanganPrestasiTahunan").disabled = true;
				document.getElementById("tunjanganMakan").disabled = false;
				document.getElementById("tunjanganTransport").disabled = true;
				document.getElementById("workingDay").disabled = false;
				document.getElementById("tunjanganShift").disabled = true;
				document.getElementById("koperasi").disabled = true;
				document.getElementById("serikat").disabled = true;
				document.getElementById("statusPajak").disabled = false;
				document.getElementById("bpjsTambahan").disabled = true;
				document.getElementById("bpjsSalary").disabled = true;
				document.getElementById("jaminanPensiun").disabled = true;
				$('#prosentaseUpah').val(100);
			} else if (tipe == 'PKL') {
				document.getElementById("upahPokok").disabled = true;
				document.getElementById("upahHarian").disabled = true;
				document.getElementById("tunjanganHarian").disabled = false;
				document.getElementById("tunjanganJabatan").disabled = true;
				document.getElementById("tunjanganSkill").disabled = true;
				document.getElementById("tunjanganPrestasiTahunan").disabled = true;
				document.getElementById("tunjanganMakan").disabled = false;
				document.getElementById("tunjanganTransport").disabled = true;
				document.getElementById("workingDay").disabled = false;
				document.getElementById("tunjanganShift").disabled = true;
				document.getElementById("koperasi").disabled = true;
				document.getElementById("serikat").disabled = true;
				document.getElementById("statusPajak").disabled = true;
				document.getElementById("bpjsTambahan").disabled = true;
				document.getElementById("bpjsSalary").disabled = true;
				document.getElementById("jaminanPensiun").disabled = true;
				$('#prosentaseUpah').val(100);
			}
		}
		function resetTipe() {
			var tipe = $('#tipeKontrak').val();
			if (tipe == 'SAI' || tipe == 'TKA' || tipe == 'MANAGEMENT') {
				document.getElementById("upahPokok").disabled = false;
				document.getElementById("upahHarian").disabled = true;
				document.getElementById("tunjanganHarian").disabled = true;
				document.getElementById("tunjanganJabatan").disabled = false;
				document.getElementById("tunjanganSkill").disabled = false;
				document.getElementById("tunjanganPrestasiTahunan").disabled = false;
				document.getElementById("tunjanganMakan").disabled = false;
				document.getElementById("tunjanganTransport").disabled = false;
				document.getElementById("workingDay").disabled = false;
				document.getElementById("tunjanganShift").disabled = false;
				document.getElementById("koperasi").disabled = false;
				document.getElementById("serikat").disabled = false;
				document.getElementById("statusPajak").disabled = false;
				document.getElementById("bpjsTambahan").disabled = false;
				document.getElementById("bpjsSalary").disabled = false;
				if (tipe == 'TKA') {
					$('#jaminanPensiun').val('0');
					document.getElementById("jaminanPensiun").disabled = false;
				} else {
					document.getElementById("jaminanPensiun").disabled = true;
				}
				$('#prosentaseUpah').val(100);
			} else if (tipe == 'MAGANG') {
				document.getElementById("upahPokok").disabled = false;
				document.getElementById("upahHarian").disabled = true;
				document.getElementById("tunjanganHarian").disabled = true;
				document.getElementById("tunjanganJabatan").disabled = true;
				document.getElementById("tunjanganSkill").disabled = true;
				document.getElementById("tunjanganPrestasiTahunan").disabled = true;
				document.getElementById("tunjanganMakan").disabled = false;
				document.getElementById("tunjanganTransport").disabled = false;
				document.getElementById("workingDay").disabled = false;
				document.getElementById("tunjanganShift").disabled = false;
				document.getElementById("koperasi").disabled = true;
				document.getElementById("serikat").disabled = true;
				document.getElementById("statusPajak").disabled = false;
				document.getElementById("bpjsTambahan").disabled = false;
				document.getElementById("bpjsSalary").disabled = false;
				document.getElementById("jaminanPensiun").disabled = true;
				$('#prosentaseUpah').val(80);
			} else if (tipe == 'PSAB') {
				document.getElementById("upahPokok").disabled = true;
				document.getElementById("upahHarian").disabled = false;
				document.getElementById("tunjanganHarian").disabled = true;
				document.getElementById("tunjanganJabatan").disabled = true;
				document.getElementById("tunjanganSkill").disabled = true;
				document.getElementById("tunjanganPrestasiTahunan").disabled = true;
				document.getElementById("tunjanganMakan").disabled = false;
				document.getElementById("tunjanganTransport").disabled = true;
				document.getElementById("workingDay").disabled = true;
				document.getElementById("tunjanganShift").disabled = true;
				document.getElementById("koperasi").disabled = true;
				document.getElementById("serikat").disabled = true;
				document.getElementById("statusPajak").disabled = false;
				document.getElementById("bpjsTambahan").disabled = true;
				document.getElementById("bpjsSalary").disabled = true;
				document.getElementById("jaminanPensiun").disabled = true;
				$('#prosentaseUpah').val(100);
			} else if (tipe == 'PKL') {
				document.getElementById("upahPokok").disabled = true;
				document.getElementById("upahHarian").disabled = true;
				document.getElementById("tunjanganHarian").disabled = false;
				document.getElementById("tunjanganJabatan").disabled = true;
				document.getElementById("tunjanganSkill").disabled = true;
				document.getElementById("tunjanganPrestasiTahunan").disabled = true;
				document.getElementById("tunjanganMakan").disabled = false;
				document.getElementById("tunjanganTransport").disabled = true;
				document.getElementById("workingDay").disabled = true;
				document.getElementById("tunjanganShift").disabled = true;
				document.getElementById("koperasi").disabled = true;
				document.getElementById("serikat").disabled = true;
				document.getElementById("statusPajak").disabled = true;
				document.getElementById("bpjsTambahan").disabled = true;
				document.getElementById("bpjsTambahan").disabled = true;
				document.getElementById("bpjsSalary").disabled = true;
				document.getElementById("jaminanPensiun").disabled = true;
				$('#prosentaseUpah').val(100);
			}
			reset();
			calculate();
		}
		function calculate() {
			var tipe = $('#tipeKontrak').val();
			var upahPokok = $('#upahPokok').val().replace(/\./g, '');
			let tunjanganJabatan = $('#tunjanganJabatan').val().replace(/\./g, '');
			let tunjanganSkill = $('#tunjanganSkill').val().replace(/\./g, '');
			let tunjanganPrestasiTahunan = $('#tunjanganPrestasiTahunan').val().replace(/\./g, '');
			var tunjanganDasar = tunjanganJabatan * 1 + tunjanganSkill * 1 + tunjanganPrestasiTahunan * 1;

			var umk = upahPokok * 100 / $('#prosentaseUpah').val();
			var salaryUmk = umk * 1 + tunjanganDasar;

			var salary = upahPokok * 1 + tunjanganDasar;
			$('#totalSalary').val(salary);
			var angka = $('#totalSalary').val();
			formatRupiahS(angka, 'totalSalary');

			var limitBpjs = '<?php echo $bpjs_kesehatan_l;?>';
			if (salaryUmk > limitBpjs) {
				var salaryBpjs = limitBpjs;
			} else if (salaryUmk > 0) {
				var salaryBpjs = salaryUmk;
			} else {
				var salaryBpjs = 0;
			}

			var upahDasar = '<?php echo $upah_dasar_a;?>';
			if (salary > 0 && salary < upahDasar) {
				salaryDasar = upahDasar;
			} else {
				salaryDasar = salary;
			}

			var limitPensiun = '<?php echo $jaminan_pensiun_l;?>';
			if (tipe == 'SAI' || tipe == 'TKA' || tipe == 'MANAGEMENT') {
				if (salaryUmk > limitPensiun) {
					var salaryPensiun = limitPensiun;
				} else {
					var salaryPensiun = salaryUmk;
				}
			} else {
				var salaryPensiun = 0;
			}

			if (tipe == 'TKA') {
				var salary_slpj = upahPokok * 1;
				var slpj = Math.round(15000000 / 173);
			} else {
				var salary_slpj = salary;
				var slpj = Math.round(salary_slpj / 173);
			}
			//var slpj=Math.round(salary/173);
			$('#slpj').val(slpj);
			var angka = $('#slpj').val();
			formatRupiahS(angka, 'slpj');

			if (tipe == 'SAI' || tipe == 'TKA' || tipe == 'MANAGEMENT') {
				var prosentaseBpjs = '<?php echo number_format($prosentase_bpjs_sai_p, 0);?>';
				var prosentaseBpjsPerusahaan = 5 - prosentaseBpjs;
				var prosentaseJht = '<?php echo number_format($jaminan_hari_tua_p, 0);?>';
				var prosentaseJp = '<?php echo number_format($jaminan_pensiun_p, 0);?>';
				var prosentaseJkk = '<?php echo number_format($jaminan_kecelakaan_kerja_p, 2);?>';
			} else if (tipe == 'MAGANG') {
				var prosentaseBpjs = '<?php echo number_format($prosentase_bpjs_magang_p, 0);?>';
				var prosentaseBpjsPerusahaan = 5 - prosentaseBpjs;
				var prosentaseJht = '<?php echo number_format($jaminan_hari_tua_perusahaan_p, 0);?>';
				//var prosentaseJht=0;
				var prosentaseJp = 0;
				var prosentaseJkk = '<?php echo number_format($jaminan_kecelakaan_kerja_dasar_p, 2);?>';
			} else {
				var prosentaseBpjs = 0;
				var prosentaseBpjsPerusahaan = 0;
				var prosentaseJht = 0;
				var prosentaseJp = 0;
				var prosentaseJkk = 0;
			}

			$('#prosentaseBpjs').val(prosentaseBpjs);
			var bpjsTambahan = $('#bpjsTambahan').val();
			var prosentaseBpjsShow = prosentaseBpjs * 1 + bpjsTambahan * 1;
			$('#bpjsKesehatanText').text('( ' + prosentaseBpjsShow + '%, ' + salaryBpjs + ' )');
			$('#kesehatanText').text('( ' + prosentaseBpjsPerusahaan + '%, ' + salaryBpjs + ' )');
			$('#jaminanHariTuaText').text('( ' + prosentaseJht + '%, ' + salaryDasar + ' )');
			$('#jaminanPensiunText').text('( ' + prosentaseJp + '%, ' + salaryPensiun + ' )');
			$('#jaminanKecelakaanKerjaText').text('( ' + prosentaseJkk + '%, ' + salaryDasar + ' )');

			var jumlahPeserta = prosentaseBpjs * 1 + bpjsTambahan * 1;
			var nominalBpjs = salaryBpjs * jumlahPeserta / 100;

			var bpjsKesehatan = Math.round(nominalBpjs);
			$('#bpjsKesehatan').val(bpjsKesehatan);
			var angka = $('#bpjsKesehatan').val();
			formatRupiahS(angka, 'bpjsKesehatan');

			var nominalJht = Math.round(salaryDasar * prosentaseJht / 100);
			$('#jaminanHariTua').val(nominalJht);
			var angka = $('#jaminanHariTua').val();
			formatRupiahS(angka, 'jaminanHariTua');

			if (tipe == 'TKA') {
				var nominalPensiun = 0;
			} else {
				var nominalPensiun = Math.round(salaryPensiun * prosentaseJp / 100);
			}
			$('#jaminanPensiun').val(nominalPensiun);
			var angka = $('#jaminanPensiun').val();
			formatRupiahS(angka, 'jaminanPensiun');

			var kesehatan = Math.round(salaryBpjs * prosentaseBpjsPerusahaan / 100);
			$('#kesehatan').val(kesehatan);
			var angka = $('#kesehatan').val();
			formatRupiahS(angka, 'kesehatan');

			var jaminanKecelakaanKerja = Math.round(salaryDasar * prosentaseJkk / 100);
			$('#jaminanKecelakaanKerja').val(jaminanKecelakaanKerja);
			var angka = $('#jaminanKecelakaanKerja').val();
			formatRupiahS(angka, 'jaminanKecelakaanKerja');

			if (tipe == 'SAI' || tipe == 'TKA' || tipe == 'MANAGEMENT') {
				var prosentaseJkm = '<?php echo number_format($jaminan_kematian_p, 2);?>';
				$('#jaminanKematianText').text('( ' + prosentaseJkm + '%, ' + salaryDasar + ' )');
				var jaminanKematian = Math.round(salaryDasar * prosentaseJkm / 100);
			} else if (tipe == 'MAGANG') {
				var jaminanKematian = '<?php echo number_format($jaminan_kematian_dasar_a, 0, ",", ".");?>';
				$('#jaminanKematianText').text('( ' + jaminanKematian + ' )');
			} else {
				var jaminanKematian = 0;
				$('#jaminanKematianText').text('( 0% )');
			}

			$('#jaminanKematian').val(jaminanKematian);
			var angka = $('#jaminanKematian').val();
			formatRupiahS(angka, 'jaminanKematian');
		}
		function calculateBPJS() {
			var tipe = $('#tipeKontrak').val();
			let bpjsSalary = $('#bpjsSalary').val().replace(/\./g, '');

			//var bpjs = bpjsSalary*100/$('#prosentaseUpah').val();
			var bpjs = bpjsSalary * 1;
			var limitBpjs = Math.round('<?php echo $bpjs_kesehatan_l;?>');

			if (bpjs > limitBpjs) {
				var salaryBpjs = limitBpjs;
			} else if (bpjs > 0) {
				var salaryBpjs = bpjs;
			} else {
				var salaryBpjs = 0;
			}

			if (tipe == 'SAI' || tipe == 'TKA' || tipe == 'MANAGEMENT') {
				var prosentaseBpjs = '<?php echo number_format($prosentase_bpjs_sai_p, 0);?>';
				var prosentaseBpjsPerusahaan = 5 - prosentaseBpjs;
			} else if (tipe == 'MAGANG') {
				var prosentaseBpjs = '<?php echo number_format($prosentase_bpjs_magang_p, 0);?>';
				var prosentaseBpjsPerusahaan = 5 - prosentaseBpjs;
			} else {
				var prosentaseBpjs = 0;
				var prosentaseBpjsPerusahaan = 0;
			}
			$('#prosentaseBpjs').val(prosentaseBpjs);

			var bpjsTambahan = $('#bpjsTambahan').val();
			var jumlahPeserta = prosentaseBpjs * 1 + bpjsTambahan * 1;
			var nominalBpjs = salaryBpjs * jumlahPeserta / 100;

			var bpjsKesehatan = Math.round(nominalBpjs);
			$('#bpjsKesehatan').val(bpjsKesehatan);
			var angka = $('#bpjsKesehatan').val();
			formatRupiahS(angka, 'bpjsKesehatan');

			var kesehatan = Math.round(salaryBpjs * prosentaseBpjsPerusahaan / 100);
			$('#kesehatan').val(kesehatan);
			var angka = $('#kesehatan').val();
			formatRupiahS(angka, 'kesehatan');

		}

		function formatRupiah(angka, prefix) {
			var number_string = angka.replace(/[^,\d]/g, '').toString(),
				split = number_string.split(','),
				sisa = split[0].length % 3,
				rupiah = split[0].substr(0, sisa),
				ribuan = split[0].substr(sisa).match(/\d{3}/gi);

			if (ribuan) {
				separator = sisa ? '.' : '';
				rupiah += separator + ribuan.join('.');
			}

			rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
			return prefix == undefined ? rupiah : (rupiah ? '' + rupiah : '');
		}
		function formatRupiahS(angka, pemilik) {
			var number_string = angka.replace(/[^,\d]/g, '').toString(),
				split = number_string.split(','),
				sisa = split[0].length % 3,
				rupiah = split[0].substr(0, sisa),
				ribuan = split[0].substr(sisa).match(/\d{3}/gi);

			if (ribuan) {
				separator = sisa ? '.' : '';
				rupiah += separator + ribuan.join('.');
			}

			rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
			$('#' + pemilik).val(rupiah);
		}
		function reset() {
			$('#upahPokok').val(0);
			$('#upahHarian').val(0);
			$('#tunjanganHarian').val(0);
			$('#tunjanganJabatan').val(0);
			$('#tunjanganSkill').val(0);
			$('#tunjanganPrestasiTahunan').val(0);
			$('#tunjanganMakan').val(1);
		}
	</script>
	<script type="text/javascript">
		$('.box-footer').on('click', '#simpan', function () {
			var idSalaryContract = $('#idSalaryContract').val();

			var idKaryawan = $('#idKaryawan').val();
			var tanggalKontrak = $('#tanggalKontrak').val();
			var tanggalExpired = $('#tanggalExpired').val();
			var tipeKontrak = $('#tipeKontrak').val();
			var upahPokok = $('#upahPokok').val();
			var prosentaseUpah = $('#prosentaseUpah').val();
			var upahHarian = $('#upahHarian').val();
			var tunjanganHarian = $('#tunjanganHarian').val();
			var tunjanganJabatan = $('#tunjanganJabatan').val();
			var tunjanganPrestasiTahunan = $('#tunjanganPrestasiTahunan').val();
			var tunjanganSkill = $('#tunjanganSkill').val();
			var tunjanganMakan = $('#tunjanganMakan').val();
			var tunjanganTransport = $('#tunjanganTransport').val();
			var workingDay = $('#workingDay').val();
			var tunjanganShift = $('#tunjanganShift').val();
			var totalSalary = $('#totalSalary').val();
			var bpjsSalary = $('#bpjsSalary').val();
			var slpj = $('#slpj').val();
			var jaminanHariTua = $('#jaminanHariTua').val();
			var jaminanPensiun = $('#jaminanPensiun').val();
			var bpjsTambahan = $('#bpjsTambahan').val();
			var bpjsKesehatan = $('#bpjsKesehatan').val();
			var prosentaseBpjs = $('#prosentaseBpjs').val();
			var serikat = $('#serikat').val();
			var koperasi = $('#koperasi').val();
			var statusPajak = $('#statusPajak').val();
			var statusBpjs = $('#statusbpjs').val();
			var jaminanKecelakaanKerja = $('#jaminanKecelakaanKerja').val();
			var jaminanKematian = $('#jaminanKematian').val();
			var kesehatan = $('#kesehatan').val();
			// var npwp=$('#nomornpwp').val();
			// var ktp=$('#nomorktp').val();
			var rekening = $('#nomorrekening').val();
			if (upahPokok == 0) {
				alert("Base Salary Can't be 0");
				return false;
			} else if (tipeKontrak == "") {
				alert("Please Choose <b>Status Karyawan</b>");
				return false;
			} else {
				$.ajaxSetup({
					type: "POST",
					url: "<?php echo e($site); ?>/Contract/Submit",
					cache: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});


				$.ajax({
					data: { id_salary_kontrak: idSalaryContract, id_employee: idKaryawan, tanggal_kontrak: tanggalKontrak, tanggal_expired: tanggalExpired, tipe_kontrak: tipeKontrak, upah_pokok: upahPokok, prosentase_upah: prosentaseUpah, upah_harian: upahHarian, tunjangan_harian: tunjanganHarian, tunjangan_jabatan: tunjanganJabatan, tunjangan_prestasi_tahunan: tunjanganPrestasiTahunan, tunjangan_skill: tunjanganSkill, tunjangan_makan: tunjanganMakan, tunjangan_transport: tunjanganTransport, tunjangan_shift: tunjanganShift, total_salary: totalSalary, slpj: slpj, jaminan_hari_tua: jaminanHariTua, jaminan_pensiun: jaminanPensiun, bpjs_tambahan: bpjsTambahan, bpjs_kesehatan: bpjsKesehatan, prosentase_bpjs: prosentaseBpjs, serikat: serikat, koperasi: koperasi, status_pajak: statusPajak, jaminan_kecelakaan_kerja: jaminanKecelakaanKerja, jaminan_kematian: jaminanKematian, kesehatan: kesehatan, bpjs_salary: bpjsSalary, rekening: rekening, working_day: workingDay, status_bpjs: statusBpjs },
					success: function (respond) {
						//alert(respond);
						location.reload();
					}
				})
			}
		});
	</script>
	<script>
		window.setTimeout(function () {
			$(".alert").fadeTo(500, 0).slideUp(500, function () {
				$(this).remove();
			});
		}, 5000);
	</script>
	<script type="text/javascript">
		// Delete Data
		$(document).on('click', '.delete-modal', function () {
			$('#delid').val($(this).data('delid'));
			$('#delname').text($(this).data('delname'));
			$('#aksesText').text($(this).data('akses'));
			$('#modal-delete').modal('show');
		});
		$('.modal-footer').on('click', '.delete', function () {
			var x = $('#delid').val();
			window.location.href = '<?php echo e($site); ?>/ContractDelete/' + x;
		});
	</script>
	<script type="text/javascript">
		// Delete Data
		$(document).on('click', '.new-modal', function () {
			$('#newid').val($(this).data('newid'));
			$('#newname').text($(this).data('newname'));
			$('#modal-new').modal('show');
		});
		$('.modal-footer').on('click', '.new', function () {
			var x = $('#newid').val();
			window.location.href = '<?php echo e($site); ?>/ContractNew/' + x;
		});
	</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/payroll', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\payroll\salary_contract.blade.php ENDPATH**/ ?>