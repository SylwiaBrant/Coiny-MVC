$(document).ready(function () {

  $('#navCollapse').on('click', function () {
    $('#sidebar').toggleClass('hidden');
  });

  $('#invoiceCheckbox').click(function () {
    if (this.checked) {
      $('#invoiceDropdown').show();
    }
    else { $('#invoiceDropdown').hide(); }
  });

  $('.modal').on('hidden.bs.modal', function () {
    $(this).find('form').trigger('reset');
  });
});


