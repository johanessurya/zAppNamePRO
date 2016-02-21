var $global = {};
$global = {};
$global.clientSource = [];
$global.user = {};
$global.misc = {};
$global.createForm = $('#edit-form-body');
$global.editForm = $('#quicksave-form-body');

// Get date time picker
$global.dateTime = {
  start: null,
  end: null
}

// Flag for activity tables
$global.activityFirstTime = true;

// Handle activity dataTables
$global.activityTable = null;

/* Called when create event form pop up
@params data json object that hold everything you need
Data Structure
{
  start: date start
  end: date end
  allDay: mouse event of row time row clicked
}
*/
$global.initCreateForm = function(data) {
  var $scope = {};
  var temp = moment(data.start);
  $scope.week = ['First', 'Second', 'Third', 'Fourth', 'Last'];
  $scope.month = 'Every :date of the Month';
  $scope.month2 = ':week_order :day of every month';

  // Get date
  $scope.date = temp.format('Do');
  $scope.month = $scope.month.replace(':date', $scope.date);

  // Get day
  $scope.day = temp.format('dddd');
  $scope.weekOrder = $scope.week[Math.ceil(temp.date()/7) - 1];

  $scope.month2 = $scope.month2.replace(':day', $scope.day);
  $scope.month2 = $scope.month2.replace(':week_order', $scope.weekOrder);

  // Update create form
  $global.createForm.find('select option[value="month"]').html($scope.month);
  $global.createForm.find('select option[value="month-2"]').html($scope.month2);
};

// Called when description modal show
// @params calendar json object that hold everything you need
$global.initEditEvent = function(calendar) {
  console.log($('#inputCategory2')[0]);
};

$global.initClients = function(clients) {
  var $scope = {};
  $scope.clients = clients;
  $scope.temp = false;

  $.get('/api/v1/client/get').done(function(resp){
    var _li = [];
    var i = null;

    for(i in resp) {
      _li.push({
        label: resp[i].name,
        value: resp[i].id
      });
    }

    var _clientIDList = [];
    console.log('scope', $scope);
    for(i in $scope.clients)
      _clientIDList.push($scope.clients[i].client_id);

    $global.misc.refreshSelectClient(_li);
    $global.misc.setSelectedOptions('#inputClient2', _clientIDList);

    // Refresh select2
    $('#inputClient2').select2({tags:true});
  });
};

/* categoryList is a json object that list category ID that selected
Structure
{
  categoryID: json_enc.id,
  subCategoryID: json_enc.subCategoryID,
  subSubCategoryID: json_enc.subSubCategoryID
}
*/
$global.initCategory = function(categoryList) {
  // console.log('CategoryList: ', categoryList);
  // console.log('Category Tree: ', $global.category);

  var _cat = $global.category;
  var _li = null;
  var _val = null;
  var i = null;

  // Category didn't need to be filled with any category
  // Just select it with correct value
  _val = categoryList.categoryID;
  $global.misc.setSelectedOptions('#inputCategory2', [_val]);
  // Trigger change
  $('#inputCategory2').trigger('change');

  // Set for topic(sub category)
  for(i in _cat) {
    if(_cat[i].id == _val) {
      _cat = _cat[i].subcategory;
      break;
    }
  }

  // Now _cat is fill with sub category associated with category
  console.log('Sub Category: ', _cat);
  // Generate correct _li
  _li = [];
  for(i in _cat) {
    _li.push({
      label: _cat[i].title,
      value: _cat[i].id
    });
  }

  _val = categoryList.subCategoryID;
  // Generate option list
  $global.misc.setOptionList('#inputTopic2', _li);
  // Select correct option
  $global.misc.setSelectedOptions('#inputTopic2', [_val]);
  // Trigger change
  $('#inputTopic2').trigger('change');
};

// To reload pie legend
$global.reloadPieChartlegend = function(data) {
  var _el = '<li><i class="fa fa-circle-o" style="color:%color% !important;"></i> %perc%% - %label%</li>';
  for(var i = 0; i < data.length; i++) {
    list.push({
      color: data[i].color,
      perc: data[i].value,
      label: data[i].label
    });
  }

  // Fill legend
  var _legend = $('#pie-chart-legend');

  // Remove all legend
  _legend.html('');

  var _legendEl = '';
  for(var i = 0; i < list.length; i++) {
    _legendEl = _el;
    _legendEl = _legendEl.replace('%color%', list[i].color);
    _legendEl = _legendEl.replace('%perc%', list[i].perc);
    _legendEl = _legendEl.replace('%label%', list[i].label);

    _legend.append(_legendEl);
  }
}

