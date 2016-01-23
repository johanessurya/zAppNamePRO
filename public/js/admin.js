$global = {

}
$(function () {
  // ======= PopUp Modal =========
  $('button[data-dismiss="modal"]').click(function() {
    closeModal('.modal');
  });

  // ======= Datatables =========
  // class prefix: "js-"" mean that class related to javascript method/function
  $global.editEl = '<button class="js-btn-edit btn btn-block btn-primary" onclick="showModal(\'#user-edit\');">Edit</button>';
  $global.deleteEl = '<button class="js-btn-delete btn btn-block btn-danger">Delete</button>';

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

function showModal(selector) {
  $(selector).fadeIn('slow');
}

function closeModal(selector) {
  $(selector).fadeOut('slow');
}
