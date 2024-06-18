<template>
    <div>
        <div
            id="stripe-popup-wrapper"
            class="w-screen h-screen bg-black bg-opacity-80 fixed top-0 left-0 overflow-y-auto z-[99999] flex justify-center items-center transition-all"
            :class="[
                (!stripePopup ? 'opacity-0 pointer-events-none' : 'opacity-100 pointer-events-auto')
            ]"
        >
            <div id="stripe-popup-content" class="block w-full md:w-1/3 mx-4 md:mx-0 rounded-sm relative min-h-[300px]">
                <a id="stripe-popup-close" href="javascript:" title="" class="absolute right-0 top-0 block p-4 z-[99999]" @click="stripePopup = ! stripePopup"><i class="fal fa-times text-rose-600 text-lg"></i></a>
                <div class="stripe-form bg-white p-5 rounded-sm" v-show="isConfirmed">
                    <form id="payment-form">
                        <div id="link-authentication-element">
                            <!--Stripe.js injects the Link Authentication Element-->
                        </div>
                        <div id="payment-element">
                            <!--Stripe.js injects the Payment Element-->
                        </div>
                        <button id="submit" @click.prevent="paymentSubmit" :disabled="isLoading">
                            <div class="spinner" v-show="isLoading" id="spinner"></div>
                            <span id="button-text" v-show="!isLoading">Confirm and Pay</span>
                        </button>
                        <div id="payment-message" v-show="paymentMessage.length">{{ paymentMessage }}</div>
                    </form>
                </div>
            </div>
        </div>

        <a href="#" title="" class="step">1. Select Multi Pass</a>

        <div class="grid grid-cols-12 gap-6">
            <template v-for="(type) in types">
                <div class="col-span-12" v-if="passesByType(type.name).length">
                    <h3 class="font-bold mb-4 ml-2 mt-4 pl-4 text-lg">{{ capitalize(type.name) }}</h3>

                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left ">
                            <thead
                                class="text-xs uppercase bg-gray-50 ">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    Multi Pass name
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Description
                                </th>
                                <th scope="col" class="px-6 py-3 text-right">
                                    Amount
                                </th>
                                <th scope="col" class="px-6 py-3 text-right">
                                    Price
                                </th>
                                <th scope="col" class="px-6 py-3 text-center">
                                    Select
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="bg-white border-b "
                                v-for="(pass, index) in passesByType(type.name)" :key="index"
                            >
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap min-w-44">
                                    {{ pass.name }}
                                </th>
                                <td class="px-6 py-4" v-html="pass.description">

                                </td>
                                <td class="px-6 py-4 text-right">
                                    {{ amountFormat(pass.amount, type) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    € {{ pass.price }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <input type="radio" :value="pass" v-model="selectedPass"
                                           class="block mx-auto w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 ">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </template>

            <div class="col-span-12 px-6">
                <h3 class="mb-4 text-lg font-bold">Multi Pass</h3>
                <template v-if="isSelected">
                    <div class="py-1 flex justify-between items-start">
                        <span class="font-bold">{{ selectedPass.name }}</span>
                        <span class="text-base">€ {{ selectedPass.price }}</span>
                    </div>
                    <div class="py-1 flex items-start">
                        <span>Price contains <b>€ {{ tax }}</b> VAT (<b>€ {{
                                tax
                            }}</b> {{ location.goods_tax ? location.goods_tax : 0 }}%)</span>
                    </div>
                </template>
                <template v-if="!isSelected && isFormDirty">
                    <div class="py-1 flex justify-between items-start">
                        <span class="text-red-500 font-bold">Please select multi-pass</span>
                    </div>
                </template>

                <div class="py-1 flex justify-between items-start mt-8 border-t pt-6 border-gray-200">
                    <span class="font-bold text-lg">Total</span>
                    <span class="text-base font-bold">€ {{ selectedPass ? selectedPass.price : 0 }}</span>
                </div>
            </div>
        </div>

        <div class="px-6 pb-8">
            <label class="block border-t mt-2 pt-3" v-for="term in terms">
                <input type="checkbox"
                    name="terms"
                    class="mr-1"
                    v-model="isTermAccepted"
                    required/>
                I have read and agree to the
                <a href="#"
                @click.prevent="showTermsPopup(term.slug)"
                data-popup
                :title="term.title"
                class="link-custom">
                    <b>{{ term.title }}</b>
                </a>

                <small class="font-sm text-danger ml-2" v-if="!isTermAccepted && isFormDirty">REQUIRED</small>
            </label>

            <label class="block border-t mt-2 pt-3">
                <input type="checkbox"
                       name="is_other_guest"
                       class="mr-1"
                       v-model="isOtherGuest"
                       />
                I want to buy this voucher for somebody else (Ich möchte den Multipass für jemand anderen kaufen)
            </label>

            <p class="mt-2 mb-2" v-if="isOtherGuest">Please enter the e-mail address to which you would like us to send the multipass here.
                <br />We will send a confirmation email to the person for whom the multipass is intended. As soon as this persons mail is registered the multipass can be used.
                <br />Book here: <a :href="route('class.shop.index')" class="text-[#82bbb3] font-bold">{{ route('class.shop.index') }}</a></p>

            <p class="mt-2 mb-2" v-if="isOtherGuest">(Bitte gib hier die Mailadresse ein, an welche wir den Multipass versenden sollen.
                <br />Sobald die E-Mailadresse der Person registriert wurde kann der Multipass genutzt werden. Buchungen können hier vorgenommen werden
                <br /><a :href="route('class.shop.index')" class="text-[#82bbb3] font-bold">{{ route('class.shop.index') }}</a>)</p>

            <div class="col-span-6 mt-3" v-if="isOtherGuest">
                <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                    Email
                </label>
                <input type="text"
                       v-model.trim="v$.otherGuestEmail.$model"
                       name="other_guest_email"
                       class="w-full border border-gray-200 py-2 px-3 text-sm rounded"
                       required
                       :class="{ 'form-group--error': v$.otherGuestEmail.$error }"/>
                <div class="text-red-500 text-xs mt-1"
                     v-if="v$.otherGuestEmail.required.$invalid && v$.otherGuestEmail.$dirty">
                    Email is required
                </div>
                <div class="text-red-500 text-xs mt-1"
                     v-if="v$.otherGuestEmail.email.$invalid && v$.otherGuestEmail.$dirty">
                    Email not valid
                </div>
            </div>

            <label class="block mt-4" v-if="!isLogined">
                <input type="checkbox" name="new_account" class="mr-1" v-model="isNewAccount"/>
                Confirm and create my account for faster checkout next time
            </label>

            <login-form
                v-if="!isNewAccount && !isLogined"
                @onLogined='setGuest'
                v-on:update:isLoading="isLoading = $event"
                ref="loginForm"
            />

            <create-account-form
                v-if="isNewAccount && !isLogined"
                @onCreatedAccount='setGuest'
                v-on:update:isLoading="isLoading = $event"
                :countries="countries"
                ref="createAccountForm"
            />

            <template v-if="errorsArray.length">
                <div class="py-1 flex justify-between items-start" v-for="error in errorsArray">
                    <span class="text-red-500 font-bold">{{error}}</span>
                </div>
            </template>

            <div class="col-span-6" v-if="isLogined">
                <div class="grid grid-cols-12">
                    <div class="col-span-12 md:col-span-6">
                        <h3 class="border-t font-bold mb-4 mt-4 pt-4 text-lg">{{ bookerTitleText }}</h3>
                        <p class="mb-2">
                            <span class="font-bold text-base">{{ guest.fname }} {{ guest.lname }}</span>
                            <br>
                            {{ guest.email }}
                            <br>
                            {{ guest.phone }}
                        </p>
                        <p>
                            <span class="font-bold">Address</span>
                            <br>
                            {{ guest.street }}, {{ guest.country }}
                        </p>
                    </div>
                    <div class="col-span-12 md:col-span-6">
                        <h3 class="border-t font-bold mb-4 mt-4 pt-4 text-lg">Payment methods</h3>
                        <div class="flex justify-start gap-4" id="payment-methods">
                            <a
                                href="javascript:"
                                title=""
                                class="rounded-sm py-5 px-6 border flex flex-col justify-start items-center space-y-2 uppercase text-xs min-w-[120px]"
                                :class="[(methods === 'stripe' ? 'border-teal-300 shadow' : 'border-gray-200'),]"
                                @click="selectPaymentMethod('stripe')"
                            >
                                <i class="fa fa-credit-card fa-fw block fa-2x" :class="[(methods === 'stripe' ? 'text-teal-300' : 'text-gray-500'),]"></i>
                                <span>Credit Card</span>
                            </a>
                            <a
                                href="javascript:"
                                title=""
                                class="rounded-sm py-5 px-6 border flex flex-col justify-start items-center space-y-2 uppercase text-xs min-w-[120px]"
                                :class="[(methods === 'paypal' ? 'border-teal-300 shadow' : 'border-gray-200'),]"
                                @click="selectPaymentMethod('paypal')"
                                v-if="paymentMethods.includes('Paypal')"
                            >
                                <i class="fab fa-cc-paypal fa-fw block fa-2x" :class="[(methods === 'paypal' ? 'text-teal-300' : 'text-gray-500'),]"></i>
                                <span>PayPal</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end items-center">
                <button
                    @click="clickBtn()"
                    v-show="this.methods == 'stripe'"
                    class="inline-block bg-teal-400 text-teal-50 rounded-sm text-xs py-3 px-5 font-bold hover:bg-teal-600 transition-all hover:text-teal-50"
                >
                    {{ buttonText }}
                </button>
                <div
                    id="paypal-button-container"
                    v-show="this.methods == 'paypal'"
                ></div>
            </div>

            <div id="stripe_cs" class="hidden" data-value=""></div>
        </div>

    </div>
</template>

<script>
import {HTTP} from "@vuePath/Plugins/http-common";
import LoginForm from "@vuePath/Partials/GuestAccount/GuestMultiPass/LoginForm.vue";
import CreateAccountForm from "@vuePath/Partials/GuestAccount/GuestMultiPass/CreateAccountForm.vue";
import { useVuelidate } from '@vuelidate/core'
import { email, required } from '@vuelidate/validators'

export default {
    name: "GuestMultiPass",
    props: {
        passes: Array,
        sk: String,
        stripeAccount: String,
        paymentLink: String,
        terms: Array,
        guestInit: Object,
        countries: Array,
        paymentMethods: Array,
        location: Object
    },
    setup () {
        return { v$: useVuelidate() }
    },
    data: function () {
        return {
            types: [
                {
                    name: 'credit',
                    measurement: '€'
                },
                {
                    name: 'session',
                    measurement: 'session'
                },
                {
                    name: 'voucher',
                    measurement: '%'
                },
            ],
            selectedPass: {},
            stripe: null,
            elements: null,
            isConfirmed: false,
            paymentMessage: '',
            isLoading: false,
            multiPassPaymentId: null,
            isNewAccount: false,
            isFormDirty: false,
            isTermAccepted: false,
            isOtherGuest: false,
            otherGuestEmail: '',
            methods: 'stripe',
            stripePopup: false,
            guest: this.guestInit,
            errors: {}
        }
    },
    validations() {
        return {
            otherGuestEmail: {
                required, email
            }
        }
    },
    components: {
        LoginForm,
        CreateAccountForm
    },
    computed: {
        isSelected: function () {
            return Object.keys(this.selectedPass).length > 0;
        },
        isLogined: function () {
            return this.guest ? Object.keys(this.guest).length > 0 : false;
        },
        buttonText: function () {
            return this.guest ? 'CONFIRM AND PAY' : (this.isNewAccount ? 'CREATE ACCOUNT' : 'LOGIN');
        },
        bookerTitleText: function () {
            return this.isLogined || this.isNewAccount ? 'Booker details' : 'Login';
        },
        tax: function () {
            if(!this.location.goods_tax) {
                return 0;
            }

            const taxPercent = this.location.goods_tax / 100;
            const tax = this.selectedPass.price - (this.selectedPass.price / (1 + taxPercent));
            return Math.round((tax + Number.EPSILON) * 100) / 100
        },
        errorsArray: function () {
            return Object.keys(this.errors).map((key) => {
                return this.errors[key][0];
            });
        }
    },
    methods: {
        route(name) {
            return route(name);
        },
        selectPaymentMethod(method) {
            this.isFormDirty = true;

            if (!this.isSelected || !this.isTermAccepted) {
                return false;
            }

            return this.methods = method;
        },
        passesByType: function (type) {
            return this.passes.filter(pass => pass.type === type.toUpperCase());
        },
        capitalize: function (str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        },
        amountFormat(amount, type) {
            if (type.name === 'credit') {
                return type.measurement + ' ' + amount;
            } else {
                return amount + ' ' + type.measurement;
            }
        },
        clickBtn: function () {
            if (!this.isNewAccount && !this.isLogined) {
                this.$refs.loginForm.login();
                return false;
            }

            if (this.isNewAccount && !this.isLogined) {
                this.$refs.createAccountForm.createAccount();
                return false;
            }

            this.isFormDirty = true;

            if (!this.isSelected || !this.isTermAccepted) {
                return false;
            }

            if (this.methods == 'stripe') {
                this.getStripeSecret();
            }
        },
        setGuest(guest) {
            this.guest = guest;
        },
        getStripeSecret() {
            this.errors = {};

            HTTP.post(route('multi-pass.client-secret'), {
                currency: 'eur',
                multi_pass_id: this.selectedPass.id,
                type: 'multi-pass',
                multi_pass_payment_id: this.multiPassPaymentId,
                is_other_guest: this.isOtherGuest,
                other_guest_email: this.isOtherGuest ? this.otherGuestEmail : null
            })
                .then(response => {

                    if (!response.data.status) {
                        this.errors = response.data.errors;
                        return false;
                    }

                    this.multiPassPaymentId = response.data.multi_pass_payment_id;
                    const OPT = {
                        clientSecret: response.data.clientSecret,
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

                    this.elements = this.stripe.elements(OPT);

                    this.elements.create("linkAuthentication").mount("#link-authentication-element");

                    let payment = this.elements.create("payment", {
                        layout: "tabs",
                    });

                    payment.on('ready', () => {
                        this.isConfirmed = true;
                        this.$nextTick(() => {
                            window.scrollTo(0, document.body.scrollHeight);
                        });
                    });

                    payment.mount("#payment-element");
                    this.stripePopup = ! this.stripePopup;
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        async paymentSubmit() {
            this.isLoading = true;

            const {error} = await this.stripe.confirmPayment({
                elements: this.elements,
                confirmParams: {
                    return_url: route('multi-pass.thank-you'),
                    receipt_email: '',
                },
            });

            if (error.type === "card_error" || error.type === "validation_error") {
                this.showMessage(error.message);
            } else {
                this.showMessage("An unexpected error occurred.");
            }

            this.isLoading = false;

            this.checkStatus();
        },
        showMessage(message) {
            this.paymentMessage = message;
            setTimeout(function () {
                this.paymentMessage = "";
            }, 4000);
        },
        async checkStatus() {
            const {paymentIntent} = await this.stripe.retrievePaymentIntent(this.clientSecret);
            await this.updateOrderStatus(paymentIntent.status);

            switch (paymentIntent.status) {
                case "succeeded":
                    this.showMessage("Payment succeeded!");
                    break;
                case "processing":
                    this.showMessage("Your payment is processing.");
                    break;
                case "requires_payment_method":
                    this.showMessage("Your payment was not successful, please try again.");
                    break;
                default:
                    this.showMessage("Something went wrong.");
                    break;
            }
        },
        showTermsPopup(slug) {
            let popup = document.getElementById('popup-wrapper');
            let loading = document.getElementById('popup-loading');
            popup.classList.remove('pointer-events-none', 'opacity-0');
            popup.classList.add('opacity-100');
            loading.classList.remove('hidden');

            HTTP.get(route('tenant.documents.api.show', {slug}))
                .then(response => {
                    let {title, content} = response.data;
                    document.getElementById('popup-title').innerHTML = title;
                    document.getElementById('popup-body').innerHTML = content;
                })
                .then(() => {
                    loading.classList.add('hidden');
                    document.getElementById('popup-body').classList.remove('hidden');
                    document.getElementById('popup-content').classList.remove('hidden');
                })
                .catch(function (error) {
                    console.log(error);
                });

        },
        initPaypal() {
            const _this = this;
            paypal.Buttons({
                style: {
                    layout: 'horizontal',
                    color:  'blue',
                    shape:  'pill',
                    label:  'pay',
                    height: 37
                },

                // Call your server to set up the transaction
                createOrder: async function() {
                    return axios.post(route('tenant.payment.paypal.create-order'), {
                        type: 'multi-pass',
                        id: _this.selectedPass.id,
                        is_other_guest: _this.isOtherGuest,
                        other_guest_email: _this.isOtherGuest ? _this.otherGuestEmail : null,
                        multi_pass_payment_id: _this.multiPassPaymentId,
                    }).then(res => {
                        return res.data.id;
                    });
                },

                // Call your server to finalize the transaction
                onApprove: async function(data, actions) {
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

                        actions.redirect(route('multi-pass.thank-you'));
                    });
                }
            }).render('#paypal-button-container');
        },
        closeTermsPopUp() {
            let popup = document.getElementById('popup-wrapper');
            popup.classList.add('pointer-events-none', 'opacity-0');
            popup.classList.remove('opacity-100')
            document.getElementById('popup-loading').classList.add('hidden');
            document.getElementById('popup-content').classList.add('hidden');
            document.getElementById('popup-title').innerHTML = '';
            document.getElementById('popup-body').innerHTML = '';
        }
    },
    async mounted() {
        this.stripe = window.Stripe(this.sk, {
            stripeAccount: this.stripeAccount
        });

        let closeBtn = document.querySelector("[popup-close]");
        closeBtn.addEventListener("click", (e) => {
            e.preventDefault();
            this.closeTermsPopUp();
        });

        this.initPaypal();
    }
}
</script>

<style scoped>

</style>
