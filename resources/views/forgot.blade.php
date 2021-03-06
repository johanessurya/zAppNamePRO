@extends('layouts.one-column')

@section('title', 'Login')
@section('logo-desc', '<strong>zAppName</strong>PRO')
@section('logo-text', '<strong>Forgot</strong> your Password?')

@section('content')
  <div class="login-box-body">
    @if (Session::has('message'))
    <div class="callout callout-danger">
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

    <p>To reset your password, please enter your zAppName associated email address.</p>
    <form action="/resetpassword" method="post">
      <div class="form-group has-feedback">
				<input type="email" class="form-control" placeholder="Email" name="email">
				<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
			</div>
      <div class="row">
        <div class="col-xs-8">
        </div><!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Reset</button>
        </div><!-- /.col -->
      </div>
    </form>

    <hr width="80%" />

		<div class="row">
      <div class="col-xs-8">
  We will email you directions.
      </div><!-- /.col -->
      <div class="col-xs-4">
  <a href="/login">Log In</a><br>
      </div><!-- /.col -->
    </div>
  </div><!-- /.login-box-body -->
@endsection
