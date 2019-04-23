<template>
  <div
    class="flex flex-col flex-wrap mb-6">
    <label
      v-if="label"
      v-html="label"
      class="block text-grey-darker text-sm font-bold mb-2"/>
    <datetime
      zone="UTC"
      value-zone="UTC"
      v-model="model"
      v-bind="$attrs"
      :type="type"
      :placeholder="label"
      :minute-step="5"
      :input-class="inputClass"/>
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

      type: {
        type: String,
        default: 'datetime',
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
      inputClass () {
        const baseClass = "shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker leading-tight focus:outline-none focus:shadow-outline";
        return this.errorMessages.length ? baseClass + ' border-red' : baseClass;
      },
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
