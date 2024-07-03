require("./bootstrap");
import axios from 'axios';
// import Vue from 'vue';
import { createApp } from "vue/dist/vue.esm-bundler";
import Dropzone from 'dropzone';

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

/**
 * UNBLOCK ELEMENT
 */
function unblockElement(el) {
    $(el).unblock();
}

/**
 * REMOVE BOOKING_ROOM DATA
 **/
function removeBookingRoom(bookingRoomID) {
    axios
        .post("/bookings/remove-booking-room", {bookingRoomID})
        .then(res => {
            window.location.href = res.data.url;
        })
        .catch(err => {
            console.log(err.response.data);
        });
}

$(document).on('click', '.confirm-dialog', function (e) {
    e.preventDefault();
    const dialogText = $(this).data('text');
    swal({
        title: "Hold Up!",
        text: dialogText != '' ? dialogText : "Removing guest will remove all rooms assigned to this guest.",
        icon: "warning",
        dangerMode: true,
        buttons: ["No", "Yes, continue"]
    }).then(confirmed => {
        if (confirmed) {
            window.location.href = $(this).attr("href");
        }
    });
});

jQuery(function () {
    initTippy();

    if (document.querySelector('.dropzone-upload') !== null) {
        let dropzone = new Dropzone('.dropzone-upload', {
            uploadMultiple: true,
            parallelUploads: 10,
        });
        dropzone.on('completemultiple', file => {
            const { status, redirect } = JSON.parse(file[0].xhr.response);

            console.log(status, redirect)

            if (status == 'success') {
                window.location = redirect;
            }
        });
    }

    $(".select").select2();
    $(".select-no-search").select2({
        minimumResultsForSearch: Infinity
    });
    $(".form-check-input-styled").uniform({
        wrapperClass: "border-slate-600 text-slate-800"
    });

    if ($('[toggler]').length) {
        $('[toggler]').on('click', function (e) {
            const target = $(this).data('target');
            $(target).toggleClass('hidden');
        })
    }

    $('#mass-update-button').on('click', function (e) {
        e.preventDefault();
        const day = $('#mass-update-day').val();
        const week = $('#mass-update-week').val();
        const id = $(this).data('id');

        if (day == '' || week == '') {
            return false;
        }

        axios.post('/classes/sessions/'+ id +'/mass-update', {
            day, week
        }).then(res => {
            if (res.data.status == 'OK') {
                window.location.href = res.data.redirect;
            } else {
                window.alert(res.data.result);
            }
        })
    })

    if ($('textarea.frl').length) {
        $('textarea.frl').froalaEditor({
            charCounterCount: false,
            toolbarButtons: ['bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript', '|', 'paragraphFormat', 'align', 'formatOL', 'formatUL', 'insertLink', 'insertHR', 'insertTable', 'html'],
            tableStyles: {
                'payment-email': 'payment-email'
            },
            heightMin: 400,
            heightMax: 800
        })
    }

    if ($('textarea.frl-short').length) {
        $('textarea.frl-short').froalaEditor({
            charCounterCount: false,
            toolbarButtons: ['bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript', '|', 'paragraphFormat', 'align', 'formatOL', 'formatUL', 'insertLink', 'insertHR', 'insertTable', 'html'],
            tableStyles: {
                'payment-email': 'payment-email'
            },
            heightMin: 150,
            heightMax: 300
        })
    }

    $('.update-bank-transfer').on('click', function (e) {
        e.preventDefault();
        const $this = $(this);
        const readyState = $this.html();
        const data = new FormData($("form#bank-form")[0]);
        const campID = $('#camp_id').val();

        $this.html('<i class="fal fa-spin fa-spinner-third mr-1"></i> Loading').attr('disabled', true);

        axios
            .post("/camps/" + campID + "/bank-transfer", data)
            .then(res => {
                if (res.data.status == "success") {
                    new PNotify({
                        title: "Success",
                        text: "Bank transfer updated",
                        addclass: "alert alert-styled-left alert-arrow-left alpha-slate text-slate-800 border-slate",
                        delay: 4000
                    });
                }
            })
            .then(() => {
                $this.html(readyState).attr('disabled', false);
            });
    })

    if ($('.sortable').length) {
        const url = $('.sortable').data('url');
        const sortable = new Draggable.Sortable(document.querySelectorAll('.sortable tbody'), {
            draggable: 'tr',
            handle: 'span.handler'
        });
        sortable.on('drag:stopped', () => {
            let ids = [];
            $('.sortable tbody tr').each(function () {
                ids.push($(this).data('id'));
            })

            axios.post(url, {
                data: ids
            })
        })
    }

    function iconFormat(icon) {
        var originalOption = icon.element;
        if (!icon.id) {
            return icon.text;
        }
        var $icon = '<i class="icon-' + $(icon.element).data('icon') + '"></i>' + icon.text;

        return $icon;
    }

    $('.room-threshold').on('click', function (e) {
        e.preventDefault();

        const $this = $(this);
        const readyState = $this.html();
        const data = new FormData($("form#threshold-form")[0]);

        $this.html('<i class="fal fa-spin fa-spinner-third mr-1"></i> Loading').attr('disabled', true);

        axios
            .post("/rooms/threshold", data)
            .then(res => {
                if (res.data.status == "success") {
                    new PNotify({
                        title: "Success",
                        text: "Threshold updated",
                        addclass: "alert alert-styled-left alert-arrow-left alpha-slate text-slate-800 border-slate",
                        delay: 4000
                    });
                }
            })
            .then(() => {
                $this.html(readyState).attr('disabled', false);
            });
    })

    $('.select-icons').select2({
        templateResult: iconFormat,
        minimumResultsForSearch: Infinity,
        templateSelection: iconFormat,
        escapeMarkup: function (m) {
            return m;
        }
    });

    function initDateRange() {
        $(".date-range").daterangepicker({
            autoApply: true,
            locale: {
                format: "DD.MM.YYYY"
            },
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'This Week': [moment().startOf('week'), moment().endOf('week')],
                'Last Week': [moment().startOf('week').subtract(7, 'days'), moment().endOf('week').subtract(7, 'days')],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
                'Last Year': [moment().startOf('year').subtract(1, 'year'), moment().endOf('year').subtract(1, 'year')],
            },
            "alwaysShowCalendars": true,
        });
    }

    initDateRange();

    $(".date-range-single").daterangepicker({
        autoApply: true,
        singleDatePicker: true,
        showDropDowns: true,
        locale: {
            format: "DD.MM.YYYY"
        }
    });

    $(".date-range-simple").daterangepicker({
        autoApply: true,
        locale: {
            format: "DD.MM.YYYY"
        },
        "alwaysShowCalendars": true,
    });

    $(document).on('click', '.board-update', function (e) {
        const room_id = $(this).data('room-id');
        const transaction_id = $(this).data('transaction-id');
        const dates = $('.board-dates').val();
        const price = $('.board-price').val();
        const currentText = $(this).find('span').html();

        $(this).find('span').html('<i class="fal fa-spinner-third fa-spin mr-1"></i> Updating');
        $(this).attr('disabled', true).addClass('disabled');

        const data = {
            room_id,
            transaction_id,
            dates,
            price
        };

        axios
            .post('/inventory/update-board', data)
            .then(res => {
                const {status} = res.data;
                if (status == 'SUCCESS') {
                    $(this).removeClass('btn-danger').addClass('btn-success');
                    $(this).find('span').html('<i class="fal fa-check mr-1"></i> Updated');

                    setTimeout(() => {
                        $(this).attr('disabled', false).removeClass('disabled btn-success').addClass('btn-danger');
                        $(this).find('span').html(currentText);
                    }, 3000)
                }
            });
    });

    /**
     * DELETE BOOKIONG ROOM
     */

    $(document).on('click', '.delete-booking-room', function (e) {
        e.preventDefault();
        const bookingRoomID = $(this).data("room-id");
        swal({
            title: "Are you sure?",
            text: "Removing room will remove all addons assigned to this room.",
            icon: "warning",
            dangerMode: true,
            buttons: ["No", "Yes"]
        }).then(confirmed => {
            if (confirmed) {
                removeBookingRoom(bookingRoomID);
            }
        });
    });

    /**
     * APPROVE BOOKING
     */
    $(document).on('click', '.approve-booking', function (e) {
        e.preventDefault();

        const $this = $(this);
        const ref = $this.data('ref');
        const email = $('#form-approve-email').is(':checked');
        const auto = $('#form-approve-auto').is(':checked');
        const url = '/bookings/' + ref + '/approve-booking';

        blockElement('body');

        axios.post(url, {
            email: email,
            auto: auto
        })
            .then(res => {
                const {status, message, url} = res.data;

                if (status == 'success') {
                    window.location.href = url;
                } else if (status == 'failed') {
                    swal({
                        title: "Error",
                        text: message,
                        icon: "warning",
                        button: "Ok"
                    });
                    unblockElement('body');
                }
            }).catch(err => {
            console.log(err);
        })
    })

    /**
     * UPDATE AGENT
     */
    $('.select-agent').on('change', function (e) {
        e.preventDefault();

        const $this = $(this);
        const ref = $this.data('ref');
        const agent = e.target.value;
        const url = '/bookings/' + ref + '/update-agent';
        const data = {
            agent: agent
        };

        blockElement('.sidebar-body');

        $this.prop('disabled', true);

        axios.post(url, data)
            .then(() => {
                $this.prop('disabled', false);
                new PNotify({
                    title: "Success",
                    text: "Agent updated",
                    addclass: "alert alert-styled-left alert-arrow-left alpha-slate text-slate-800 border-slate",
                    delay: 4000
                });
                unblockElement('.sidebar-body');
            })
    });

    /**
     * UPDATE EXPIRED DATE
     */
    $('.expire-date').on('change', function (e) {
        e.preventDefault();

        const $this = $(this);
        const ref = $this.data('ref');
        const url = '/bookings/' + ref + '/update-expiry';
        const data = {
            expired_at: $this.val()
        };

        $this.prop('disabled', true);

        axios.post(url, data)
            .then(res => {
                $('.expired-at').text(res.data.date);
            })
            .then(() => {
                $this.prop('disabled', false);
                new PNotify({
                    title: "Success",
                    text: "Expiry date updated",
                    addclass: "alert alert-styled-left alert-arrow-left alpha-slate text-slate-800 border-slate",
                    delay: 4000
                });
            })
    });

    $('.tax-visibility').on('change', function (e) {
        e.preventDefault();

        const $this = $(this);
        const ref = $this.data('ref');
        const url = '/bookings/' + ref + '/update-tax-visibility';
        const data = {
            tax_visibility: $this.val()
        };

        $this.prop('disabled', true);

        axios.post(url, data)
            .then(() => {
                $this.prop('disabled', false);
                new PNotify({
                    title: "Success",
                    text: "Tax visibility updated",
                    addclass: "alert alert-styled-left alert-arrow-left alpha-slate text-slate-800 border-slate",
                    delay: 4000
                });
            })
    });

    /**
     * UPDATE DUE DATE
     */
    $('.due-date').on('change', function (e) {
        e.preventDefault();

        const $this = $(this);
        const ref = $this.data('ref');
        const url = '/bookings/' + ref + '/update-duedate';
        const data = {
            due_date: $this.val()
        };

        $this.prop('disabled', true);

        axios.post(url, data)
            .then(res => {
                $('.due-date').val(res.data.date);
            })
            .then(() => {
                $this.prop('disabled', false);
                new PNotify({
                    title: "Success",
                    text: "Deposit due date updated",
                    addclass: "alert alert-styled-left alert-arrow-left alpha-slate text-slate-800 border-slate",
                    delay: 4000
                });
            })
    });

    /**
     * GUEST SEARCH
     **/
    $(".select-remote-data").select2({
        width: 400,
        ajax: {
            url: "/guests/quick-search",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page || 1,
                    ref: $("#ref").val()
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: data.results
                };
            },
            cache: false
        },
        minimumInputLength: 3
    });

    /**
     * ADD PAYMENT - MAKE SURE IT'S NOT CLICKED TWICE
     **/
    $('.btn-add-payment').on('click', function (e) {
        const value = parseInt($('.amount-payment').val());

        if (value != '' && value > 0) {
            $(this)
                .html('<i class="fal fa-spinner-third fa-spin"></i>')
                .attr('disabled', true);
            $('#new-payment').submit();
        }
    })

    /**
     * ADD GUEST FROM SEARCH MENU
     **/
    $(".add-guest").on("click", function (e) {
        e.preventDefault();
        const email = $('[name="guest-search"]').val();
        const ref = $("#ref").val();

        if (email == "") {
            alert("please search guest first");
            return false;
        }

        axios
            .post("/bookings/" + ref + "/add-guest", {email, ref})
            .then(res => {
                const {status, url} = res.data;

                if (status == "success") {
                    window.location = url;
                }
            })
            .catch(err => {
                console.log(err);
            });
    });

    /**
     * REPLACE GUEST
     **/
    $(".replace-guest").on("click", function (e) {
        e.preventDefault();
        const id = $("#guest_id").val();
        const email = $('[name="guest-search"]').val();
        const ref = $("#ref").val();

        if (email == "") {
            alert("please search guest first");
            return false;
        }

        axios
            .post("/bookings/" + ref + "/replace-guest", {email, ref, id})
            .then(res => {
                const {status, url} = res.data;
                if (status == "success") {
                    window.location = url;
                }
            })
            .catch(err => {
                console.log(err);
            });
    });

    /**
     * NEW ROOM SEARCH
     **/
    $(".btn-room-search").on('click', function (e) {
        e.preventDefault();

        let el = $(".search-container");
        const dates = $("#room-search-dates").val();
        const location = $('[name="location"]').val();
        const action = $("#action").val();
        const id = $("#guest_id").val();
        const ref = $("#ref").val();
        const bookingID = $('#booking_id').val();
        const bookingStatus = $('#booking_status').val();
        let datePeriods = dates.split(" - ");
        const minDate = datePeriods[0];
        const maxDate = datePeriods[1];

        if (location == "") {
            swal({
                title: "Error",
                text: "Please select location.",
                icon: "warning",
                button: "Ok"
            });
            return false;
        }

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

        axios
            .post("/rooms/search", {
                dates,
                location,
                ref,
                id: bookingID,
                status: bookingStatus
            })
            .then(res => {
                const rooms = res.data[0];
                let template = ``;
                let totalRooms = _.size(rooms);

                Object.keys(rooms).map((key, index) => {
                    index += 1;
                    let sortIndex = _.findKey(rooms, ['pos', index]);
                    let r = rooms[sortIndex];

                    if (sortIndex) {

                        template += `
						<div class="card border-left-green border-left-2 mb-2">
							<div class="card-header bg-transparent">
								<h6 class="card-title">
									<a data-toggle="collapse" class="text-default collapsed" href="#room-${r.id}" aria-expanded="true">
										<span style="display: inline-block; width: 350px;">${r.name} <span class="text-muted font-size-sm">${r.room_type}</span></span>
										${window.IS_AGENT ? `` : `<span class="ml-5">Available: ${r.available_beds} beds</span>`}
									</a>
								</h6>
							</div>

							<div id="room-${r.id}" class="collapse show">
								<div class="card-body p-0">`;

                        r.rooms.map(subroom => {
                            let freeBeds = parseInt(subroom.capacity) - parseInt(subroom.occupied);
                            template += `
										<div class="mb-1 d-flex justify-content-start align-items-center py-1 px-3 border-bottom-1 border-alpha-grey" id="subroom-${
                                subroom.id
                            }">
											<div class="mr-1" style="width: 200px;">
												${subroom.name}
												${subroom.ota_reserved ? `<b style="color: #aaa; border-bottom: 1px dotted #aaa; font-size: 11px;" class="tippy" data-tippy-content="Connected to Smoobu">OTA</b>` : ''}
											</div>
											<div class="mr-1 d-flex justify-content-start align-items-center">
												<input
													type="text"
													data-room-id="${r.id}"
													data-subroom-id="${subroom.id}"
													class="form-control form-control-sm daterange-basic-limited period-${subroom.id}"
													style="width: 200px;"
													value="${dates}"
												/>
												<div class="ml-2 mr-1" style="width: 90px">
													<select class="form-control form-control-sm select bed-${subroom.id}" data-fouc data-placeholder="Bed type" data-container-css-class="select-sm" disabled>`;
                            r.bed_type.map(bed => {
                                template += `<option value="${bed}">${bed}</option>`;
                            });
                            template += `
														</select>
												</div>
												${(r.room_type == 'Shared' && r.allow_private && subroom.occupied <= 0) || (r.force_private && subroom.occupied <= 0 || r.allow_private)
                                ? `<input
													type="checkbox"
													name="private_${subroom.id}"
													class="ml-2 mr-1 book-private book-private-${subroom.id}"
													data-room-id="${r.id}"
													data-subroom-id="${subroom.id}"
													${!freeBeds ? "disabled" : ""}
													> <i class="fa fa-lock tippy" data-tippy-content="Book as private"></i>`
                                : ""}
											</div>

											<div class="mr-3 ml-auto" style="width: 180px;">
												<div class="occupancy-bar-wrapper occupancy-bar-${subroom.id}">`;
                            Object.keys(subroom.occupancy_bar).map((skey, sindex) => {
                                let b = subroom.occupancy_bar[skey];

                                template += `
														<div class="occupancy-bar tippy" style="width: calc(${b.length}/${r.duration} * 100%)" data-tippy-content="${b.from} to ${b.to}<br />${b.text}">
															<div class="occupancy-bar-line ${b.color_code}"></div>
														</div>`;
                            });
                            template += `
												</div>
											</div>

											<div class="d-flex justify-content-start align-items-center subroom-price-${subroom.id}">
												<div class="input-group input-group-sm tippy" data-tippy-content="&euro;${r.average_price} average per night.<br />${r.duration} nights total.">
													<span class="input-group-prepend">
														<span class="input-group-text">&euro;</span>
													</span>
													<input type="text" class="form-control form-control-sm price-${subroom.id}" ${window.IS_AGENT ? 'readonly' : ''} value="${r.final_price}" style="width: 80px;">
													<button
														${!freeBeds ? "disabled" : ""}
														class="btn btn-sm bg-grey-600 ml-1 ${action == "new" ? "guest-add-room" : "guest-replace-room"} ${!freeBeds ? "disabled" : ""}"
														data-subroom-id="${subroom.id}"
														data-ref="${ref}"
														data-id="${id}"
														data-room-id="${r.id}"
														data-beds="${JSON.stringify(subroom.occupied_beds)}"
														data-final-price="${r.final_price}"
														data-normal-price="${r.price}"
														data-free-beds="${freeBeds}"
														data-duration-price="${r.duration_discount}"
													>
														<i class="font-size-sm ${action == "new" ? "icon-plus22" : "icon-loop"}"></i>
													</button>
												</div>
											</div>
										</div>`;
                        });

                        template += `
								</div>
							</div>
						</div>`;
                    }
                });

                template = `<div class="card-group-control card-group-control-right">${template}</div>`;

                $("#search-result").html(template);
                $(".select").select2({
                    minimumResultsForSearch: Infinity
                });

                $(".daterange-basic-limited").daterangepicker({
                    autoApply: true,
                    minDate: minDate,
                    maxDate: maxDate,
                    locale: {
                        format: "DD.MM.YYYY"
                    }
                });

                $(".daterange-basic-limited").on("apply.daterangepicker", function (e, picker) {
                    let $range = $(this);
                    let bookingRoomID = $("#booking_room_id").val();
                    let roomID = $range.data("room-id");
                    let subroomID = $range.data("subroom-id");
                    let startDate = picker.startDate.format("DD.MM.YYYY");
                    let endDate = picker.endDate.format("DD.MM.YYYY");
                    let privateBooking = $(`.book-private-${subroomID}`).attr(':checked');
                    let data = {
                        roomID,
                        subroomID,
                        bookingRoomID,
                        startDate,
                        endDate,
                        privateBooking,
                        ref,
                        id: bookingID,
                        status: bookingStatus
                    };

                    checkSingleRoomAvailability(data);
                });

                tippy(".tippy", {
                    content: "Tooltip",
                    animation: "fade",
                    arrow: true,
                    allowHTML: true,
                });
                $(el).unblock();
            })
            .catch(err => {
                $(el).unblock();
            });
    });

    /**
     * BOOK ROOM AS PRIVATE
     */
    $(document).on('change', '.book-private', function (e) {
        const isChecked = $(this).is(':checked');
        const subroomID = $(this).data('subroom-id');
        const bookingRoomID = $("#booking_room_id").val();
        const roomID = $(this).data("room-id");
        const action = $("#action").val();

        let dates = $(`.period-${subroomID}`).val().split(' - ');
        let startDate = dates[0];
        let endDate = dates[1];

        const data = {
            bookingRoomID,
            subroomID,
            privateBooking: isChecked,
            roomID,
            startDate,
            endDate
        };

        /*
        $(`.guest-${action == "new" ? "add" : "replace"}-room`)
        .addClass("disabled")
        .attr("disabled", true);*/
        $(`.guest-${action == "new" ? "add" : "replace"}-room[data-subroom-id=${subroomID}]`)
            .addClass("disabled")
            .attr("disabled", true);

        axios
            .post('/rooms/search-subroom', data)
            .then(res => {
                const room = res.data[roomID];
                $(`.price-${subroomID}`).val(room.final_price);
                $(`.guest-${action == "new" ? "add" : "replace"}-room[data-subroom-id=${subroomID}]`)
                    .attr('data-final-price', room.final_price)
                    .attr('data-normal-price', room.price)
            })
            .then(() => {
                if (isChecked) {
                    // open dropdown to select bed
                    $(`.bed-${subroomID}`).attr('disabled', false);
                } else {
                    // close dropdown
                    $(`.bed-${subroomID}`).attr('disabled', true);
                }

                $(`.guest-${action == "new" ? "add" : "replace"}-room[data-subroom-id=${subroomID}]`)
                    .removeClass("disabled")
                    .attr("disabled", false);
            })
    });

    /**
     * CHECK SINGLE ROOM SEARCH
     **/
    function checkSingleRoomAvailability(data) {
        const {roomID, subroomID, startDate, endDate} = data;
        const id = $("#guest_id").val();
        const ref = $("#ref").val();
        const action = $("#action").val();

        $(`.guest-${action == "new" ? "add" : "replace"}-room[data-subroom-id=${subroomID}]`)
            .addClass("disabled")
            .attr("disabled", true);

        axios
            .post("/rooms/search-subroom", data)
            .then(res => {
                const room = res.data[roomID];
                const subroom = room.rooms;
                //let freeBeds = parseInt(room.capacity) - parseInt(subroom.occupied);
                let freeBeds = (subroom.can_book);

                let occupancyTemplate = ``;
                let roomPriceTemplate = ``;

                Object.keys(subroom.occupancy_bar).map((skey, sindex) => {
                    let b = subroom.occupancy_bar[skey];

                    occupancyTemplate += `
				<div
				class="occupancy-bar tippy"
				style="width: calc(${b.length}/${room.duration} * 100%)"
				data-tippy-content="${b.from} to ${b.to}<br />${b.text}"
				>
				<div class="occupancy-bar-line ${b.color_code}"></div>
				</div>`;
                });

                roomPriceTemplate = `
			<div
			class="input-group input-group-sm tippy"
			data-tippy-content="&euro;${room.average_price} average per night.<br />
			${room.duration} nights total."
			>
			<span class="input-group-prepend">
			<span class="input-group-text">&euro;</span>
			</span>
			<input
			type="text"
			class="form-control form-control-sm price-${subroom.id}"
			value="${room.final_price}"
			style="width: 80px;"
			/>
			<button
			${!freeBeds ? "disabled" : ""}
			class="btn btn-sm bg-grey-600 ml-1 ${
                    action == "new" ? "guest-add-room" : "guest-replace-room"
                } ${!freeBeds ? "disabled" : ""}"
			data-subroom-id="${subroom.id}"
			data-ref="${ref}"
			data-id="${id}"
			data-room-id="${room.id}"
			data-beds="${JSON.stringify(subroom.occupied_beds)}"
			data-final-price="${room.final_price}"
			data-normal-price="${room.price}"
			data-duration-price="${room.duration_discount}">
			<i class="font-size-sm ${action == "new" ? "icon-plus22" : "icon-loop"}"></i>
			</button>
			</div>`;

                $(`.occupancy-bar-${subroomID}`).html(occupancyTemplate);
                $(`.subroom-price-${subroomID}`).html(roomPriceTemplate);

                tippy(".tippy", {
                    content: "Tooltip",
                    animation: "fade",
                    arrow: true,
                    allowHTML: true,
                });

                return freeBeds;
            })
            .then(freeBeds => {
                if (freeBeds) {
                    $(`.guest-${action == "new" ? "add" : "replace"}-room[data-subroom-id=${subroomID}]`)
                        .removeClass("disabled")
                        .attr("disabled", false);
                }
            });
    }

    /**
     * GUEST ADD ROOM
     **/
    $(document).on("click", ".guest-add-room", function (e) {
        e.preventDefault();

        let $el = $(this);
        const id = $el.data("id");
        const ref = $el.data("ref");
        const subroomID = $el.data("subroom-id");
        const occupiedBeds = $el.data("beds");
        const location = $('[name="location"]').val();
        const dates = $(`.period-${subroomID}`).val();
        const bedType = $(`.bed-${subroomID}`).val();
        const isChecked = $(`.book-private-${subroomID}`).is(':checked');
        const price = $(`.price-${subroomID}`).val(); // price that user enter
        const normalPrice = $el.data("normal-price"); // price without duration discount
        const finalPrice = $el.data("final-price"); // price with duration discount
        const durationDiscount = $el.data("duration-price");

        $(".guest-add-room")
            .addClass("disabled")
            .attr("disabled", true);

        $el.html('<i class="font-size-sm icon-spinner10 spinner"></i>');

        axios
            .post(`/bookings/${ref}/guest/${id}/new-room`, {
                subroom: subroomID,
                occupiedBeds,
                dates,
                price,
                bedType,
                privateBooking: isChecked,
                normalPrice,
                finalPrice,
                durationDiscount,
                location
            })
            .then(res => {
                window.location = res.data.url;
            })
            .catch(err => {
                alert(err.response.data.message);
            })
            .then(() => {
                $(".guest-add-room")
                    .removeClass("disabled")
                    .attr("disabled", false);

                $el.html('<i class="font-size-sm icon-plus22"></i>');
            });
    });

    /**
     * GUEST REPLACE ROOM
     **/
    $(document).on("click", ".guest-replace-room", function (e) {
        e.preventDefault();

        let $el = $(this);
        const id = $el.data("id");
        const ref = $el.data("ref");
        const roomID = $el.data("room-id");
        const subroomID = $el.data("subroom-id");
        const bookingRoomID = parseInt($("#booking_room_id").val());
        const occupiedBeds = $el.data("beds");
        const location = $('[name="location"]').val();
        const dates = $(`.period-${subroomID}`).val();
        const bedType = $(`.bed-${subroomID}`).val();
        const isChecked = $(`.book-private-${subroomID}`).is(':checked');
        const durationDiscount = $el.data("duration-price");
        const keepOldPrice = $("#keep-price").is(":checked");
        const price = keepOldPrice ? $("#current-room-price").val() : $(`.price-${subroomID}`).val();
        const normalPrice = $el.data("normal-price"); // price without duration discount
        const finalPrice = $el.data("final-price"); // price with duration discount

        $(".guest-replace-room")
            .addClass("disabled")
            .attr("disabled", true);

        $el.html('<i class="font-size-sm icon-spinner10 spinner"></i>');

        axios
            .post(`/bookings/${ref}/guest/${id}/rooms/${bookingRoomID}`, {
                roomID: roomID,
                subroomID: subroomID,
                bookingRoomID: bookingRoomID,
                occupiedBeds,
                dates,
                price,
                bedType,
                privateBooking: isChecked,
                keepOldPrice,
                normalPrice,
                finalPrice,
                durationDiscount,
                location
            })
            .then(res => {
                window.location = res.data.url;
            })
            .catch(err => {
                alert(err.response.data.message);
            })
            .then(() => {
                $(".guest-replace-room")
                    .removeClass("disabled")
                    .attr("disabled", false);

                $el.html('<i class="font-size-sm icon-loop"></i>');
            });
    });

    /**
     * UPDATE ROOM PRICE & ADDONS PRICE
     **/
    $(".btn-room-price").on("click", function (e) {
        e.preventDefault();

        const el = $(".room-details");

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

        // ajax
        let data = new FormData($("form#room-details")[0]);
        axios.post("/bookings/update-room-price", data).then(res => {
            if (res.data == "OK") {
                $(el).unblock();
                new PNotify({
                    title: "Success",
                    text: "Room updated",
                    addclass: "alert alert-styled-left alert-arrow-left alpha-slate text-slate-800 border-slate",
                    delay: 4000
                });
            }
        });
    });

    /**
     * UPDATE BOOKING OVERVIEW PRICE
     **/
    $(".btn-booking-price").on("click", function (e) {
        e.preventDefault();

        const el = $(".booking-details");
        const ref = window.bookingRef;

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

        // ajax
        let data = new FormData($("form#booking-details")[0]);
        axios.post("/bookings/" + ref, data).then(res => {
            if (res.data == "OK") {
                $(el).unblock();
                window.location.href = '/bookings/' + ref + '?' + Math.random();
            }
        });
    });

    /**
     * UPDATE BOOKING QUESTIONNAIRE ANSWERS
     **/
    $(".update-answer").on("click", function (e) {
        let answers = $(this).data('questionnaire_answers');
        let addon_id = $(this).data('addon_id');
        let user_answers = $(this).data('user_answers');
        let type = $(this).data('type');

        $('#answers').html('');

        if (type != 'input') {
            let select = $('<select>').attr('name', 'answers[]')
                .addClass('custom-select')
                .appendTo('#answers');

            if (type == 'checkbox') {
                select.attr('multiple', 'multiple');
            }

            for (const [answer] of Object.entries(answers)) {
                let option = $("<option>").attr('value', answer).text(answer);
                if ($.inArray(answer, user_answers) > -1) {
                    option.attr('selected', 'selected');
                }
                select.append(option);
            }
        } else {
            $('<input>').attr('name', 'answers[]')
                .addClass('form-control')
                .val(user_answers[0]).appendTo('#answers');
        }

        $('#addon_id_input').val(addon_id);

        $('#questionnaire-modal').modal('show');
    });

    /**
     * CALCULATE FLEXIBLE ADDONS PRICE
     */
    $(".addon-flexible-dd").on('change', function (e) {
        e.preventDefault();

        const $this = $(this);
        const amount = parseInt(e.target.value);
        const id = $this.data('addon');
        const data = {
            amount,
            id
        };

        $('.addon-flexible-button').addClass('disabled').attr('disaled', true);

        axios
            .post('/bookings/calculate-addon', data)
            .then(res => {
                $('#addon_price_' + id).val(res.data.price);
            })
            .then(() => {
                $('.addon-flexible-button').removeClass('disabled').attr('disaled', false);
            })
            .catch(err => {
                alert(err.response.data);
                $('.addon-flexible-button').removeClass('disabled').attr('disaled', false);
            });
    })

    /**
     * REMOVE ADDON FROM BOOKING ROOM
     */
    $('.remove-addon').on('click', function (e) {
        e.preventDefault();

        const $this = $(this);
        const addonID = parseInt($this.data('id'));
        const guestID = parseInt($('#guest_id').val());
        const bookingRoomID = parseInt($('#booking_room_id').val());
        const data = {
            addonID,
            guestID,
            bookingRoomID
        };

        swal({
            title: "Hold Up!",
            text: "Remove addon from this room ?",
            icon: "warning",
            dangerMode: true,
            buttons: ["No", "Yes, continue"]
        }).then(confirmed => {
            if (confirmed) {
                axios
                    .post('/bookings/remove-addon', data)
                    .then(res => {
                        $('.tr-addon-' + addonID).fadeOut(500, function () {
                            $(this).remove();
                        });
                        return res.data.extra_id;
                    })
                    .then(id => {
                        new PNotify({
                            title: "Success",
                            text: "Addon removed",
                            addclass: "alert alert-styled-left alert-arrow-left alpha-slate text-slate-800 border-slate",
                            delay: 4000
                        });
                    })
            }
        });
    });

    /**
     * REMOVE SPECIAL OFFER FROM BOOKING ROOM
     */
    $('.remove-offer').on('click', function (e) {
        e.preventDefault();

        const $this = $(this);
        const offerID = parseInt($this.data('id'));
        const guestID = parseInt($('#guest_id').val());
        const bookingRoomID = parseInt($('#booking_room_id').val());
        const data = {
            offerID,
            guestID,
            bookingRoomID
        };

        swal({
            title: "Hold Up!",
            text: "Remove special offer from this room ?",
            icon: "warning",
            dangerMode: true,
            buttons: ["No", "Yes, continue"]
        }).then(confirmed => {
            if (confirmed) {
                axios
                    .post('/bookings/remove-offer', data)
                    .then(res => {
                        $('.tr-offer-' + offerID).fadeOut(500, function () {
                            $(this).remove();
                        });
                    })
                    .then(() => {
                        new PNotify({
                            title: "Success",
                            text: "Special Offer removed",
                            addclass: "alert alert-styled-left alert-arrow-left alpha-slate text-slate-800 border-slate",
                            delay: 4000
                        });
                    })
            }
        });
    });

    /**
     * ADD ADDON TO BOOKING ROOM
     */
    $('.addon-button').on('click', function (e) {
        e.preventDefault();

        const $this = $(this);
        const addonID = parseInt($this.data('id'));
        const guestID = parseInt($('#guest_id').val());
        const bookingRoomID = parseInt($('#booking_room_id').val());
        const ref = $('#ref').val();
        const price = parseFloat($('#addon_price_' + addonID).val());
        const amount = parseInt($('#addon_amount_' + addonID).val());
        const weeks = parseInt($('#addon_weeks_' + addonID).val());
        const data = {
            addonID,
            guestID,
            bookingRoomID,
            price,
            amount,
            ref,
            weeks
        };

        $('.addon-flexible-button').addClass('disabled').attr('disaled', true);

        axios
            .post('/bookings/addon', data)
            .then(res => {
                window.location.href = res.data.url;
            })
            .then(() => {
                $('.addon-flexible-button').removeClass('disabled').attr('disaled', false);
            })
            .catch(err => {
                alert(err.response.data);
                $('.addon-flexible-button').removeClass('disabled').attr('disaled', false);
            })
    });

    /**
     * EDIT BLACKLIST
     */
    $('.edit-blacklist').on('click', function (e) {
        e.preventDefault();

        const $this = $(this);
        const id = $this.data('id');

        axios
            .get('/blacklist/' + id)
            .then(res => {
                const {data} = res;
                $('#list-fname').val(data.fname);
                $('#list-lname').val(data.lname);
                $('#list-email').val(data.email);
                $('#list-notes').val(data.notes);
                $('#list-id').val(data.id);

                $('#modal-edit-blacklist').modal('show');
            });
    });

    /**
     * SAVE ROOM DETAILS
     */
    $('.update-room-details').on('click', function (e) {
        e.preventDefault();

        const $this = $(this);
        const readyState = $this.html();
        const data = new FormData($("form#room-details")[0]);
        const roomID = $('#room_id').val();

        $this.html('<i class="fal fa-spin fa-spinner-third mr-1"></i> Loading').attr('disabled', true);

        axios
            .post("/rooms/" + roomID + "/room-details", data)
            .then(res => {
                if (res.data.status == "success") {
                    new PNotify({
                        title: "Success",
                        text: "Room details updated",
                        addclass: "alert alert-styled-left alert-arrow-left alpha-slate text-slate-800 border-slate",
                        delay: 4000
                    });
                }
            })
            .then(() => {
                $this.html(readyState).attr('disabled', false);
            });

    })

    $('.new-room').on('submit', function (e) {
        e.preventDefault();

        const $this = $(this);
        const readyState = $this.html();
        const data = new FormData($("form#room-details")[0]);

        $this.html('<i class="fal fa-spin fa-spinner-third mr-1"></i> Loading').attr('disabled', true);

        axios
            .post("/rooms/new", data)
            .then(res => {
                if (res.data.status == "success") {
                    new PNotify({
                        title: "Success",
                        text: "Room details updated",
                        addclass: "alert alert-styled-left alert-arrow-left alpha-slate text-slate-800 border-slate",
                        delay: 4000
                    });
                }
                window.location.href = `/rooms/${res.data.id}#room-details`
            })
            .then(() => {
                $this.html(readyState).attr('disabled', false);
            })
            .catch(err => {
                console.log(err)
            });

    })

    /**
     * ADD PROGRESSIVE PRICING THRESHOLD
     */
    $('.add-threshold').on('click', function (e) {
        e.preventDefault();

        const $this = $(this);
        const capacity = parseInt($this.data('capacity'));
        const randomKey = Math.random().toString().substr(2, 5);

        let el = $('#progressive-pricing');
        let template = `
		<div class="py-2 border-bottom-1 border-alpha-grey" id="tmp-${randomKey}">
		<div class="d-flex justify-content-start align-items-center">
		<div style="width: 120px;" class="mr-2">
		<select class="form-control select-no-search new-progressive-select" data-fouc data-placeholder="Bed number" name="progressive_price_new[${randomKey}][beds]">`;
        for (let i = 0; i <= capacity; i++) {
            template += `<option value="${i}">${i} beds</option>`;
        }
        template += `
		</select>
		</div>
		<div>
		<div class="input-group">
		<span class="input-group-prepend">
		<span class="input-group-text"><i class="fal fa-plus fa-fw"></i></span>
		<span class="input-group-text"><i class="fal fa-percent fa-fw"></i></span>
		</span>
		<input type="text" class="form-control new-progressive-field" placeholder="0.0" name="progressive_price_new[${randomKey}][amount]" style="width: 70px"/>
		</div>
		</div>

		<a href="#" title="" class="ml-auto btn alpha-danger text-danger-800 btn-icon btn-sm delete-pp new-progressive-button" data-target="tmp-${randomKey}" data-source="tmp">
		<i class="fal fa-fw fa-times"></i>
		</a>
		</div>
		</div>`;

        el.append(template);
        $(".select-no-search").select2({
            minimumResultsForSearch: Infinity
        });
    });

    /**
     * REMOVE THRESHOLD
     */
    $(document).on('click', '.delete-pp', function (e) {
        e.preventDefault();

        const $this = $(this);
        const target = $this.data('target');
        const source = $this.data('source');
        const roomID = $this.data('room-id');
        const id = $this.data('id');

        if (source == 'database') {
            // delete form database
            axios
                .post('/rooms/' + roomID + '/remove-progressive-pricing', {id})
                .then(res => {
                    if (res.data.status == 'success') {
                        $('#' + target).fadeOut(500, function () {
                            $(this).remove();
                        });
                    }
                })
        } else {
            $('#' + target).fadeOut(500, function () {
                $(this).remove();
            });
        }
    });

    /**
     * ADD OCCUPANCY SURCHARGE THRESHOLD
     */
    $('.add-threshold-surcharge').on('click', function (e) {
        e.preventDefault();

        const $this = $(this);
        const randomKey = Math.random().toString().substr(2, 5);
        const total = $('.occupancy-fields').length;

        let el = $('#occupancy_surcharge');
        let template = `
		<div class="py-2 border-bottom-1 border-alpha-grey" id="tmp-${randomKey}">
			<div class="d-flex justify-content-start align-items-center">
				<div class="d-flex justify-content-start align-items-center" style="width: 600px;">
					<div class="input-group mr-2">
						<span class="input-group-prepend">
							<span class="input-group-text">Pax</span>
						</span>
						<input type="text" class="form-control new-occupancy_surcharge-field" placeholder="0.0" name="occupancy_price_new[${randomKey}][pax]" value="${total + 1}" style="width: 50px;"/>
					</div>
					<div class="input-group mr-2">
						<span class="input-group-prepend">
							<span class="input-group-text">Low &euro;</span>
						</span>
						<input type="text" class="form-control new-occupancy_surcharge-field" placeholder="0.0" name="occupancy_price_new[${randomKey}][amount_low]" style="width: 50px"/>
					</div>
					<div class="input-group mr-2">
						<span class="input-group-prepend">
							<span class="input-group-text">Main &euro;</span>
						</span>
						<input type="text" class="occupancy-fields form-control new-occupancy_surcharge-field" placeholder="0.0" name="occupancy_price_new[${randomKey}][amount_main]" style="width: 50px"/>
					</div>
					<div class="input-group">
						<span class="input-group-prepend">
							<span class="input-group-text">Peak &euro;</span>
						</span>
						<input type="text" class=" form-control new-occupancy_surcharge-field" placeholder="0.0" name="occupancy_price_new[${randomKey}][amount_peak]" style="width: 50px"/>
					</div>
				</div>

				<a href="#" title="" class="ml-auto btn alpha-danger text-danger-800 btn-icon btn-sm delete-op new-occupancy_surcharge-button" data-target="tmp-${randomKey}" data-source="tmp">
					<i class="fal fa-fw fa-times"></i>
				</a>
			</div>
		</div>`;

        el.append(template);
        $(".select-no-search").select2({
            minimumResultsForSearch: Infinity
        });
    });

    /**
     * REMOVE OCCUPANCY SURCHARGE THRESHOLD
     */
    $(document).on('click', '.delete-op', function (e) {
        e.preventDefault();

        const $this = $(this);
        const target = $this.data('target');
        const source = $this.data('source');
        const roomID = $this.data('room-id');
        const id = $this.data('id');

        if (source == 'database') {
            // delete form database
            axios
                .post('/rooms/' + roomID + '/remove-occupancy-pricing', {id})
                .then(res => {
                    if (res.data.status == 'success') {
                        $('#' + target).fadeOut(500, function () {
                            $(this).remove();
                        });
                    }
                })
        } else {
            $('#' + target).fadeOut(500, function () {
                $(this).remove();
            });
        }
    });

    /**
     * SAVE ROOM PRICES
     */
    $('.update-room-prices').on('click', function (e) {
        e.preventDefault();

        const $this = $(this);
        const readyState = $this.html();
        const data = new FormData($("form#room-prices")[0]);
        const roomID = $('#room_id').val();

        $this.html('<i class="fal fa-spin fa-spinner-third mr-1"></i> Loading').attr('disabled', true);

        axios
            .post("/rooms/" + roomID + "/room-prices", data)
            .then(res => {
                if (res.data.status == "success") {
                    new PNotify({
                        title: "Success",
                        text: "Room pricing updated",
                        addclass: "alert alert-styled-left alert-arrow-left alpha-slate text-slate-800 border-slate",
                        delay: 4000
                    });
                }
            })
            .then(() => {
                $('.new-progressive-select').attr('disabled', true);
                $('.new-progressive-button').addClass('tippy disabled').attr('data-tippy-content', 'Please refresh the page to make this field editable');
                $('.new-progressive-field').attr('readonly', true).addClass('tippy').attr('data-tippy-content', 'Please refresh the page to make this field editable');
                tippy('.tippy', {
                    content: 'Tooltip',
                    arrow: true,
                    allowHTML: true,
                });
                $this.html(readyState).attr('disabled', false);
            });

    });

    /**
     * OPEN MODAL TO UPDATE CALENDAR PRICE
     */
    $('.cal-action').on('click', function (e) {
        e.preventDefault();

        const $this = $(this);
        const roomID = $this.data('room-id');
        const action = $this.data('action');
        const dates = $('#cal-dates').val();

        if (!dates || dates == '') {
            swal({
                title: "Ooppss",
                text: "Please select the dates first",
                icon: "error",
                button: "Ok"
            });
        } else {
            switch (action) {
                case 'update':
                    $('#add-pricing-date-range').html(dates);
                    $('#add-pricing-dates').val(dates);
                    $('#modal_add_pricing_calendar').modal('show');
                    break;
                case 'block':
                    swal({
                        title: "Hold Up!",
                        text: "Do you want to block the selected dates ?",
                        icon: "warning",
                        dangerMode: true,
                        buttons: ["No", "Yes, continue"]
                    }).then(confirmed => {
                        if (confirmed) {
                            axios
                                .post(`/rooms/${roomID}/block-calendar-dates`, {
                                    id: roomID,
                                    message: 'BLOCK',
                                    dates
                                })
                                .then(res => {
                                    const {protocol, host, pathname, hash} = window.location;
                                    window.location.href = protocol + '//' + host + pathname + '?year=' + res.data.year + '&' + Math.random() + hash;
                                })
                        }
                    });
                    break;
                case 'full':
                    swal({
                        title: "Hold Up!",
                        text: "Do you want to set the selected dates as FULL ?",
                        icon: "warning",
                        dangerMode: true,
                        buttons: ["No", "Yes, continue"]
                    }).then(confirmed => {
                        if (confirmed) {
                            axios
                                .post(`/rooms/${roomID}/block-calendar-dates`, {
                                    id: roomID,
                                    message: 'FULL',
                                    dates
                                })
                                .then(res => {
                                    const {protocol, host, pathname, hash} = window.location;
                                    window.location.href = protocol + '//' + host + pathname + '?year=' + res.data.year + '&' + Math.random() + hash;
                                })
                        }
                    });
                    break;
                case 'restore':
                    swal({
                        title: "Hold Up!",
                        text: "Restore selected dates?",
                        icon: "warning",
                        dangerMode: true,
                        buttons: ["No", "Yes, continue"]
                    }).then(confirmed => {
                        if (confirmed) {
                            axios
                                .post(`/rooms/${roomID}/restore-calendar-dates`, {
                                    id: roomID,
                                    dates
                                })
                                .then(res => {
                                    const {protocol, host, pathname, hash} = window.location;
                                    window.location.href = protocol + '//' + host + pathname + '?year=' + res.data.year + '&' + Math.random() + hash;
                                })
                        }
                    });
                    break;
            }
        }
    });

    /**
     * WHEN SELECT YEAR DROPDOWN CHANGED
     */
    $('.pricing_calendar_year').on('change', function (e) {
        const year = e.target.value;
        const {protocol, host, pathname, hash} = window.location;
        window.location.href = protocol + '//' + host + pathname + '?year=' + year + hash;
    });

    /**
     * ADD SUBROOM
     */
    $('.add-subroom').on('click', function (e) {
        e.preventDefault();

        const $this = $(this);
        const randomKey = Math.random().toString().substr(2, 5);

        let el = $('#subrooms');
        let template = `
		<div class="py-2 border-bottom-1 border-alpha-grey" id="key-${randomKey}">
		<div class="d-flex justify-content-start align-items-center">

		<div class="mr-1">
		<div class="input-group">
		<span class="input-group-prepend">
		<span class="input-group-text">NAME</span>
		</span>
		<input type="text" class="form-control new-subroom-field" placeholder="Name" name="new_subroom[${randomKey}][name]" required />
		</div>
		</div>

		<div class="ml-auto" style="width: 115px;">
		<div class="input-group">
		<span class="input-group-prepend">
		<span class="input-group-text">BED</span>
		</span>
		<input type="number" class="form-control new-subroom-field" placeholder="1" name="new_subroom[${randomKey}][beds]" required />
		</div>
		</div>

		</div>
		</div>`;

        el.append(template);
    });

    /**
     * SAVE SUBROOMS
     */
    $('.update-room-subrooms').on('click', function (e) {
        e.preventDefault();

        const $this = $(this);
        const readyState = $this.html();
        const data = new FormData($("form#room-subrooms")[0]);
        const roomID = $('#room_id').val();

        $this.html('<i class="fal fa-spin fa-spinner-third mr-1"></i> Loading').attr('disabled', true);

        axios
            .post("/rooms/" + roomID + "/update-sub-rooms", data)
            .then(res => {
                if (res.data.status == "success") {
                    new PNotify({
                        title: "Success",
                        text: "Room list updated",
                        addclass: "alert alert-styled-left alert-arrow-left alpha-slate text-slate-800 border-slate",
                        delay: 4000
                    });
                }
            })
            .then(() => {
                $('.new-subroom-field').attr('readonly', true).addClass('tippy').attr('data-tippy-content', 'Please refresh the page to make this field editable');
                $this.html(readyState).attr('disabled', false);
                tippy('.tippy', {
                    content: 'Tooltip',
                    arrow: true,
                    allowHTML: true,
                });
            });

    });

    /**
     * SAVE PAYMENT TEMPLATE
     */
    $('.update-payment-template').on('click', function (e) {
        e.preventDefault();

        const $this = $(this);
        const readyState = $this.html();
        const data = new FormData($("form#payment-form")[0]);
        const campID = $('#camp_id').val();

        $this.html('<i class="fal fa-spin fa-spinner-third mr-1"></i> Loading').attr('disabled', true);

        axios
            .post("/camps/" + campID + "/payment-template", data)
            .then(res => {
                if (res.data.status == "success") {
                    new PNotify({
                        title: "Success",
                        text: "Payment template updated",
                        addclass: "alert alert-styled-left alert-arrow-left alpha-slate text-slate-800 border-slate",
                        delay: 4000
                    });
                }
            })
            .then(() => {
                $this.html(readyState).attr('disabled', false);
            });

    });

    /**
     * SEND CONFIRMED PAYMENT EMAIL
     */
    $('.send-confirmed-payment-email').on('click', function (e) {
        e.preventDefault();

        const $this = $(this);
        const transferID = $this.data('transfer-id');
        const index = $this.data('index');
        const readyState = $this.html();
        let url = $this.data('url');
        if (!url) {
            url = '/send-confirmed-payment-email';
        }

        $this.html('<i class="fal fa-fw fa-spin fa-spinner-third"></i>').attr('disabled', true);

        swal({
            title: "Hold Up!",
            text: "Send confirmed payment email to this guest?",
            icon: "warning",
            dangerMode: true,
            buttons: ["No", "Yes, send email"]
        }).then(confirmed => {
            if (confirmed) {
                axios
                    .post(url, {
                        id: transferID,
                        index: index
                    })
                    .then(res => {
                        if (res.data.status == "success") {
                            new PNotify({
                                title: "Success",
                                text: "Email sent",
                                addclass: "alert alert-styled-left alert-arrow-left alpha-slate text-slate-800 border-slate",
                                delay: 4000
                            });

                            $this.html(readyState).attr('disabled', false);
                        }
                    })
            } else {
                $this.html(readyState).attr('disabled', false);
            }
        });
    })

    /**
     * SAVE TERMS TEMPLATE
     */
    $('.update-terms-template').on('click', function (e) {
        e.preventDefault();

        const $this = $(this);
        const readyState = $this.html();
        const data = new FormData($("form#terms-form")[0]);
        const campID = $('#camp_id').val();

        $this.html('<i class="fal fa-spin fa-spinner-third mr-1"></i> Loading').attr('disabled', true);

        axios
            .post("/camps/" + campID + "/terms-template", data)
            .then(res => {
                if (res.data.status == "success") {
                    new PNotify({
                        title: "Success",
                        text: "Terms and Conditions template updated",
                        addclass: "alert alert-styled-left alert-arrow-left alpha-slate text-slate-800 border-slate",
                        delay: 4000
                    });
                }
            })
            .then(() => {
                $this.html(readyState).attr('disabled', false);
            });

    });

    /**
     * CREATE NEW CAMP
     */
    $('.create-new-camp').on('click', function (e) {
        e.preventDefault();

        const $this = $(this);
        const readyState = $this.html();
        const data = new FormData($("form#camp-details")[0]);
        const url = $this.data('url') + '?' + Math.random().toString().substring(2, 10);

        $this.html('<i class="fal fa-spin fa-spinner-third mr-1"></i> Loading').attr('disabled', true);

        axios
            .post(url, data)
            .then(res => {
                if (res.data.status == "success") {
                    new PNotify({
                        title: "Success",
                        text: "Camp added",
                        addclass: "alert alert-styled-left alert-arrow-left alpha-slate text-slate-800 border-slate",
                        delay: 4000
                    });
                }

                window.location.href = `/camps/${res.data.id}#general`
            })
    });

    /**
     * SAVE CAMP DETAILS
     */
    $('.update-camp-details').on('click', function (e) {
        e.preventDefault();

        const $this = $(this);
        const readyState = $this.html();
        const data = new FormData($("form#camp-details")[0]);
        const campID = $('#camp_id').val();
        const url = $this.data('url');

        $this.html('<i class="fal fa-spin fa-spinner-third mr-1"></i> Loading').attr('disabled', true);

        axios
            .post(url, data)
            .then(res => {
                if (res.data.status == "success") {
                    new PNotify({
                        title: "Success",
                        text: "General settings updated",
                        addclass: "alert alert-styled-left alert-arrow-left alpha-slate text-slate-800 border-slate",
                        delay: 4000
                    });
                }
            })
            .then(() => {
                $this.html(readyState).attr('disabled', false);
            });
    });

    /**
     * ADMIN UPDATE PAYMENT TRANSFER
     */
    $('.admin-update-payment-record').on('click', function (e) {
        e.preventDefault();

        const $this = $(this);
        const transferID = $this.data('id');
        const amount = $('.payment-record-' + transferID).val();
        const readyState = $this.html();
        let url = $this.data('url');
        if (!url) {
            url = '/update-payment-record';
        }

        $this.html('<i class="fal fa-spin fa-spinner-third"></i>').attr('disabled', true);

        axios.post(url, {
            id: transferID,
            amount: amount
        })
            .then(res => {
                if (res.data.status == "success") {
                    new PNotify({
                        title: "Success",
                        text: "Payment record updated",
                        addclass: "alert alert-styled-left alert-arrow-left alpha-slate text-slate-800 border-slate",
                        delay: 4000
                    });
                }
            })
            .then(() => {
                $this.html(readyState).attr('disabled', false);
            });
    });

    /**
     * ADMIN UPDATE REMAINING MP USAGE
     */
    $('.admin-mp-update-remaining-usage').on('click', function (e) {
        e.preventDefault();

        const $this = $(this);
        const id = $this.data('id');
        const amount = $('.mp-remaining-' + id).val();
        const readyState = $this.html();
        let url = $this.data('url');
        if (!url) {
            url = '/update-multipass-remaining';
        }

        $this.html('<i class="fal fa-spin fa-spinner-third"></i>').attr('disabled', true);

        axios.post(url, {
            id: id,
            amount: amount
        })
            .then(res => {
                if (res.data.status == "success") {
                    new PNotify({
                        title: "Success",
                        text: "Multipass remaining usage updated",
                        addclass: "alert alert-styled-left alert-arrow-left alpha-slate text-slate-800 border-slate",
                        delay: 4000
                    });
                }
            })
            .then(() => {
                $this.html(readyState).attr('disabled', false);
            });
    });

    /**
     * UPDATE SESSION PRICE & ADDONS PRICE
     **/
    $(".btn-session-price").on("click", function (e) {
        e.preventDefault();

        const el = $("#session-details");
        const url = el.attr('action');
        const $this = $(this);
        const needReload = $this.data('reload') ?? false;

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

        // ajax
        let data = new FormData($("form#session-details")[0]);
        axios.post(url, data).then(res => {
            if (res.data.status == "success") {
                $(el).unblock();
                new PNotify({
                    title: "Success",
                    text: "Session updated",
                    addclass: "alert alert-styled-left alert-arrow-left alpha-slate text-slate-800 border-slate",
                    delay: 4000
                });

                if (needReload) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            }
        });
    });

    /**
     * ADD ADDON TO BOOKING SESSION
     */
    $('.addon-session-button').on('click', function (e) {
        e.preventDefault();

        const $this = $(this);
        const addonID = parseInt($this.data('id'));
        const url = $this.data('url');
        const price = parseFloat($('#addon_price_' + addonID).val());

        $('.addon-session-button').addClass('disabled').attr('disaled', true);

        axios.post(url, {id: addonID, price}).then(res => {
            window.location.reload();
        }).catch(err => {
            $('.addon-session-button').removeClass('disabled').attr('disaled', false);
        })
    });

    /**
     * REMOVE SESSION ADDON FROM BOOKING SESSION
     */
    $('.remove-session-addon').on('click', function (e) {
        e.preventDefault();

        const $this = $(this);
        const el = $("#session-details");
        const id = parseInt($this.data('id'));
        const url = $this.data('url');

        swal({
            title: "Hold Up!",
            text: "Remove addon from this booking ?",
            icon: "warning",
            dangerMode: true,
            buttons: ["No", "Yes, continue"]
        }).then(confirmed => {
            if (confirmed) {
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

                axios.post(url, {_method: 'DELETE'}).then(res => {
                    if (res.data.status === 'success') {
                        $(el).unblock();
                        if ($('.tr-addon-' + id).length) {
                            $('.tr-addon-' + id).fadeOut(500, function () {
                                $(this).remove();
                            });
                        } else {
                            window.location.href = '/classes/bookings/' + res.data.ref
                        }

                        return res.data.id
                    }
                })
                    .then(id => {
                        new PNotify({
                            title: "Success",
                            text: "Addon removed",
                            addclass: "alert alert-styled-left alert-arrow-left alpha-slate text-slate-800 border-slate",
                            delay: 4000
                        });
                    })
            }
        });
    });

    /**
     * SESSION BOOKING CODE BELOW
     */
    $(document).on('click', '.class-cancel-booking', function (e) {
        e.preventDefault();

        const $this = $(this);
        const ref = $this.data('ref');

        swal({
            title: "Hold Up!",
            text: "Please enter cancellation reason. If no text is provided, the booking will not be cancelled.",
            content: "input",
            icon: "warning",
            dangerMode: true,
            button: {
                text: "Continue",
            }
        }).then(confirmed => {
            if (confirmed) {
                $this.addClass('disabled').attr('disaled', true);
                axios.post('/classes/bookings/' + ref + '/cancel', {message: confirmed}).then(res => {
                    if (res.data.status == "success") {
                        window.location.href = '/classes/bookings/' + ref;
                        return false;
                    }
                })
            } else {
                window.alert('Please enter cancellation reason');
            }
        });
    })

    /**
     * AUTO GENERATE MULTI PASS CODE
     */
    $('#btn-generate-passcode').on('click', function (e) {
        const el = $("#pass-code");

        axios.get('/classes/multi-pass/generate-code').then(res => {
            el.val(res.data);
        });
    });

    /**
     * AUTO GENERATE VOUCHER CODE
     */
    $('#btn-generate-voucher-code').on('click', function (e) {
        const el = $("#voucher_code");

        axios.get('/vouchers/generate-code').then(res => {
            el.val(res.data);
        });
    });

});

