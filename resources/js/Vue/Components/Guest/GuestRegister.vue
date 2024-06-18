<template>
    <form action="" method="post">
        <h3 class="mb-4 text-lg font-bold">Registration</h3>

        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-12">
                <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                    First Name *
                </label>
                <input type="text"
                        v-model.trim="v$.form.fname.$model"
                        name="fname"
                        class="w-full border border-gray-200 py-2 px-3 text-sm rounded"
                        :class="{ 'form-group--error': v$.form.fname.$error }"
                        required />
                <div class="text-red-500 text-xs mt-1"
                        v-if="v$.form.fname.required.$invalid && v$.form.fname.$dirty" >
                First Name is required</div>
                <template v-if="form.errors.fname">
                    <div class="text-red-500 text-xs mt-1" v-for="error in form.errors.fname">
                        {{ error }}
                    </div>
                </template>
            </div>

            <div class="col-span-12">
                <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                    Last Name *
                </label>
                <input type="text"
                        v-model.trim="v$.form.lname.$model"
                        name="lname"
                        class="w-full border border-gray-200 py-2 px-3 text-sm rounded"
                        :class="{ 'form-group--error': v$.form.lname.$error }"
                        required />
                <div class="text-red-500 text-xs mt-1"
                        v-if="v$.form.lname.required.$invalid && v$.form.lname.$dirty">
                    Last Name is required</div>
                <template v-if="form.errors.lname">
                    <div class="text-red-500 text-xs mt-1" v-for="error in form.errors.lname">
                        {{ error }}
                    </div>
                </template>
            </div>

            <div class="col-span-12">
                <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                    Email *
                </label>
                <input type="text"
                        v-model.trim="v$.form.email.$model"
                        name="email"
                        class="w-full border border-gray-200 py-2 px-3 text-sm rounded"
                        :class="{ 'form-group--error': v$.form.email.$error }"
                        required />
                <div class="text-red-500 text-xs mt-1"
                        v-if="v$.form.email.required.$invalid && v$.form.email.$dirty">
                    Email is required</div>
                <div class="text-red-500 text-xs mt-1"
                        v-if="v$.form.email.email.$invalid && v$.form.email.$dirty">
                    Email not valid</div>
                <template v-if="form.errors.email">
                    <div class="text-red-500 text-xs mt-1" v-for="error in form.errors.email">
                        {{ error }}
                    </div>
                </template>
            </div>

            <div class="col-span-12">
                <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                    Mobile Number
                </label>
                <input type="text"
                        v-model.trim="v$.form.mobile.$model"
                        name="mobile"
                        class="w-full border border-gray-200 py-2 px-3 text-sm rounded"
                        :class="{ 'form-group--error': v$.form.mobile.$error }"
                        />
                <div class="text-red-500 text-xs mt-1"
                        v-if="v$.form.mobile.numeric.$invalid && v$.form.mobile.$dirty">
                    Number not valid</div>
                <template v-if="form.errors.mobile">
                    <div class="text-red-500 text-xs mt-1" v-for="error in form.errors.mobile">
                        {{ error }}
                    </div>
                </template>
            </div>

            <div class="col-span-12">
                <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                    Password *
                </label>
                <input type="password"
                        v-model.trim="v$.form.password.$model"
                        name="password"
                        class="w-full border border-gray-200 py-2 px-3 text-sm rounded"
                        :class="{ 'form-group--error': v$.form.password.$error }"
                />
                <div class="text-red-500 text-xs mt-1"
                        v-if="v$.form.password.required.$invalid && v$.form.password.$dirty">
                    Password is required</div>
                <div class="text-red-500 text-xs mt-1"
                        v-if="v$.form.password.minLength.$invalid && v$.form.password.$dirty">
                    Password must have at least {{v$.form.password.minLength.$params.min}} characters</div>
                <template v-if="form.errors.password">
                    <div class="text-red-500 text-xs mt-1" v-for="error in form.errors.password">
                        {{ error }}
                    </div>
                </template>
            </div>

            <div class="col-span-12">
                <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                    Repeat password *
                </label>

                <input type="password"
                        v-model.trim="v$.form.passwordRepeat.$model"
                        name="password_repeat"
                        class="w-full border border-gray-200 py-2 px-3 text-sm rounded"
                        :class="{ 'form-group--error': v$.form.passwordRepeat.$error }"
                />
                <div class="text-red-500 text-xs mt-1"
                        v-if="v$.form.passwordRepeat.sameAsPassword.$invalid && v$.form.passwordRepeat.$dirty">
                    Passwords must be identical</div>
                <template v-if="form.errors.passwordRepeat">
                    <div class="text-red-500 text-xs mt-1" v-for="error in form.errors.passwordRepeat">
                        {{ error }}
                    </div>
                </template>
            </div>
        </div>

        <div class="mt-16 text-right">
            <button class="w-full block bg-sky-400 text-sky-50 rounded py-3 px-5 font-bold hover:bg-sky-600 transition-all hover:text-sky-50
            disabled:opacity-70 disabled:cursor-not-allowed disabled:text-gray-400 disabled:bg-gray-100"
                :disabled="v$.$invalid"
                    @click.prevent="submit()"
            >
                Register
            </button>

            <a :href="loginUrl" title="" class="inline-block text-gray-600 text-md transition-all mt-4">
                Or login if you have an account
            </a>
        </div>
    </form>
</template>

<script>
import {HTTP} from "@vuePath/Plugins/http-common";
import { useVuelidate } from '@vuelidate/core'
import { required, email, minLength, sameAsPassword, numeric, sameAs } from '@vuelidate/validators'


export default {
    name: "GuestRegister",
    setup () {
        return { v$: useVuelidate() }
    },
    props: {
        backUrl: String,
        redirect: String
    },
    data: () => ({
        form: {
            password: '',
            passwordRepeat: '',
            fname: '',
            lname: '',
            email: '',
            mobile: '',
            errors: {}
        }
    }),
    validations() {
        return {
            form: {
                fname: {
                    required,
                },
                lname: {
                    required,
                },
                email: {
                    required,
                    email
                },
                mobile: {
                    numeric,
                },
                password: {
                    required,
                    minLength: minLength(8)
                },
                passwordRepeat: {
                    sameAsPassword: sameAs(this.form.password)
                }
            }
        }
    },
    methods: {
        submit () {
            HTTP.post(route('guest.create'), this.form)
                .then(response => {
                    if (!response.data.success) {
                        this.form.errors = response.data.errors;
                        return false;
                    }

                    let redirectUrl = this.redirect ? this.redirect : route('guest.login');
                    window.location.replace(redirectUrl);
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    }
}
</script>

<style scoped>

</style>
