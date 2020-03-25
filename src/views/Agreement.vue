<template>
  <div class="agreement">
    <div v-if="!load" class="loading-container">
      <md-progress-spinner class="loading" md-mode="indeterminate"></md-progress-spinner>
    </div>
    <article class="content" v-if="load" v-html="agreement"></article>
  </div>
</template>

<script>
import Vue from "vue";
import MdProgress from "vue-material/dist/components/MdProgress";

Vue.use(MdProgress);

export default {
  data() {
    return {
      agreement: "",
      load: false
    };
  },
  mounted() {
    this.$server.post("get-agreement", {}, r => {
      this.load = true;
      this.agreement = r.data;
    });
  }
};
</script>