<template>
    <div class="grid grid-cols-12 gap-6 mt-4">
        <div class="col-span-6">
            <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                FIRST NAME
            </label>
            <input type="text"
                   v-model.trim="v$.form.fname.$model"
                   name="text"
                   class="w-full border border-gray-200 py-2 px-3 text-sm rounded"
                   :class="{ 'form-group--error': v$.form.fname.$error }"
                   required/>
            <div class="text-red-500 text-xs mt-1"
                 v-if="v$.form.fname.required.$invalid && v$.form.fname.$dirty">
                First name is required
            </div>
            <template v-if="form.errors.fname">
                <div class="text-red-500 text-xs mt-1" v-for="error in form.errors.fname">
                    {{ error }}
                </div>
            </template>
        </div>

        <div class="col-span-6">
            <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                LAST NAME
            </label>
            <input type="text"
                   v-model.trim="form.lname"
                   name="text"
                   class="w-full border border-gray-200 py-2 px-3 text-sm rounded"/>
        </div>

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
                Phone
            </label>
            <input type="text"
                   v-model.trim="v$.form.phone.$model"
                   name="number"
                   class="w-full border border-gray-200 py-2 px-3 text-sm rounded"
                   :class="{ 'form-group--error': v$.form.phone.$error }"
                   required/>
            <div class="text-red-500 text-xs mt-1"
                 v-if="v$.form.phone.numeric.$invalid && v$.form.phone.$dirty">
                Phone not valid
            </div>
            <template v-if="form.errors.phone">
                <div class="text-red-500 text-xs mt-1" v-for="error in form.errors.phone">
                    {{ error }}
                </div>
            </template>
        </div>

        <div class="col-span-6">
            <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                Address
            </label>
            <input type="text"
                   v-model.trim="form.street"
                   name="text"
                   class="w-full border border-gray-200 py-2 px-3 text-sm rounded"/>
        </div>

        <div class="col-span-6">
            <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                Country
            </label>
            <select name="country"
                    class="w-full border border-gray-200 py-2 px-3 text-sm rounded"
                    v-model="form.country"
            >
                <option value="">Select country</option>
                <option v-for="country in countries" :value="country.cc_iso2">
                    {{ country.country_name }}
                </option>
            </select>
        </div>

        <div class="col-span-6">
            <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                Create a password
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
            <div class="text-red-500 text-xs mt-1"
                 v-if="v$.form.password.minLength.$invalid && v$.form.password.$dirty">
                Password must have at least {{v$.form.password.minLength.$params.min}} characters</div>
            <template v-if="form.errors.password">
                <div class="text-red-500 text-xs mt-1" v-for="error in form.errors.password">
                    {{ error }}
                </div>
            </template>
        </div>
    </div>
</template>

<script>
import {HTTP} from "@vuePath/Plugins/http-common";
import { useVuelidate } from '@vuelidate/core'
import {email, minLength, numeric, required} from '@vuelidate/validators'

export default {
    name: "CreateAccountForm",
    setup () {
        return { v$: useVuelidate() }
    },
    props:{
        countries: Array
    },
    data: function () {
        return {
            form: {
                password: '',
                email: '',
                fname: '',
                lname: '',
                phone: '',
                street: '',
                errors: {}
            },
        }
    },
    validations() {
        return {
            form: {
                email: {
                    required,
                    email
                },
                password: {
                    required,
                    minLength: minLength(8)
                },
                fname: {
                    required
                },
                phone: {
                    numeric
                }
            }
        }
    },
    methods: {
        async createAccount() {
            const isFormCorrect = await this.v$.$validate()

            if (!isFormCorrect) {
                return false;
            }

            this.$emit('update:isLoading', true);
            this.form.passwordRepeat = this.form.password;
            HTTP.post(route('guest.create'), this.form)
                .then(response => {
                    if (!response.data.success) {
                        this.form.errors = response.data.errors;
                        return false;
                    }

                    this.$emit('onCreatedAccount', response.data.guest)
                    this.$emit('update:isLoading', false);
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
    }
}
</script>

<style scoped>

</style>
