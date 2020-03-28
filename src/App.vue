<template>
  <div id="app">
    <router-view v-if="router_available"/>
  </div>
</template>

<script>
import Vue from 'vue';

export default {
  data() {
    return {
      router_available: true
    }
  },
  methods: {
    setTheme(theme){
      let file = document.createElement('link');
      file.rel = 'stylesheet';
      file.href = '/css/themes/default-' + theme + '.css';
      document.head.appendChild(file);
    },
    reload() {
      this.router_available = false;
      this.$nextTick(() => {
        this.router_available = true;
      })
    }
  },
  mounted() {
    this.$server.getcfg("oasis.color-theme", r => {
      this.setTheme(r);
    });
    this.$bus.$on("reload", r => {
      this.reload();
    })
  },
}
</script>

<style lang="less" scoped>
#app {
  height: 100%;
  position: relative;
}
</style>