// To reload pie chart with correct data
$global.reloadPieChart = function() {
    $.ajax({
      method: 'POST',
      url: '/api/v1/logs/activity/piechart',
      data: {
        start: $global.dateTime.start,
        end: $global.dateTime.end
      }
    }).done(function(data) {
      $global.reloadPieChartlegend(data);

      //-------------
      //- PIE CHART (Report)-
      //-------------
      // Get context with jQuery - using jQuery's .get() method.
      var pieChartCanvas = $("#pieChart").get(0).getContext("2d");
      var pieChart = new Chart(pieChartCanvas);
      var PieData = data;

      var pieOptions = {
        //Boolean - Whether we should show a stroke on each segment
        segmentShowStroke: true,
        //String - The colour of each segment stroke
        segmentStrokeColor: "#fff",
        //Number - The width of each segment stroke
        segmentStrokeWidth: 2,
        //Number - The percentage of the chart that we cut out of the middle
        percentageInnerCutout: 50, // This is 0 for Pie charts
        //Number - Amount of animation steps
        animationSteps: 100,
        //String - Animation easing effect
        animationEasing: "easeOutBounce",
        //Boolean - Whether we animate the rotation of the Doughnut
        animateRotate: true,
        //Boolean - Whether we animate scaling the Doughnut from the centre
        animateScale: false,
        //Boolean - whether to make the chart responsive to window resizing
        responsive: true,
        // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
        maintainAspectRatio: true,
        //String - A legend template
        legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
      };
      //Create pie or douhnut chart
      // You can switch between pie and douhnut using the method below.
      pieChart.Doughnut(PieData, pieOptions);
    });
};

// Save comment every onBlur
$global.saveCommentOnBlur = function() {
  $.ajax({
    method: 'POST',
    url: '/api/v1/logs/setcomment/activity_log_comment',
    data: {
      value: $global.editor1.getData()
    }
  });
}

$global.initCKEditor = function() {
  CKEDITOR.config.customConfig = '/js/ckeditor-config.js';
  // Replace the <textarea id="editor1"> with a CKEditor
  // instance, using default configuration.
  CKEDITOR.replace('editor1');

  // Attatch onBlur
  $global.editor1 = CKEDITOR.instances.editor1;
  $global.editor1.on('blur', $global.saveCommentOnBlur);

  $.ajax({
    method: 'GET',
    url: '/api/v1/logs/getcomment/activity_log_comment'
  }).done(function(data){
    if(data.value.length > 0) {
      var r = confirm('Do you want load last comment?');

      if(r == true)
        $global.editor1.setData(data.value);
    }
  });

  //bootstrap WYSIHTML5 - text editor
  $(".textarea").wysihtml5();
};

$global.dateTimePicker = function() {
  $('#daterange-btn').on('apply.daterangepicker', function(ev, picker) {
    $global.dateTime.start = picker.startDate.format($config.format.date) + ' 00:00';
    $global.dateTime.end = picker.endDate.format($config.format.date) + ' 23:59';

    // Choosen label
    $(this).find('span').html(picker.chosenLabel);

    // Change activity log date range
    var dateRange;
    dateRange = picker.startDate.format($config.format.fullDateTime) + ' - ' + picker.endDate.format($config.format.fullDateTime);
    $('#title-small').html(dateRange);

    // Change date range above table
    dateRange = picker.startDate.format($config.format.date) + ' - ' + picker.endDate.format($config.format.date);
    $('#activity-date-range').html(dateRange);

    // Count activity
    $.ajax({
      method: 'POST',
      url: '/api/v1/logs/activity',
      data: {
        start: $global.dateTime.start,
        end: $global.dateTime.end
      }
    }).done(function(data) {
      $('#activity-count').html(data.data.length);
    });

    // Reload chart js
    $global.reloadPieChart();

    // Reload activity data table
    $global.reloadActivityTable();
  });
}

$global.reloadActivityTable = function(start, end) {
    if($global.activityFirstTime)
      $global.activityTable = $('#activity-table').DataTable( {
          'ajax': {
            url: '/api/v1/logs/activity',
            type: 'POST',
            data: function(d) {
              d.start = $global.dateTime.start;
              d.end = $global.dateTime.end;
            }
          },
          'columns': [
            {'data': 'date', 'searchable': true},
            {'data': 'start', 'searchable': true},
            {'data': 'end', 'searchable': true},
            {'data': 'description', 'searchable': true},
            {'data': 'note', 'searchable': true},
          ]
      } );
    else {
      $global.activityTable.ajax.reload();
    }

    $global.activityFirstTime = false;
}

