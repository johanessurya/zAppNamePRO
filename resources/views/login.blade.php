@extends('layouts.one-column')

@section('title', 'Login')
@section('logo-desc', '<b>zAdmin</b>LTE')

@section('content')
  <div class="login-box-body">
    <p class="login-box-msg">Sign in to start your session</p>
    <form action="/login" method="post">
      <div class="form-group has-feedback">
        <input type="text" class="form-control" placeholder="Username">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" placeholder="Password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <label>
              <input type="checkbox"> Remember Me
            </label>
          </div>
        </div><!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
        </div><!-- /.col -->
      </div>
    </form>

    <div class="social-auth-links text-center">
      <p>- OR -</p>
    </div><!-- /.social-auth-links -->

    <a href="#">I forgot my password</a><br>
    <a href="/register" class="text-center">Register a new membership</a>

  </div><!-- /.login-box-body -->
@endsection
