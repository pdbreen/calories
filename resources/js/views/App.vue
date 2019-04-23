<template>
    <div class="mb-10">
        <nav class="bg-blue-darkest shadow mb-8 py-6">
            <div class="container mx-auto px-6 md:px-0">
                <div class="flex items-center justify-center">
                    <div class="mr-6">
                        <span class="text-lg font-semibold text-white no-underline mr-6">
                            Calorie Counter
                        </span>
                        <router-link v-if="loggedIn" class="no-underline hover:underline text-grey-lightest text-sm p-3" :to="{ name: 'meals.index' }">My Meals</router-link>
                        <router-link v-if="loggedIn && user.role >= 1" class="no-underline hover:underline text-grey-lightest text-sm p-3" :to="{ name: 'users.index' }">Manage Users</router-link>
                    </div>
                    <div class="flex-1 text-right">
                        <router-link v-if="!loggedIn" class="no-underline hover:underline text-grey-lightest text-sm p-3" :to="{ name: 'login' }">Login</router-link>
                        <router-link v-if="!loggedIn" class="no-underline hover:underline text-grey-lightest text-sm p-3" :to="{ name: 'register' }">Register</router-link>
                        <div v-if="loggedIn">
                            <router-link class="no-underline hover:underline text-grey-lightest text-sm p-3" :to="{ name: 'account', params: {user_id: user.id} }">{{user.email}}</router-link>
                            <a class="no-underline hover:underline text-grey-lightest text-sm p-3" href="#" @click.prevent="logout">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container mx-auto">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'

    export default {
        computed: {
            ...mapGetters([
                'user',
                'loggedIn',
            ])
        },
        methods: {
            logout() {
                this.$store.dispatch('logout');
                this.$router.push('/login');
            },
        }
    }
</script>

<style scoped>

</style>