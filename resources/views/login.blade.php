@extends('layouts.one-column')

@section('title', 'Login')
@section('logo-desc', '<b>zAppName</b>PRO')
@section('logo-text', 'Please Log In.')

@section('content')
  <div class="login-box-body">
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
    <form action="/login" method="post">
      <div class="form-group has-feedback">
        <input type="text" class="form-control" placeholder="Username" name="username">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" placeholder="Password" name="password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <label>
              <input type="checkbox" name="remember_me"> Remember Me
            </label>
          </div>
        </div><!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Log In</button>
        </div><!-- /.col -->
      </div>
    </form>

    <hr width="80%" />

		<div class="row">
        <div class="col-xs-8">
		<a href="/forgot">Forgot Your Password?</a>
        </div><!-- /.col -->
        <div class="col-xs-4">
		<a href="/register">Try for Free!</a><br>
        </div><!-- /.col -->
    </div>
  </div><!-- /.login-box-body -->
@endsection
