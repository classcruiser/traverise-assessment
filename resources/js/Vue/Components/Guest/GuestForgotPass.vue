<template>
    <form action="" method="post">
        <h3 class="mb-4 text-lg font-bold">Forgot Password</h3>
        <p>Enter your email and we will send a link to reset your password.</p>

        <div class="grid grid-cols-12 gap-6 mt-6">
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
                     v-if="!v$.form.email.required && v$.form.email.$dirty">
                    Email is required</div>
                <div class="text-red-500 text-xs mt-1"
                     v-if="v$.form.email.$invalid && v$.form.email.$dirty">
                    Email not valid</div>
            </div>

            <div class="col-span-12" v-if="message && message.length">
                <div class="p-2 rounded text-md" :class="classMessage" >
                    {{ message }}
                </div>
            </div>
        </div>

        <div class="mt-16">
            <button class="w-full block bg-sky-400 text-sky-50 rounded py-3 px-5 font-bold hover:bg-sky-600 transition-all hover:text-sky-50 disabled:opacity-70 disabled:cursor-not-allowed disabled:text-gray-400 disabled:bg-gray-100"
                    :disabled="v$.$invalid"
                    @click.prevent="submit()" >
                Send
            </button>
        </div>
        <div class="mt-4 flex justify-between items-center">
            <a :href="backUrl" title="" class="inline-block bg-gray-50 text-gray-500 text-xs rounded py-3 px-5 hover:bg-gray-200 transition-all">
                <i class="ml-1 fal fa-arrow-left"></i> Back
            </a>
            <a :href="route('guest.login')" title="" class="inline-block text-gray-600 text-md transition-all">
                Login
            </a>
        </div>
    </form>
</template>

<script>
import { useVuelidate } from '@vuelidate/core'
import { required, email } from '@vuelidate/validators'
import {HTTP} from "@vuePath/Plugins/http-common";

export default {
    name: "GuestForgotPass",
    setup () {
        return { v$: useVuelidate() }
    },
    props: {
        backUrl: String,
    },
    data: () => ({
        form: {
            email: ''
        },
        message: null,
        success: null
    }),
    validations() {
        return {
            form: {
                email: {
                    required,
                    email
                }
            }
        }
    },
    computed: {
        classMessage() {
            return {
                'bg-danger-400': !this.success,
                'bg-success-400' : this.success,
                'text-white': !this.success
            }
        }
    },
    methods: {
        route (name) {
            return route(name);
        },
        async submit () {
            const isFormCorrect = await this.v$.$validate();

            if (!isFormCorrect) {
                return false;
            }

            console.log(this.form);
            HTTP.post(route('guest.password.email'), this.form)
                .then(response => {
                    this.success = response.data.success;
                    this.message = response.data.message;
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    }
}
</script>
