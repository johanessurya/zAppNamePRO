@extends('layouts.one-column')

@section('sidebar')
<div>
  <!-- Horizontal Form -->
  <div class="box box-info">
    <div class="box-header with-border">
      <h3 class="box-title">Register a new membership</h3>
    </div><!-- /.box-header -->
    <!-- form start -->
    <form class="form-horizontal">
      <div class="box-body">
        <div class="form-group">
          <label for="full-name" class="col-sm-2 control-label">Full Name</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="full-name" placeholder="Full Name">
          </div>
        </div>
        <div class="form-group">
          <label for="email" class="col-sm-2 control-label">Email</label>
          <div class="col-sm-10">
            <input type="email" class="form-control" id="email" placeholder="Email">
          </div>
        </div>
        <div class="form-group">
          <label for="password" class="col-sm-2 control-label">Password</label>
          <div class="col-sm-10">
            <input type="password" class="form-control" id="password" placeholder="Password">
          </div>
        </div>
        <div class="form-group">
          <label for="retype-password" class="col-sm-2 control-label">Retype Password</label>
          <div class="col-sm-10">
            <input type="password" class="form-control" id="retype-password" placeholder="Retype Password">
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-10">
            <div class="checkbox">
              <label>
                <input type="checkbox"> I agree to the <a href="#">terms</a>
              </label>
            </div>
          </div>
        </div>
      </div><!-- /.box-body -->
      <div class="box-footer">
        <button type="submit" class="btn btn-info pull-right">Register</button>
      </div><!-- /.box-footer -->
      <div class="box-body">
        <div>
          <a href="#">I already have a membership</a>
        </div>
      </div>
    </form>
  </div><!-- /.box -->
</div>
@endsection
