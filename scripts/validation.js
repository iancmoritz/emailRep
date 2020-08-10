// IMPORTANT! DO NOT IMPLEMENT CLIENT-SIDE VALIDATION UNLESS THE ASSIGNMENT EXPLICITLY PERMITS IT!
$(document).ready(function () {

  $("#flower_order").on("submit", function () {
    // assume the form is valid by default
    var formValid = true;

    // order's name
    if ($("#name_field").prop("validity").valid) {
      $("#name_feedback").addClass("hidden");
    } else {
      $("#name_feedback").removeClass("hidden");
      formValid = false;
    }

    // number of stems
    var roses = Number($("#roses_input").val());
    var daisies = Number($("#daisies_input").val());
    var gardenias = Number($("#gardenias_input").val());

    // Must order a minimum of 3 stems
    if ((roses + daisies + gardenias) < 3) {
      $("#stems_feedback").removeClass("hidden");
      formValid = false;
    } else {
      $("#stems_feedback").addClass("hidden");
    }

    // Tell the browser whether the form is valid (sent form data to server).
    return formValid;
  });

  $("#insurance_form").on("submit", function () {
    // assume the form is valid by default
    var formValid = true;

    // first name
    if ($("#insurance_first").prop("validity").valid) {
      $("#first_feedback").addClass("hidden");
    } else {
      $("#first_feedback").removeClass("hidden");
      formValid = false;
    }

    // last name
    if ($("#insurance_last").prop("validity").valid) {
      $("#last_feedback").addClass("hidden");
    } else {
      $("#last_feedback").removeClass("hidden");
      formValid = false;
    }

    // email
    if ($("#insurance_email").prop("validity").valid) {
      $("#email_feedback").addClass("hidden");
    } else {
      $("#email_feedback").removeClass("hidden");
      formValid = false;
    }

    // Tell the browser whether the form is valid (sent form data to server).
    return formValid;
  });

});
