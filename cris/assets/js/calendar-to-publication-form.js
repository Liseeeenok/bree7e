$(document).ready(function () {
  $('.ui.calendar.field').calendar({
    type: 'date',
    formatter: {
      date: function (date, settings) {
        if (!date) return '';
        var day = date.getDate();
        var month = date.getMonth() + 1;
        var year = date.getFullYear();
        return day + '.' + month + '.' + year;
      }
    }
  });
});
