@extends('layouts/payroll_recap')
@section('Contents')
	<?php 
		$awal=date('Y-m-d',strtotime('-31 days',strtotime($periode.'-01')));
		$text_awal=' 25 '.date('F',strtotime($awal));
		$text_akhir=' - 24 '.date('F Y',strtotime($periode.'-01'));
		$text_periode='PERIODE'.$text_awal.$text_akhir;
		$periode_text=strtoupper(date('F Y',strtotime($periode.'-01')));

	?>

	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<div class="pull-left">
							<input type="month" id="periode" class="form-control" value="{{$periode}}">	
						</div>
						<div class="pull-right">
                            <a href="{{$site}}/RecapPDF/{{$periode}}/{{$tipe}}" class="btn btn-primary btn-md" target="_blank"><i class="fa fa-print"></i> &nbsp;Preview</a>
						</div>
					</div>
					<div class="box-body">
						@php
							$categories = ['PKL_Siswa', 'PKL_Mahasiswa'];
							$grand_total_all = 0;
						@endphp

						@foreach($categories as $category)
							@php
								$filtered_data = $tb_salary_summary->where('kategori', $category);
								$sub_total = 0;
								$no = 0;
							@endphp
							
							@if($filtered_data->count() > 0)
								<h4>{{ str_replace('_', ' ', $category) }}</h4>
								<table class="table table-bordered table-striped" style="margin-bottom: 30px;">
									<thead>
										<tr>
											<th width="40">NO</th>
											<th>EMPLOYEE_NAME</th>
											<th>DAYS</th>
											<th>SALARY/DAY</th>
											<th>RAPEL</th>
											<th>TOTAL_AMOUNT</th>
										</tr>
									</thead>
									<tbody>
										@foreach($filtered_data as $dt)
											<tr>
												<td>{{ ++$no }}</td>
												<td>{{ $dt->nama ?? '' }}</td>
												<td>{{ $dt->days ?? '0' }}</td>
												<td style="text-align:right;">{{ isset($dt->salary_perday) ? number_format($dt->salary_perday, 0) : '0' }}</td>
												<td style="text-align:right;">{{ isset($dt->rapel) ? number_format($dt->rapel, 0) : '0' }}</td>
												<td style="text-align:right;">{{ isset($dt->total_amount) ? number_format($dt->total_amount, 0) : '0' }}</td>
											</tr>
											@php $sub_total += ($dt->total_amount ?? 0); @endphp
										@endforeach
									</tbody>
									<tfoot>
										<tr style="background: #f9f9f9; font-weight: bold;">
											<th colspan="5" style="text-align:right;">SUB TOTAL {{ str_replace('_', ' ', $category) }}</th>
											<th style="text-align:right;">{{ number_format($sub_total, 0) }}</th>
										</tr>
									</tfoot>
								</table>
								@php $grand_total_all += $sub_total; @endphp
							@endif
						@endforeach

						<div class="row">
							<div class="col-md-12">
								<div class="well" style="text-align:right; font-weight:bold; font-size:1.2em; background: #eee;">
									GRAND TOTAL: {{ number_format($grand_total_all, 0) }}
								</div>
							</div>
						</div>
                        @foreach($tb_approval as $dt2)
                            <table border="1" cellspacing="0" cellpadding="5" width="1200" align="center">
                                <tr>
                                    <td style="text-align:center;">Prepared by</td>
                                    <td style="text-align:center;">Checked by</td>
                                    <td colspan="4" style="text-align:center;">Approved by</td>
                                    <td style="text-align:center;">Verified by</td>
                                </tr>
                                <tr>
                                    <td style="text-align:center;vertical-align:middle;height:100px;" class="relative">
                                        @if($dt2->id_approver_1==$id_employee&&$dt2->status_1==0)
                                            <button class="btn btn-success btn-md simpan" data-periode="{{$dt2->periode}}" data-category="{{$dt2->category}}" data-tipe="{{$dt2->tipe}}" data-kolom="1" data-status="1"><i class="fa fa-check"></i></button>
                                            <button class="btn btn-danger btn-md simpan" data-periode="{{$dt2->periode}}" data-category="{{$dt2->category}}" data-tipe="{{$dt2->tipe}}" data-kolom="1" data-status="2"><i class="fa fa-times"></i></button>
                                        @elseif($dt2->status_1==1)
                                            <img src='/EMS/public/approval/confirm.png' style='width:100px;height:40px;'>
                                            <br>{{$dt2->date_1}}
                                        @elseif($dt2->status_1==2)
                                            <img src='/EMS/public/approval/rejected.png' style='width:100px;height:50px;'>
                                            <br>{{$dt2->date_1}}
                                        @else
                                            &nbsp;
                                        @endif
                                    </td>
                                    <td style="text-align:center;vertical-align:middle;">
                                        @if($dt2->id_approver_2==$id_employee&&$dt2->status_2==0&&$dt2->status_1==1)
                                            <button class="btn btn-success btn-md simpan" data-periode="{{$dt2->periode}}" data-category="{{$dt2->category}}" data-tipe="{{$dt2->tipe}}" data-kolom="2" data-status="1"><i class="fa fa-check"></i></button>
                                            <button class="btn btn-danger btn-md simpan" data-periode="{{$dt2->periode}}" data-category="{{$dt2->category}}" data-tipe="{{$dt2->tipe}}" data-kolom="2" data-status="2"><i class="fa fa-times"></i></button>
                                        @elseif($dt2->status_2==1)
                                            <img src='/EMS/public/approval/confirm.png' style='width:100px;height:40px;'>
                                            <br>{{$dt2->date_2}}
                                        @elseif($dt2->status_2==2)
                                            <img src='/EMS/public/approval/rejected.png' style='width:100px;height:50px;'>
                                            <br>{{$dt2->date_2}}
                                        @else
                                            &nbsp;
                                        @endif
                                    </td>
                                    <td style="text-align:center;vertical-align:middle;">
                                        @if($dt2->id_approver_3==$id_employee&&$dt2->status_3==0&&$dt2->status_2==1)
                                            <button class="btn btn-success btn-md simpan" data-periode="{{$dt2->periode}}" data-category="{{$dt2->category}}" data-tipe="{{$dt2->tipe}}" data-kolom="3" data-status="1"><i class="fa fa-check"></i></button>
                                            <button class="btn btn-danger btn-md simpan" data-periode="{{$dt2->periode}}" data-category="{{$dt2->category}}" data-tipe="{{$dt2->tipe}}" data-kolom="3" data-status="2"><i class="fa fa-times"></i></button>
                                        @elseif($dt2->status_3==1)
                                            <img src='/EMS/public/approval/confirm.png' style='width:100px;height:40px;'>
                                            <br>{{$dt2->date_3}}
                                        @elseif($dt2->status_3==2)
                                            <img src='/EMS/public/approval/rejected.png' style='width:100px;height:50px;'>
                                            <br>{{$dt2->date_3}}
                                        @else
                                            &nbsp;
                                        @endif
                                    </td>
                                    <td style="text-align:center;vertical-align:middle;">
                                        @if($dt2->id_approver_4==$id_employee&&$dt2->status_4==0&&$dt2->status_3==1)
                                            <button class="btn btn-success btn-md simpan" data-periode="{{$dt2->periode}}" data-category="{{$dt2->category}}" data-tipe="{{$dt2->tipe}}" data-kolom="4" data-status="1"><i class="fa fa-check"></i></button>
                                            <button class="btn btn-danger btn-md simpan" data-periode="{{$dt2->periode}}" data-category="{{$dt2->category}}" data-tipe="{{$dt2->tipe}}" data-kolom="4" data-status="2"><i class="fa fa-times"></i></button>
                                        @elseif($dt2->status_4==1)
                                            <img src='/EMS/public/approval/confirm.png' style='width:100px;height:40px;'>
                                            <br>{{$dt2->date_4}}
                                        @elseif($dt2->status_4==2)
                                            <img src='/EMS/public/approval/rejected.png' style='width:100px;height:50px;'>
                                            <br>{{$dt2->date_4}}
                                        @else
                                            &nbsp;
                                        @endif
                                    </td>
                                    <td style="text-align:center;vertical-align:middle;">
                                        @if($dt2->id_approver_5==$id_employee&&$dt2->status_5==0&&$dt2->status_4==1)
                                            <button class="btn btn-success btn-md simpan" data-periode="{{$dt2->periode}}" data-category="{{$dt2->category}}" data-tipe="{{$dt2->tipe}}" data-kolom="5" data-status="1"><i class="fa fa-check"></i></button>
                                            <button class="btn btn-danger btn-md simpan" data-periode="{{$dt2->periode}}" data-category="{{$dt2->category}}" data-tipe="{{$dt2->tipe}}" data-kolom="5" data-status="2"><i class="fa fa-times"></i></button>
                                        @elseif($dt2->status_5==1)
                                            <img src='/EMS/public/approval/confirm.png' style='width:100px;height:40px;'>
                                            <br>{{$dt2->date_5}}
                                        @elseif($dt2->status_5==2)
                                            <img src='/EMS/public/approval/rejected.png' style='width:100px;height:50px;'>
                                            <br>{{$dt2->date_5}}
                                        @else
                                            &nbsp;
                                        @endif
                                    </td>
                                    <td style="text-align:center;vertical-align:middle;">
                                        @if($dt2->id_approver_6==$id_employee&&$dt2->status_6==0&&$dt2->status_5==1)
                                            <button class="btn btn-success btn-md simpan" data-periode="{{$dt2->periode}}" data-category="{{$dt2->category}}" data-tipe="{{$dt2->tipe}}" data-kolom="4" data-status="1"><i class="fa fa-check"></i></button>
                                            <button class="btn btn-danger btn-md simpan" data-periode="{{$dt2->periode}}" data-category="{{$dt2->category}}" data-tipe="{{$dt2->tipe}}" data-kolom="4" data-status="2"><i class="fa fa-times"></i></button>
                                        @elseif($dt2->status_6==1)
                                            <img src='/EMS/public/approval/confirm.png' style='width:100px;height:40px;'>
                                            <br>{{$dt2->date_6}}
                                        @elseif($dt2->status_6==2)
                                            <img src='/EMS/public/approval/rejected.png' style='width:100px;height:50px;'>
                                            <br>{{$dt2->date_6}}
                                        @else
                                            &nbsp;
                                        @endif
                                    </td>
                                    <td style="text-align:center;vertical-align:middle;">
                                        @if($dt2->id_approver_7==$id_employee&&$dt2->status_7==0&&$dt2->status_6==1)
                                            <button class="btn btn-success btn-md simpan" data-periode="{{$dt2->periode}}" data-category="{{$dt2->category}}" data-tipe="{{$dt2->tipe}}" data-kolom="4" data-status="1"><i class="fa fa-check"></i></button>
                                            <button class="btn btn-danger btn-md simpan" data-periode="{{$dt2->periode}}" data-category="{{$dt2->category}}" data-tipe="{{$dt2->tipe}}" data-kolom="4" data-status="2"><i class="fa fa-times"></i></button>
                                        @elseif($dt2->status_7==1)
                                            <img src='/EMS/public/approval/confirm.png' style='width:100px;height:40px;'>
                                            <br>{{$dt2->date_7}}
                                        @elseif($dt2->status_7==2)
                                            <img src='/EMS/public/approval/rejected.png' style='width:100px;height:50px;'>
                                            <br>{{$dt2->date_7}}
                                        @else
                                            &nbsp;
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width:180px;text-align:center;vertical-align:bottom;">{{$dt2->name_1}}</th>
                                    <th style="width:180px;text-align:center;vertical-align:bottom;">{{$dt2->name_2}}</th>
                                    <th style="width:180px;text-align:center;vertical-align:bottom;">{{$dt2->name_3}}</th>
                                    <th style="width:180px;text-align:center;vertical-align:bottom;">{{$dt2->name_4}}</th>
                                    <th style="width:180px;text-align:center;vertical-align:bottom;">{{$dt2->name_5}}</th>
                                    <th style="width:180px;text-align:center;vertical-align:bottom;">{{$dt2->name_6}}</th>
                                    <th style="width:180px;text-align:center;vertical-align:bottom;">{{$dt2->name_7}}</th>
                                </tr>
                                <tr>
                                    <td style="text-align:center;">{{$dt2->pos_1}}</td>
                                    <td style="text-align:center;">{{$dt2->pos_2}}</td>
                                    <td style="text-align:center;">{{$dt2->pos_3}}</td>
                                    <td style="text-align:center;">{{$dt2->pos_4}}</td>
                                    <td style="text-align:center;">{{$dt2->pos_5}}</td>
                                    <td style="text-align:center;">{{$dt2->pos_6}}</td>
                                    <td style="text-align:center;">{{$dt2->pos_7}}</td>
                                </tr>
                            </table>
                        @endforeach
                        
					</div>
					<!-- /.box-body -->


                </div>

			</div>
		</div>
	</section>
@endsection
@section('Modals')

@endsection
@section('Scripts')
<script>
    $('body').on("change","#periode",function(){
        //var department=document.getElementById('idDepartment').value;
        var periode=document.getElementById('periode').value;
        if(periode=='') var periode=0;
        var tipe="<?php echo $tipe;?>";
        window.location.href="{{$site}}/Recap/"+periode+"/"+tipe;
    });
    $(document).on('click', '.simpan', function() {
			var periode=$(this).data('periode');
			var category=$(this).data('category');
			var tipe=$(this).data('tipe');
            var kolom=$(this).data('kolom');
			var status=$(this).data('status');

			$.ajaxSetup({
				type:"POST",
				url: "{{$site}}/RecapSave",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data:{periode:periode,category:category,tipe:tipe,kolom:kolom,status:status},
				success: function(respond){
					location.reload();
					// if(respond=='Sukses'){
					// 	location.reload();
					// }else{
					// 	alert(respond);
					// }
				}
			})
		});

</script>
@endsection
