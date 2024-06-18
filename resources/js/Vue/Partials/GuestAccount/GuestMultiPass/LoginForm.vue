<template>
    <div class="grid grid-cols-12 gap-6 mt-4">
        <div class="col-span-6">
            <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                Email
            </label>
            <input type="text"
                   v-model.trim="v$.form.email.$model"
                   name="email"
                   class="w-full border border-gray-200 py-2 px-3 text-sm rounded"
                   :class="{ 'form-group--error': v$.form.email.$error }"
                   required/>
            <div class="text-red-500 text-xs mt-1"
                 v-if="v$.form.email.required.$invalid && v$.form.email.$dirty">
                Email is required
            </div>
            <div class="text-red-500 text-xs mt-1"
                 v-if="v$.form.email.email.$invalid && v$.form.email.$dirty">
                Email not valid
            </div>
            <template v-if="form.errors.email">
                <div class="text-red-500 text-xs mt-1" v-for="error in form.errors.email">
                    {{ error }}
                </div>
            </template>
        </div>

        <div class="col-span-6">
            <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                Password
            </label>
            <input type="password"
                   v-model.trim="v$.form.password.$model"
                   name="password"
                   class="w-full border border-gray-200 py-2 px-3 text-sm rounded"
                   :class="{ 'form-group--error': v$.form.password.$error }"
            />
            <div class="text-red-500 text-xs mt-1"
                 v-if="v$.form.password.required.$invalid && v$.form.password.$dirty">
                Password is required
            </div>
            <template v-if="form.errors.password">
                <div class="text-red-500 text-xs mt-1" v-for="error in form.errors.password">
                    {{ error }}
                </div>
            </template>
        </div>

        <div v-if="form.errors.auth"
             class="col-span-12 mb-6 p-4 alert bg-danger-400 text-white alert-dismissible border has-error text-sm leading-relaxed">
            {{ form.errors.auth }}
        </div>

        <div class="col-span-12 flex justify-end">
            <a :href="route('guest.forgot-password')" title="" class="block mr-4">Forgot password</a>
        </div>
    </div>
</template>

<script>
import { useVuelidate } from '@vuelidate/core'
import { email, required } from '@vuelidate/validators'

import {HTTP} from "@vuePath/Plugins/http-common";

export default {
    name: "LoginForm",
    setup () {
        return { v$: useVuelidate() }
    },
    data: function () {
        return {
            form: {
                email: '',
                password: '',
                errors: {}
            },
        };
    },
    validations() {
        return {
            form: {
                email: {
                    required, email
                },
                password: {
                    required
                }
            }
        }
    },
    methods: {
        async login() {
            const isFormCorrect = await this.v$.$validate()

            if (!isFormCorrect) {
                return false;
            }

            this.$emit('update:isLoading', true);
            HTTP.post(route('guest.login.attempt'), this.form)
                .then(response => {
                    if (!response.data.success) {
                        this.form.errors = response.data.errors;
                        return false;
                    }

                    this.$emit('onLogined', response.data.guest);
                    this.$emit('update:isLoading', false);
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