// ==== List of function (START) ====

/* Set an select element with list of options.
@param _selector element selector. Called with jQuery. Example $(_selector)
@param _li array of object. Structure:
[
  {
    label: 'Place in innerHTML',
    value: 'Place in <option value="{here}"'
  }
]
*/
$global.misc.setOptionList = function(_selector, _li) {
  var $scope = {};
  var _temp = null;

  $scope.selector = _selector;
  $scope.jquery = $(_selector);
  $scope.option = '<option value=":value">:label</option>';

  $scope.jquery.html('');
  for(var i in _li) {
    _temp = $scope.option;
    _temp = _temp.replace(':value', _li[i].value);
    _temp = _temp.replace(':label', _li[i].label);
    $scope.jquery.append(_temp);
  }

  return true;
};

/* Select multiple element with list of value.
@param _selector element selector. Called with jQuery. Example $(_selector)
@param _li array of string. Example: [1,2,3,'testing']
*/
$global.misc.setSelectedOptions = function(_selector, _li) {
  $scope = {};
  $scope.selector = _selector;
  $scope.jquery = $(_selector);

  // Clear all selected option
  $scope.jquery.find('option').removeAttr('selected');
  for(var i in _li) {
    $scope.jquery.find('option[value="' + _li[i] + '"]').attr('selected', 'selected');
  }

  return true;
};

// Refresh client list. Get ajax and regen opton list
$global.misc.refreshSelectClient = function (_li) {
  $global.misc.setOptionList('#inputClient2', _li);
  $global.misc.setOptionList('#inputClient', _li);
};
// ==== List of function (END) ====

