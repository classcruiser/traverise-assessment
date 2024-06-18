<template>
    <form action="" method="post">
        <h3 class="mb-4 text-lg font-bold">Login</h3>

        <div class="grid grid-cols-12 gap-6">
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
                <template v-if="form.errors.password">
                    <div class="text-red-500 text-xs mt-1" v-for="error in form.errors.password">
                        {{ error }}
                    </div>
                </template>
            </div>

            <div class="col-span-12" v-if="message && message.length">
                <div class="bg-danger-400 p-2 rounded text-md text-white" >
                    {{ message }}
                </div>
            </div>
        </div>

        <div class="mt-16">
            <button class="block w-full bg-sky-400 text-sky-50 rounded py-3 px-5 font-bold hover:bg-sky-600 transition-all hover:text-sky-50
                disabled:opacity-70 disabled:cursor-not-allowed disabled:text-gray-400 disabled:bg-gray-100"
                    :disabled="v$.$invalid"
                    @click.prevent="submit()" >
                Login
            </button>
            <a :href="route('guest.register')" title="" class="w-full block bg-gray-100 text-gray-500 text-md transition-all rounded py-3 px-5 text-center mt-2 font-bold hover:bg-gray-200">
                Register
            </a>
        </div>
        <div class="mt-4 flex justify-between items-center">
            <a :href="backUrl" title="" class="inline-block bg-gray-50 text-gray-500 text-xs rounded py-3 px-5 hover:bg-gray-200 transition-all">
                <i class="ml-1 fal fa-arrow-left"></i> Back
            </a>
            <a :href="route('guest.forgot-password')" title="" class="inline-block text-gray-600 text-md transition-all">
                Forgot password
            </a>
        </div>
    </form>
</template>

<script>
import { useVuelidate } from '@vuelidate/core'
import { required, email } from '@vuelidate/validators'
import {HTTP} from "@vuePath/Plugins/http-common";

export default {
    name: "Guestlogin",
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
            email: '',
            errors: {}
        },
        message: false
    }),
    validations() {
        return {
            form: {
                email: {
                    required,
                    email
                },
                password: {
                    required,
                },
            }
        }
    },
    methods: {
        async submit () {
            const isFormCorrect = await this.v$.$validate()

            if (!isFormCorrect) {
                return false;
            }

            HTTP.post(route('guest.login.attempt'), this.form)
                .then(response => {
                    if (!response.data.success) {
                        this.form.errors = response.data.errors;
                        this.message = response.data.message;
                        return false;
                    }

                    let redirectUrl = this.redirect ? this.redirect : route('class.shop.index');
                    window.location.replace(redirectUrl);
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    }
}
</script>
