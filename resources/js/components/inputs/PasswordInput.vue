<template>
  <VTextField
    v-model="password"
    :error-messages="passwordErrors"
    :label="label"
    :hint="hint"
    type="password"
    @blur="v.$touch">
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
      label: {
        type: String,
        default: null,
      },
      passwordType: {
        type: String,
        required: true,
      },
      v: {
        type: Object,
        required: true,
      }
    },

    computed: {
      password: {
        get () {
          return this.value
        },
        set (value) {
          this.$emit("input", value)
        },
      },
      passwordErrors () {
        const errors = []
        if (!this.v.$dirty) return errors
        if (this.passwordType === 'login') {
          !this.v.required && errors.push('Password is required')
        } else if (this.passwordType === 'current') {
          !this.v.required && errors.push('Current password is required')
        } else if (this.passwordType === 'new') {
          !this.v.required && errors.push('Password is required')
          !this.v.minLength && errors.push('Password should be at least 6 characters')
        } else if (this.passwordType === 'confirm') {
          !this.v.required && errors.push('Password confirmation is required')
          !this.v.sameAs && errors.push('Passwords do not match')
        }
        return errors
      },
      hint () {
        if (this.passwordType === 'new') {
          return 'Passwords need to be at least 6 characters'
        }
        return ''
      }
    },
  }
</script>
