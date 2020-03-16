function addSuccessFlash(form, message) {
  console.log('jestem we flashu');
  let alert = "<div id='successAlert' class='alert success text-center alert-dismissible fade show align-items-center' role='alert'>" +
    "<span id='message'>" + message + "</span>" +
    "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
    "<span aria-hidden='true'>&times;</span>" +
    "</button>" +
    "</div>";
  console.log($(form + ' .mainBtn'));
  $(form + ' .mainBtn').parents().eq(1).prepend(alert);
}

function addFailFlash(form, message) {
  let alert = "<div id='successAlert' class='alert success text-center alert-dismissible fade show' role='alert'>" +
    "<span id='message'>" + message + "</span>" +
    "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
    "<span aria-hidden='true'>& times;</span>" +
    "</button>" +
    "</div>";
  $(form + ' .mainBtn').prepend(alert);
}

$(document).ready(function () {

  $('#navCollapse').on('click', function () {
    $('#sidebar').toggleClass('hidden');
  });

  $('.modal').on('hidden.bs.modal', function () {
    $(this).find('form').trigger('reset');
  });

  $('.alert').on('click', 'button.close', function () {
    $('.alert').addClass('d-none');
    $('.alert #message').empty();
  })
});



