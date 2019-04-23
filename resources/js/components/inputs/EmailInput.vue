<template>
  <VTextField
    v-model="email"
    :error-messages="emailErrors"
    type="email"
    label="E-Mail Address:"
    v-bind="$attrs"
    v-on="$listeners"
    @blur="v.$touch">
    <template slot="append">
      <slot name="append" />
    </template>
  </VTextField>
</template>

<script>
  import VTextField from "./VTextField"

  export default {
    components: {
      VTextField,
    },

    props: {
      value: {
        type: String,
        default: null,
      },
      v: {
        type: Object,
        required: true,
      }
    },

    computed: {
      email: {
        get () {
          return this.value
        },
        set (value) {
          this.$emit("input", value)
        },
      },
      emailErrors () {
        const errors = []
        if (!this.v.$dirty) return errors
        !this.v.required && errors.push('E-mail is required')
        !this.v.email && errors.push('Please enter a valid e-mail')
        return errors
      },
    },
  }
</script>
