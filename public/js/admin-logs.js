$(document).ready(function() {
  // Auto open tab
  $('#tabs-log').trigger('click');

  // Init pie chart
  $global.initPieChart();

  // Init CKEditor
  $global.initCKEditor();

  // Hanlde all event and action
  $global.dateTimePicker();

  $global.reportInit();
});
