$(document).ready(function () {

  $('#navCollapse').on('click', function () {
      $('#sidebar').toggleClass('active');
  });
});

$(document).ready(function() {
  $('#invoiceCheckbox').click(function() {
      if(this.checked) {
        $('#invoiceDropdown').show();
      }
      else{$('#invoiceDropdown').hide();}
    });
});