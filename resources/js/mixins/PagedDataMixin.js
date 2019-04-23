import {mapGetters} from 'vuex'

export function PagedDataMixin (api, fetchData) {

    return {
        data() {
            return {
                api: api,
                data: null,
                meta: {},
                errorObject: null,
            }
        },
        beforeRouteEnter(to, from, next) {
            fetchData(to.params.user_id, to.query, (err, data) => {
                next(vm => vm.setData(err, data));
            });
        },
        beforeRouteUpdate(to, from, next) {
            this.errorObject = this.data = this.meta = null;
            fetchData(to.params.user_id, to.query, (err, data) => {
                this.setData(err, data);
                next();
            });
        },
        computed: {
            ...mapGetters([
                'user',
            ]),
            userId() {
                return this.$route.params.user_id || this.user.id;
            },
            pageCount() {
                return this.meta ? this.meta.last_page : null;
            },
            nextPage() {
                if (!this.meta || this.meta.current_page === this.meta.last_page) {
                    return;
                }

                return this.meta.current_page + 1;
            },
            prevPage() {
                if (!this.meta || this.meta.current_page === 1) {
                    return;
                }

                return this.meta.current_page - 1;
            },
            paginationLabel() {
                if (!this.meta) {
                    return;
                }

                const {current_page, last_page} = this.meta;

                return `${current_page} of ${last_page}`;
            },
        },
        methods: {
            setQueryParams (page) {
                this.$router.push({
                    query: {
                        page: page,
                    }
                });
            },
            reload() {
                if (this.meta) {
                    this.setQueryParams(this.$route.query.page);
                    fetchData(this.userId, this.$route.query, (err, data) => {
                        this.setData(err, data);
                    });
                }
            },
            setData(err, {data: data, meta}) {
                if (err) {
                    this.errorObject = err.response.data;
                } else {
                    this.data = data;
                    this.meta = meta;

                    // Check if we are out of bounds
                    if (!data.length && meta.current_page > meta.last_page) {
                        this.gotoPage(Math.min(meta.current_page, meta.last_page))
                    }
                }
            },
            gotoPage(page) {
                this.setQueryParams(page);
            },
        }
    }
};
