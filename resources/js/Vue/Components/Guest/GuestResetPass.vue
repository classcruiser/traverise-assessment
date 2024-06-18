<template>
    <form action="" method="post">
        <h3 class="mb-4 text-lg font-bold">Reset password</h3>

        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-6">
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

            <div class="col-span-6">
                <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                    Repeat password *
                </label>

                <input type="password"
                       v-model.trim="v$.form.password_confirmation.$model"
                       name="password_repeat"
                       class="w-full border border-gray-200 py-2 px-3 text-sm rounded"
                       :class="{ 'form-group--error': v$.form.password_confirmation.$error }"
                />
                <div class="text-red-500 text-xs mt-1"
                     v-if="v$.form.password_confirmation.sameAsPassword.$invalid && v$.form.password_confirmation.$dirty">
                    Passwords must be identical</div>
                <template v-if="form.errors.password_confirmation">
                    <div class="text-red-500 text-xs mt-1" v-for="error in form.errors.password_confirmation">
                        {{ error }}
                    </div>
                </template>
            </div>
        </div>

        <div class="mt-16 flex justify-between">
            <button class="inline-block bg-sky-400 text-sky-50 rounded py-3 px-5 font-bold hover:bg-sky-600 transition-all hover:text-sky-50
                disabled:opacity-70 disabled:cursor-not-allowed disabled:text-gray-400 disabled:bg-gray-100"
                    :disabled="v$.$invalid"
                    @click.prevent="submit()"
            >
                Reset password
            </button>
        </div>
    </form>
</template>

<script>
import { useVuelidate } from '@vuelidate/core'
import { required, minLength, sameAs } from '@vuelidate/validators'
import {HTTP} from "@vuePath/Plugins/http-common";

export default {
    name: "GuestResetPass",
    setup () {
        return { v$: useVuelidate() }
    },
    props: {
        email: String,
        token: String,
    },
    data: function () {
        return {
            form: {
                password: '',
                password_confirmation: '',
                token: this.token,
                email: this.email,
                errors: {}
            },
        }
    },
    validations() {
        return {
            form: {
                password: {
                    required,
                    minLength: minLength(8)
                },
                password_confirmation: {
                    sameAsPassword: sameAs(this.form.password)
                }
            }
        }
    },
    methods: {
        submit () {
            HTTP.post(route('guest.password.update'), this.form)
                .then(response => {
                    if (!response.data.success) {
                        this.form.errors = response.data.errors;
                        return false;
                    }

                    window.location.replace(route('guest.login'));
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