$(function () {
  $global.createForm = $('#quicksave-form-body');

  // Load category tree
  $.get('/api/v1/category/get').success(function(_res) {
    $global.category = _res.category;

    // Init category. Select first item.
    $('#inputCategory').trigger('change');

    // console.log('Category => ', $global.category);
  });

  // ======= PopUp Modal =========
  $('button[data-dismiss="modal"]').click(function() {
    closeModal('.modal');
  });

  // ======= Datatables =========
  // class prefix: "js-"" mean that class related to javascript method/function
  $global.user.editEl = '<a href="/dashboard/user/:user_id" class="js-btn-edit btn btn-primary"><span class="glyphicon glyphicon-pencil"></span></a>';
  $global.user.deleteEl = '<a href="/dashboard/user/delete/:user_id" class="js-btn-delete btn btn-danger"><span class="glyphicon glyphicon-remove"></span></a>';
  $global.user.active = '<a href="/dashboard/user/active/:user_id" class="js-btn-delete btn btn-success"><span class="glyphicon glyphicon-ok"></span></a>';

  // Load user list
  $('#user-table').DataTable( {
      'ajax': '/api/v1/user',
      'columns': [
        {
          mRender: function (data, type, row) {
            var _temp = $global.user.editEl;
            return _temp.replace(':user_id', row.id);
          }
        },
        {
          mRender: function (data, type, row) {
            var _temp = $global.user.deleteEl;
            return _temp.replace(':user_id', row.id);
          }
        },
        {
          mRender: function (data, type, row) {
            var _temp = $global.user.active;
            return _temp.replace(':user_id', row.id);
          }
        },
        {'data': 'username', 'searchable': true},
        {'data': 'active', 'searchable': true},
        {'data': 'userType', 'searchable': true},
        {'data': 'email', 'searchable': true},
        {'data': 'CompanyID', 'searchable': true},
        {'data': 'created', 'searchable': true},
        {'data': 'firstLogin', 'searchable': true},
        {'data': 'lastLogin', 'searchable': true},
        {'data': 'loginCount', 'searchable': true},
        {'data': 'expires', 'searchable': true},
      ],
      'columnDefs': [{
        targets: [0,1,2],
        orderable: false
      }],
      "order": [[ 3, "asc" ]]
  } );

  // class prefix: "js-"" mean that class related to javascript method/function
  $global.editEl = '<a href="/dashboard/client/edit/:user_id" class="js-btn-edit btn btn-block btn-primary"><span class="glyphicon glyphicon-pencil"></span></a>';
  $global.deleteEl = '<a href="/dashboard/client/delete/:user_id" class="js-btn-delete btn btn-block btn-danger"><span class="glyphicon glyphicon-remove"></span></a>';
  $('#client-table').DataTable( {
      'ajax': '/api/v1/client',
      'columns': [
        {
          mRender: function (data, type, row) {
            return $global.editEl.replace(':user_id', row.id);
          }
        },
        {
          mRender: function (data, type, row) {
              return $global.deleteEl.replace(':user_id', row.id);
            }
        },
        {'data': 'clientCode', 'searchable': true},
        {'data': 'name', 'searchable': true},
        {'data': 'gender', 'searchable': true},
        {'data': 'type', 'searchable': true},
        {'data': 'note', 'searchable': true},
      ],
      'columnDefs': [{
        targets: [0,1],
        orderable: false
      }],
      "order": [[ 2, "asc" ]]
  } );

  // Load sub category option
  $('#inputCategory, #inputCategory2').change(function(){
    // ID attribute
    var _id = $(this).attr('id');
    var _categoryId = $(this).val();
    // console.log(this, _categoryId);

    var _index = $('#' + _id).prop('selectedIndex');
    var _res = $global.category[_index].subcategory;
    var _el = '<option value=":value">:innerHTML</option>';
    var _temp = null;

    _temp = _el.replace(':value', '');
    _temp = _temp.replace(':innerHTML', '');

    // Determine #inputTopic or #inputTopic2
    var _id2 = 'inputTopic';
    if(_id == 'inputCategory2')
      _id2 = 'inputTopic2';

    // Remove all option
    var _select = null;
    _select = $('#' + _id2);
    _select.html('');

    // Start add with blank option
    // _select.append(_temp);

    for(var i in _res) {
      _temp = _el.replace(':value', _res[i].id);
      _temp = _temp.replace(':innerHTML', _res[i].title);

      _select.append(_temp);
    }

    // Init category. Select first item.
    $('#' + _id2).trigger('change');
  });

  $('#inputTopic, #inputTopic2').change(function(){
    // ID attribute
    var _idTopic = $(this).attr('id');
    var _categoryId = $(this).val();

    var _idCategory = 'inputCategory';
    var _idSubCategory = 'inputSubTopic';

    if(_idTopic == 'inputTopic2'){
      _idCategory = 'inputCategory2';
      _idSubCategory = 'inputSubTopic2';
    }

    var _index = $('#' + _idCategory).prop('selectedIndex');
    var _index2 = $('#' + _idTopic).prop('selectedIndex');

    var _category = $global.category[_index].subcategory[_index2];

    var _res = _category.subsubcategory;

    // console.log('category', _category, _res);

    // console.log('input topic', _index, _index2, _res);

    var _el = '<option value=":value">:innerHTML</option>';
    var _temp = null;
    var _show = false;

    _temp = _el.replace(':value', '');
    _temp = _temp.replace(':innerHTML', '');

    // Remove all option
    var _select = $('#' + _idSubCategory);
    _select.html('');

    // Start add with blank option
    // _select.append(_temp);

    for(var i in _res) {
      _show = true;
      _temp = _el.replace(':value', _res[i].id);
      _temp = _temp.replace(':innerHTML', _res[i].title);

      _select.append(_temp);
    }

    if(_show) {
      // console.log('show');
      $('#' + _idSubCategory).parent().show();
    }else{
      // console.log('hide');
      $('#' + _idSubCategory).parent().hide();
    }

    // console.log('Sub Category changed');
  });

  // Testing using select2
  $('#inputClient, #inputClient2').select2({
    tags: true
  });

  // When set repeat to "yes", show how many times
  $('#inputRepeat').change(function() {
    if($(this).val() === '') {
      $('#inputRepeatN').parent().hide();
    } else {
      $('#inputRepeatN').parent().show();
    }
  });

  // When selecting a client
  $('#inputClient, #inputClient2').change(function() {
    var $scope = {};

    // Get selected item;
    $scope.value = $(this).val();

    if($scope.value === null)
      return;

    $scope.lastValue = $scope.value[$scope.value.length - 1];

    // console.log($scope, parseInt($scope.lastValue));

    if(isNaN(parseInt($scope.lastValue))) {
      // Show confirm box
      $scope.confirm = confirm("Should I add this new client?");

      if($scope.confirm !== true) {
        // Cancel pressed

        // Remove option with value = $scope.lastValue
        $scope.test = $(this).find('option[value="' + $scope.lastValue + '"]').remove();

        // Remove select2 tag
        $(this).parent().find('span.select2 ul li[title="' + $scope.lastValue + '"]').remove();
      }
    }
  });

  //Date range as a button
  $('#daterange-btn').daterangepicker(
      {
        ranges: {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate: moment()
      },
  function (start, end) {
    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
  }
  );

}); // End of onReadyDocument

function showModal(selector) {
  $(selector).fadeIn('slow');
}

function closeModal(selector) {
  $(selector).fadeOut('slow');
}
