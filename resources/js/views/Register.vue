<template>
    <div class="flex flex-wrap justify-center">
        <div class="w-full max-w-sm">
            <div class="flex flex-col break-words bg-white border border-2 rounded shadow-md">

                <div class="font-semibold bg-grey-lightest text-grey-darkest py-3 px-6 mb-0 shadow-inner">
                    Register
                </div>

                <div class="px-6">
                    <ErrorList :error-object="errorObject"/>
                </div>

                <form class="w-full p-6" novalidate @submit.prevent="submit">
                    <VTextField
                            v-model="user.name"
                            label="Name"
                            :error-messages="nameErrors"
                            @blur="$v.user.name.$touch()"/>
                    <EmailInput
                            v-model.trim="user.email"
                            :v="$v.user.email"/>
                    <PasswordInput
                            v-model="user.password"
                            :v="$v.user.password"
                            label="Password"
                            password-type="new">
                    </PasswordInput>
                    <PasswordInput
                            v-model="user.password_confirm"
                            :v="$v.user.password_confirm"
                            label="Confirm Password"
                            password-type="confirm">
                    </PasswordInput>

                    <div class="flex flex-wrap">
                        <button type="submit" class="btn btn-primary">
                            Register
                        </button>

                        <p class="w-full text-xs text-center text-grey-dark mt-8 -mb-4">
                            Already have an account?
                            <router-link class="text-blue hover:text-blue-dark no-underline" :to="{ name: 'login' }">Login</router-link>
                        </p>
                    </div>
                </form>

            </div>
        </div>
    </div>
</template>

<script>
    import {required, minLength, sameAs, email} from 'vuelidate/lib/validators'
    import VTextField from "../components/inputs/VTextField"
    import EmailInput from "../components/inputs/EmailInput"
    import PasswordInput from "../components/inputs/PasswordInput"
    import {ErrorMessageMixin} from "../mixins/ErrorMessageMixin";
    import ErrorList from "../components/ErrorList";

    export default {
        mixins: [
            ErrorMessageMixin,
        ],
        components: {
            ErrorList,
            VTextField,
            PasswordInput,
            EmailInput,
        },

        data () {
            return {
                errorObject: null,
                user: {
                    name: null,
                    email: null,
                    password: null,
                }
            }
        },

        validations: {
            user: {
                name: {
                    required,
                },
                email: {
                    required,
                    email,
                },
                password: {
                    required,
                    minLength: minLength(6),
                },
                password_confirm: {
                    required,
                    sameAs: sameAs('password'),
                },
            }
        },

        computed: {
            nameErrors () {
                return this.requiredError('name', 'Name is required.')
            },
        },
        methods: {
            getFieldState (field) {
                return this.$v.user[field]
            },
            submit () {
                this.errorObject = null;
                this.$v.$touch()
                if (!this.$v.$error) {
                    this.$store.dispatch('register', this.user).then(response => {
                        this.$store.dispatch('loadUser');
                        this.$store.watch((state, getters) => getters.userLoadStatus, () => {
                            this.$router.push({name: 'account', params: {user_id: this.$store.getters.user.id}});
                        });
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