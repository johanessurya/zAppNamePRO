@extends('layouts.log-admin')

@section('title-h1', 'Use-Of-Time Analysis')
@section('title-small', ' ')

@push('scripts')
  <!-- fullCalendar 2.2.5 -->
  <script src="{{ asset('js/admin-logs.js') }}"></script>
@endpush

@section('content')
<div class="row">
  <div class="col-md-12">
    <!-- TABLE: LATEST ORDERS -->
    <div class="box-body pad table-responsive">
      <div class="btn-group">
        <a href="#" class="btn btn-default">PDF</a>
        <a href="#" class="btn btn-default">Print</a>
      </div>

      <!-- Date and time range -->
      <div class="form-group pull-right">
        <div class="input-group">
          <button class="btn btn-default pull-right" id="daterange-btn" data-type="use-of-time">
            <i class="fa fa-calendar"></i>
            <span>Please select a date</span>
            <i class="fa fa-caret-down"></i>
          </button>
        </div>
      </div><!-- /.form group -->
    </div><!-- /.box-body -->
  </div><!-- /.col -->
</div>

<div class="row">
    <!-- Chart JS -->
    <div class="col-md-6">
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 id="graph-title" class="box-title">Use-Of-Time Analysis</h3>
          <div class="box-tools pull-right">
            <button class="min-max btn btn-box-tool" data-widget="collapse" data-type="graph"><i class="fa fa-minus"></i></button>
          </div>
        </div><!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-8">
              <div class="chart-responsive">
                <canvas id="pieChart" height="150"></canvas>
              </div><!-- ./chart-responsive -->
            </div><!-- /.col -->
            <div class="col-md-4">
              <ul id="pie-chart-legend" class="chart-legend clearfix">
                <li class="hide"><i class="fa fa-circle-o" style="color:#ff00ff !important;"></i> 50% - Chrome</li>
              </ul>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.box-body -->

      </div><!-- /.box -->
    </div><!-- /.col -->

    <div class="col-md-6">
      <!-- USERS LIST -->
      <div class="box box-danger">
        <div class="box-header with-border">
          <h3 class="box-title">Comments</h3>
          <div class="box-tools pull-right">
            <button id="load-last-comment" class="btn btn-box-tool"><i class="fa fa-fw fa-refresh"></i></button>
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div><!-- /.box-header -->
        <div class="box-body pad">
          <form>
            <textarea id="editor1" name="editor1" rows="10" cols="80"></textarea>
          </form>
        </div><!-- /.box-body -->
      </div><!--/.box -->
    </div><!-- /.col -->
</div>

<div class="row">
  <div class="col-xs-12">
    <!-- TABLE: LATEST ORDERS -->
    <div class="box">
      <div class="box-header with-border">
        <h3 id="table-title" class="box-title">Activities Found</h3>
        <div class="box-tools pull-right">
          <button class="min-max btn btn-box-tool" data-widget="collapse" data-type="table"><i class="fa fa-minus"></i></button>
        </div>
      </div><!-- /.box-header -->
      <div class="box-body">
        <table id="topic-stat-table" class="use-of-time table table-bordered table-striped">
          <thead>
            <tr>
              <th>Category</th>
              <th>Topic</th>
              <th>% Time</th>
              <th>Hours</th>
              <th>Freq</th>
              <th>% Total Time</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div><!-- /.box-body -->
    </div><!-- /.box -->
  </div><!-- /.col -->
</div><!-- /.row -->
@endsection
