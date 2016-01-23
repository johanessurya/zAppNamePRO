$global = {}
$(function () {
  $global.editEl = '<button class="btn btn-block btn-primary">Edit</button>';
  $global.deleteEl = '<button class="btn btn-block btn-danger">Delete</button>';

  $('#user-table').DataTable( {
      'ajax': '/api/v1/user',
      'columns': [
        {'data': 'username', 'searchable': true},
        {'data': 'email', 'searchable': true},
        {
          mRender: function (data, type, row) {
              return $global.editEl;
            }
        },
        {
          mRender: function (data, type, row) {
              return $global.deleteEl;
            }
        },
      ],
      'columnDefs': [{
        targets: [2,3],
        orderable: false
      }]
  } );
});
