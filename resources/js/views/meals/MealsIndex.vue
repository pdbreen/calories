<template>
    <div class="flex flex-wrap justify-center px-2">

        <div class="w-full max-w-md">
            <div v-if="errorObject">
                <ErrorList :error-object="errorObject"/>
                <button @click.prevent="reload" class="btn btn-primary mt-4">
                    Try Again
                </button>
            </div>

            <div class="flex">
                <router-link class="btn btn-primary mb-4" :to="{ name: 'meals.create', params: { user_id: userId } }">
                    Add Meal
                </router-link>
            </div>

            <div class="flex flex-col sm:flex-row">
                <div class="mr-4 mb-4">
                    <label class="block text-grey-darker text-sm font-bold mb-2">Start date</label>
                    <datetime
                            type="date"
                            placeholder="Start Date"
                            value-zone="UTC"
                            zone="UTC"
                            v-model="filter.start_date"
                            :max-datetime="filter.end_date"
                            input-class="shadow appearance-none border rounded py-2 px-3 text-grey-darker leading-tight focus:outline-none focus:shadow-outline">
                    </datetime>
                </div>
                <div class="mb-4">
                    <label class="block text-grey-darker text-sm font-bold mb-2">End date</label>
                    <datetime class="flex"
                              type="date"
                              placeholder="End Date"
                              value-zone="UTC"
                              zone="UTC"
                              v-model="filter.end_date"
                              :min-datetime="filter.start_date"
                              input-class="shadow appearance-none border rounded py-2 px-3 text-grey-darker leading-tight focus:outline-none focus:shadow-outline">
                    </datetime>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row">
                <div class="mr-4 mb-4">
                    <label class="block text-grey-darker text-sm font-bold mb-2">Start time</label>
                    <datetime
                            type="time"
                            placeholder="Start Time"
                            value-zone="UTC"
                            zone="UTC"
                            v-model="filter.start_time"
                            :max-datetime="filter.end_time"
                            :minute-step="5"
                            input-class="shadow appearance-none border rounded py-2 px-3 text-grey-darker leading-tight focus:outline-none focus:shadow-outline">
                    </datetime>
                </div>
                <div class="mb-4">
                    <label class="block text-grey-darker text-sm font-bold mb-2">End time</label>
                    <datetime
                            type="time"
                            placeholder="End Time"
                            value-zone="UTC"
                            zone="UTC"
                            v-model="filter.end_time"
                            :min-datetime="filter.start_time"
                            :minute-step="5"
                            input-class="shadow appearance-none border rounded py-2 px-3 text-grey-darker leading-tight focus:outline-none focus:shadow-outline">
                    </datetime>
                </div>
            </div>
            <div class="flex">
                <button class="btn btn-primary mb-4" @click.prevent="clearFilter">
                    Clear Filter
                </button>
            </div>

            <div v-if="!data">
                <h4>No meals available</h4>
            </div>
            <div v-if="data">
                <div v-for="(meals, date) in groupedData">
                    <h4 class="mt-4">{{date}}</h4>
                    <small class="mb-2">Total calories: {{meals[0].total_calories}} of {{meals[0].expected_calories}}</small>
                    <div v-for="meal in meals"
                         class="max-w-md w-full border border-grey-light bg-white rounded mb-1 flex"
                         :class="mealClass(meal)">
                        <div class="flex-grow p-4">
                            <div class="mb-2">
                                <span class="text-black font-bold text-lg mr-3">{{meal.calories}} cal</span>
                                <span class="inline-block bg-grey-lighter rounded-full px-3 py-1 text-sm font-semibold text-grey-darker mr-3">
                                <span v-if="meal.is_breakfast">Breakfast</span>
                                <span v-if="meal.is_lunch">Lunch</span>
                                <span v-if="meal.is_dinner">Dinner</span>
                                <span v-if="meal.is_snack">Snack</span>
                            </span>
                                <span class="text-grey-darker text-base mr-3">{{ meal.eaten_at_day }} @ {{ meal.eaten_at_time }}</span>
                            </div>
                            <div>
                                {{ meal.description }}
                            </div>
                        </div>
                        <div>
                            <router-link class="btn btn-icon" :to="{ name: 'meals.edit', params: { meal_id: meal.id }}">
                                <FontAwesomeIcon :icon="['far', 'edit']"/>
                            </router-link>
                            <button class="btn btn-icon" @click.prevent="deleteItem(meal.id)">
                                <FontAwesomeIcon :icon="['far', 'trash-alt']"/>
                            </button>
                        </div>
                    </div>
                </div>

                <div v-if="pageCount > 1" class="mt-2 w-full text-center">
                    <button class="btn btn-default inline-block" :disabled="! prevPage"
                            @click.prevent="gotoPage(prevPage)">Prev
                    </button>
                    <span class="px-4 text-lg">{{ paginationLabel }}</span>
                    <button class="btn btn-default inline-block" :disabled="! nextPage"
                            @click.prevent="gotoPage(nextPage)">Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import store from '../../store/index';
    import meals from '../../api/meals';
    import users from '../../api/users';
    import {PagedDataMixin} from '../../mixins/PagedDataMixin';
    import ErrorList from "../../components/ErrorList";

    const fetchData = (userId, params, callback) => {
        const id = userId || store.getters.user.id;
        users.meals(id, params)
            .then(response => {
                callback(null, response.data);
            }).catch(error => {
            callback(error, error.response.data);
        });
    };

    export default {
        components: {ErrorList},
        mixins: [
            PagedDataMixin(meals, fetchData),
        ],
        data() {
            return {
                filter: {}
            };
        },
        created() {
            this.clearFilter()
        },
        watch: {
            'filter': {
                handler() {
                    this.setQueryParams(this.$route.query.page);
                },
                deep: true,
            }
        },
        computed: {
            groupedData() {
                if (this.data && this.data.length) {
                    return this.data.reduce(function (groups, meal) {
                        (groups[meal.eaten_at_day] = groups[meal.eaten_at_day] || []).push(meal);
                        return groups;
                    }, {});
                }
                return {};
            }
        },
        methods: {
            clearFilter() {
                this.filter = {
                    start_date: null,
                    end_date: null,
                    start_time: null,
                    end_time: null,
                };
            },
            setQueryParams(page) {
                const query = Object.assign({}, this.$route.query, {page: page}, this.filter)
                this.$router.push({query: query});
            },
            deleteItem(id) {
                this.api.delete(id).then(response => {
                    this.reload();
                }).catch(error => {
                    this.errorObject = error.response.data;
                });
            },
            mealClass(meal) {
                return meal.total_calories <= meal.expected_calories ? 'bg-green-lightest' : 'bg-red-lightest';
            }
        },
    }
</script>

<style scoped>

</style>