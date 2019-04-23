<template>
    <div class="mb-10">
        <nav class="bg-blue-darkest shadow mb-8 py-6">
            <div class="container mx-auto px-6 md:px-0">
                <span class="text-lg font-semibold text-white no-underline mb-2">
                    Calorie Counter
                </span>
                <div v-if="loggedIn" class="flex items-center justify-between">
                    <div class="flex flex-col md:flex-row">
                        <router-link class="no-underline hover:underline text-grey-lightest text-sm pr-3 py-1" :to="{ name: 'meals.index' }">My Meals</router-link>
                        <router-link v-if="user.role >= 1" class="no-underline hover:underline text-grey-lightest text-sm pr-3 py-1" :to="{ name: 'users.index' }">Manage Users</router-link>
                    </div>
                    <div class="flex flex-col md:flex-row text-right">
                        <router-link class="no-underline hover:underline text-grey-lightest text-sm pr-3 py-1" :to="{ name: 'account', params: {user_id: user.id} }">{{user.email}}</router-link>
                        <a class="no-underline hover:underline text-grey-lightest text-sm pr-3 py-1" href="#" @click.prevent="logout">Logout</a>
                    </div>
                </div>
                <div v-if="!loggedIn" class="text-right">
                    <router-link class="no-underline hover:underline text-grey-lightest text-sm pr-3 py-1" :to="{ name: 'login' }">Login</router-link>
                    <router-link class="no-underline hover:underline text-grey-lightest text-sm pr-3 py-1" :to="{ name: 'register' }">Register</router-link>
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