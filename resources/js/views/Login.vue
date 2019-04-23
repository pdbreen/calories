<template>
    <div class="flex flex-wrap justify-center">
        <div class="w-full max-w-sm">
            <div class="flex flex-col break-words bg-white border border-2 rounded shadow-md">

                <div class="font-semibold bg-grey-lightest text-grey-darkest py-3 px-6 mb-0 shadow-inner">
                    Login
                </div>

                <div class="px-6">
                    <ErrorList :error-object="errorObject"/>
                </div>

                <form class="w-full p-6" novalidate @submit.prevent="submit">
                    <EmailInput
                            v-model.trim="login.email"
                            :v="$v.login.email"/>
                    <PasswordInput
                            v-model="login.password"
                            :v="$v.login.password"
                            label="Password"
                            password-type="login">
                    </PasswordInput>

                    <div class="flex flex-wrap items-center">
                        <button type="submit" class="btn btn-primary">
                            Login
                        </button>

                        <p class="w-full text-xs text-center text-grey-dark mt-8 -mb-4">
                            Don't have an account?
                            <router-link class="text-blue hover:text-blue-dark no-underline" :to="{ name: 'register' }">Register</router-link>
                        </p>
                    </div>
                </form>

            </div>
        </div>
    </div>
</template>

<script>
    import {required, email} from 'vuelidate/lib/validators'
    import EmailInput from "../components/inputs/EmailInput"
    import PasswordInput from "../components/inputs/PasswordInput"
    import ErrorList from "../components/ErrorList";

    export default {
        components: {
            ErrorList,
            PasswordInput,
            EmailInput,
        },

        data () {
            return {
                errorObject: null,
                login: {
                    email: null,
                    password: null,
                },
            }
        },

        validations: {
            login: {
                email: {
                    required,
                    email,
                },
                password: {
                    required,
                },
            },
        },

        methods: {
            submit () {
                this.errorObject = null;
                this.$v.$touch()
                if (!this.$v.$error) {
                    this.$store.dispatch('login', this.login).then(response => {
                        this.$router.push('/');
                    }).catch(error => {
                        this.errorObject = error.response.data;
                    });
                }
            },
        },
    }
</script>

<style scoped>

</style>