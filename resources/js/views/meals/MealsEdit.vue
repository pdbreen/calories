<template>
    <div class="flex flex-wrap justify-center">
        <div class="w-full max-w-md">
            <div class="flex flex-col break-words bg-white border border-2 rounded shadow-md">

                <div class="font-semibold bg-grey-lightest text-grey-darkest py-3 px-6 mb-0 shadow-inner">
                    {{ editTypeLabel }} Meal
                </div>

                <div class="px-6">
                    <ErrorList :error-object="errorObject"/>
                </div>

                <form v-if="data" class="w-full p-6" novalidate @submit.prevent="submit">

                    <VTextField
                            v-model="data.description"
                            label="Description"
                            :error-messages="descriptionErrors"
                            @blur="$v.data.description.$touch()"/>
                    <VTextField
                            v-model="data.calories"
                            label="Calories"
                            :error-messages="caloriesErrors"
                            @blur="$v.data.calories.$touch()"/>

                    <VDateTime
                            v-model="data.eaten_at"
                            label="Eaten at"
                            :error-messages="eatenAtErrors"
                            @blur="$v.data.eaten_at.$touch()"/>

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
    import {required} from 'vuelidate/lib/validators'
    import meals from '../../api/meals';
    import {EditDataMixin} from '../../mixins/EditDataMixin';
    import ErrorList from "../../components/ErrorList";
    import VTextField from "../../components/inputs/VTextField";
    import VDateTime from "../../components/inputs/VDateTime";

    export default {
        components: {VDateTime, VTextField, ErrorList},
        mixins: [
            EditDataMixin(meals),
        ],

        validations: {
            data: {
                description: {
                    required,
                },
                calories: {
                    required,
                },
                eaten_at: {
                    required,
                }
            }
        },

        computed: {
            ...mapGetters([
                'user',
            ]),
            itemId() {
                return this.$route.params.meal_id;
            },
            userId() {
                return this.data.user_id || this.$route.params.user_id || this.user.id;
            },
            descriptionErrors() {
                return this.requiredError('description', 'Description is required.')
            },
            caloriesErrors() {
                return this.requiredError('calories', 'Calories is required.')
            },
            eatenAtErrors() {
                return this.requiredError('eaten_at', 'Eaten at is required.')
            },
        },
        methods: {
            protoItem () {
                return {
                    user_id: this.userId
                };
            },
            goBack() {
                if ((this.isNewItem && this.$route.params.user_id === this.user.id) ||
                    (!this.isNewItem && this.user.id === this.data.user_id)) {
                    this.$router.push({name: 'meals.index'});
                } else {
                    this.$router.push(`/users/${this.userId}/meals`);
                }
            },
        }
    }
</script>

<style scoped>

</style>