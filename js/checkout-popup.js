var AbaPayway = (function (options) {
"use strict"

var _checkout = function () {
$ = jQuery.noConflict();
if ($(window).width() < 500) {
//Disable scrool in iOS
$('.aba-modal').height($(window).height() - 30);
$('html, body').css({"overflow": "hidden", "height": "0"});
$('.aba-modal-content').css({'width': $(window).width() - 25 + 'px', 'margin-left': '0'});
}

$('.dropdown-menu').hide();

$('#aba_main_modal').append('<div class="aba-loader"></div>');

$('.aba-modal-content').append('<iframe scrolling="yes" class="aba-iframe" src="" name="aba_webservice" id="aba_webservice"></iframe><span class="aba-close"><img src="https://payway-dev.ababank.com/fileadmin/templates/img/close.png"/></span>');

// Get the modal
$('#aba_main_modal').css('display', 'block');

$('#aba_main_modal').click(function () {
$(this).focus();
});

if ($('#aba_merchant_request, #respayment').submit()) {

var form = this;
$(':submit', form).attr('disabled', false);

setTimeout(function () {
$('.aba-loader').hide();
}, 5000);

$('#aba_webservice').load(function () {
$('.aba-loader').hide();
});
}

//---------Close Popup----------------------------
$(document).on("click touchstart", ".aba-close", function () {
closePopup();
});
}

var closePopup = function () {
var confirmClose = true;

confirmClose = confirm("Do you want to leave?");


if (confirmClose == true) {
if ($('#aba_main_modal').hide()) {
$('#aba_webservice').attr('src', "");
// document.location.reload(true);

if ($(window).width() < 500) {
$('html, body').css({"overflow": "auto", "height": "auto"});
//Enable scrool in iOS
}
}
location.href = encodeURI(atob($("#cancel_url").val()));
}
}

return {
checkout: _checkout
}
})();