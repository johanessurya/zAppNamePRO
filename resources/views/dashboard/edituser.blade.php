@extends('layouts.default-admin')

@section('content')
  <div class="box-body">
    @if (Session::has('message'))
    <div class="callout callout-success">
      <p>{{ Session::get('message') }}</p>
    </div>
    @endif

    @if (count($errors) > 0)
    <div class="callout callout-danger">
      @foreach ($errors->all() as $error)
        <p>{{ $error }}</p>
      @endforeach
    </div>
    @endif
    <!-- Horizontal Form -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">{{ $title }}</h3>
      </div><!-- /.box-header -->
      <!-- form start -->
      <form action="/dashboard/user" method="post" class="form-horizontal">
        <input type="hidden" name="id" value="{{ old('id', $user['id']) }}">
        <div class="box-body">
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Username</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="inputEmail3" placeholder="Username" name="username" value="{{ old('username', $user['username']) }}">
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail4" class="col-sm-2 control-label">Company Name</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="inputEmail4" placeholder="Company Name" name="company_name" value="{{ old('company_name', $user['company_name']) }}">
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail5" class="col-sm-2 control-label">State Abbreviation</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="inputEmail5" placeholder="State Abbreviation" name="state" value="{{ old('state', $user['state']) }}">
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail6" class="col-sm-2 control-label">Email</label>
            <div class="col-sm-10">
              <input type="email" class="form-control" id="inputEmail6" placeholder="Email" name="email" value="{{ old('email', $user['email']) }}">
            </div>
          </div>
          <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">Password</label>
            <div class="col-sm-10">
              <input type="password" class="form-control" id="inputPassword3" placeholder="Password" name="password">
            </div>
          </div>
          <div class="form-group">
            <label for="inputPassword4" class="col-sm-2 control-label">Retype Password</label>
            <div class="col-sm-10">
              <input type="password" class="form-control" id="inputPassword4" placeholder="Retype Password" name="password_confirmation">
            </div>
          </div>
        </div><!-- /.box-body -->
        <div class="box-footer">
          <button type="reset" class="btn btn-default">Reset</button>
          <button type="submit" class="btn btn-info pull-right">Create</button>
        </div><!-- /.box-footer -->
      </form>
    </div><!-- /.box -->
  </div><!-- /.box-body -->
@endsection
