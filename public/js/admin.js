$global = {
}
$(function () {
  // Load category tree
  $.get('/api/v1/category/get').success(function(_res) {
    $global.category = _res.category;

    // Init category. Select first item.
    $('#inputCategory').trigger('change');

    console.log('Category => ', $global.category);
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
});

function showModal(selector) {
  $(selector).fadeIn('slow');
}

function closeModal(selector) {
  $(selector).fadeOut('slow');
}
