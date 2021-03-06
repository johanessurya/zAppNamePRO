$(document).ready(function() {
  // Auto open tab
  // $('#tabs-log').trigger('click');

  var timerId = setInterval(function() {
    var el = $('#' + $('#daterange-btn').attr('data-type'));

    if(!el.hasClass('active'))
      el.addClass("active");
    else
      clearInterval(timerId);
  }, 500);

  // When graph min button clicked
  $('.min-max').click(function() {
    var $scope = {};
    $scope.el = $(this);
    $scope.i = $scope.el.find('i');

    switch($scope.el.attr('data-type')) {
      // For graph
      case 'graph':
        if($scope.i.hasClass('fa-plus')) {
          // fa-plus
          $scope.title = $('#title-h1').html();

          $('#graph-title').html($scope.title);
        } else {
          // fa-minus
          $('#graph-title').html('Graph');
        }

        break;

      // For table
      case 'table':
        if($scope.i.hasClass('fa-plus')) {
          // fa-plus
          $('#table-title').html('Activities Found');
        } else {
          // fa-minus
          $('#table-title').html('Data');
        }

        break;
    }
  });

  // Init pie chart
  $global.initPieChart();

  // Init CKEditor
  $global.initCKEditor();

  // Hanlde all event and action
  $global.dateTimePicker();

  $global.reportInit();
});
