<?php

declare(strict_types=1);

/**
 * HOTEL RELATED CONTROLLERS
 */

use App\Http\Controllers\API\CartController as APICartController;
use App\Http\Controllers\Booking\AddonController;
use App\Http\Controllers\Booking\AgentController;
use App\Http\Controllers\Booking\AppearanceController;
use App\Http\Controllers\Booking\AuthController;
use App\Http\Controllers\Booking\BlacklistController;
use App\Http\Controllers\Booking\BookingCancellationController;
use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\Booking\CalendarController;
use App\Http\Controllers\Booking\DashboardController;
use App\Http\Controllers\Booking\DesklineController;
use App\Http\Controllers\Booking\DocumentController;
use App\Http\Controllers\Booking\EmailController;
use App\Http\Controllers\Booking\GuestController;
use App\Http\Controllers\Booking\IndexController;
use App\Http\Controllers\Booking\LocationController;
use App\Http\Controllers\Booking\OfferController;
use App\Http\Controllers\Booking\PaymentController;
use App\Http\Controllers\Booking\PaypalController;
use App\Http\Controllers\Booking\ProfileController;
use App\Http\Controllers\Booking\QuestionnaireController;
use App\Http\Controllers\Booking\RolesController;
use App\Http\Controllers\Booking\RoomController;
use App\Http\Controllers\Booking\ScannerController;
use App\Http\Controllers\Booking\SchedulerController;
use App\Http\Controllers\Booking\SpecialPackageController;
use App\Http\Controllers\Booking\TaxesController;
use App\Http\Controllers\Booking\TransferController;
use App\Http\Controllers\Booking\UserController;
use App\Http\Controllers\Booking\VoucherController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/**
 * CLASS SESSION RELATED CONTROLLERS
 */

