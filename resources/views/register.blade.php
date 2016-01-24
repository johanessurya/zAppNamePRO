@extends('layouts.one-column')

@section('title', 'Register')
@section('logo-desc', '<strong>zAppName</strong>PRO')
@section('logo-text', 'Register to <strong>Try for Free</strong>')

@section('content')
  <div class="login-box-body">
    @if (isset($message))
    <div class="callout callout-{{ $messageType }}">
      @foreach ($message as $x)
      <p>{{ $x }}</p>
      @endforeach
    </div>
    @endif

    <form action="/register" method="post">
      <div class="form-group has-feedback">
        <input type="text" class="form-control" placeholder="User name" name="username" value="{{ old('username') }}">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
         <input type="OfficeName" class="form-control" placeholder="Company name" name="company_name" value="{{ old('company_name')}}">
         <span class="glyphicon glyphicon-briefcase form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
         <input type="state" class="form-control" placeholder="State Abbreviation" name="state" value="{{ old('state') }}">
         <span class="glyphicon glyphicon-map-marker form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" placeholder="Password" name="password">
        <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" placeholder="Retype Password" name="password_confirmation">
        <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
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

    <a href="/login" class="text-center"> I already have an account</a>

  </div><!-- /.login-box-body -->
@endsection
