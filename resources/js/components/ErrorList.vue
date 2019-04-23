<template>
  <ul
    v-if="errorList.length"
    class="message-list error py-2">
    <li
      v-for="(error, index) in errorList"
      :key="index">
      {{ error.message }}
    </li>
  </ul>
</template>

<script>
  export default {
    props: {
      errorObject: {
        type: Object,
        default: null
      },
    },

    computed: {
      errorList () {
        const errors = []
        const obj = this.errorObject
        if (obj) {
          const self = this
          Object.keys(obj).forEach(function (key) {
            self.processKey(errors, obj, key)
          })
        }
        return errors
      }
    },

    methods: {
      processKey (errors, obj, key) {
        const value = obj[key]
        if (Array.isArray(value) || typeof value === "object") {
          const self = this
          Object.keys(value).forEach(function (index) {
            self.processKey(errors, value, index)
          })
        } else {
          errors.push({key: key, message: value})
        }
      },
    },
  }
</script>
