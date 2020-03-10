$(document).ready(function () {

  $('#navCollapse').on('click', function () {
    $('#sidebar').toggleClass('hidden');
  });

  $('.modal').on('hidden.bs.modal', function () {
    $(this).find('form').trigger('reset');
  });
});


