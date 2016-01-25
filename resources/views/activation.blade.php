@extends('layouts.one-column')

@section('title', 'Reset Password')
@section('logo-desc', '<b>zAppName</b>PRO')
@section('logo-text', 'Active.')

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
    <form action="/reset" method="post">
      <div class="form-group has-feedback">
        <input type="password" class="form-control" placeholder="Password" name="password">
        <span class="glyphicon glyphicon-ok form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-4 pull-right">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Active</button>
        </div><!-- /.col -->
      </div>
    </form>

    <hr width="80%" />

		<div class="row">
        <div class="col-xs-8">
		<a href="#">Buy a license</a>
        </div><!-- /.col -->
    </div>
  </div><!-- /.login-box-body -->
@endsection
