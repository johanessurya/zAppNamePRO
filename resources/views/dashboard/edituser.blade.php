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
      <form action="/dashboard/user/edit/do
      " method="post" class="form-horizontal">
        <input type="hidden" name="id" value="{{ old('id', $user['id']) }}">
        <div class="box-body">
          <div class="form-group">
            <label for="inputUsername" class="col-sm-2 control-label">Username</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="inputUsername" placeholder="Username" name="username" value="{{ old('username', $user['username']) }}">
            </div>
          </div>
          <div class="form-group">
            <label for="inputUserType" class="col-sm-2 control-label">User Type</label>
            <div class="col-sm-10">
              <select id="inputUserType" class="form-control" name="userType">
                @for ($i=0; $i<count($userType); $i++)
                  <option value="{{ $i }}" {{ $user['userType'] == $i ? 'selected="selected"' : '' }}>{{ $userType[$i] }}</option>
                @endfor
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail" class="col-sm-2 control-label">Email</label>
            <div class="col-sm-10">
              <input type="email" class="form-control" id="inputEmail" placeholder="Email" name="email" value="{{ old('email', $user['email']) }}">
            </div>
          </div>
          <div class="form-group">
            <label for="inputCompanyId" class="col-sm-2 control-label">Company ID</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="inputCompanyId" placeholder="Company ID" name="CompanyID" value="{{ old('company_id', $user['CompanyID']) }}">
            </div>
          </div>
          <div class="form-group">
            <label for="inputActive" class="col-sm-2 control-label">Active</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="inputActive" placeholder="Active" name="active" value="{{ old('created', $user['active']) }}">
            </div>
          </div>
          <div class="form-group">
            <label for="inputFirstLogin" class="col-sm-2 control-label">First Login</label>
            <div class="col-sm-10">
              <input type="text" readonly="readonly" class="form-control" id="inputFirstLogin" placeholder="First Login" name="firstLogin" value="{{ old('first_login', $user['firstLogin']) }}">
            </div>
          </div>
          <div class="form-group">
            <label for="inputLastLogin" class="col-sm-2 control-label">Last Login</label>
            <div class="col-sm-10">
              <input type="text" readonly="readonly" class="form-control" id="inputLastLogin" placeholder="Last Login" name="lastLogin" value="{{ old('last_login', $user['lastLogin']) }}">
            </div>
          </div>
          <div class="form-group">
            <label for="inputLoginCount" class="col-sm-2 control-label">Login Count</label>
            <div class="col-sm-10">
              <input type="text" readonly="readonly" class="form-control" id="inputLoginCount" placeholder="Login Count" name="loginCount" value="{{ old('login_count', $user['loginCount']) }}">
            </div>
          </div>
          <div class="form-group">
            <label for="inputCreated" class="col-sm-2 control-label">Created</label>
            <div class="col-sm-10">
              <input type="text" readonly="readonly" readonly="readyonly" class="form-control" id="inputCreated" placeholder="Created" name="created" value="{{ old('created', $user['created']) }}">
            </div>
          </div>
          <div class="form-group">
            <label for="inputExpires" class="col-sm-2 control-label">Expires</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="inputExpires" placeholder="Expires" name="expires" value="{{ old('expires', $user['expires']) }}">
            </div>
          </div>
        </div><!-- /.box-body -->
        <div class="box-footer">
          <button type="reset" class="btn btn-default">Reset</button>
          <button type="submit" class="btn btn-info pull-right">Save</button>
        </div><!-- /.box-footer -->
      </form>
    </div><!-- /.box -->
  </div><!-- /.box-body -->
@endsection
