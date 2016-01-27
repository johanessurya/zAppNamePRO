@extends('layouts.default-admin')

@section('title-h1', 'Client Management')

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
        <h3 class="box-title">Edit Client</h3>
      </div><!-- /.box-header -->
      <!-- form start -->
      <form action="/dashboard/client/edit" method="post" class="form-horizontal">
        <input type="hidden" name="id" value="{{ old('id', $client['id']) }}">
        <div class="box-body">
          <div class="form-group">
            <label for="inputUsername" class="col-sm-2 control-label">User ID</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="inputUsername" placeholder="User ID" name="user_id" value="{{ old('user_id', $client['user_id']) }}">
            </div>
          </div>
          <div class="form-group">
            <label for="inputUserType" class="col-sm-2 control-label">Client Code</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="inputUserType" placeholder="Client Code" name="clientCode" value="{{ old('clientCode', $client['clientCode']) }}">
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail" class="col-sm-2 control-label">Name</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="inputEmail" placeholder="Name" name="name" value="{{ old('name', $client['name']) }}">
            </div>
          </div>
          <div class="form-group">
            <label for="inputCompanyId" class="col-sm-2 control-label">Gender</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="inputCompanyId" placeholder="Gender" name="gender" value="{{ old('gender', $client['gender']) }}">
            </div>
          </div>
          <div class="form-group">
            <label for="inputActive" class="col-sm-2 control-label">Type</label>
            <div class="col-sm-10">
              <select id="inputActive" class="form-control" name="type">
                <option value="Startup" selected="selected">Startup</option>
                <option value="Small">Small</option>
                <option value="Medium">Medium</option>
                <option value="Large">Large</option>
              </select>
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
