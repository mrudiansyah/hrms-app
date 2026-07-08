@extends('layouts/admin')
@section('Contents')
   <!-- Contents -->
   	<style>
		#tablesx th {
		border-top: 1px solid #999;
		border-bottom: 1px solid #999;
		background-color: #2F4F4F;
		color: white;
		}	
        .table1 tr:hover {
		  cursor:pointer;
        }
		#tables th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#tables tbody tr:hover{
			cursor:pointer;
		}
		#table2 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table2 tbody tr:hover{
			cursor:pointer;
		}
		#table3 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table3 tbody tr:hover{
			cursor:pointer;
		}
		#table4 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table4 tbody tr:hover{
			cursor:default;
		}
    </style>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Dashboard
				<small>employee</small>
			</h1>
			<ol class="breadcrumb">
				<li>
				<a href="#">
					<i class="fa fa-calendar"></i> 
					<?php 
						date_default_timezone_set("Asia/Jakarta");
						echo date('l, d M Y H:i');
					?>
				</a>
				</li>
			</ol>
		</section>

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-user"></i>
						<h3 class="box-title">Summary <?php if($kategori=='Workingtime')echo "Service Time";else echo $kategori;?></h3>
						<div class="box-tools pull-right">
							<div class="form-group col-lg-6" style="padding:3px;">
								<input class="form-control" type="month" id="periode" value="{{$periode}}">
							</div>
							<div class="form-group col-lg-6" style="padding:3px;">
								<select class="form-control" name="kategori" id="kategori">
									<option value="{{$kategori}}"><?php if($kategori=='Workingtime')echo "Service Time";else echo $kategori;?></option>
									<option value="Position">Position</option>
									<option value="Contract">Contract</option>
									<option value="Age">Age</option>
									<option value="Gender">Gender</option>
									<option value="Service Time">Service Time</option>
									<option value="Religion">Religion</option>
									<option value="Education">Education</option>
									<option value="Marital Status">Marital Status</option>
									<option value="Address">Address</option>
								</select>
							</div>
						</div>
					</div>
					<div class="box-body">
						@if($kategori=='Department')
							<div style="padding:20px;overflow-x: scroll;">
								<table id="tables" class="table table-hover">
									<thead>
										<tr>
											<th>No</th>
											<th>ID</th>
											<th>DEPT_CODE</th>
											<th>DEPT_NAME</th>
											<th>DIVISION</th>
										</tr>
									</thead>
									<tbody>
										<?php $no=0;?>
										@foreach($data_table as $dt)
										<tr>
											<td><?php $no++;echo $no;?></td>
											<td>{{$dt->id}}</td>
											<td>{{$dt->dept_code}}</td>
											<td>{{$dt->dept_name}}</td>
											<td>{{$dt->divisi}}</td>
										</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						@endif
						@if($kategori=='Position')
							<div style="padding:20px;overflow-x: scroll;">
								<table id="tables" class="table table-hover">
									<thead>
										<tr>
											<th>No</th>
											<th>DEPT_CODE</th>
											<th>CATEGORY</th>
											<th>SAI+MAGANG</th>
											<th>MAGANG</th>
											<th>MAGANG_HUB</th>
											<th>SAI</th>
											<th>PRESDIR</th>
											<th>VICE_PD</th>
											<th>DIRECTOR</th>
											<th>GM</th>
											<th>AGM</th>
											<th>DEPT.HEAD</th>
											<th>AST.MAN</th>
											<th>SECT.HEAD</th>
											<th>SPECIALTIST</th>
											<th>LEADER</th>
											<th>OFFICER</th>
											<th>STAFF</th>
											<th>PELAKSANA</th>
											<th>DRIVER</th>
											<th>DPK</th>
										</tr>
									</thead>
									<tbody>
										<?php $no=0;?>
										@foreach($data_table as $dt)
										<tr>
											<td><?php $no++;echo $no;?></td>
											<td>{{$dt->dept_code}}</td>
											<td>{{$dt->dept_category}}</td>
											<td>{{$dt->jml_karyawan_all}}</td>
											<td>{{$dt->magang}}</td>
											<td>{{$dt->magang_hub}}</td>
											<td>{{$dt->jml_karyawan}}</td>
											<td>{{$dt->pd}}</td>
											<td>{{$dt->vp}}</td>
											<td>{{$dt->director}}</td>
											<td>{{$dt->gm}}</td>
											<td>{{$dt->agm}}</td>
											<td>{{$dt->dh}}</td>
											<td>{{$dt->astman}}</td>
											<td>{{$dt->sh}}</td>
											<td>{{$dt->specialist}}</td>
											<td>{{$dt->leader}}</td>
											<td>{{$dt->officer}}</td>
											<td>{{$dt->staff}}</td>
											<td>{{$dt->pelaksana}}</td>
											<td>{{$dt->driver}}</td>
											<td>{{$dt->DPK}}</td>
										</tr>
										@endforeach
									</tbody>
									<tfoot>
										@foreach($data_table1 as $dt)
										<tr>
											<th>&nbsp;</th>
											<th>TOTAL</th>
											<th>&nbsp;</th>
											<th><label class="label bg-blue">{{$dt->total_jml_karyawan_all}}</label></th>
											<th>{{$dt->total_magang}}</th>
											<th>{{$dt->total_magang_hub}}</th>
											<th style="background:#eba434;">{{$dt->total_jml_karyawan}}</th>
											<th><label class="label bg-yellow">{{$dt->total_pd}}</label></th>
											<th><label class="label bg-yellow">{{$dt->total_vp}}</label></th>
											<th><label class="label bg-yellow">{{$dt->total_director}}</label></th>
											<th><label class="label bg-yellow">{{$dt->total_gm}}</label></th>
											<th><label class="label bg-yellow">{{$dt->total_agm}}</label></th>
											<th><label class="label bg-yellow">{{$dt->total_dh}}</label></th>
											<th><label class="label bg-yellow">{{$dt->total_astman}}</label></th>
											<th><label class="label bg-yellow">{{$dt->total_sh}}</label></th>
											<th><label class="label bg-yellow">{{$dt->total_specialist}}</label></th>
											<th><label class="label bg-yellow">{{$dt->total_leader}}</label></th>
											<th><label class="label bg-yellow">{{$dt->total_officer}}</label></th>
											<th><label class="label bg-yellow">{{$dt->total_staff}}</label></th>
											<th><label class="label bg-yellow">{{$dt->total_pelaksana}}</label></th>
											<th><label class="label bg-yellow">{{$dt->total_driver}}</label></th>
											<th><label class="label bg-yellow">{{$dt->total_DPK}}</label></th>
										</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						@endif
						@if($kategori=='Contract')
							<div style="padding:20px;overflow-x: scroll;">
								<table id="tables" class="table table-hover">
									<thead>
										<tr>
											<th>No</th>
											<th>DEPT_CODE</th>
											<th>CATEGORY</th>
											<th>MAGANG</th>
											<th>MAGANG_HUB</th>
											<th>KONTRAK</th>
											<th>TOTAL PERMANEN</th>
											<th>PERMANEN PELAKSANA</th>
											<th>PERMANEN LEADER</th>
											<th>PERMANEN MANAGEMENT</th>
										</tr>
									</thead>
									<tbody>
										<?php $no=0;?>
										@foreach($data_table as $dt)
										<tr>
											<td><?php $no++;echo $no;?></td>
											<td>{{$dt->dept_code}}</td>
											<td>{{$dt->dept_category}}</td>
											<td>{{$dt->jml_magang}}</td>
											<td>{{$dt->jml_magang_hub}}</td>
											<td>{{$dt->jml_kontrak}}</td>
											<td>{{$dt->jml_permanen}}</td>
											<td>{{$dt->jml_pelaksana}}</td>
											<td>{{$dt->jml_leader}}</td>
											<td>{{$dt->jml_management}}</td>
										</tr>
										@endforeach
									</tbody>
									<tfoot>
										@foreach($data_table1 as $dt)
										<tr>
											<th>&nbsp;</th>
											<th>SUB TOTAL</th>
											<th>&nbsp;</th>
											<th>{{$dt->total_jml_magang}}</th>
											<th>{{$dt->total_jml_magang_hub}}</th>
											<th>{{$dt->total_jml_kontrak}}</th>
											<th style="background:#eba434;">{{$dt->total_jml_permanen}}</th>
											<th><label class="label bg-yellow">{{$dt->total_jml_pelaksana}}</label></th>
											<th><label class="label bg-yellow">{{$dt->total_jml_leader}}</label></th>
											<th><label class="label bg-yellow">{{$dt->total_jml_management}}</label></th>
										</tr>
										<tr>
											<th>&nbsp;</th>
											<th colspan="2">TOTAL (Magang & SAI)</th>
											<th colspan="4" style="background:#0FF;text-align:center;">
												<?php
													$all=$dt->total_jml_magang+$dt->total_jml_magang_hub+$dt->total_jml_kontrak+$dt->total_jml_permanen;
												?>
												<label class="label bg-blue">{{$all}}</label>
											</th>
											<th colspan="3">Not included Other Contract (SAB & SAI Corporate)
												<?php
													$permanen=$dt->total_jml_pelaksana+$dt->total_jml_leader+$dt->total_jml_management;
												?>
												<!-- {{$permanen}} -->
											</th>
										</tr>
										@endforeach
									</tfoor>
								</table>
							</div>
						@endif
						@if($kategori=='Age')
							<div style="padding:20px;overflow-x: scroll;">
								<table id="tables" class="table table-hover">
									<thead>
										<tr>
											<th>No</th>
											<th>DEPT_CODE</th>
											<th>CATEGORY</th>
											<th>< 19</th>
											<th>19 ~ 25 YEARS</th>
											<th>26 ~ 35 YEARS</th>
											<th>36 ~ 45 YEARS</th>
											<th>46 ~ 55 YEARS</th>
											<th>> 55 YEARS</th>
											<th>Undefined</th>
										</tr>
									</thead>
									<tbody>
										<?php $no=0;?>
										@foreach($data_table as $dt)
										<tr>
											<td><?php $no++;echo $no;?></td>
											<td>{{$dt->dept_code}}</td>
											<td>{{$dt->dept_category}}</td>
											<td>{{$dt->b18}}</td>
											<td>{{$dt->b25}}</td>
											<td>{{$dt->b35}}</td>
											<td>{{$dt->b45}}</td>
											<td>{{$dt->b55}}</td>
											<td>{{$dt->m55}}</td>
											<td>{{$dt->other}}</td>
										</tr>
										@endforeach
									</tbody>
									<tfoot>
										@foreach($data_table1 as $dt)
										<tr>
											<th>&nbsp;</th>
											<th>TOTAL</th>
											<th>
												<?php
													$all=$dt->total_b18+$dt->total_b25+$dt->total_b35+$dt->total_b45+$dt->total_b55+$dt->total_m55+$dt->total_other;
												?>
												<label class="label bg-blue">{{$all}}</label>
											</th>
											<th>{{$dt->total_b18}}</th>
											<th>{{$dt->total_b25}}</th>
											<th>{{$dt->total_b35}}</th>
											<th>{{$dt->total_b45}}</th>
											<th>{{$dt->total_b55}}</th>
											<th>{{$dt->total_m55}}</th>
											<th>{{$dt->total_other}}</th>
										</tr>
										@endforeach
									</tfoot>
								</table>
							</div>
						@endif
						@if($kategori=='Gender')
							<div style="padding:20px;overflow-x: scroll;">
								<table id="tables" class="table table-hover">
									<thead>
										<tr>
											<th>No</th>
											<th>DEPT_CODE</th>
											<th>CATEGORY</th>
											<th>PRIA</th>
											<th>PRIA_KARTAP</th>
											<th>PRIA_KONTRAK</th>
											<th>WANITA</th>
											<th>WANITA_KARTAP</th>
											<th>WANITA_KONTRAK</th>
										</tr>
									</thead>
									<tbody>
										<?php $no=0;?>
										@foreach($data_table as $dt)
										<?php 
											$pria=$dt->jml_permanen_pria+$dt->jml_kontrak_pria;
											$perempuan=$dt->jml_permanen_perempuan+$dt->jml_kontrak_perempuan;
										?>
										<tr>
											<td><?php $no++;echo $no;?></td>
											<td>{{$dt->dept_code}}</td>
											<td>{{$dt->dept_category}}</td>
											<td>{{$pria}}</td>
											<td>{{$dt->jml_permanen_pria}}</td>
											<td>{{$dt->jml_kontrak_pria}}</td>
											<td>{{$perempuan}}</td>
											<td>{{$dt->jml_permanen_perempuan}}</td>
											<td>{{$dt->jml_kontrak_perempuan}}</td>
										</tr>
										@endforeach
									</tbody>
									<tfoot>
										@foreach($data_table1 as $dt)
										<tr>
											<th>&nbsp;</th>
											<th>TOTAL</th>
											<th>
												<?php
													$prias=$dt->total_jml_permanen_pria+$dt->total_jml_kontrak_pria;
													$perempuans=$dt->total_jml_permanen_perempuan+$dt->total_jml_kontrak_perempuan;
													$all=$prias+$perempuans;
												?>
												<label class="label bg-blue">{{$all}}</label>
											</th>
											<th>{{$prias}}</th>
											<th>{{$dt->total_jml_permanen_pria}}</th>
											<th>{{$dt->total_jml_kontrak_pria}}</th>
											<th>{{$perempuans}}</th>
											<th>{{$dt->total_jml_permanen_perempuan}}</th>
											<th>{{$dt->total_jml_kontrak_perempuan}}</th>
										</tr>
										@endforeach
									</tfoot>
								</table>
							</div>
						@endif
						@if($kategori=='Service Time')
							<div style="padding:20px;overflow-x: scroll;">
								<table id="tables" class="table table-hover">
									<thead>
										<tr>
											<th>No</th>
											<th>DEPT_CODE</th>
											<th>CATEGORY</th>
											<th>< 1 YEARS</th>
											<th>01 ~ 02</th>
											<th>02 ~ 03</th>
											<th>03 ~ 04</th>
											<th>04 ~ 05</th>
											<th>05 ~ 06</th>
											<th>06 ~ 07</th>
											<th>07 ~ 08</th>
											<th>08 ~ 09</th>
											<th>09 ~ 10</th>
											<th>10 ~ 11</th>
											<th>11 ~ 12</th>
											<th>12 ~ 13</th>
											<th>13 ~ 14</th>
											<th>14 ~ 15</th>
											<th>15 ~ 16</th>
											<th>16 ~ 17</th>
											<th>17 ~ 18</th>
											<th>18 ~ 19</th>
											<th>19 ~ 20</th>
											<th>> 20 YEARS</th>
										</tr>
									</thead>
									<tbody>
										<?php $no=0;?>
										@foreach($data_table as $dt)
										<tr>
											<td><?php $no++;echo $no;?></td>
											<td>{{$dt->dept_code}}</td>
											<td>{{$dt->dept_category}}</td>
											<td>{{$dt->b1}}</td>
											<td>{{$dt->b2}}</td>
											<td>{{$dt->b3}}</td>
											<td>{{$dt->b4}}</td>
											<td>{{$dt->b5}}</td>
											<td>{{$dt->b6}}</td>
											<td>{{$dt->b7}}</td>
											<td>{{$dt->b8}}</td>
											<td>{{$dt->b9}}</td>
											<td>{{$dt->b10}}</td>
											<td>{{$dt->b11}}</td>
											<td>{{$dt->b12}}</td>
											<td>{{$dt->b13}}</td>
											<td>{{$dt->b14}}</td>
											<td>{{$dt->b15}}</td>
											<td>{{$dt->b16}}</td>
											<td>{{$dt->b17}}</td>
											<td>{{$dt->b18}}</td>
											<td>{{$dt->b19}}</td>
											<td>{{$dt->b20}}</td>
											<td>{{$dt->m20}}</td>
										</tr>
										@endforeach
									</tbody>
									<tfoot>
										@foreach($data_table1 as $dt)
										<tr>
											<th>&nbsp;</th>
											<th>TOTAL</th>
											<th>
												<?php
													$all=$dt->total_b1+$dt->total_b2+$dt->total_b3+$dt->total_b4+$dt->total_b5+$dt->total_b6+$dt->total_b7+$dt->total_b8+$dt->total_b9+$dt->total_b10+$dt->total_b11+$dt->total_b12+$dt->total_b13+$dt->total_b14+$dt->total_b15+$dt->total_b16+$dt->total_b17+$dt->total_b18+$dt->total_b19+$dt->total_b20+$dt->total_m20;
												?>
												<label class="label bg-blue">{{$all}}</label>
											</th>
											<th>{{$dt->total_b1}}</th>
											<th>{{$dt->total_b2}}</th>
											<th>{{$dt->total_b3}}</th>
											<th>{{$dt->total_b4}}</th>
											<th>{{$dt->total_b5}}</th>
											<th>{{$dt->total_b6}}</th>
											<th>{{$dt->total_b7}}</th>
											<th>{{$dt->total_b8}}</th>
											<th>{{$dt->total_b9}}</th>
											<th>{{$dt->total_b10}}</th>
											<th>{{$dt->total_b11}}</th>
											<th>{{$dt->total_b12}}</th>
											<th>{{$dt->total_b13}}</th>
											<th>{{$dt->total_b14}}</th>
											<th>{{$dt->total_b15}}</th>
											<th>{{$dt->total_b16}}</th>
											<th>{{$dt->total_b17}}</th>
											<th>{{$dt->total_b18}}</th>
											<th>{{$dt->total_b19}}</th>
											<th>{{$dt->total_b20}}</th>
											<th>{{$dt->total_m20}}</th>
										</tr>
										@endforeach
									</tfoot>
								</table>
							</div>
						@endif
						@if($kategori=='Religion')
							<div style="padding:20px;overflow-x: scroll;">
								<table id="tables" class="table table-hover">
									<thead>
										<tr>
											<th>No</th>
											<th>DEPT_CODE</th>
											<th>CATEGORY</th>
											<th>ISLAM</th>
											<th>PROTESTAN</th>
											<th>KATOLIK</th>
											<th>HINDU</th>
											<th>BUDHA</th>
											<th>KONGHUCU</th>
											<th>UNDEFINED</th>
										</tr>
									</thead>
									<tbody>
										<?php $no=0;?>
										@foreach($data_table as $dt)
										<tr>
											<td><?php $no++;echo $no;?></td>
											<td>{{$dt->dept_code}}</td>
											<td>{{$dt->dept_category}}</td>
											<td>{{$dt->islam}}</td>
											<td>{{$dt->protestan}}</td>
											<td>{{$dt->katolik}}</td>
											<td>{{$dt->hindu}}</td>
											<td>{{$dt->budha}}</td>
											<td>{{$dt->konghucu}}</td>
											<td>{{$dt->undefined}}</td>
										</tr>
										@endforeach
									</tbody>
									<tfoot>
										@foreach($data_table1 as $dt)
										<tr>
											<th>&nbsp;</th>
											<th>TOTAL</th>
											<th>
												<?php
													$all=$dt->total_islam+$dt->total_protestan+$dt->total_katolik+$dt->total_hindu+$dt->total_budha+$dt->total_konghucu+$dt->total_undefined;
												?>
												<label class="label bg-blue">{{$all}}</label>
											</th>
											<th>{{$dt->total_islam}}</th>
											<th>{{$dt->total_protestan}}</th>
											<th>{{$dt->total_katolik}}</th>
											<th>{{$dt->total_hindu}}</th>
											<th>{{$dt->total_budha}}</th>
											<th>{{$dt->total_konghucu}}</th>
											<th>{{$dt->total_undefined}}</th>
										</tr>
										@endforeach
									</tfoot>
								</table>
							</div>
						@endif
						@if($kategori=='Education')
							<div style="padding:20px;overflow-x: scroll;">
								<table id="tables" class="table table-hover">
									<thead>
										<tr>
											<th>No</th>
											<th>DEPT_CODE</th>
											<th>CATEGORY</th>
											<th>SD</th>
											<th>SLTP</th>
											<th>SLTA</th>
											<th>D1</th>
											<th>D2</th>
											<th>D3</th>
											<th>S1</th>
											<th>S2</th>
											<th>UNDEFINED</th>
										</tr>
									</thead>
									<tbody>
										<?php $no=0;?>
										@foreach($data_table as $dt)
										<tr>
											<td><?php $no++;echo $no;?></td>
											<td>{{$dt->dept_code}}</td>
											<td>{{$dt->dept_category}}</td>
											<td>{{$dt->SD}}</td>
											<td>{{$dt->SLTP}}</td>
											<td>{{$dt->SLTA}}</td>
											<td>{{$dt->D1}}</td>
											<td>{{$dt->D2}}</td>
											<td>{{$dt->D3}}</td>
											<td>{{$dt->S1}}</td>
											<td>{{$dt->S2}}</td>
											<td>{{$dt->blank}}</td>
										</tr>
										@endforeach
									</tbody>
									<tfoor>
										@foreach($data_table1 as $dt)
										<tr>
											<th>&nbsp;</th>
											<th>TOTAL</th>
											<th>
												<?php
													$all=$dt->total_SD+$dt->total_SLTP+$dt->total_SLTA+$dt->total_D1+$dt->total_D2+$dt->total_D3+$dt->total_S1+$dt->total_S2+$dt->total_blank;
												?>
												<label class="label bg-blue">{{$all}}</label>
											</th>
											<th>{{$dt->total_SD}}</th>
											<th>{{$dt->total_SLTP}}</th>
											<th>{{$dt->total_SLTA}}</th>
											<th>{{$dt->total_D1}}</th>
											<th>{{$dt->total_D2}}</th>
											<th>{{$dt->total_D3}}</th>
											<th>{{$dt->total_S1}}</th>
											<th>{{$dt->total_S2}}</th>
											<th>{{$dt->total_blank}}</th>
										</tr>
										@endforeach
									</tfoot>
								</table>
							</div>
						@endif
						@if($kategori=='Marital Status')
							<div style="padding:20px;overflow-x: scroll;">
								<table id="tables" class="table table-hover">
									<thead>
										<tr>
											<th>No</th>
											<th>DEPT_CODE</th>
											<th>CATEGORY</th>
											<th>TK</th>
											<th>TK1</th>
											<th>TK2</th>
											<th>K0</th>
											<th>K1</th>
											<th>K2</th>
											<th>K3</th>
											<th>BLANK</th>
										</tr>
									</thead>
									<tbody>
										<?php $no=0;?>
										@foreach($data_table as $dt)
										<tr>
											<td><?php $no++;echo $no;?></td>
											<td>{{$dt->dept_code}}</td>
											<td>{{$dt->dept_category}}</td>
											<td>{{$dt->TK}}</td>
											<td>{{$dt->TK1}}</td>
											<td>{{$dt->TK2}}</td>
											<td>{{$dt->K0}}</td>
											<td>{{$dt->K1}}</td>
											<td>{{$dt->K2}}</td>
											<td>{{$dt->K3}}</td>
											<td>{{$dt->blank}}</td>
										</tr>
										@endforeach
									</tbody>
									<tfoot>
										@foreach($data_table1 as $dt)
										<tr>
											<th>&nbsp;</th>
											<th>TOTAL</th>
											<th>
												<?php
													$all=$dt->total_TK+$dt->total_TK1+$dt->total_TK2+$dt->total_K0+$dt->total_K1+$dt->total_K2+$dt->total_K3+$dt->total_blank;
												?>
												<label class="label bg-blue">{{$all}}</label>
											</th>
											<th>{{$dt->total_TK}}</th>
											<th>{{$dt->total_TK1}}</th>
											<th>{{$dt->total_TK2}}</th>
											<th>{{$dt->total_K0}}</th>
											<th>{{$dt->total_K1}}</th>
											<th>{{$dt->total_K2}}</th>
											<th>{{$dt->total_K3}}</th>
											<th>{{$dt->total_blank}}</th>
										</tr>
										@endforeach
									</tfoot>
								</table>
							</div>
						@endif
						@if($kategori=='Address')
							<div style="padding:20px;overflow-x: scroll;">
								<table id="tables" class="table table-hover">
									<thead>
										<tr>
											<th>No</th>
											<th>DEPT_CODE</th>
											<th>CATEGORY</th>
											<th>KARAWANG</th>
											<th>LUAR KARAWANG</th>
											<th>JUMLAH</th>
											<th>PERSENTASE</th>
										</tr>
									</thead>
									<tbody>
										<?php $no=0;?>
										@foreach($data_table as $dt)
										<tr>
											<td><?php $no++;echo $no;?></td>
											<td>{{$dt->dept_code}}</td>
											<td>{{$dt->dept_category}}</td>
											<td>{{$dt->karawang}}</td>
											<td>{{$dt->luar_karawang}}</td>
											<td>
												<?php $all=$dt->karawang+$dt->luar_karawang;echo $all;?>
											</td>
											<td>
												<?php 
													if($all==0)$persentase=0;
													else $persentase=$dt->karawang*100/$all;
													echo number_format($persentase,2)."%";
													?>
											</td>
										</tr>
										@endforeach
									</tbody>
									<tfoot>
										@foreach($data_table1 as $dt)
										<tr>
											<th>&nbsp;</th>
											<th>TOTAL</th>
											<th>&nbsp;</th>
											<th>{{$dt->jml_karawang}}</th>
											<th>{{$dt->jml_luar_karawang}}</th>
											<th>
												<?php $all=$dt->jml_karawang+$dt->jml_luar_karawang;echo $all;?>
											</th>
											<th>
												<?php 
													if($all==0)$persentase=0;
													else $persentase=$dt->jml_karawang*100/$all;
													echo number_format($persentase,2)."%";
													?>
											</th>
										</tr>
										@endforeach
									</tfoot>
								</table>
							</div>
						@endif
					</div>
					<!-- /.box-body -->
				</div>
			</div>
		</div>
		<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
    <!-- /.Content -->

	<div class="modal fade" id="modal-delete">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Delete Confirmation</h4>
				</div>
				<div class="modal-body">
					Click Yes to Delete : <b id="delname1"></b> ?
					<input type="hidden" id="delid1">
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


