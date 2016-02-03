$global = {};
$global.clientSource = [];
$global.user = {};
$global.misc = {};
$global.editForm = $('#edit-form-body');

// Called when description modal show
$global.initEditEvent = function() {
  console.log($('#inputCategory2')[0]);
};

$global.initClients = function(clients) {
  var $scope = {};
  $scope.clients = clients;
  $scope.temp = false;

  $.get('/api/v1/client/get').done(function(resp){
    var _li = [];

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
  for(i in _li) {
    _temp = $scope.option;
    _temp = _temp.replace(':value', _li[i].value);
    _temp = _temp.replace(':label', _li[i].label);
    $scope.jquery.append(_temp);
  }

  return true;
}

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
  for(i in _li) {
    $scope.jquery.find('option[value="' + _li[i] + '"]').attr('selected', 'selected');
  }

  return true;
}

// Refresh client list. Get ajax and regen opton list
$global.misc.refreshSelectClient = function (_li) {
  $global.misc.setOptionList('#inputClient2', _li);
  $global.misc.setOptionList('#inputClient', _li);
}
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

    for(i in _res) {
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
    var _select = $('#' + _idSubCategory)
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
    if($(this).val() == '') {
      $('#inputRepeatN').parent().hide();
    } else {
      $('#inputRepeatN').parent().show();
    }
  })

  // When selecting a client
  $('#inputClient, #inputClient2').change(function() {
    var $scope = {};

    // Get selected item;
    $scope.value = $(this).val();

    if($scope.value == null)
      return;

    $scope.lastValue = $scope.value[$scope.value.length - 1];

    // console.log($scope, parseInt($scope.lastValue));

    if(isNaN(parseInt($scope.lastValue))) {
      // Show confirm box
      $scope.confirm = confirm("Should I add this new client?");

      if($scope.confirm != true) {
        // Cancel pressed

        // Remove option with value = $scope.lastValue
        $scope.test = $(this).find('option[value="' + $scope.lastValue + '"]').remove();

        // Remove select2 tag
        $(this).parent().find('span.select2 ul li[title="' + $scope.lastValue + '"]').remove();
      }
    }
  });
});

function showModal(selector) {
  $(selector).fadeIn('slow');
}

function closeModal(selector) {
  $(selector).fadeOut('slow');
}
