<template>
    <div class="flex flex-wrap justify-center">
        <div class="w-full max-w-md">
            <div class="flex flex-col break-words bg-white border border-2 rounded shadow-md">

                <div class="font-semibold bg-grey-lightest text-grey-darkest py-3 px-6 mb-0 shadow-inner">
                    {{ editTypeLabel }} User
                </div>

                <div class="px-6">
                    <ErrorList :error-object="errorObject"/>
                </div>

                <form v-if="data" class="w-full p-6" novalidate @submit.prevent="submit">

                    <VTextField
                            v-model="data.name"
                            label="Name"
                            :error-messages="nameErrors"
                            @blur="$v.data.name.$touch()"/>
                    <EmailInput
                            v-model.trim="data.email"
                            :v="$v.data.email"/>
                    <PasswordInput v-if="isNewItem"
                            v-model="data.password"
                            :v="$v.data.password"
                            label="Password"
                            password-type="new">
                    </PasswordInput>
                    <PasswordInput v-if="isNewItem"
                            v-model="data.password_confirm"
                            :v="$v.data.password_confirm"
                            label="Confirm Password"
                            password-type="confirm">
                    </PasswordInput>
                    <VTextField
                            v-model="data.expected_calories"
                            label="Expected Daily Calories"/>
                    <VSelect
                            v-if="roles.length"
                            v-model="data.role"
                            label="Role"
                            :options="roles"/>

                    <div class="flex flex-wrap items-center">
                        <button type="submit" class="btn btn-primary">
                            Save
                        </button>
                        <button class="btn btn-default ml-2" @click.prevent="goBack">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'
    import {required, requiredIf, email, minLength, sameAs} from 'vuelidate/lib/validators'
    import users from '../../api/users';
    import {EditDataMixin} from '../../mixins/EditDataMixin';
    import ErrorList from "../../components/ErrorList";
    import VTextField from "../../components/inputs/VTextField";
    import EmailInput from "../../components/inputs/EmailInput";
    import VSelect from "../../components/inputs/VSelect";
    import PasswordInput from "../../components/inputs/PasswordInput";

    export default {
        components: {PasswordInput, VSelect, EmailInput, VTextField, ErrorList},
        mixins: [
            EditDataMixin(users),
        ],
        validations: {
            data: {
                name: {
                    required,
                },
                email: {
                    required,
                    email,
                },
                password: {
                    required: requiredIf(function() {
                        return this.isNewItem;
                    }),
                    minLength: minLength(6),
                },
                password_confirm: {
                    required: requiredIf(function() {
                        return this.isNewItem;
                    }),
                    sameAs: sameAs('password'),
                },
            }
        },

        computed: {
            ...mapGetters([
                'user',
            ]),
            itemId() {
                return this.$route.params.user_id;
            },
            nameErrors () {
                return this.requiredError('name', 'Name is required.')
            },
            roles () {
                const roles = [];
                if (this.user.role >= 1) {
                    roles.push({label: 'User', value: 0});
                    roles.push({label: 'User manager', value: 1});
                }
                if (this.user.role >= 2) {
                    roles.push({label: 'Admin', value: 2});
                }
                return roles;
            }
        },
        methods: {
            protoItem () {
                return {
                    role: 0,
                };
            },
            goBack () {
                if (this.user.role) {
                    // TODO - we are assuming manager/admin always go back to user list
                    this.$router.push({name: 'users.index'});
                } else {
                    this.$router.push({name: 'meals.index'});
                }
            },
        }
    }
</script>

<style scoped>

</style>