/**
 * VUE CODE BELOW
 */

const $tableArrivingGuest = $('.arriving-guest-container');
const $tableDepartureGuest = $('.departure-guest-container');
const $bookingPage = $('.App-bookings');
const $calculatorPage = $('.App-Calculator');

if ($tableArrivingGuest.length) {
    blockElement($tableArrivingGuest);

    const arrivingGuest = {
        data: function () {
            return {
                items: null,
                defaultArrivalDate: window.defaultArrivalDate,
                camp: "all",
                selectedBooking: null,
                form: [],
                booking: null,
                dataLoaded: false,
            }
        },
        created: function () {
            $('#modal-pickup').modal({
                show: false
            });
            this.loadTransferGuests(this.camp, this.defaultArrivalDate);
        },
        methods: {
            onFocus() {
                let vm = this;
                $("#daterange-arrival").on("apply.daterangepicker", function (e, picker) {
                    let $range = $(this);
                    let startDate = picker.startDate.format("DD.MM.YYYY");
                    let endDate = picker.endDate.format("DD.MM.YYYY");
                    vm.defaultArrivalDate = startDate + ' - ' + endDate;
                });
            },
            loadTransferGuests: function (camp, date) {
                axios
                    .get('/' + window.scheduleURL + '/arrival?camp=' + camp + '&date=' + date)
                    .then(res => {
                        this.items = res.data;
                        this.defaultArrivalDate = date;
                        this.camp = camp;
                        unblockElement($tableArrivingGuest);
                    })
                    .then(() => {
                        initTippy();
                    });
            },
            updateArrivingGuest() {
                const camp = this.camp;
                const date = this.defaultArrivalDate;
                blockElement($tableArrivingGuest);
                this.loadTransferGuests(camp, date);
            },
            loadGuests(ref) {
                this.dataLoaded = false;
                axios
                    .get('/driver-guests/' + ref)
                    .then(res => {
                        this.selectedBooking = ref;
                        this.booking = res.data;
                    })
                    .then(() => {
                        const {guests} = this.booking;
                        guests.map(guest => {
                            const data = {
                                driver: guest.driver ? guest.driver.driver_id : null,
                                notes: guest.driver ? guest.driver.notes : null
                            }
                            //this.form[guest.id] = data;
                            this.$set(this.form, guest.id, data);
                        });
                        this.dataLoaded = true;
                    })
                    .then(() => {
                        $('#modal-pickup').modal('show');
                    });
            }
        }
    }

   createApp(arrivingGuest).mount('#arriving-guest-app')
}

