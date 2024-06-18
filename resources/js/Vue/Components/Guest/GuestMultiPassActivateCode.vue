<template>
    <form action="" method="post">
        <h3 class="mb-4 text-lg font-bold">Multi-Pass Activation Code</h3>

        <template v-if="guestInit">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                        Activation Code *
                    </label>
                    <input type="text"
                           v-model.trim="v$.code.$model"
                           name="code"
                           class="w-full border border-gray-200 py-2 px-3 text-sm rounded"
                           :class="{ 'form-group--error': v$.code.$error }"
                           required/>
                    <div class="text-red-500 text-xs mt-1"
                         v-if="v$.code.required.$invalid && v$.code.$dirty">
                        Code is required
                    </div>
                    <template v-if="errors.code">
                        <div class="text-red-500 text-xs mt-1" v-for="error in errors.code">
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
                    Activate
                </button>
            </div>
        </template>

        <div v-else>
            You should login first
            <a :href="`${route('guest.login')}?redirect=${route('multi-pass.activation-code-form')}`" title="" class="mt-4">
                <button type="button"
                        class="block w-full bg-sky-400 text-sky-50 rounded py-3 px-5 font-bold hover:bg-sky-600 transition-all hover:text-sky-50" >
                    Login
                </button>
            </a>
            <br />
            Or create account
            <a :href="`${route('guest.register')}?redirect=${route('multi-pass.activation-code-form')}`" title="" class="mt-4">
                <button type="button"
                        class="block w-full bg-sky-400 text-sky-50 rounded py-3 px-5 font-bold hover:bg-sky-600 transition-all hover:text-sky-50" >
                    Register
                </button>
            </a>
        </div>

        <div class="grid grid-cols-12 gap-6 mt-4" v-if="isSubmited">
            <div class="col-span-12">
                <p class="text-lg-center" :class="[status ? 'text-green-600' : 'text-red-600']">
                    {{ message }}
                </p>
            </div>
        </div>
    </form>
</template>

<script>
import {HTTP} from "@vuePath/Plugins/http-common";
import {useVuelidate} from "@vuelidate/core";
import {required} from "@vuelidate/validators";

export default {
    name: "GuestMultiPassActivateCode",
    props: {
        guestInit: Object,
    },
    data: () => ({
        code: "",
        errors: {},
        status: false,
        message: "",
        isSubmited: false
    }),
    setup() {
        return {v$: useVuelidate()}
    },
    validations() {
        return {
            code: {
                required
            }
        }
    },
    methods: {
        route(name) {
            return route(name);
        },
        submit() {
            HTTP.post(route('multi-pass.activate-code'), {
                code: this.code
            })
                .then(response => {
                    if (response.data.errors) {
                        this.errors = response.data.errors;
                    }

                    this.status = response.data.status;
                    this.message = response.data.message;
                    this.isSubmited = true;

                    if (this.status) {
                        setTimeout(function () {
                            window.location.href = route('multi-pass.index');
                        }, 5000);
                    }
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
