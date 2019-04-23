import {ErrorMessageMixin} from "./ErrorMessageMixin";

export function EditDataMixin (api) {
    return {
        mixins: [
            ErrorMessageMixin,
        ],
        data() {
            return {
                api: api,
                loaded: false,
                data: {},
                errorObject: null,
            }
        },
        created() {
            this.loadItem();
        },
        computed: {
            itemId () {
                return null;
            },
            editTypeLabel () {
                return this.itemId ? 'Edit' : 'Add';
            },
            isNewItem () {
                return !this.itemId;
            }
        },
        methods: {
            protoItem() {
                return {};
            },
            loadItem() {
                this.errorObject = null;
                this.data = this.protoItem();
                this.loaded = false;
                if (this.itemId) {
                    this.api.find(this.itemId).then((response) => {
                        this.loaded = true;
                        this.data = response.data.data;
                    }).catch(error => {
                        this.loaded = true;
                        this.errorObject = error.response.data;
                    })
                } else {
                    this.loaded = true
                }
            },
            getFieldState (field) {
                return this.$v.data[field]
            },
            submit () {
                this.errorObject = null;
                this.$v.$touch();
                if (!this.$v.$error) {
                    if (this.isNewItem) {
                        api.create(this.data).then(response => {
                            // TODO - Pop Message?
                            this.goBack();
                        }).catch(error => {
                            this.errorObject = error.response.data;
                        });
                    } else {
                        api.update(this.data.id, this.data).then(response => {
                            // TODO - Pop Message?
                            this.goBack();
                        }).catch(error => {
                            this.errorObject = error.response.data;
                        });
                    }
                }
            },
            goBack () {
            }
        }
    };
}