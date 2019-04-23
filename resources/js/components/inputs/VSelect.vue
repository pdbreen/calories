<template>
  <div
    class="flex flex-wrap mb-6">
    <label
      v-if="label"
      v-html="label"
      class="block text-grey-darker text-sm font-bold mb-2"/>
    <select
      v-model="model"
      v-bind="$attrs"
      :class="{'border-red': errorMessages.length}"
      class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker leading-tight focus:outline-none focus:shadow-outline">
      <option v-for="option in options" :value="option.value">{{option.label}}</option>
    </select>
    <p
      v-if="hint && !errorMessages.length"
      class="text-sm text-grey"
      v-html="hint"/>
    <div v-if="errorMessages.length" class="mt-1">
      <p
        v-for="(e, i) in errorMessages"
        :key="i"
        class="text-red text-xs italic mt-1"
        v-html="e"/>
    </div>
  </div>
</template>

<script>
  export default {
    props: {
      value: {
        type: [String, Number],
        default: null,
      },

      label: {
        type: String,
        default: null,
      },

      options: {
        type: Array,
        default () {
          return []
        },
      },

      hint: {
        type: String,
        default: null,
      },

      errorMessages: {
        type: Array,
        default () {
          return []
        },
      },
    },

    computed: {
      model: {
        get () {
          return this.value
        },
        set (value) {
          this.$emit("input", value)
        },
      }
    },

  }
</script>

<style scoped lang="scss">

</style>