if ($tableDepartureGuest.length) {
    blockElement($tableDepartureGuest);
    const departureGuest = {
        data: function () {
            return {
                items: null,
                defaultDepartureDate: window.defaultDepartureDate,
                camp: "all",
            }
        },
        created: function () {
            this.loadTransferGuests(this.camp, this.defaultDepartureDate);
        },
        methods: {
            onFocus() {
                let vm = this;
                $("#daterange-departure").on("apply.daterangepicker", function (e, picker) {
                    let $range = $(this);
                    let startDate = picker.startDate.format("DD.MM.YYYY");
                    let endDate = picker.endDate.format("DD.MM.YYYY");
                    vm.defaultDepartureDate = startDate + ' - ' + endDate;
                });
            },
            loadTransferGuests: function (camp, date) {
                axios
                    .get('/' + window.scheduleURL + '/departure?camp=' + camp + '&date=' + date)
                    .then(res => {
                        this.items = res.data;
                        this.date = date;
                        this.camp = camp;
                        unblockElement($tableDepartureGuest);
                    })
                    .then(() => {
                        initTippy();
                    });
            },
            updateDepartureGuest() {
                const camp = this.camp;
                const date = this.defaultDepartureDate;
                blockElement($tableDepartureGuest);
                this.loadTransferGuests(camp, date);
            }
        }
    }

    createApp(departureGuest).mount('#departure-guest-app')
}

