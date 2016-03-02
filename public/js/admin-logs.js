$(document).ready(function() {
  // Auto open tab
  $('#tabs-log').trigger('click');

  var timerId = setInterval(function() {
    var el = $('#' + $('#daterange-btn').attr('data-type'));

    if(!el.hasClass('active'))
      el.addClass("active");
    else
      clearInterval(timerId);

    console.log('test');
  }, 500);

  // Init pie chart
  $global.initPieChart();

  // Init CKEditor
  $global.initCKEditor();

  // Hanlde all event and action
  $global.dateTimePicker();

  $global.reportInit();
});
