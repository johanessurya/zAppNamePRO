@extends('layouts.one-column')

@section('title', 'Register')
@section('logo-desc', '<b>zLogo</b>')

@section('content')
  <div class="login-box-body">
    @if (isset($message))
    <div class="callout callout-danger">
      @foreach ($message as $x)
      <p>{{ $x }}</p>
      @endforeach
    </div>
    @endif

    <p class="login-box-msg">Register a new membership</p>
    <form action="/register" method="post">
      <div class="form-group has-feedback">
        <input type="text" class="form-control" placeholder="Full Name" name="full-name">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="email" class="form-control" placeholder="Email" name="email">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" placeholder="Password" name="password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" placeholder="Retype Password" name="retype-password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <label>
              <input type="checkbox" name="agree"> I agree to the <a href="#">terms</a>
            </label>
          </div>
        </div><!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Register</button>
        </div><!-- /.col -->
      </div>
    </form>

    <a href="/login" class="text-center">I already have a membership</a>

  </div><!-- /.login-box-body -->
@endsection
