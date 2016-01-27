@extends('layouts.default-admin')

@section('title-h1', 'Client Management')

@section('content')
<div class="box">
  <div class="box-header">
    <h3 class="box-title">Data Table With Full Features</h3> <a href="/dashboard/client/create" class="btn btn-success btn-xs">Add New Client</a>
  </div><!-- /.box-header -->
  <div class="box-body">
    <table id="client-table" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th></th>
          <th></th>
          <th>User ID</th>
          <th>Client Code</th>
          <th>Name</th>
          <th>Gender</th>
          <th>Type</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div><!-- /.box-body -->
</div><!-- /.box -->
@endsection
