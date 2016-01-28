@extends('layouts.default-admin')

@section('title-h1', 'Client List')

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
        <h3 class="box-title">Create New Client</h3>
      </div><!-- /.box-header -->
      <!-- form start -->
      <form action="/dashboard/client/create" method="post" class="form-horizontal">
        <div class="box-body">
          <div class="form-group">
            <label for="inputEmail" class="col-sm-2 control-label">Name</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="inputEmail" placeholder="Name" name="name" value="{{ old('name') }}">
            </div>
          </div>
          <div class="form-group">
            <label for="inputUserType" class="col-sm-2 control-label">Client Code</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="inputUserType" placeholder="Client Code" name="clientCode" value="{{ old('clientCode') }}">
            </div>
          </div>
          <div class="form-group">
            <label for="inputCompanyId" class="col-sm-2 control-label">Gender</label>
            <div class="col-sm-10">
              <select id="inputCompanyId" class="form-control" name="gender">
                @foreach($gender as $x)
                  <option value="{{ $x }}" {{ old('gender') == $x ? 'selected="selected"' : '' }}>{{ $x }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="inputActive" class="col-sm-2 control-label">Type</label>
            <div class="col-sm-10">
              <select id="inputActive" class="form-control" name="type">
                @foreach($type as $x)
                  <option value="{{ $x }}" {{ old('type') == $x ? 'selected="selected"' : '' }}>{{ $x }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="inputActive" class="col-sm-2 control-label">Note</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="inputEmail" placeholder="Note" name="note" value="{{ old('note') }}">
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