@endsection
@section('Scripts')
	<!-- page script Tabel-->
	<script>
		$(function () {
			$('#table2').DataTable({
			'paging'      : true,
			'lengthChange': true,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 10,
			'autoWidth'   : false,
			})
		})
		$(function () {
			$('#table3').DataTable({
			'paging'      : true,
			'lengthChange': true,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 10,
			'autoWidth'   : false,
			})
		})
	</script>
	<script>
		$(document).ready(function() {
			var table = $('#tables').DataTable({
				'paging'      : false,
				'lengthChange': true,
				'searching'   : true,
				'ordering'    : true,
				'info'        : true,
				"pageLength"  : 30,
				'autoWidth'   : false,
				"pagingType": "full",
				"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]]
        //"iDisplayLength": 50
				//dom: 'Bfrtip',buttons: ['print']
			});
		
			new $.fn.dataTable.Buttons( table, {
				buttons: ['copy', 'excel', 'print']
			} );
		
			table.buttons( 0, null ).container().prependTo(
				table.table().container()
			);
		} );


	</script>
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<script>
		$('body').on("change","#kategori",function(){
			var kategori=document.getElementById('kategori').value;
			var periode=document.getElementById('periode').value;
			window.location.href="/Admin/Department/"+kategori+"/"+periode;
		});
		$('body').on("change","#periode",function(){
			var kategori=document.getElementById('kategori').value;
			var periode=document.getElementById('periode').value;
			window.location.href="/Admin/Department/"+kategori+"/"+periode;
		});
	</script>
@endsection
