<template>
    <div class="page-content">
        <div class="content-wrapper">
            <div class="content">
                <div class="card">
                    <div class="px-6 py-3 font-bold text-base">Profile</div>
                    <div class="border-t border-top-grey-50 px-6 py-3">
                        <div class="grid gap-0 md:gap-4 grid-cols-1 md:grid-cols-2">
                            <div class="">
                                <table width="100%" class="cust-table">
                                    <tr>
                                        <td class="font-bold">Full Name</td>
                                        <td>{{ guest.full_name }}</td>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td><a :href="'mailto:' + guest.email"
                                               class="text-danger">{{ guest.email }}</a></td>
                                    </tr>
                                    <tr>
                                        <td>Mobile</td>
                                        <td>{{ emptyStringBlank(guest.mobile) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Phone</td>
                                        <td>{{ emptyStringBlank(guest.phone) }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="">
                                <table width="100%" class="cust-table">
                                    <tr>
                                        <td width="40%">Street</td>
                                        <td width="60%">{{ emptyStringBlank(guest.street) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Zip Code</td>
                                        <td>{{ emptyStringBlank(guest.zip) }}</td>
                                    </tr>
                                    <tr>
                                        <td>City</td>
                                        <td>{{ emptyStringBlank(guest.city) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Country</td>
                                        <td>{{ emptyStringBlank(guest.country) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-top-grey-50" v-if="bookings.data.length">
                        <div class="py-3 px-6">
                            <h5 class="font-bold leading-normal text-base text-gray-700">Bookings</h5>
                        </div>

                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-sky-50 text-sky-800 uppercase text-xs">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Ref
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Guests
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Location
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Booked
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Check
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Price
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="bg-white border-b hover:bg-blue-100 cursor-pointer"
                                    v-for="booking in bookings.data">
                                    <th scope="row" class="px-6 py-4 font-normal  whitespace-nowrap">
                                        {{ booking.ref }}
                                    </th>
                                    <td class="px-6 py-4">
                                        <span class="badge badge-pill"
                                              :class="booking.status_badge">{{ booking.booking_status }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ booking.guests.length }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <b>{{ booking.location.short_name }}</b>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $moment.utc(booking.created_at).format('DD.MM.YY HH:mm:ss') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <b>{{ $moment(booking.check_in).format('DD.MM.YY') }}</b> -
                                        <b>{{ $moment(booking.check_out).format('DD.MM.YY') }}</b>
                                    </td>
                                    <td class="px-6 py-4">
                                        <b>&euro;{{ booking.payment.total.toFixed(2) }}</b>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex justify-center p-4" v-if="bookings.total > bookings.per_page">
                        <pagination
                            :current="bookings.current_page"
                            :total="bookings.total"
                            :per-page="bookings.per_page"
                            @page-changed="fetchData($event, 'bookings')"
                        >
                        </pagination>
                    </div>

                    <div class="border-t border-top-grey-50" v-if="classes.data.length">
                        <div class="py-3 px-6">
                            <h5 class="font-bold leading-normal text-base text-gray-700">Sessions</h5>
                        </div>
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-sky-50 text-sky-800 uppercase text-xs">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Ref
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center">
                                        Guests
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center">
                                        Sessions
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Booked
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right">
                                        Price
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right">
                                        Paid
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="bg-white border-b border-gray-100 hover:bg-blue-100 cursor-pointer"
                                    v-for="sessionClass in classes.data"
                                    @click="showModal('sessionModal', sessionClass)"
                                >
                                    <td scope="row" class="px-6 py-2 text-gray-800 whitespace-nowrap">
                                        {{ sessionClass.ref }}
                                    </td>
                                    <td class="px-6 py-2 text-center">
                                        {{ sessionClass.people }}
                                    </td>
                                    <td class="px-6 py-2 text-center">
                                        {{ sessionClass.sessions.length }}
                                    </td>
                                    <td class="px-6 py-2">
                                        {{ $moment.utc(sessionClass.created_at).format('DD.MM.YY') }}
                                    </td>
                                    <td class="px-6 py-2 text-right">
                                        <b>&euro;{{ sessionClass.payment.total }}</b>
                                    </td>
                                    <td class="px-6 py-2 text-right">
                                        <b>&euro;{{ sessionClass.payment.total_paid }}</b>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex justify-center p-4" v-if="classes.total > classes.per_page">
                        <pagination
                            :current="classes.current_page"
                            :total="classes.total"
                            :per-page="classes.per_page"
                            @page-changed="fetchData($event, 'classes')"
                        >
                        </pagination>
                    </div>


                    <div class="border-t border-top-grey-50" v-if="multiPassOrders.data.length">
                        <div class="py-3 px-6">
                            <h5 class="font-bold leading-normal text-base text-gray-700">Multi Passes</h5>
                        </div>
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-sky-50 text-sky-800 uppercase text-xs">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Ref
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Multi Pass
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Amount
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="bg-white border-b border-gray-100 hover:bg-blue-100 cursor-pointer"
                                    v-for="order in multiPassOrders.data"
                                    @click="showModal('multiPassModal', order)"
                                >
                                    <th scope="row" class="px-6 py-2 font-normal whitespace-nowrap">
                                        {{ order.ref }}
                                    </th>
                                    <td class="px-6 py-2 whitespace-nowrap">
                                        {{ order.multi_pass.name }}
                                    </td>
                                    <td class="px-6 py-2">
                                        <b>
                                            {{ (order.multi_pass.type == 'CREDIT' ? '&euro;' : '') }}
                                            {{ order.multi_pass.amount }}
                                            {{ (order.multi_pass.type == 'SESSION' ? ' Sessions' : '') }}
                                        </b>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex justify-center p-4" v-if="multiPassOrders.total > multiPassOrders.per_page">
                        <pagination
                            :current="multiPassOrders.current_page"
                            :total="multiPassOrders.total"
                            :per-page="multiPassOrders.per_page"
                            @page-changed="fetchData($event, 'multiPassOrders')"
                        >
                        </pagination>
                    </div>

                    <div class="py-3 px-6" v-if="isEmptyAllBookings">
                        <h5 class="font-bold leading-normal text-base text-gray-700">You don't have any booking yet</h5>
                    </div>
                </div>
            </div>

            <session-modal modal-id="sessionModal"
                           :session-data="modalData"
                           v-if="modalData && modalId == 'sessionModal'"
                           @onCloseModal="closeModal"
            />

            <multi-pass-modal modal-id="multiPassModal"
                           :multi-pass-data="modalData"
                           v-if="modalData && modalId == 'multiPassModal'"
                           @onCloseModal="closeModal"
            />

        </div>

        <div class="p-6">
            <p class="text-teal-500 mb-2"><b>Um deinen Multipass zu nutzen:</b></p>
            <ul>
                <li>&middot; Gehe auf <a href="https://rheinriff.de" title="Rhein Riff" class="text-teal-500">https://rheinriff.de/</a></li>
                <li>&middot; Suche dir deine passende Session aus</li>
                <li>&middot; Logge dich beim Buchen ein</li>
                <li>&middot; Und wähle den passenden Multipass bei der Zahlung aus</li>
            </ul>
        </div>
    </div>
</template>

<script>
import Pagination from '@vuePath/Partials/Pagination.vue';
import {HTTP} from "@vuePath/Plugins/http-common";
import {initFlowbite} from 'flowbite'
import SessionModal from "@vuePath/Partials/GuestAccount/SessionModal.vue";
import MultiPassModal from "@vuePath/Partials/GuestAccount/MultiPassModal.vue";
import {Modal} from 'flowbite';

export default {
    name: "GuestAccount",
    props: {
        guest: Object,
        classesInit: Object,
        multiPassOrdersInit: Object,
        bookingsInit: Object
    },
    data() {
        return {
            classes: this.classesInit,
            multiPassOrders: this.multiPassOrdersInit,
            bookings: this.bookingsInit,
            modalData: null,
            modalId: '',
            modal: null
        }
    },
    components: {
        Pagination,
        SessionModal,
        MultiPassModal
    },
    computed: {
        isEmptyAllBookings() {
            return !this.bookings.data.length && !this.classes.data.length && !this.multiPassOrders.data.length;
        }
    },
    methods: {
        emptyStringBlank(str) {
            return str == '' || str == null ? '---' : str
        },
        fetchData(page, type) {
            HTTP.get(route('guest.account.get-' + type, {page: page}))
                .then(response => {
                    this[type] = response.data
                    this[type].current_page = page
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        showModal(modalId, data) {
            this.modalData = data;
            this.modalId = modalId;

            this.$nextTick(() => {
                const $targetEl = document.getElementById(modalId);
                const options = {
                    closable: true
                };

                this.modal = new Modal($targetEl, options);
                this.modal.show();
            })
        },
        closeModal() {
            this.modal.hide();
        }
    },
    mounted: () => {
        initFlowbite();
    }
}
</script>
