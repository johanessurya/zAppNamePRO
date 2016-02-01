$global = {};
$global.clientSource = [];

// Defined function
$global.clientAutocompleteInit = function() {
  $('#inputClient').autocomplete({
    delay: 0,
    source: $global.clientSource,
    change: function(event, ui) {
      var $scope = {};
      var _form = $global.createForm[0];

      console.log('Client ID', $global.createForm.client);
      if(ui.item == null) {
        console.log("Item doesn't exists");

        // If item doesn't exists
        $scope.confirm = confirm("Should I add this new client?");

        if($scope.confirm == true) {
          console.log('Ok pressed')
          // Ok pressed
          // Add hidden input clientID value to null or blank
          _form.clientID.value = '';
        } else {
          console.log('Cancel pressed');
          // Cancel pressed
          // Remove textbox
          _form.client.value = '';
        }
      } else {
        // if user selected an item
        // Set clientID
        _form.clientID.value = ui.item.id;
        console.log('selected item');
      }
      console.log('on change', event, ui);
    }
  });
}

$(function () {
  $global.createForm = $('#quicksave-form-body');

  // Load category tree
  $.get('/api/v1/category/get').success(function(_res) {
    $global.category = _res.category;

    // Init category. Select first item.
    $('#inputCategory').trigger('change');

    console.log('Category => ', $global.category);
  });

  // Get related client
  $.get('/api/v1/client/get').success(function(_res) {
    var data = [];

    for(i in _res) {
      data.push({
        label: _res[i].name,
        value: _res[i].name,
        id: _res[i].id
      });
    }

    $global.clientSource = data;

    $global.clientAutocompleteInit();
  });

  // ======= PopUp Modal =========
  $('button[data-dismiss="modal"]').click(function() {
    closeModal('.modal');
  });

  // ======= Datatables =========
  // class prefix: "js-"" mean that class related to javascript method/function
  $global.editEl = '<a href="/dashboard/user/:user_id" class="js-btn-edit btn btn-primary"><span class="glyphicon glyphicon-pencil"></span></a>';
  $global.deleteEl = '<a href="/dashboard/user/delete/:user_id" class="js-btn-delete btn btn-danger"><span class="glyphicon glyphicon-remove"></span></a>';
  $global.active = '<a href="/dashboard/user/active/:user_id" class="js-btn-delete btn btn-success"><span class="glyphicon glyphicon-ok"></span></a>';

  // Load user list
  $('#user-table').DataTable( {
      'ajax': '/api/v1/user',
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
        {
          mRender: function (data, type, row) {
              return $global.active.replace(':user_id', row.id);
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
  $('#inputCategory').change(function(){
    var _categoryId = $(this).val();
    console.log(this, _categoryId);

    var _index = $('#inputCategory').prop('selectedIndex');
    var _res = $global.category[_index].subcategory;
    var _el = '<option value=":value">:innerHTML</option>';
    var _temp = null;

    _temp = _el.replace(':value', '');
    _temp = _temp.replace(':innerHTML', '');

    // Remove all option
    var _select = $('#inputTopic')
    _select.html('');

    // Start add with blank option
    // _select.append(_temp);

    for(i in _res) {
      _temp = _el.replace(':value', _res[i].id);
      _temp = _temp.replace(':innerHTML', _res[i].title);

      _select.append(_temp);
    }

    // Init category. Select first item.
    $('#inputTopic').trigger('change');
  });

  $('#inputTopic').change(function(){
    var _categoryId = $(this).val();

    var _index = $('#inputCategory').prop('selectedIndex');
    var _index2 = $('#inputTopic').prop('selectedIndex');

    var _category = $global.category[_index].subcategory[_index2];

    var _res = _category.subsubcategory;

    console.log('category', _category, _res);

    // console.log('input topic', _index, _index2, _res);

    var _el = '<option value=":value">:innerHTML</option>';
    var _temp = null;
    var _show = false;

    _temp = _el.replace(':value', '');
    _temp = _temp.replace(':innerHTML', '');

    // Remove all option
    var _select = $('#inputSubTopic')
    _select.html('');

    // Start add with blank option
    // _select.append(_temp);

    for(i in _res) {
      _show = true;
      _temp = _el.replace(':value', _res[i].id);
      _temp = _temp.replace(':innerHTML', _res[i].title);

      _select.append(_temp);
    }

    if(_show) {
      console.log('show');
      $('#inputSubTopic').parent().show();
    }else{
      console.log('hide');
      $('#inputSubTopic').parent().hide();
    }

    console.log('Sub Category changed');
  });

  // Testing using select2
  $('#inputClient').select2({
    tags: true
  });
});

function showModal(selector) {
  $(selector).fadeIn('slow');
}

function closeModal(selector) {
  $(selector).fadeOut('slow');
}
