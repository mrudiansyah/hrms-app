@extends('layouts/admin')
@section('Contents')
	<div class="content-wrapper">
		<section class="content-header">
			<h1>
				Edit User
				<small>Update system user</small>
			</h1>
		</section>

		<section class="content">
		<div class="row">
			<div class="col-xs-8">
				<div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">User Form</h3>
                    </div>
                    
                    <form action="{{ url('/user-management/'.$user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="box-body">
                            
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul style="margin-bottom:0;">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="form-group">
                                <label>Email address</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="form-group">
                                <label>Password (Leave blank to keep current)</label>
                                <input type="password" name="password" class="form-control" placeholder="New Password (optional)">
                            </div>
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm New Password">
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-warning"><i class="fa fa-save"></i> Update</button>
                            <a href="{{ url('/user-management') }}" class="btn btn-default">Cancel</a>
                        </div>
                    </form>
				</div>
			</div>
		</div>
		</section>
  	</div>
@endsection
