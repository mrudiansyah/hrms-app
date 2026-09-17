<style>
	html{
		margin:20px;
	}
	.tabelot th{
		font-family: Arial, Helvetica, sans-serif;
		font-weight:bold;
		text-align:center;
		font-weight:normal;
		font-size:16px;
		padding:5px;
		border:1px solid #000;
		
	}
	.tabelot td{
		font-family: Arial, Helvetica, sans-serif;
		font-size:14px;
	}
	.tabeldt td{
		padding:6px;
	}
	.tabelsign td{
		text-align:center;
		font-weight:bold;
		width:20%;
	}
	.tabelsign th{
		font-size:12px;
		text-align:center;
	}
</style>
@foreach($tb_assigment as $dt)
	<table style="width:100%;" cellspacing="3" cellpadding="0" class="tabelot">
		<tr><th colspan="2" style="background:#000;color:#FFF;padding:10px 5px;border:1px solid #000;">Form Of Assigment Letter For Working On The Day Off / Holiday</th></tr>
		<tr>
			<th style="text-align:center;font-weight:bold;font-size:16px;width:50%;">Taskmaster Data</th>
			<th style="text-align:center;font-weight:bold;font-size:16px;">Employee Data</th>
		</tr>
		<tr>
			<td>
				@foreach($tb_atasan as $dt2)
				<table style="width:100%" cellspacing="0" border="1" class="tabeldt">
					<tr>
						<td style="width:100px;padding:15px 0px 15px 10px;">Name<br>Department<br>Job Title</td>
						<td style="width:3px;">:<br>:<br>:</td>
						<td>{{$dt2->employee_name}}<br>{{$dt2->dept_name}}<br>{{$dt2->position_name}}</td>
					</tr>
				</table>
				@endforeach
			</td>
			<td>
				<table style="width:100%" cellspacing="0" border="1" class="tabeldt">
					<tr>
						<td style="width:100px;padding:15px 0px 15px 10px;">Name<br>Department<br>Job Title</td>
						<td style="width:3px;">:<br>:<br>:</td>
						<td>{{$dt->employee_name}}<br>{{$dt->dept_name}}<br>{{$dt->position_name}}</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<th style="text-align:center;font-size:16px;background:#000;color:#FFF;border:1px solid #000;padding: 3px;">Plan of Working Hour</th>
			<th style="text-align:center;font-size:16px;background:#000;color:#FFF;border:1px solid #000;padding: 3px;">Actual of Working Hour</th>
		</tr>
		<tr>
			<td>
				@foreach($tb_atasan as $dt2)
				<table style="width:100%" cellspacing="0" border="1" class="tabeldt">
					<tr>
					<td style="width:100px;padding:15px 0px 15px 10px;">Date<br>Starting Hour<br>End Hour</td>
						<td style="width:3px;">:<br>:<br>:</td>
						<td><?php echo date('d F Y',strtotime($dt->start_plan))."<br>".date('H:i',strtotime($dt->start_plan))."<br>".date('H:i',strtotime($dt->finish_plan));?></td>
					</tr>
				</table>
				@endforeach
			</td>
			<td>
				<table style="width:100%" cellspacing="0" border="1" class="tabeldt">
					<tr>
						<td style="width:100px;padding:15px 0px 15px 10px;">Date<br>Starting Hour<br>End Hour</td>
						<td style="width:3px;">:<br>:<br>:</td>
						<td><?php if($dt->hours_act>0)echo date('d F Y',strtotime($dt->start_act))."<br>".date('H:i',strtotime($dt->start_act))."<br>".date('H:i',strtotime($dt->finish_act));?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="padding:5px;border:1px solid #000;height:80px;vertical-align:top;">
				<u>Please describe the jobs that should be done:</u>
				<p style="text-align:center;">{{$dt->jobs}}</p>
			</td>
		</tr>
		<tr><th colspan="2" style="background:#000;color:#FFF;padding:3px 5px;border:1px solid #000;">Legalization</th></tr>
		<tr>
			<td colspan="2">
				<table style="width:100%" cellspacing="0" border="1" class="tabelsign">
					<tr>
						<td>Assigned by</td>
						<td>Approved 1 by</td>
						<td>Approved 2 by</td>
						<td>Conducted by</td>
						<td>Legalized by</td>
					</tr>
					<tr>
						<td style="height:60px;">
							<?php 
								if($dt->assigned_status=='1'){
									$ttd_app=base_path().'/public/approval/'.$dt->assigned.'.png';
									$confirm=base_path().'/public/approval/confirm.png';
									$reject=base_path().'/public/approval/rejected.png';
									$filename = 'c:/xampp/htdocs/public/approval/'.$dt->assigned.'.png';
									if (file_exists($filename)){?>
										<img src='{{ $ttd_app }}' style='width:100px;height:40px;'>
									<?php }else{?>
										<img src='{{ $confirm }}' style='width:100px;height:40px;'>
									<?php }
								}elseif($dt->assigned_status=='3'){?>
									<img src='{{ $reject }}' style='width:100px;height:40px;'>
								<?php }
							?>
						</td>
						<td>
							<?php 
								if($dt->approved1_status=='1'){
									$ttd_app=base_path().'/public/approval/'.$dt->approved1.'.png';
									$confirm=base_path().'/public/approval/confirm.png';
									$reject=base_path().'/public/approval/rejected.png';
									$filename = 'c:/xampp/htdocs/public/approval/'.$dt->approved1.'.png';
									if (file_exists($filename)){?>
										<img src='{{ $ttd_app }}' style='width:100px;height:40px;'>
									<?php }else{?>
										<img src='{{ $confirm }}' style='width:100px;height:40px;'>
									<?php }
								}elseif($dt->approved1_status=='3'){?>
									<img src='{{ $reject }}' style='width:100px;height:40px;'>
								<?php }
							?>
						</td>
						<td>
							<?php 
								if($dt->approved2_status=='1'){
									$ttd_app=base_path().'/public/approval/'.$dt->approved2.'.png';
									$confirm=base_path().'/public/approval/confirm.png';
									$reject=base_path().'/public/approval/rejected.png';
									$filename = 'c:/xampp/htdocs/public/approval/'.$dt->approved2.'.png';
									if (file_exists($filename)){?>
										<img src='{{ $ttd_app }}' style='width:100px;height:40px;'>
									<?php }else{?>
										<img src='{{ $confirm }}' style='width:100px;height:40px;'>
									<?php }
								}elseif($dt->approved2_status=='3'){?>
									<img src='{{ $reject }}' style='width:100px;height:40px;'>
								<?php }
							?>
						</td>
						<td>
							<?php 
								if($dt->conducted_status=='1'){
									$ttd_app=base_path().'/public/approval/'.$dt->conducted.'.png';
									$confirm=base_path().'/public/approval/confirm.png';
									$reject=base_path().'/public/approval/rejected.png';
									$filename = 'c:/xampp/htdocs/public/approval/'.$dt->conducted.'.png';
									if (file_exists($filename)){?>
										<img src='{{ $ttd_app }}' style='width:100px;height:40px;'>
									<?php }else{?>
										<img src='{{ $confirm }}' style='width:100px;height:40px;'>
									<?php }
								}elseif($dt->conducted_status=='3'){?>
									<img src='{{ $reject }}' style='width:100px;height:40px;'>
								<?php }
							?>
						</td>
						<td>
							<?php 
								if($dt->legalized_status=='1'){
									$ttd_app=base_path().'/public/approval/'.$dt->legalized.'.png';
									$confirm=base_path().'/public/approval/confirm.png';
									$reject=base_path().'/public/approval/rejected.png';
									$filename = 'c:/xampp/htdocs/public/approval/'.$dt->legalized.'.png';
									if (file_exists($filename)){?>
										<img src='{{ $ttd_app }}' style='width:100px;height:40px;'>
									<?php }else{?>
										<img src='{{ $confirm }}' style='width:100px;height:40px;'>
									<?php }
								}elseif($dt->legalized_status=='3'){?>
									<img src='{{ $reject }}' style='width:100px;height:40px;'>
								<?php }
							?>
						</td>
					</tr>
					<tr>
						<th>{{$dt->assigned_name}}</th>
						<th>{{$dt->approved1_name}}</th>
						<th>{{$dt->approved2_name}}</th>
						<th>{{$dt->employee_name}}</th>
						<th>{{$dt->legalized_name}}</th>
					</tr>
					<tr>
						<th>@if($dt->assigned_status_date!=''){{date('d-m-Y H:i',strtotime($dt->assigned_status_date))}}@endif</th>
						<th>@if($dt->approved1_status_date!=''){{date('d-m-Y H:i',strtotime($dt->approved1_status_date))}}@endif</th>
						<th>@if($dt->approved2_status_date!=''){{date('d-m-Y H:i',strtotime($dt->approved2_status_date))}}@endif</th>
						<th>@if($dt->conducted_status_date!=''){{date('d-m-Y H:i',strtotime($dt->conducted_status_date))}}@endif</th>
						<th>@if($dt->legalized_status_date!=''){{date('d-m-Y H:i',strtotime($dt->legalized_status_date))}}@endif</th>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td style="text-align:center;" colspan="2">
				<div style="font-size:14px;padding:5px;border:0px solid #000;">
					Note: "Assignment ini dibuat berdasarkan kesepakatan dan persetujuan kedua belah pihak"
				</div>
			</td>
		</tr>
	</table>
@endforeach

