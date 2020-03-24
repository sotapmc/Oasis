<template>
  <div id="app">
    <router-view/>
  </div>
</template>

<script>
import Vue from 'vue';

export default {
  methods: {
    setTheme(theme){
      let file = document.createElement('link');
      file.rel = 'stylesheet';
      file.href = './css/themes/default-' + theme + '.css';
      document.head.appendChild(file);
    }
  },
  mounted() {
    this.$server.getcfg("oasis.color-theme", r => {
      this.setTheme(r);
    })
  }
}
</script>