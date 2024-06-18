<template>
    <div class="input-group input-group-sm mr-1" style="width: 280px;">
        <input type="text" class="form-control"
               placeholder="Resend booking confirmation to"
               v-model="resendEmail"/>
        <span class="input-group-append">
            <a class="input-group-text bg-danger-400 resend-confirmation"
               v-html="resendButton"
               :disabled="resendLoading"
               @click.prevent="resendConfirmation()" />
        </span>
    </div>
</template>

<script>
import {HTTP} from "@vuePath/Plugins/http-common";

export default {
    name: "ResendConfirmation",
    props: {
        orderId: Number
    },
    data: () => ({
        resendLoading: false,
        resendEmail: '',
    }),
    methods: {
        resendConfirmation() {
            if (!this.resendEmail) {
                swal({
                    title: "Hold Up!",
                    text: "Please enter an email address",
                    icon: "warning",
                    dangerMode: false,
                    button: "OK"
                });
                return false;
            }

            const data = {
                email: this.resendEmail,
                order: this.orderId
            };

            this.resendLoading = true;

            HTTP.post(route('tenant.classes.multi-pass.resend-confirmation-email', data))
                .then((res) => {
                    if (res.data.success) {
                        swal({
                            title: "Success",
                            text: `Email confirmation sent to ${this.resendEmail}`,
                            icon: "success",
                            dangerMode: false,
                            button: "OK"
                        });
                    } else {
                        swal({
                            title: "Error",
                            text: res.data.message,
                            icon: "warning",
                            dangerMode: false,
                            button: "OK"
                        });
                    }

                    this.resendLoading = false;
                    this.resendEmail = '';
                })
        },

    },
    computed: {
        resendButton () {
            return this.resendLoading ?
                '<i class="fal fa-spin fa-fw fa-spinner-third mr-1"></i> Please wait' :
                '<i class="fa fa-fw fa-envelope mr-1"></i> Send';
        }
    }
}
</script>

<style scoped>

</style>
