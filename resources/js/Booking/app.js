require("./bootstrap");
import axios from 'axios';
import Splide from '@splidejs/splide';
import '@splidejs/splide/css';

function initTippy() {
    tippy('.tippy', {
        content: 'Tooltip',
        arrow: true,
        allowHTML: true,
    })
};

/**
 * BLOCK ELEMENT
 */
function blockElement(el) {
    $(el).block({
        message: '<i class="icon-spinner10 spinner"></i>',
        overlayCSS: {
            backgroundColor: "#fff",
            opacity: 0.8,
            cursor: "wait"
        },
        css: {
            border: 0,
            padding: 0,
            backgroundColor: "none"
        }
    });
}

$(".confirm-dialog").on("click", function(e) {
    e.preventDefault();
    const dialogText = $(this).data('text');
    swal({
        title: "Hold up",
        text: dialogText,
        icon: "warning",
        dangerMode: true,
        buttons: ["No", "Yes, continue"]
    }).then(confirmed => {
        if (confirmed) {
            window.location.href = $(this).attr("href");
        }
    });
});

/**
 * UNBLOCK ELEMENT
 */
function unblockElement(el) {
    $(el).unblock();
}

$(function() {
    //var sticky = new Sticky('.sidebar-stick');
    //$('.sidebar-stick').on('sticky-bottom-reached', function() { $('.sidebar-stick').unstick() });
    $('.form-check-input-styled').uniform();
    $('.form-check-input-styled-danger').uniform({
        wrapperClass: 'border-danger-600 text-danger-800'
    });
    $('#location-select').on('change', function(e) {
        e.preventDefault();
        const selected = e.target.value;

        $('.cover-image').hide();
        $('.cover-' + selected).fadeIn();
    });

    if ($('.splide').length) {
        $('.splide').each(function (index, el) {
            new Splide(el, {
                type: 'loop',
                perpage: 1,
                autoplay: true,
                interval: 3000,
                items: [],
            }).mount();
        })
    }

    $('.daterange-basic').daterangepicker({
        autoApply: true,
        minDate: new Date(),
        locale: {
            cancelLabel: 'Clear',
            format: 'DD MMM YYYY'
        }
    });

    if ($('.check_duration').length) {
        $('.check_duration').on('click', function(e) {
            e.preventDefault();
            let btn = $(this);
            const dates = $('input[name="dates"]').val();
            const minimum = btn.data('minimum');
            const html = btn.html();

            btn.attr('disabled', true);
            btn.html('<i class="fa fa-spin fa-spinner-third fa-fw"></i>');

            axios
                .post('/book-now/check-duration', { dates, minimum })
                .then(res => {
                    const { data } = res;
                    if (!data.result) {
                        window.alert(`Please select minimum of ${minimum} nights`);
                        btn.attr('disabled', false).html(html)
                        return false;
                    } else {
                        $('form#update_dates').trigger('submit');
                    }
                })
        })
    }

    $('.guest-select').on('change', function(e) {
        const guest = parseInt(e.target.value);
        const roomID = $(this).data('id');
        const checked = $('.book-private-' + roomID).is(':checked');
        const allowPrivate = parseInt($(this).data('allow-private'));
        const data = {
            guest,
            roomID,
            privateBooking: checked
        };

        blockElement('.cover-' + roomID);

        axios
            .post('/book-now/update-room-guest', data)
            .then(res => {
                const { data } = res;
                const isAvailable = data.is_available ? '<div class="text-success">ROOM IS AVAILABLE</div>' : '<div class="text-orange">LIMITED AVAILABILITY</div>';
                $('#room-' + roomID + '-price').html('&euro;' + Math.round(data.price));
                $('#room-' + roomID + '-availability').html(isAvailable);

                if (guest > 1) {
                    $('.book-private-' + roomID).attr('checked', false);
                    $('.private-' + roomID).removeClass('private-booking-active');
                    $('.bed-type-' + roomID).addClass('bed-select-active');
                } else {
                    $('.book-private-' + roomID).attr('checked', false);
                    $('.private-' + roomID).addClass('private-booking-active');
                    $('.bed-type-' + roomID).addClass('bed-select-active');
                }

                unblockElement('.cover-' + roomID);
            })
            .catch(err => {
                console.log(err.response.data);
                unblockElement('.cover-' + roomID);
            })
    });

    $('.cancel-voucher').on('click', function(e) {
        e.preventDefault();
        const url = $('.current-url').val();

        axios
            .post('/book-now/cancel-voucher', { url })
            .then(res => {
                const { data } = res;
                window.location = data.url;
            })
    })

    $('#voucher-btn').on('click', function(e) {
        const voucher = $('#voucher').val();
        let cont = $('.voucher-container');
        let errorCont = $('#voucher-error');

        axios
            .post('/book-now/voucher', { voucher })
            .then(res => {
                const { data } = res;
                if (data.status == 'SUCCESS') {
                    // okay
                    cont.html('<p>Thanks! You have successfully applied the voucher code to your booking.</p><p><b>CODE</b>: <b class="text-custom">' + data.code + '</b></p>');
                    window.location.reload()
                } else {
                    errorCont.html('Sorry! The voucher you applied does not exist or has expired.');
                }
            })
    });

    $('#btn-sp-comment').on('click', function (e) {
        e.preventDefault();

        let html = $(this).html();
        $(this).html('<i class="fa fa-spin fa-spinner-third fa-fw"></i>');

        axios.post('/book-package/save-comment', {
            comment: $('textarea[name="comment"]').val()
        }).then(res => {
            if (res.data == 'OK') {
                $(this).html('SAVED <i class="fal fa-check-circle ml-1"></i>');
                setTimeout(() => {
                    $(this).html('SAVE');
                }, 2000);
            }
        })
    })

    $('#btn-comment').on('click', function (e) {
        e.preventDefault();

        let html = $(this).html();
        $(this).html('<i class="fa fa-spin fa-spinner-third fa-fw"></i>');

        axios.post('/book-now/save-comment', {
            comment: $('textarea[name="comment"]').val()
        }).then(res => {
            if (res.data == 'OK') {
                $(this).html('SAVED <i class="fal fa-check-circle ml-1"></i>');
                setTimeout(() => {
                    $(this).html('SAVE');
                }, 2000);
            }
        })
    })

    $('.book-as-private').on('change', function(e) {
        const checked = $(this).is(':checked');
        const roomID = $(this).data('id');
        const step = parseInt($(this).data('step'));
        const allowPrivate = parseInt($(this).data('allow-private'));
        const bedType = $('.bed_type').val();
        const roomType = $(this).data('room-type');
        const guest = $(this).data('guest');
        const data = {
            guest: 1,
            roomID,
            privateBooking: checked,
            bedType: bedType
        };
        let defaultBed = '';

        blockElement('.cover-' + roomID);

        axios
            .post('/book-now/update-room-guest', data)
            .then(res => {
                const { data } = res;
                const isAvailable = data.is_available ? '<div class="text-success">ROOM IS AVAILABLE</div>' : '<div class="text-orange">LIMITED AVAILABILITY</div>';
                $('#room-' + roomID + '-price').html('&euro;' + Math.round(data.price));
                $('#room-' + roomID + '-availability').html(isAvailable);

                if (checked && allowPrivate) {
                    if (guest > 1) {
                        $('.bed-type-' + roomID).addClass('bed-select-active');
                    }

                    defaultBed = 'Double';
                    $('.bed-type-' + roomID).val(defaultBed);
                    $('.room-accommodation-' + roomID).text('PRIVATE');
                } else {
                    defaultBed = 'Twin';
                    $('.bed-type-' + roomID).removeClass('bed-select-active');
                    $('.bed-type-' + roomID).val(defaultBed);
                    $('.room-accommodation-' + roomID).text('SHARED WITH STRANGER');
                }

                if (step == 3) {
                    $('#bed-type').text(defaultBed);
                    $('#roomprice-info').html(res.data.basic_price_text);
                    if (checked) {
                        $('#surcharge-info').html('<br />' + res.data.surcharge_text);
                    } else {
                        $('#surcharge-info').html('&nbsp;');
                    }
                    $('#roomprice-total').html(res.data.price_text);
                    $('#grand-total').html('<b>' + res.data.grand_total + '</b>');
                }

                unblockElement('.cover-' + roomID);
            })
            .catch(err => {
                console.log(err);
                unblockElement('.cover-' + roomID);
            })
    });

    $('.bed_type').on('change', function(e) {
        const val = e.target.value;
        const data = { bed: val }
        const bedTypeEl = $('#bed-type');
        axios.post('/book-now/update-bed-type', data);

        if (bedTypeEl.length) {
            bedTypeEl.text(val);
        }
    });

    $(document).on('click', '.remove-addon', function(e) {
        e.preventDefault();
        const addonID = $(this).data('id');
        const origin = $('.addon-origin').val();

        blockElement('.addon-container');

        axios
            .post('/book-now/remove-addon', {
                id: addonID,
                origin
            })
            .then(res => {
                $('#addon_' + addonID).prop('checked', false);
                $.uniform.update('#addon_' + addonID);
                $('.addon-' + addonID).fadeOut(750, function() {
                    $(this).remove();
                });
                $('#grand-total').html(res.data.grand_total);
            })
            .then(() => {
                unblockElement('.addon-container');
                $(this).attr('disabled', false);
                $('.addon-select-' + addonID).attr('disabled', false);
            })
    });

    $(document).on('click', '.remove-transfer', function(e) {
        e.preventDefault();
        const transferID = $(this).data('id');
        const origin = $('.addon-origin').val();

        blockElement('.transfer-container');

        axios
            .post('/book-now/remove-transfer', {
                id: transferID,
                origin
            })
            .then(res => {
                $('#transfer_' + transferID).prop('checked', false);
                $.uniform.update('#transfer_' + transferID);
                $('.transfer-' + transferID).fadeOut(750, function() {
                    $(this).remove();
                });
                $('#grand-total').html(res.data.grand_total);
            })
            .then(() => {
                unblockElement('.transfer-container');
                $(this).attr('disabled', false);
            })
    });

    $('.addon-check').on('change', function(e) {
        const checked = $(this).is(':checked');
        const addonID = $(this).data('id');
        const guest = $('.addon-guest-' + addonID).val();
        const duration = $('.addon-duration-' + addonID).val();
        const weeks = $('.addon-weeks-' + addonID).val();
        const spDuration = $('.sp-duration').length ? $('.sp-duration').val() : 0;
        const origin = $('.addon-origin').val();
        const spSlug = $('.sp-slug').val();
        let template = ``;

        $(this).attr('disabled', true);
        blockElement('.addon-container');

        if (!checked) {
            // enable select dropdown
            $('.addon-select-' + addonID).attr('disabled', false);

            // remove from sidebar
            axios
                .post('/book-now/remove-addon', {
                    id: addonID,
                    origin
                })
                .then(res => {
                    $('.addon-' + addonID).fadeOut(750, function() {
                        $(this).remove();
                    });
                    $('#grand-total').html(res.data.grand_total);
                })
                .then(() => {
                    unblockElement('.addon-container');
                    $(this).attr('disabled', false);
                })
        } else {
            $('.addon-select-' + addonID).attr('disabled', true);

            // call server
            axios
                .post('/book-now/add-addon', {
                    id: addonID,
                    guest,
                    duration,
                    origin,
                    weeks,
                    spDuration,
                    spSlug
                })
                .then(res => {
                    const { data } = res;
                    template = `<div class="d-flex justify-content-between align-items-end py-1 addon-${data.id}">
                        <div class="mr-1 w-100">
                            <div class="font-size-lg"><b>${data.name}</b></div>
                            <div class="w-100 d-flex justify-content-between align-items-center">
                                <span>${data.unit}${data.duration}</span>
                                <span style="font-size: 1.1em;"><b>${data.price > 0 ? data.total : 'FREE'}</b></span>
                            </div>
                        </div>
                        <a href="#" title="" class="text-muted remove-addon" data-id="${data.id}"><i class="fal font-size-xl fa-times fa-fw"></i></a>
                    </div>`;
                    $('.addon-container').append(template);
                    $('#grand-total').html(data.grand_total);
                    $('#tax-info').html(data.tax_info);
                })
                .then(() => {
                    unblockElement('.addon-container');
                    $(this).attr('disabled', false);
                });
        }
    });

    $('.addon-guest, .addon-duration').on('change', function(e) {
        const addonID = $(this).data('id');
        const guest = $('.addon-guest-' + addonID).val();
        const duration = $('.addon-duration-' + addonID).val();
        const spDuration = $('.sp-duration').length ? $('.sp-duration').val() : 0;
        const origin = $('.addon-origin').val();

        axios
            .post('/book-now/addon-price', {
                id: addonID,
                guest,
                duration,
                origin,
                spDuration
            })
            .then(res => {
                const { data } = res;
                $('.price-' + addonID).html(data.price);
            })
    });

    $('.transfer-check').on('change', function(e) {
        const checked = $(this).is(':checked');
        const transferID = $(this).data('id');
        const guest = $('.transfer-guest-' + transferID).val();
        const spDuration = $('.sp-duration').length ? $('.sp-duration').val() : 0;
        const origin = $('.addon-origin').val();
        const spSlug = $('.sp-slug').val();
        let template = ``;

        $(this).attr('disabled', true);
        blockElement('.transfer-container');

        if (!checked) {
            // enable select dropdown
            $('.addon-select-' + transferID).attr('disabled', false);

            // remove from sidebar
            axios
                .post('/book-now/remove-transfer', {
                    id: transferID,
                    origin
                })
                .then(res => {
                    $('.transfer-' + transferID).fadeOut(750, function() {
                        $(this).remove();
                    });
                    $('#grand-total').html(res.data.grand_total);
                })
                .then(() => {
                    unblockElement('.transfer-container');
                    $(this).attr('disabled', false);
                })
        } else {
            $('.transfer-select-' + transferID).attr('disabled', true);

            // call server
            axios
                .post('/book-now/add-transfer', {
                    id: transferID,
                    guest,
                    origin,
                    spDuration,
                    spSlug
                })
                .then(res => {
                    const { data } = res;
                    template = `<div class="d-flex justify-content-between align-items-end py-1 transfer-${data.id}">
                        <div class="mr-1 w-100">
                            <div class="font-size-lg"><b>${data.name}</b></div>
                            <div class="w-100 d-flex justify-content-between align-items-center">
                                <span>${data.guest}</span>
                                <span class="text-custom" style="font-size: 1.1em;"><b>${data.price > 0 ? data.total : 'FREE'}</b></span>
                            </div>
                        </div>
                        <a href="#" title="" class="remove-transfer text-muted" data-id="${data.id}"><i class="fal font-size-xl fa-times fa-fw"></i></a>
                    </div>`;
                    $('.transfer-container').append(template);
                    $('#grand-total').html(data.grand_total);
                })
                .then(() => {
                    unblockElement('.transfer-container');
                    $(this).attr('disabled', false);
                });
        }
    });

    $('.transfer-guest').on('change', function(e) {
        const transferID = $(this).data('id');
        const guest = $('.transfer-guest-' + transferID).val();
        const origin = $('.addon-origin').val();

        axios
            .post('/book-now/transfer-price', {
                id: transferID,
                guest,
                origin
            })
            .then(res => {
                const { data } = res;
                $('.transfer-price-' + transferID).html(data.price);
            })
    });

    if ($('.form-check-emails').length) {
        $('button[type="submit"]').on('click', function (e) {
            e.preventDefault();
            let arr = [];

            $('.form-check-emails').each(function (index, el) {
                let value = $(el).val();
                if (value == '') {
                    return false;
                }

                arr.push(value)
            })

            if ((new Set(arr)).size !== arr.length) {
                window.alert("Please use different email address for each guest");
                return false;
            }

            $('form#details-form').submit();
        })
    }

    $('a.mobile-menu').on('click', function(e) {
        e.preventDefault();

        $('header ul').show();
    });

    $('.mobile-close a').on('click', function(e) {
        e.preventDefault();

        $('header ul').hide();
    });

    if ($('[data-popup]').length) {
        $('[data-popup]').on('click', function (e) {
            e.preventDefault();
            const url = '/api' + $(this).attr('href');

            $('#popup-wrapper').removeClass('pointer-events-none opacity-0').addClass('opacity-100');
            $('#popup-loading').removeClass('hidden');

            axios.get(url)
                .then(res => {
                    const { name, title, content } = res.data;
                    $('#popup-title').html(title);
                    $('#popup-body').html(content);
                })
                .then(() => {
                    $('#popup-loading').addClass('hidden');
                    $('#popup-content').removeClass('hidden');
                })
        })
    }

    if ($('[popup-close]').length) {
        $('[popup-close]').on('click', function (e) {
            e.preventDefault();

            $('#popup-wrapper').addClass('pointer-events-none opacity-0').removeClass('opacity-100');
            $('#popup-loading').addClass('hidden');
            $('#popup-content').addClass('hidden');

            $('#popup-title').html('');
            $('#popup-body').html('');
        })
    }
});
