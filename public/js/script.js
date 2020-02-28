$(document).ready(function () {

  $('#navCollapse').on('click', function () {
    $('#sidebar').toggleClass('hidden');
  });
});

$(document).ready(function () {
  $('#invoiceCheckbox').click(function () {
    if (this.checked) {
      $('#invoiceDropdown').show();
    }
    else { $('#invoiceDropdown').hide(); }
  });
});


