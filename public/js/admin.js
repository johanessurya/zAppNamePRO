$global = {

}
$(function () {
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
});

function showModal(selector) {
  $(selector).fadeIn('slow');
}

function closeModal(selector) {
  $(selector).fadeOut('slow');
}
