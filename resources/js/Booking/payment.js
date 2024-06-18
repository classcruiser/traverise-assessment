require("./bootstrap");

$(".payment--link").on('click', function(e) {
    var $el = $(this);
    var type = $el.data("value");
    var $total = $("#grand-total");
    var currentTotal = $total.data("original");
    $(".payment--link").removeClass("active");
    $(".info-box").hide();
    $el.addClass("active");
    $(".info-" + type).fadeIn();

    if (type == 'transfer') {
        $('.hide-for-bank').addClass('hidden');
        $('.hide-for-cc').removeClass('hidden');
    } else {
        $('.hide-for-bank').removeClass('hidden');
        $('.hide-for-cc').addClass('hidden');
    }
});

$(".payment-transfer").on("click", function(e) {
    e.preventDefault();
    const paymentType = $(this).data('type');
    if (paymentType == 'transferwise') {
        $('.hideable').addClass('d-none');
    } else {
        $('.hideable').removeClass('d-none');
    }
    $('#payment-type').val(paymentType);
});

$(".transfer-submit-button").on("click", function(e) {
    e.preventDefault();

    let formData = new FormData($("#transfer-form")[0]);
    const type = $('#payment-type').val();
    const url = type == 'banktransfer' ? 'bank-transfer' : 'transferwise';

    $(this)
        .html('<i class="fal fa-spinner-third fa-spin"></i>')
        .attr("disabled", true);

    $(".is-error")
        .text("")
        .parent()
        .parent()
        .find("input")
        .removeClass("is-error");

    axios
        .post("/payment/" + url, formData)
        .then(res => {
            window.alert("Your transfer confirmation has been submitted");
            $(this)
                .html("Submit Confirmation")
                .attr("disabled", false);
            $("body").removeClass("popup-open");
            $(".transfer-popup").removeClass("transfer-popup-active");
            window.location.href = "/payment/bank-transfer/thank-you";
        })
        .catch(err => {
            var error = err.response.data;
            var errors = error.errors;
            if (errors) {
                for (var prop in errors) {
                    if (errors.hasOwnProperty(prop)) {
                        $(".err-" + prop)
                            .text(errors[prop][0])
                            .parent()
                            .parent()
                            .find("input")
                            .addClass("is-error");
                    }
                }
            }
            $(this)
                .html("Submit Confirmation")
                .attr("disabled", false);
        });
});