if ($bookingPage.length) {
    const bookingPage = {
        data: function () {
            return {
                payment: {
                    amount: 0,
                    record: null,
                    verify_button_text: 'Verify Payment',
                    resend_button: '<i class="fa fa-fw fa-envelope mr-1"></i> Send',
                    resend_loading: false,
                    resend_email: ''
                },
                notes: {
                    data: null,
                    message: '',
                    isLoading: false
                }
            }
        },
        created() {
            this.reloadNotes(window.bookingID);
        },
        methods: {
            loadPaymentRecord: function (id) {
                blockElement('body');
                axios
                    .get('/bookings/payment-record/' + id)
                    .then(res => {
                        this.payment.record = res.data;
                        this.payment.amount = !res.data.default_amount ? res.data.open_balance : res.data.default_amount;
                        $('#modal_payment_record').modal("show");
                        unblockElement('body');
                    })
            },

            closeModal: function () {
                window.location.href = window.location.href;
            },

            verifyPayment(id, index) {
                const data = {
                    amount: this.payment.amount,
                    methods: this.payment.record.methods,
                    id: id,
                    index: index,
                };
                this.payment.verify_button_text = '<i class="fal fa-fw fa-spin fa-spinner-third mr-1"></i> Please wait';
                axios
                    .post('/bookings/verify-payment', data)
                    .then(res => {
                        window.location.href = res.data.url;
                    })
            },

            cancelBooking(bookingRef) {
                const data = {
                    bookingRef: bookingRef
                }

                swal({
                    title: "Hold Up!",
                    text: "Please enter cancellation reason. If no text is provided, the booking will not be cancelled.",
                    content: "input",
                    icon: "warning",
                    dangerMode: true,
                    button: {
                        text: "Continue",
                    }
                }).then(confirmed => {
                    if (confirmed) {
                        axios
                            .post(`/bookings/${bookingRef}/cancel-booking`, {message: confirmed})
                            .then(res => {
                                window.location.href = '/bookings';
                            })
                    }
                });
            },

            resendSurfPlannerEmail(bookingRef) {
                if (!window.confirm('Send Surf Planner email with username and password to guest?')) {
                    return false;
                }

                blockElement('body');

                axios
                    .post(`/bookings/${bookingRef}/resend-surf-planner-user`)
                    .then(res => {
                        window.location.href = `/bookings/${bookingRef}`
                    })
            },

            resendConfirmation(bookingID) {
                const data = {
                    email: this.payment.resend_email,
                    id: bookingID
                };
                if (data.email == '') {
                    swal({
                        title: "Hold Up!",
                        text: "Please enter an email address",
                        icon: "warning",
                        dangerMode: false,
                        button: "OK"
                    });
                    return false;
                }
                this.payment.resend_loading = true;
                this.payment.resend_button = '<i class="fal fa-spin fa-fw fa-spinner-third mr-1"></i> Please wait';

                axios
                    .post('resend-confirmation-email', data)
                    .then(() => {
                        swal({
                            title: "Success",
                            text: `Email confirmation sent to ${this.payment.resend_email}`,
                            icon: "success",
                            dangerMode: false,
                            button: "OK"
                        });
                        this.payment.resend_loading = false;
                        this.payment.resend_button = '<i class="fa fa-fw fa-envelope mr-1"></i> Send';
                    })
            },

            sendBookingLink(bookingId) {
                swal({
                    title: "Booking link email",
                    text: "Please enter email for send booking link.",
                    content: {
                        element: "input",
                        attributes: {
                            type: "email",
                            defaultValue: window.guestEmail
                        },
                    },
                    button: {
                        text: "Send",
                    }
                }).then(email => {
                    if (!email) {
                        email = window.guestEmail;
                    }

                    let emailRegex = new RegExp(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/);

                    if (emailRegex.test(email)) {
                        let data = {
                            email,
                            'booking_id': bookingId
                        };

                        axios
                            .post('send-booking-link', data)
                            .then(() => {
                                swal({
                                    title: "Success",
                                    text: `Booking link sent to ${email}`,
                                    icon: "success",
                                    dangerMode: false,
                                    button: "OK"
                                });
                            })
                    } else {
                        swal({
                            title: "Error",
                            text: 'Please enter a valid email address',
                            icon: "error",
                            button: "Ok"
                        });
                    }
                });
            },

            reloadNotes(id) {
                axios
                    .get('internal-notes/' + id)
                    .then(res => {
                        this.notes.data = res.data;
                        unblockElement('.internal-notes');
                    })
            },

            postNote(bookingID) {
                if (this.notes.message == '') {
                    swal({
                        title: "Hold Up!",
                        text: "Please enter a message",
                        icon: "warning",
                        dangerMode: false,
                        button: "OK"
                    });
                    return false;
                }

                blockElement('.internal-notes');

                this.notes.isLoading = true;
                const data = {
                    booking_id: bookingID,
                    message: this.notes.message
                };

                axios
                    .post('internal-notes', data)
                    .then(() => {
                        this.notes.isLoading = false;
                        this.notes.message = '';
                        this.reloadNotes(bookingID);
                    })
            }
        }
    };

    createApp(bookingPage).mount('.App-bookings')
}

