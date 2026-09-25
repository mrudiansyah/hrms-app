@extends('layouts/admin')
@section('Contents')

<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Overtime Request
				<small>&nbsp;</small>
			</h1>
		</section>

		<!-- Main content -->
		<section class="content">
			<div class="row">
				<div class="col-xs-12 col-md-6 col-lg-8">
                    <form action="{{ route('memos.update', $memo->id) }}" method="POST">
                        <div class="box box-primary" style="background:#FFF;">
                            <div class="box-header">
                                <i class="fa fa-edit"></i>
                                <h3 class="box-title">Form Entry Request</h3>
                                <div class="box-tools pull-right">
                                    &nbsp;
                                </div>
                            </div>
                            <div class="box-body">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-xs-12 col-md-6 col-lg-4">
                                        <input type="text" class="form-control" id="id_memo" name="id_memo" value="{{$id_memo}}" required>
                                        <input type="hidden" class="form-control" id="statua" name="status" value="1" required>
                                        <div class="form-group">
                                            <label for="part_no">Part No</label>
                                            <input type="text" class="form-control" id="part_no" name="part_no" maxlength="30" value="{{ $memo->part_no }}">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="part_name">Part Name</label>
                                            <input type="text" class="form-control" id="part_name" name="part_name" maxlength="50" value="{{ $memo->part_name }}">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="lines">Lines</label>
                                            <input type="text" class="form-control" id="lines" name="lines" maxlength="50" value="{{ $memo->lines }}">
                                        </div>
        
                                        <div class="form-group">
                                            <label for="jph_gsph">JPH/GSPH</label>
                                            <input type="number" class="form-control" id="jph_gsph" name="jph_gsph" value="{{ $memo->jph_gsph }}">
                                        </div>
                                        
                                    </div>
                                    <div class="col-xs-12 col-md-6 col-lg-4">
                                        <div class="form-group">
                                            <label for="process">Process</label>
                                            <input type="text" class="form-control" id="process" name="process" maxlength="50" value="{{ $memo->process }}">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="date_ot">Date OT</label>
                                            <input type="date" class="form-control" id="date_ot" name="date_ot" value="{{ $memo->date_ot }}">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="start_ot">Start OT</label>
                                            <input type="time" class="form-control" id="start_ot" name="start_ot" value="{{ $memo->start_ot }}">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="finish_ot">Finish OT</label>
                                            <input type="time" class="form-control" id="finish_ot" name="finish_ot" value="{{ $memo->finish_ot }}">
                                        </div>
                                        
                                    </div>
                                    <div class="col-xs-12 col-md-6 col-lg-4">
                                        <div class="form-group">
                                            <label for="plan_qty">Plan Quantity</label>
                                            <input type="number" class="form-control" id="plan_qty" name="plan_qty" value="{{ $memo->plan_qty }}">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Reason Category</label>
                                            <select name="reason_ot" class="form-control selectpicker" data-live-search="true" id="reason_ot">
                                                <option value="{{$memo->reason_ot}}">{{$memo->reason_ot}}</option>
                                                @foreach($tb_reason as $dt)
                                                    <option value="{{$dt->reason_ot}}">{{$dt->reason_ot}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="remark">Remark</label>
                                            <textarea class="form-control" id="remark" name="remark" maxlength="100" rows="3">{{ $memo->remark }}</textarea>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">
                                <div class=" pull-right">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <a class="btn btn-default" href="/EMS/GeneralMemo/Detail/{{$id_memo}}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </form>

				</div>
			</div>
			<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
@endsection
