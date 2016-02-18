@extends('layouts.default-admin')

@section('title-h1', 'Activity Log')
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
          <button class="btn btn-default pull-right" id="daterange-btn">
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
          <h3 class="box-title">Report Graph</h3>
          <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
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
                <li><i class="fa fa-circle-o" style="color:#ff00ff !important;"></i> 50% - Chrome</li>
                <li><i class="fa fa-circle-o text-green"></i> 22% - IE</li>
                <li><i class="fa fa-circle-o text-yellow"></i> 12% - FireFox</li>
                <li><i class="fa fa-circle-o text-aqua"></i> 5% - Safari</li>
                <li><i class="fa fa-circle-o text-light-blue"></i> 3% - Opera</li>
                <li><i class="fa fa-circle-o text-gray"></i> 1% - Navigator</li>
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
  <div class="col-md-12">
    <!-- TABLE: LATEST ORDERS -->
    <div class="box box-info">
      <div class="box-header with-border">
        <p><strong><span id="activity-count">23</span> activities were found during the time period: <span id="activity-date-range">8/5/15 – 1/19/16</span></strong></p>
      </div><!-- /.box-header -->
      <div class="box-body">
        <div class="table-responsive">
          <table id="activity-table" class="table no-margin">
            <thead>
              <tr>
                <th>Date</th>
                <th>Start</th>
                <th>End</th>
                <th>Topic/description</th>
                <th>Notes</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div><!-- /.table-responsive -->
      </div><!-- /.box-body -->
    </div><!-- /.box -->
  </div><!-- /.col -->
</div><!-- /.row -->
@endsection