if ($calculatorPage.length) {
    const calculatorPage = {
        data: function () {
            return {
                camps: CAMPS_DATA,
                rooms: [],
                privateBooking: false,
                dates: '',
                camp: null,
                room: null,
                results: [],
                isLoading: false,
                allowPrivate: false
            }
        },
        computed: {
            isBlank() {
                const {dates, camp, room} = this;
                return dates == '' || !camp || !room;
            }
        },
        created() {
            let v = this;
            $('#calculator-copy').on('click', function (e) {
                $('#calculator-template').select();
                document.execCommand('copy');
                $(this).removeClass('bg-danger').addClass('bg-success').text('Copied!');

                setTimeout(() => {
                    $(this).removeClass('bg-success').addClass('bg-danger').text('Copy');
                }, 3000)
            });
            setTimeout(() => {
                $('.daterange-empty').daterangepicker({
                    autoApply: true,
                    showDropdowns: true,
                    minDate: "01/01/2018",
                    minYear: 2018,
                    maxYear: 2030,
                    autoUpdateInput: false,
                    locale: {
                        format: 'DD.MM.YYYY'
                    }
                });
                $('.daterange-empty').on('apply.daterangepicker', function (ev, picker) {
                    const dates = picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY');
                    $(this).val(dates);
                    v.dates = dates;
                });

                $('.daterange-empty').on('cancel.daterangepicker', function (ev, picker) {
                    $(this).val('');
                });

            }, 100);
        },
        methods: {
            updateRoom() {
                const camps = this.camps;
                const camp_id = this.camp;
                let rooms = camps.filter(camp => camp.id == camp_id)[0].rooms;
                rooms = rooms.filter(room => (room.id != 7 && room.id != 10 && room.id != 11));
                this.rooms = rooms;
                this.room = null
            },

            checkPrivate() {
                const {room} = this;
                const rooms = this.rooms.filter(r => r.id == room)[0];
                this.privateBooking = false;
                this.allowPrivate = parseInt(rooms.allow_private);
                setTimeout(() => {
                    $(".form-check-input-styled").uniform({
                        wrapperClass: "border-slate-600 text-slate-800"
                    });
                }, 100);
            },

            showOfferTemplate() {
                const results = this.results;
                let template = '';
                let durationDiscount = 0;
                results.forEach((result, index) => {
                    let roomType = result.private_booking ? 'Private ' : 'Shared ';
                    durationDiscount = result.duration <= 50 ? result.duration : 50;
                    let durDiscount = parseFloat(result.dur_discount);

                    template += (parseInt(index) + 1) + ". " + roomType + result.title + "\n";
                    if (this.camp == 3 || this.camp == 4) {
                        template += `The price would be ${result.total_price} EUR per person without add-ons\n\n`;
                    } else {
                        if (result.private_booking) {
                            template += `The price would be ${result.basic_price} EUR per person\n`;
                            template += `+ ${result.surcharge} EUR Single Surcharge\n\n`;
                            template += `Total room price = ${result.total_price} EUR\n\n`;
                        } else {
                            template += `The price would be ${result.basic_price} EUR per person\n\n`;
                        }
                    }
                })

                $('#calculator-template').val(template);

                $('#modal_calculator').modal('show');
            },

            removeResult(index) {
                this.results.splice(index, 1);
            },

            calculateRoom() {
                const {camps, room, privateBooking, dates} = this;
                const camp = camps.filter(camp => camp.id == this.camp)[0];
                const data = {
                    dates,
                    privateBooking,
                    roomID: room
                };
                this.isLoading = true;

                axios
                    .post("/rooms/search-room", data)
                    .then(res => {
                        const {data} = res;
                        const r = data[room];
                        const durDiscount = parseFloat(r.duration_discount).toFixed(2);
                        const surcharge = privateBooking || r.room_type == 'Private' ? parseInt(r.empty_fee) : 0;
                        const basicPrice = (parseFloat(r.price) - surcharge).toFixed(2);
                        const priceWithDurationDiscount = (basicPrice - durDiscount).toFixed(2);
                        const splitDates = dates.split(' - ');
                        let rates = [];
                        _.forOwn(r.rates, function (rate, key) {
                            rates.push({
                                'date': moment(key).format('DD.MM.YYYY'),
                                'season': rate.season,
                                'beds': rate.beds,
                                'surcharge': parseFloat(rate.surcharge).toFixed(2),
                                'price': parseFloat(rate.price).toFixed(2),
                                'progressive_price': parseFloat(rate.progressive_price).toFixed(2),
                                'subtotal': parseFloat(rate.subtotal + rate.surcharge).toFixed(2)
                            });
                        });
                        const arr = {
                            'title': `${r.name} from ${splitDates[0]} to ${splitDates[1]}`,
                            'private_booking': r.private_booking,
                            'dur_discount': durDiscount,
                            'basic_price': basicPrice,
                            'with_dur_discount': priceWithDurationDiscount,
                            'surcharge': surcharge,
                            'total_price': parseFloat(r.final_price).toFixed(2),
                            'available_beds': parseInt(r.available_beds),
                            'details': rates,
                            'duration': parseInt(r.duration),
                            'addons': data['template']['addons'],
                            'service': data['template']['service'],
                        }
                        this.results.push(arr);
                        this.isLoading = false;
                        setTimeout(() => {
                            initTippy();
                        }, 100);
                    })
                    .catch(err => {
                        this.isLoading = false;
                    })
            }
        }
    };

    createApp(calculatorPage).mount('.App-Calculator')
}
