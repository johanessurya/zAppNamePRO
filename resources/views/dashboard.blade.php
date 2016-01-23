@extends('layouts.default-admin')

@section('content')
<div class="box">
  <div class="box-header">
    <h3 class="box-title">Data Table With Full Features</h3>
  </div><!-- /.box-header -->
  <div class="box-body">
    <table id="user-table" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Username</th>
          <th>Email</th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Trident</td>
          <td>Internet
            Explorer 4.0</td>
          <td>
            <button class="btn btn-block btn-primary">Edit</button>
          </td>
          <td>
            <button class="btn btn-block btn-danger">Delete</button>
          </td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <th>Rendering engine</th>
          <th>Browser</th>
        </tr>
      </tfoot>
    </table>
  </div><!-- /.box-body -->
</div><!-- /.box -->
@endsection
