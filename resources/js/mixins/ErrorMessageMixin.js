import { validationMixin } from 'vuelidate'

export const ErrorMessageMixin = {
  mixins: [validationMixin],

  methods: {
    requiredError (field, error) {
      const errors = []
      const state = this.getFieldState(field)
      if (!state.$dirty) return errors
      !state.required && errors.push(error)
      return errors
    },
    getFieldState (field) {
      return this.$v[field]
    },
  },
};