/** API CONTROLLERS */

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::group(['prefix' => 'api'], function () {
        Route::get('doc/{slug}', [DocumentController::class, 'APIshow'])->name('tenant.documents.api.show');
        Route::get('camps', [LocationController::class, 'indexAPI'])->name('tenant.camps.api.index');
        Route::get('booking/{ref}', [BookingController::class, 'getRef']);
        Route::get('extras', [AddonController::class, 'indexAPI']);
        Route::get('transfers', [TransferController::class, 'indexAPI']);
        Route::get('rooms/{slug}', [RoomController::class, 'roomDetails']);

        Route::prefix('cart')->group(function () {
            Route::get('/{key}', [APICartController::class, 'show']);
            Route::post('/{key}', [APICartController::class, 'store']);
            Route::delete('/{key}', [APICartController::class, 'destroy']);
            Route::put('/{key}/{id}', [APICartController::class, 'update']);
        });
    });
});

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    Route::group(['prefix' => 'auth'], function () {
        Route::get('login', [AuthController::class, 'index'])->name('tenant.login');
        Route::post('login', [AuthController::class, 'login'])->name('tenant.login.attempt');
        Route::middleware('tenant.auth:tenant')->group(function () {
            Route::get('logout', [AuthController::class, 'logout'])->name('tenant.logout');
        });
    });

    /**
     * HOTEL & CLASS SESSION RELATED PAYMENT ROUTES
     */
    Route::group(['prefix' => 'payment'], function () {
        Route::get('/bank-transfer/thank-you', [PaymentController::class, 'thankYouBank'])->name('tenant.payment.thank-you-bank');
        Route::post('/bank-transfer', [PaymentController::class, 'processBanktransfer'])->name('tenant.payment.bank-transfer');

        Route::prefix('paypal')->name('tenant.payment.paypal.')->group(function () {
            Route::post('create', [PaypalController::class, 'create'])->name('create-order');
            Route::post('capture', [PaypalController::class, 'capture'])->name('capture-order');
        });

        Route::get('/{id}/success/{session_id}', [PaymentController::class, 'successStripe'])->where('id', '[a-z0-9]{5}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{5}')->name('tenant.payment.stripe-success');
        Route::post('/{id}/stripe', [PaymentController::class, 'startStripe'])->where('id', '[a-z0-9]{5}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{5}')->name('tenant.payment.stripe');
        Route::get('/{id}/failed', [PaymentController::class, 'failed'])->where('id', '[a-z0-9]{5}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{5}')->name('tenant.payment.failed');
        Route::get('/{id}/thank-you', [PaymentController::class, 'thankYou'])->name('tenant.payment.thank-you');
        Route::get('/{id}', [PaymentController::class, 'index'])->where('id', '[a-z0-9]{5}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{5}')->name('tenant.payment.show');
    });

    Route::get('invoice/{id}', [PaymentController::class, 'invoice'])->where('id', '[a-z0-9]{5}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{5}')->name('tenant.payment.invoice');
    Route::get('doc/{slug}', [DocumentController::class, 'page'])->name('tenant.documents.page');
    Route::post('client_secret', [PaymentController::class, 'getStripeClientSecret'])->name('tenant.payment.stripe-client-secret');

    Route::group(['prefix' => 'scheduler'], function () {
        Route::get('reservation', [SchedulerController::class, 'checkReservedBookings']);
        Route::get('draft', [SchedulerController::class, 'checkDraftBookings']);
        Route::get('email-tasks', [EmailController::class, 'tasks']);
        Route::get('automated-emails', [EmailController::class, 'automated']);
    });

    Route::group(['prefix' => 'deskline'], function () {
        Route::get('/', [DesklineController::class, 'index']);
        Route::get('availability', [DesklineController::class, 'availability']);
    });

    Route::group(['prefix' => 'book-now'], function () {
        Route::get('/', [IndexController::class, 'bookNow'])->name('booknow.index');
        Route::post('/', [IndexController::class, 'selectLocation'])->name('booknow.process-location');
        Route::post('save-comment', [IndexController::class, 'saveComment']);
        Route::post('check-duration', [IndexController::class, 'checkDatesDuration']);
        Route::post('update-dates', [IndexController::class, 'updateDatesAndGuest']);
        Route::post('update-room-guest', [IndexController::class, 'updateRoomGuest']);
        Route::post('update-bed-type', [IndexController::class, 'updateBedType']);
        Route::post('rooms', [IndexController::class, 'processRooms'])->name('booknow.process-room');
        Route::get('rooms', [IndexController::class, 'selectRooms'])->name('booknow.select-room');
        Route::get('extras', [IndexController::class, 'selectExtras'])->name('booknow.select-addons');
        Route::post('extras', [IndexController::class, 'processExtras']);
        Route::get('details', [IndexController::class, 'guestDetails'])->name('booknow.guest-details');
        Route::post('details', [IndexController::class, 'saveGuestDetails']);
        Route::post('voucher', [IndexController::class, 'applyVoucher']);
        Route::post('cancel-voucher', [IndexController::class, 'cancelVoucher']);

        Route::post('remove-addon', [IndexController::class, 'removeAddon']);
        Route::post('add-addon', [IndexController::class, 'addAddon']);
        Route::post('addon-price', [IndexController::class, 'getAddonPrice']);

        Route::post('remove-transfer', [IndexController::class, 'removeTransfer']);
        Route::post('add-transfer', [IndexController::class, 'addTransfer']);
        Route::post('transfer-price', [IndexController::class, 'getTransferPrice']);

        Route::get('email-link/{hashid}', [IndexController::class, 'emailLinkRedirect'])->name('booknow.email-link-redirect');
        Route::get('confirm', [IndexController::class, 'confirmBooking']);
        Route::get('confirmed', [IndexController::class, 'refreshConfirmBooking']);
        Route::post('confirmed', [IndexController::class, 'processConfirmBooking'])->name('booknow.process-confirm');

        Route::get('finished', [IndexController::class, 'finishedBooking']);
    });

    Route::group(['prefix' => 'book-package'], function () {
        Route::post('save-comment', [SpecialPackageController::class, 'saveComment']);
        Route::get('{slug}', [SpecialPackageController::class, 'book']);
        Route::get('{slug}/details', [SpecialPackageController::class, 'guestDetails']);
        Route::get('{slug}/confirm', [SpecialPackageController::class, 'confirm']);
        Route::post('{slug}', [SpecialPackageController::class, 'book']);
        Route::post('{slug}/details', [SpecialPackageController::class, 'saveGuestDetails']);
        Route::post('{slug}/confirm', [SpecialPackageController::class, 'processConfirm']);
        Route::post('{slug}/process', [SpecialPackageController::class, 'processBook']);
    });

    Route::middleware('tenant.auth:tenant')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('tenant.dashboard');
        Route::get('schedule', [DashboardController::class, 'schedule'])->name('tenant.schedule');
        Route::post('schedule/update-driver', [DashboardController::class, 'updateDriver'])->name('tenant.updateDriver');
        Route::get('transfer-guests/{type}', [DashboardController::class, 'transferGuests'])->name('tenant.transferGuests');
        Route::get('driver-guests/{ref}', [DashboardController::class, 'driverGuests'])->name('tenant.driverGuests');
        Route::get('staying-guests/{type}', [DashboardController::class, 'stayingGuests'])->name('tenant.stayingGuests');
        Route::get('payment-report', [DashboardController::class, 'paymentReport'])->name('tenant.paymentReport');
        Route::get('update-transfers', [DashboardController::class, 'updateTransfers'])->name('tenant.updateTransfers');
        Route::post('update-payment-record', [PaymentController::class, 'updatePaymentRecord'])->name('tenant.updatePaymentRecord');
        Route::post('send-confirmed-payment-email', [PaymentController::class, 'sendConfirmedPaymentEmail'])->name('tenant.sendConfirmedPaymentEmail');
        Route::get('price-calculator', [RoomController::class, 'priceCalculatorIndex'])->name('tenant.price-calculator');
        Route::get('room-move', [DashboardController::class, 'roomMove'])->name('tenant.room-move');

        Route::group(['prefix' => 'scanner'], function () {
            Route::get('checkin', [ScannerController::class, 'checkin'])->name('tenant.scanner.checkin');
            Route::get('addons', [ScannerController::class, 'addons'])->name('tenant.scanner.addons');
        });

        Route::group(['prefix' => 'taxes'], function () {
            Route::get('/', [TaxesController::class, 'index'])->name('tenant.taxes');
            Route::get('new', [TaxesController::class, 'create'])->name('tenant.taxes.create');
            Route::post('new', [TaxesController::class, 'insert'])->name('tenant.taxes.insert');
            Route::post('sort', [TaxesController::class, 'sort'])->name('tenant.taxes.sort');
            Route::get('{id}', [TaxesController::class, 'show'])->name('tenant.taxes.show');
            Route::post('{id}', [TaxesController::class, 'update'])->name('tenant.taxes.update');
            Route::get('delete/{id}', [TaxesController::class, 'delete'])->name('tenant.taxes.delete');
        });

        Route::group(['prefix' => 'bookings', 'middleware' => ['permission:add booking|edit booking|delete booking|view booking']], function () {
            Route::get('/', [BookingController::class, 'index'])->name('tenant.bookings');
            Route::get('new', [BookingController::class, 'create'])->name('tenant.bookings.create');
            Route::get('deleted', [BookingController::class, 'trashbin'])->name('tenant.bookings.trashbin');
            Route::get('draft', [BookingController::class, 'draft'])->name('tenant.bookings.draft');
            Route::get('pending', [BookingController::class, 'pending'])->name('tenant.bookings.pending');
            Route::get('trash', [BookingController::class, 'trash'])->name('tenant.bookings.trash');
            Route::post('quick-move', [BookingController::class, 'quickRoomMove'])->name('tenant.bookings.quickRoomMove');

            Route::get('payment-record/{id}', [PaymentController::class, 'getRecord'])->name('tenant.bookings.getRecord');
            Route::post('verify-payment', [PaymentController::class, 'verifyPayment'])->name('tenant.bookings.verifyPayment');
            Route::post('resend-confirmation-email', [BookingController::class, 'resendConfirmationEmail'])->name('tenant.bookings.resendConfirmationEmail');
            Route::post('send-booking-link', [BookingController::class, 'sendBookingLink'])->name('tenant.bookings.sendBookingLink');
            Route::post('add-payment', [PaymentController::class, 'addRecord'])->name('tenant.bookings.addRecord');

            Route::get('internal-notes/{id}', [BookingController::class, 'getInternalNotes'])->name('tenant.bookings.getInternalNotes');
            Route::post('internal-notes', [BookingController::class, 'postInternalNotes'])->name('tenant.bookings.postInternalNotes');

            Route::post('addon', [BookingController::class, 'insertAddon'])->name('tenant.bookings.insertAddon');
            Route::post('remove-addon', [BookingController::class, 'removeAddon'])->name('tenant.bookings.removeAddon');
            Route::post('remove-offer', [BookingController::class, 'removeSpecialOffer'])->name('tenant.bookings.removeSpecialOffer');
            Route::post('calculate-addon', [BookingController::class, 'calculateAddon'])->name('tenant.bookings.calculateAddon');

            Route::post('update-room-price', [BookingController::class, 'updateRoomPrice'])->name('tenant.bookings.updateRoomPrice');
            Route::post('remove-booking-room', [BookingController::class, 'removeBookingRoom'])->name('tenant.bookings.removeBookingRoom');
            Route::post('update-questionnaire-answer', [BookingController::class, 'updateQuestionnaireAnswer'])->name('tenant.bookings.updateQuestionnaireAnswer');

            Route::post('check-in/accommodation', [BookingController::class, 'accommodationCheckIn'])
                ->name('tenant.bookings.check_in.accommodation');
            Route::post('check-in/addon', [BookingController::class, 'addonCheckIn'])
                ->name('tenant.bookings.check_in.addon');

            Route::get('{ref}', [BookingController::class, 'show'])->name('tenant.bookings.show')->where('ref', '[A-Z0-9]{3,4}-[A-Z0-9]{10}-[A-Z0-9]{3}');
            Route::get('{ref}/preview-email', [BookingController::class, 'previewEmail']);
            Route::get('details/{ref}', [BookingController::class, 'details'])->name('tenant.bookings.details')->where('ref', '[A-Z0-9]{3,4}-[A-Z0-9]{10}-[A-Z0-9]{3}-[A-Z0-9]+');
            Route::get('{ref}/duplicate', [BookingController::class, 'duplicate'])->name('tenant.bookings.duplicate');
            Route::get('{ref}/mailable', [BookingController::class, 'mailPreview'])->name('tenant.bookings.mailPreview');
            Route::post('{ref}/resend-surf-planner-user', [BookingController::class, 'resendSurfPlannerUser'])->name('tenant.bookings.resendSurfPlannerUser');
            Route::post('{ref}', [BookingController::class, 'updateBookingPrices'])->name('tenant.bookings.updateBookingPrices');
            Route::get('{ref}/pdf-invoice', [BookingController::class, 'downloadPDFInvoice'])->name('tenant.bookings.downloadPDFInvoice');
            Route::post('{ref}/cancel-booking', [BookingController::class, 'cancelBooking'])->name('tenant.bookings.cancelBooking');
            Route::post('{ref}/update-duedate', [BookingController::class, 'updateDuedate'])->name('tenant.bookings.updateDuedate');
            Route::post('{ref}/update-expiry', [BookingController::class, 'updateExpiry'])->name('tenant.bookings.updateExpiry');
            Route::post('{ref}/update-agent', [BookingController::class, 'updateAgent'])->name('tenant.bookings.updateAgent');
            Route::post('{ref}/update-tax-visibility', [BookingController::class, 'updateTaxVisibility'])->name('tenant.bookings.updateTaxVisibility');
            Route::post('{ref}/approve-booking', [BookingController::class, 'approveBooking'])->name('tenant.bookings.approveBooking');
            Route::get('{ref}/new-guest', [BookingController::class, 'newGuest'])->name('tenant.bookings.newGuest');
            Route::post('{ref}/new-guest', [BookingController::class, 'insertGuest'])->name('tenant.bookings.insertGuest');
            Route::get('{ref}/delete', [BookingController::class, 'deleteBooking'])->name('tenant.bookings.deleteBooking');

            Route::get('{ref}/cancellation-invoice/{cancellation_id}', [BookingCancellationController::class, 'invoice'])->name('tenant.bookings.cancellationInvoice');

            Route::get('{ref}/invoice-snapshot/{history_id}', [BookingController::class, 'downloadInvoiceSnapshot'])->name('tenant.bookings.downloadInvoiceSnapshot');
            Route::get('{ref}/invoice-payment/{payment_transfer}/{id}', [BookingController::class, 'downloadInvoicePayment'])->name('tenant.bookings.downloadInvoicePayment');

            Route::post('{ref}/add-transfer', [BookingController::class, 'addTransfer'])->name('tenant.bookings.addTransfer');
            Route::post('{ref}/edit-transfer/{booking_transfer_id}', [BookingController::class, 'updateTransfer'])->name('tenant.bookings.updateTransfer');
            Route::get('{ref}/remove-transfer/{booking_transfer_id}', [BookingController::class, 'removeTransfer'])->name('tenant.bookings.removeTransfer');

            Route::post('{ref}/add-discount', [BookingController::class, 'addDiscount'])->name('tenant.bookings.addDiscount');
            Route::post('{ref}/edit-discount/{discount_id}', [BookingController::class, 'updateDiscount'])->name('tenant.bookings.updateDiscount');
            Route::get('{ref}/remove-discount/{discount_id}', [BookingController::class, 'removeDiscount'])->name('tenant.bookings.removeDiscount');

            Route::get('{ref}/edit-guest/{booking_guest_id}', [BookingController::class, 'editGuest'])->name('tenant.bookings.editGuest');
            Route::post('{ref}/edit-guest/{booking_guest_id}', [BookingController::class, 'updateGuest'])->name('tenant.bookings.updateGuest');

            Route::post('{ref}/add-guest', [BookingController::class, 'addGuest'])->name('tenant.bookings.addGuest');
            Route::post('{ref}/replace-guest', [BookingController::class, 'replaceGuest'])->name('tenant.bookings.replaceGuest');

            Route::get('{ref}/guest/{booking_guest_id}/remove', [BookingController::class, 'removeGuest'])->name('tenant.bookings.removeGuest');
            Route::get('{ref}/guest/{booking_guest_id}/new-room', [BookingController::class, 'newGuestRoom'])->name('tenant.bookings.newGuestRoom');
            Route::post('{ref}/guest/{booking_guest_id}/new-room', [BookingController::class, 'saveGuestRoom'])->name('tenant.bookings.saveGuestRoom');

            Route::get('{ref}/guest/{booking_guest_id}/rooms/{roomid}', [BookingController::class, 'editGuestRoom'])->name('tenant.bookings.editGuestRoom');
            Route::post('{ref}/guest/{booking_guest_id}/rooms/{roomid}', [BookingController::class, 'replaceGuestRoom'])->name('tenant.bookings.replaceGuestRoom');
        });

        Route::group(['prefix' => 'calendar'], function () {
            Route::get('/', [CalendarController::class, 'list'])->name('tenant.calendar');
            Route::get('{id}', [CalendarController::class, 'index'])->name('tenant.calendar.show');
            Route::get('date-details', [CalendarController::class, 'getDateDetails'])->name('tenant.calendar.getDateDetails');
        });

        Route::group(['prefix' => 'guests', 'middleware' => ['permission:add guest|edit guest|delete guest|view guest']], function () {
            Route::get('/', [GuestController::class, 'index'])->name('tenant.guests');
            Route::get('quick-search', [GuestController::class, 'quickSearch'])->name('tenant.guests.quickSearch');
            Route::get('generate-ids', [GuestController::class, 'generateIds'])->name('tenant.guests.generateIds');
            Route::get('{id}', [GuestController::class, 'show'])->name('tenant.guests.show');
            Route::get('{type}/{ref}', [GuestController::class, 'checkGuest'])->name('tenant.guests.checkGuest');
            Route::post('{type}/{ref}', [GuestController::class, 'updateCheckGuest'])->name('tenant.guests.updateCheckGuest');
        });

        Route::group(['prefix' => 'payments'], function () {
            Route::get('/', [PaymentController::class, 'indexAdmin'])->name('tenant.payments');
            Route::get('{id}/delete/{ref}', [PaymentController::class, 'deleteRecord'])->name('tenant.payments.delete');
            Route::get('assign-invoice-number', [PaymentController::class, 'assignInvoiceNumber']);
        });

        Route::group(['prefix' => 'rooms', 'middleware' => ['permission:add room|edit room|delete room|search room']], function () {
            Route::get('/', [RoomController::class, 'index'])->name('tenant.rooms');
            Route::get('threshold', [RoomController::class, 'threshold'])->name('tenant.rooms.threshold');
            Route::post('threshold', [RoomController::class, 'updateThreshold'])->name('tenant.rooms.updateThreshold');
            Route::post('sort', [RoomController::class, 'sort'])->name('tenant.rooms.sort');
            Route::get('new', [RoomController::class, 'create'])->name('tenant.rooms.create');
            Route::post('new', [RoomController::class, 'insert'])->name('tenant.rooms.insert');
            Route::get('{id}', [RoomController::class, 'show'])->name('tenant.rooms.show');
            Route::post('{id}/upload', [RoomController::class, 'upload'])->name('tenant.rooms.upload');
            Route::post('{id}/room-details', [RoomController::class, 'updateRoomDetails'])->name('tenant.rooms.updateRoomDetails');
            Route::post('{id}/room-prices', [RoomController::class, 'updateRoomPrices'])->name('tenant.rooms.updateRoomPrices');
            Route::post('{id}/update-calendar-price', [RoomController::class, 'updateCalendarPrice'])->name('tenant.rooms.updateCalendarPrice');
            Route::post('{id}/block-calendar-dates', [RoomController::class, 'blockCalendarDates'])->name('tenant.rooms.blockCalendarDates');
            Route::post('{id}/restore-calendar-dates', [RoomController::class, 'restoreCalendarDates'])->name('tenant.rooms.restoreCalendarDates');
            Route::post('{id}/remove-progressive-pricing', [RoomController::class, 'removeProgressivePricing'])->name('tenant.rooms.removeProgressivePricing');
            Route::post('{id}/remove-occupancy-pricing', [RoomController::class, 'removeOccupancyPricing'])->name('tenant.rooms.removeOccupancyPricing');
            Route::post('{id}/update-sub-rooms', [RoomController::class, 'updateSubrooms'])->name('tenant.rooms.updateSubrooms');
            Route::get('{id}/delete', [RoomController::class, 'remove'])->name('tenant.rooms.remove');
            Route::get('{id}/delete/{subroom_id}', [RoomController::class, 'removeSubroom'])->name('tenant.rooms.removeSubroom');
            Route::get('{id}/set-main-picture/{filename}', [RoomController::class, 'setMainPicture'])->name('tenant.rooms.setMainPicture');
            Route::get('{id}/delete-picture/{filename}', [RoomController::class, 'deletePicture'])->name('tenant.rooms.deletePicture');
            Route::post('search', [RoomController::class, 'search'])->name('tenant.rooms.search');
            Route::post('search-subroom', [RoomController::class, 'searchSubroom'])->name('tenant.rooms.searchSubroom');
            Route::post('search-room', [RoomController::class, 'searchRoom'])->name('tenant.rooms.searchRoom');
        });

        Route::group(['prefix' => 'transfers', 'middleware' => ['permission:add addon|edit addon|delete addon']], function () {
            Route::get('/', [TransferController::class, 'index'])->name('tenant.transfers');
            Route::get('new', [TransferController::class, 'create'])->name('tenant.transfers.create');
            Route::post('new', [TransferController::class, 'insert'])->name('tenant.transfers.insert');
            Route::post('sort', [TransferController::class, 'sort'])->name('tenant.transfers.sort');
            Route::get('{id}', [TransferController::class, 'show'])->name('tenant.transfers.show');
            Route::post('{id}', [TransferController::class, 'update'])->name('tenant.transfers.update');
            Route::get('{id}/delete', [TransferController::class, 'remove'])->name('tenant.transfers.remove');
        });

        Route::group(['prefix' => 'roles-and-permissions', 'middleware' => ['permission:manage roles']], function () {
            Route::get('/', [RolesController::class, 'index'])->name('tenant.roles');
            Route::get('new', [RolesController::class, 'create'])->name('tenant.roles.create');
            Route::post('new', [RolesController::class, 'insert'])->name('tenant.roles.insert');
            Route::get('{id}', [RolesController::class, 'show'])->name('tenant.roles.show');
            Route::post('{id}', [RolesController::class, 'update'])->name('tenant.roles.update');
            Route::get('{id}/delete', [RolesController::class, 'delete'])->name('tenant.roles.delete');
        });

        Route::group(['prefix' => 'users', 'middleware' => ['permission:add user|edit user|delete user']], function () {
            Route::get('/', [UserController::class, 'index'])->name('tenant.users');
            Route::get('new', [UserController::class, 'create'])->name('tenant.users.create');
            Route::post('new', [UserController::class, 'insert'])->name('tenant.users.insert');
            Route::get('{id}', [UserController::class, 'show'])->name('tenant.users.show');
            Route::post('{id}', [UserController::class, 'update'])->name('tenant.users.update');
            Route::get('{id}/delete', [UserController::class, 'delete'])->name('tenant.users.delete');
        });

        Route::group(['prefix' => 'camps', 'middleware' => ['permission:add camp|edit camp']], function () {
            Route::get('/', [LocationController::class, 'index'])->name('tenant.camps');
            Route::get('new', [LocationController::class, 'create'])->name('tenant.camps.create');
            Route::post('new', [LocationController::class, 'insert'])->name('tenant.camps.insert');
            Route::get('{id}', [LocationController::class, 'show'])->name('tenant.camps.show');
            Route::get('{id}/duplicate', [LocationController::class, 'duplicate'])->name('tenant.camps.duplicate');
            Route::get('{id}/delete', [LocationController::class, 'destroy'])->name('tenant.camps.destroy');
            Route::post('{id}/upload', [LocationController::class, 'upload'])->name('tenant.camps.upload');
            Route::post('{id}/camp-details', [LocationController::class, 'updateCampDetails'])->name('tenant.camps.details');
            Route::post('{id}/terms-template', [LocationController::class, 'updateTermsTemplate'])->name('tenant.camps.terms-template');
            Route::post('{id}/bank-transfer', [LocationController::class, 'updateBankTransfer'])->name('tenant.camps.bank-transfer');
        });

        Route::group(['prefix' => 'addons', 'middleware' => ['permission:add addon|edit addon|delete addon']], function () {
            Route::get('/', [AddonController::class, 'index'])->name('tenant.addons');
            Route::get('new', [AddonController::class, 'create'])->name('tenant.addons.create');
            Route::post('sort', [AddonController::class, 'sort'])->name('tenant.addons.sort');
            Route::post('new', [AddonController::class, 'insert'])->name('tenant.addons.insert');
            Route::get('{id}', [AddonController::class, 'show'])->name('tenant.addons.show');
            Route::post('{id}', [AddonController::class, 'update'])->name('tenant.addons.update');
            Route::get('{id}/delete', [AddonController::class, 'remove'])->name('tenant.addons.remove');
        });

        Route::group(['prefix' => 'questionnaires', 'middleware' => ['permission:add addon|edit addon|delete addon']], function () {
            Route::get('/', [QuestionnaireController::class, 'index'])->name('tenant.questionnaire');
            Route::get('new', [QuestionnaireController::class, 'create'])->name('tenant.questionnaire.create');
            Route::post('new', [QuestionnaireController::class, 'insert'])->name('tenant.questionnaire.insert');
            Route::get('{questionnaire}', [QuestionnaireController::class, 'show'])->name('tenant.questionnaire.show');
            Route::post('{questionnaire}', [QuestionnaireController::class, 'update'])->name('tenant.questionnaire.update');
            Route::get('{questionnaire}/delete', [QuestionnaireController::class, 'remove'])->name('tenant.questionnaire.remove');
        });

        Route::group(['prefix' => 'blacklist', 'middleware' => ['permission:manage blacklist']], function () {
            Route::get('/', [BlacklistController::class, 'index'])->name('tenant.blacklist');
            Route::post('/', [BlacklistController::class, 'insert'])->name('tenant.blacklist.insert');
            Route::get('{id}', [BlacklistController::class, 'show'])->name('tenant.blacklist.show');
            Route::post('{id}', [BlacklistController::class, 'update'])->name('tenant.blacklist.update');
            Route::get('{id}/delete', [BlacklistController::class, 'remove'])->name('tenant.blacklist.remove');
        });

        Route::group(['prefix' => 'special-packages', 'middleware' => ['permission:manage special package']], function () {
            Route::get('/', [SpecialPackageController::class, 'index'])->name('tenant.special-packages');
            Route::get('new', [SpecialPackageController::class, 'create'])->name('tenant.special-packages.create');
            Route::post('new', [SpecialPackageController::class, 'insert'])->name('tenant.special-packages.insert');
            Route::get('{id}', [SpecialPackageController::class, 'show'])->name('tenant.special-packages.show');
            Route::post('{id}', [SpecialPackageController::class, 'update'])->name('tenant.special-packages.update');
            Route::get('{id}/delete', [SpecialPackageController::class, 'delete'])->name('tenant.special-packages.delete');
        });

        Route::group(['prefix' => 'special-offers', 'middleware' => ['permission:manage special offer']], function () {
            Route::get('/', [OfferController::class, 'index'])->name('tenant.special-offers');
            Route::get('new', [OfferController::class, 'create'])->name('tenant.special-offers.create');
            Route::post('new', [OfferController::class, 'insert'])->name('tenant.special-offers.insert');
            Route::get('{id}', [OfferController::class, 'show'])->name('tenant.special-offers.show');
            Route::post('{id}', [OfferController::class, 'update'])->name('tenant.special-offers.update');
            Route::get('{id}/delete', [OfferController::class, 'remove'])->name('tenant.special-offers.remove');
        });

        Route::group(['prefix' => 'agents', 'middleware' => ['permission:manage agent']], function () {
            Route::get('/', [AgentController::class, 'index'])->name('tenant.agents');
            Route::get('new', [AgentController::class, 'create'])->name('tenant.agents.create');
            Route::post('new', [AgentController::class, 'insert'])->name('tenant.agents.insert');
            Route::get('{id}', [AgentController::class, 'show'])->name('tenant.agents.show');
            Route::post('{id}', [AgentController::class, 'update'])->name('tenant.agents.update');
            Route::get('{id}/delete', [UserController::class, 'delete'])->name('tenant.agents.delete');
        });

        Route::group(['prefix' => 'documents', 'middleware' => ['permission:add document|edit document|delete document']], function () {
            Route::get('/', [DocumentController::class, 'index'])->name('tenant.documents');
            Route::get('new', [DocumentController::class, 'create'])->name('tenant.documents.create');
            Route::post('new', [DocumentController::class, 'insert'])->name('tenant.documents.insert');
            Route::post('sort', [DocumentController::class, 'sort'])->name('tenant.documents.sort');
            Route::get('{id}', [DocumentController::class, 'show'])->name('tenant.documents.show');
            Route::post('{id}', [DocumentController::class, 'update'])->name('tenant.documents.update');
            Route::get('{id}/delete', [DocumentController::class, 'delete'])->name('tenant.documents.delete');
        });

        Route::group(['prefix' => 'appearances', 'middleware' => ['permission:manage appearances']], function () {
            Route::get('/', [AppearanceController::class, 'index'])->name('tenant.appearances');
            Route::post('/', [AppearanceController::class, 'update'])->name('tenant.appearances.update');
        });

        Route::group(['prefix' => 'profile', 'middleware' => ['permission:edit profile']], function () {
            Route::get('/', [ProfileController::class, 'index'])->name('tenant.profile');
            Route::post('/', [ProfileController::class, 'update'])->name('tenant.profile.update');
        });

        Route::group(['prefix' => 'vouchers', 'middleware' => ['permission:manage voucher']], function () {
            Route::get('/', [VoucherController::class, 'index'])->name('tenant.vouchers');
            Route::get('new', [VoucherController::class, 'create'])->name('tenant.vouchers.create');
            Route::post('new', [VoucherController::class, 'insert'])->name('tenant.vouchers.insert');
            Route::get('generate-code', [VoucherController::class, 'generateCode'])->name('tenant.vouchers.generate_code');
            Route::get('{id}', [VoucherController::class, 'show'])->name('tenant.vouchers.show');
            Route::post('{id}', [VoucherController::class, 'update'])->name('tenant.vouchers.update');
            Route::get('{id}/delete', [VoucherController::class, 'delete'])->name('tenant.vouchers.delete');
        });

        Route::group(['prefix' => 'automated-emails', 'middleware' => ['permission:add automated email|edit automated email|delete automated email']], function () {
            Route::get('/', [EmailController::class, 'index'])->name('tenant.automated-emails');
            Route::get('new', [EmailController::class, 'create'])->name('tenant.automated-emails.create');
            Route::post('new', [EmailController::class, 'insert'])->name('tenant.automated-emails.insert');
            Route::get('{id}', [EmailController::class, 'show'])->name('tenant.automated-emails.show');
            Route::post('{id}', [EmailController::class, 'update'])->name('tenant.automated-emails.update');
            Route::get('{id}/delete', [EmailController::class, 'delete'])->name('tenant.automated-emails.delete');
            Route::get('{id}/preview-recipient', [EmailController::class, 'previewRecipient'])->name('tenant.automated-emails.preview-recipient');
            Route::get('{id}/exclude-bookings/{booking_id}', [EmailController::class, 'excludeBooking'])->name('tenant.automated-emails.exclude-bookings');
        });

        Route::get('fix-addon-dates', [AddonController::class, 'fixAddonDates']);
    });

    Route::get('questionnaire/{ref}', [QuestionnaireController::class, 'oldBookingsQuestionnaire'])->name('questionnaire.old-bookings');
    Route::post('questionnaire/{ref}', [QuestionnaireController::class, 'processOldBookingsQuestionnaire'])->name('questionnaire.old-bookings.store');

    /**
     * DEFAULT ROUTE - REDIRECT TO HOTEL BOOK NOW PAGE
     */
    Route::get('/', function () {
        return redirect(route('booknow.index'));
    });
});
