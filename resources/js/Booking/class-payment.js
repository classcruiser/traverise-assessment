let emailAddress = '';
let currency = '';
let stripeElements;
let checkoutButton = $('.creditcard-cc-button');

// STRIPE
checkoutButton.on('click', function(e) {
    e.preventDefault();
    var $this = $(this);
    let $container = $('#stripe_cs');
    let cs = $container.attr('data-value');
    const stripeForm = $('#payment-form');

    stripeForm.addClass('block').removeClass('hidden');

    if (cs != '') {
        return false;
    }

    const amount = $this.data('amount');
    const paymentLink = $this.data('payment-link');
    // Get client secret
    axios.post('/client_secret', {
        currency: 'eur',
        amount: Number.parseFloat(amount),
        payment_link: paymentLink,
        type: 'class'
    }).then(({data}) => {

        $container.attr('data-value', data.clientSecret);
        const opt = {
            clientSecret: data.clientSecret,
            appearance: {
                theme: 'stripe',
                variables: {
                    colorPrimary: '#fd5b60',
                    spacingGridRow: '14px',
                    spacingGridColumn: '14px',
                },
                rules: {
                    '.Label': {
                        marginBottom: '6px',
                        marginTop: '12px',
                        textTransform: 'uppercase',
                        fontSize: '12px'
                    },
                    '.Tab': {
                        marginTop: '12px'
                    }
                }
            }
        }
        stripeElements = stripe.elements(opt);

        const linkAuthenticationElement = stripeElements.create("linkAuthentication");
        linkAuthenticationElement.mount("#link-authentication-element");

        const paymentElementOptions = {
            layout: "tabs",
        };

        const paymentElement = stripeElements.create("payment", paymentElementOptions);
        paymentElement.mount("#payment-element");

    }).catch(err => {
        // Handle error here if needed
    })
})

// PAYPAL
paypal.Buttons({
    style: {
        layout: 'horizontal',
        color:  'blue',
        shape:  'pill',
        label:  'pay',
        height: 37
    },

    // Call your server to set up the transaction
    createOrder: function(data, actions) {
        return axios.post(route('tenant.payment.paypal.create-order'), {
            type: 'class',
            payment_link: paymentLink,
            admin_amount: paypalAmount,
        }).then(res => {
            return res.data.id;
        });
    },

    // Call your server to finalize the transaction
    onApprove: function(data, actions) {
        return axios.post(route('tenant.payment.paypal.capture-order'), {
            order_id: data.orderID,
        }).then(function(res) {
            const orderData = res.data;
            var errorDetail = Array.isArray(orderData.details) && orderData.details[0];

            if (errorDetail && errorDetail.issue === 'INSTRUMENT_DECLINED') {
                return actions.restart();
            }

            if (errorDetail) {
                var msg = 'Sorry, your transaction could not be processed.';
                if (errorDetail.description) msg += '\n\n' + errorDetail.description;
                if (orderData.debug_id) msg += ' (' + orderData.debug_id + ')';
                return alert(msg); // Show a failure message (try to avoid alerts in production environments)
            }

            actions.redirect(successUrl);
        });
    }
}).render('#paypal-button-container');

$("#payment-form").on("submit", async function (e) {
    e.preventDefault();
    setLoading(true);

    const thankYouUrl = $(this).data('success-url');
    const { error } = await stripe.confirmPayment({
        elements: stripeElements,
        confirmParams: {
            // Make sure to change this to your payment completion page
            return_url: thankYouUrl,
            receipt_email: emailAddress,
        },
    });

    if (error.type === "card_error" || error.type === "validation_error") {
        showMessage(error.message);
    } else {
        showMessage("An unexpected error occurred.");
    }

    setLoading(false);

    // Fetches the payment intent status after payment submission
    async function checkStatus() {
        const clientSecret = new URLSearchParams(window.location.search).get(
            "payment_intent_client_secret"
        );

        if (!clientSecret) {
            return;
        }

        const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);

        switch (paymentIntent.status) {
            case "succeeded":
                showMessage("Payment succeeded!");
            break;
            case "processing":
                showMessage("Your payment is processing.");
            break;
            case "requires_payment_method":
                showMessage("Your payment was not successful, please try again.");
            break;
            default:
                showMessage("Something went wrong.");
            break;
        }
    }

    checkStatus();

    // ------- UI helpers -------
    function showMessage(messageText) {
        const messageContainer = document.querySelector("#payment-message");
        messageContainer.classList.remove("hidden");
        messageContainer.textContent = messageText;

        setTimeout(function () {
            messageContainer.classList.add("hidden");
            messageText.textContent = "";
        }, 4000);
    }

    // Show a spinner on payment submission
    function setLoading(isLoading) {
        if (isLoading) {
            // Disable the button and show a spinner
            document.querySelector("#submit").disabled = true;
            document.querySelector("#spinner").classList.remove("hidden");
            document.querySelector("#button-text").classList.add("hidden");
        } else {
            document.querySelector("#submit").disabled = false;
            document.querySelector("#spinner").classList.add("hidden");
            document.querySelector("#button-text").classList.remove("hidden");
        }
    }
});

$(".payment-link").on("click", function(e) {
    var $el = $(this);
    var type = $el.data("value");

    $(".payment-link").removeClass("active");
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
        $('.hideable').addClass('hidden');
    } else {
        $('.hideable').removeClass('hidden');
    }
    $('#payment-type').val(paymentType);
});

$(".transfer-submit-button").on("click", function(e) {
    e.preventDefault();

    let formData = new FormData($("#transfer-form")[0]);
    //const type = $('#payment-type').val();
    //const url = type == 'banktransfer' ? 'bank-transfer' : 'transferwise';
    const url = $("#transfer-form").attr('action');
    const thankYouUrl = $(this).data('success-url');

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
        .post(url, formData)
        .then(res => {
            window.alert("Your transfer confirmation has been submitted");
            $(this)
                .html("Submit Confirmation")
                .attr("disabled", false);

            window.location.href = thankYouUrl;
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
