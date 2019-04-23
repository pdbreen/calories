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
                <router-link class="btn btn-primary mb-4" :to="{ name: 'users.create' }">Add User</router-link>
            </div>

            <div v-if="data">

                <div v-for="user in data" class="max-w-md w-full border border-grey-light bg-white rounded p-4 mb-1 relative">
                    <div class="mb-2">
                        <span class="text-black font-bold text-lg mr-3">{{ user.name }}</span>
                        <div v-if="user.role" class="inline-block bg-red-lighter rounded-full px-3 py-1 text-sm font-semibold mr-3">
                            {{ roleLabel(user) }}
                        </div>
                        <span class="text-grey-darker text-base mr-3">{{ user.email }}</span>
                    </div>
                    <div v-if="!isAdmin" class="inline-block bg-grey-lighter rounded-full px-3 py-1 text-sm font-semibold text-grey-darker mr-3">
                        {{ user.meal_count }} Meals
                    </div>
                    <router-link v-if="isAdmin" class="inline-block bg-grey-lighter rounded-full px-3 py-1 text-sm font-semibold text-grey-darker mr-3" :to="{ name: 'users.meals', params: { user_id: user.id }}">
                        {{ user.meal_count }} Meals
                    </router-link>
                    <div v-if="user.expected_calories" class="inline-block bg-grey-lighter rounded-full px-3 py-1 text-sm font-semibold text-grey-darker mr-3">
                        {{ user.expected_calories }} Daily Calories
                    </div>
                    <div v-if="canEdit(user)" class="absolute pin-r pin-t">
                        <router-link class="btn btn-icon" :to="{ name: 'users.edit', params: { user_id: user.id }}">
                            <FontAwesomeIcon :icon="['far', 'edit']"/>
                        </router-link>
                        <button class="btn btn-icon" @click.prevent="deleteItem(user.id)">
                            <FontAwesomeIcon :icon="['far', 'trash-alt']"/>
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="pageCount > 1" class="mt-2 w-full text-center">
                <button class="btn btn-default inline-block" :disabled="! prevPage" @click.prevent="gotoPage(prevPage)">Prev</button>
                <span class="px-4 text-lg">{{ paginationLabel }}</span>
                <button class="btn btn-default inline-block" :disabled="! nextPage" @click.prevent="gotoPage(nextPage)">Next</button>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'
    import users from '../../api/users';
    import {PagedDataMixin} from '../../mixins/PagedDataMixin';
    import ErrorList from "../../components/ErrorList";

    const fetchData = (userId, params, callback) => {
        users.all(params)
            .then(response => {
                callback(null, response.data);
            }).catch(error => {
            callback(error, error.response.data);
        });
    };

    export default {
        components: {ErrorList},
        mixins: [
            PagedDataMixin(users,fetchData),
        ],
        methods: {
            deleteItem(id) {
                this.api.delete(id).then(response => {
                    if (id === this.user.id) {
                        // Gotta logout if we delete ourselves!!
                        this.$store.dispatch('logout');
                    } else {
                        this.reload();
                    }
                }).catch(error => {
                    this.errorObject = error.response.data;
                });
            },
            canEdit (user) {
                return this.user.role >= user.role;
            },
            roleLabel (user) {
                if (user.role === 2) {
                    return 'Admin'
                } else if (user.role === 1) {
                    return 'User Manager'
                }
            }
        },
        computed: {
            ...mapGetters([
                'user',
            ]),
            isAdmin () {
                return this.user.role == 2;
            }
        },
    }
</script>

<style scoped>

</style>