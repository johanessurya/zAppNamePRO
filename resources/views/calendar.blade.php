@extends('layouts.default-admin')

@section('title-h1', 'Calendar')

@push('scripts')
  <!-- fullCalendar 2.2.5 -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.6.0/fullcalendar.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.6.0/fullcalendar.min.js"></script>
  <script src="{{ asset('js/jquery.calendar.js')}}"></script>
  <script src="{{ asset('js/admin-calendar.js') }}"></script>
@endpush

@section('popup-modal')
<div class="example-modal">
  <div id="calendarModal" class="modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <!-- Create an event -->
        <form id="quicksave-form-body">
          <input id="start" type="hidden" name="start">
          <input id="end" type="hidden" name="end">
          <input type="hidden" name="clientID" value="0">
          <div class="modal-header remove-border-bottom">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"></h4>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="inputCategory">Category</label>
              <select class="form-control" id="inputCategory" name="categoryID">
                @foreach($categories as $x)
                  <option value="{{ $x['id'] }}">{{ $x['title'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="inputTopic">Topic</label>
              <select class="form-control" id="inputTopic" name="subCategoryID">
              </select>
            </div>
            <div class="form-group" style="display: none">
              <label for="inputSubTopic">Sub Topic</label>
              <select class="form-control" id="inputSubTopic" name="subSubCategoryID">
              </select>
            </div>
            <div class="form-group">
              <label for="inputTitle">Title</label>
              <input type="text" class="form-control" id="inputTitle" placeholder="Title" name="title">
            </div>
            <div class="form-group">
              <label for="inputClient">Client(s)</label>
              <select class="form-control" id="inputClient" name="clients[]" multiple="multipe" style="width: 100%">
                @foreach($clients as $x)
                  <option value="{{ $x['id'] }}">{{ $x['name'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="inputNote">Note</label>
              <textarea class="form-control" id="inputNote" rows="3" placeholder="Note" name="description"></textarea>
            </div>

            <hr width="80%">

            <div class="row">
              <div class="col-lg-4 hide">
                  <label for="inputAllDay">All Day</label>
                  <input class="display-block" type="checkbox" id="inputAllDay" name="allDay" />
              </div>
              <div class="col-lg-4">
                <label for="inputRepeat">Repeat</label>
                <select class="form-control" id="inputRepeat" name="repeat_type">
                  <option value="" selected="selected">No</option>
                  <option value="day">Daily</option>
                  <option value="week">Weekly</option>
                  <option value="month">Monthly</option>
                </select>
              </div>
              <div class="col-lg-4">
                <label for="inputRepeatN">How many times?</label>
                <select class="form-control" id="inputRepeatN" name="repeatN">
                  <option value="1" selected="selected">1</option>
                  @for ($i = 2; $i <= 40; $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                  @endfor;
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            <button id="add-event" type="button" class="btn btn-primary">Create</button>
          </div>
        </form>

        <!-- Show preview -->
        <div id="cal-preview">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 id="details-body-title" class="modal-title">Modal Default</h4>
          </div>
          <div class="modal-body">
            <p id="details-body-content">One fine body&hellip;</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" id="delete-event">Delete</button>
            <button type="button" class="btn btn-primary" id="edit-event">Edit</button>
          </div>
        </div>

        <!-- Edit Form -->
        <form id="edit-form-body">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title js-details-body-title">Modal Default</h4>
          </div>

          <div class="modal-body">
            <div class="form-group">
              <label for="inputCategory">Category</label>
              <select class="form-control" id="inputCategory" name="categoryID">
                @foreach($categories as $x)
                  <option value="{{ $x['id'] }}">{{ $x['title'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="inputTopic2">Topic</label>
              <select class="form-control" id="inputTopic2" name="subCategoryID">
              </select>
            </div>
            <div class="form-group" style="display: none">
              <label for="inputSubTopic2">Sub Topic</label>
              <select class="form-control" id="inputSubTopic2" name="subSubCategoryID">
              </select>
            </div>
            <div class="form-group">
              <label for="inputTitle2">Title</label>
              <input type="text" class="form-control" id="inputTitle2" placeholder="Title" name="title">
            </div>
            <div class="form-group">
              <label for="inputClient2">Client(s)</label>
              <select class="form-control" id="inputClient2" name="clients[]" multiple="multipe" style="width: 100%">
                @foreach($clients as $x)
                  <option value="{{ $x['id'] }}">{{ $x['name'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="inputNote2">Note</label>
              <textarea class="form-control" id="inputNote2" rows="3" placeholder="Note" name="description"></textarea>
            </div>

            <hr width="80%">

            <div class="row">
              <div class="col-lg-4 hide">
                  <label for="inputAllDay2">All Day</label>
                  <input class="display-block" type="checkbox" id="inputAllDay2" name="allDay" />
              </div>
              <div class="col-lg-4">
                <label for="inputRepeat2">Repeat</label>
                <select class="form-control" id="inputRepeat2" name="repeat_type">
                  <option value="" selected="selected">No</option>
                  <option value="day">Daily</option>
                  <option value="week">Weekly</option>
                  <option value="month">Monthly</option>
                </select>
              </div>
              <div class="col-lg-4">
                <label for="inputRepeatN2">How many times?</label>
                <select class="form-control" id="inputRepeatN2" name="repeatN">
                  <option value="1" selected="selected">1</option>
                  @for ($i = 2; $i <= 40; $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                  @endfor;
                </select>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="save-changes">Save</button>
          </div>
        </form>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  <div id="user-new" class="modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Add New User</h4>
        </div>
        <div class="modal-body">
          <!-- general form elements -->
          <div class="box box-primary">
            <!-- form start -->
            <form role="form">
              <div class="box-body">
                <div class="form-group">
                  <label for="exampleInputEmail1">Email address</label>
                  <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
                </div>
                <div class="form-group">
                  <label for="exampleInputPassword1">Password</label>
                  <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                </div>
                <div class="form-group">
                  <label for="exampleInputFile">File input</label>
                  <input type="file" id="exampleInputFile">
                  <p class="help-block">Example block-level help text here.</p>
                </div>
                <div class="checkbox">
                  <label>
                    <input type="checkbox"> Check me out
                  </label>
                </div>
              </div><!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </form>
          </div><!-- /.box -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
</div><!-- /.example-modal -->
@endsection

@section('content')
<div class="box">
  <div class="box-header">
    <h3 class="box-title">Data Table With Full Features</h3>
  </div><!-- /.box-header -->
  <div class="box-body">
    <div id="calendar"></div>
  </div><!-- /.box-body -->
</div><!-- /.box -->
@endsection
