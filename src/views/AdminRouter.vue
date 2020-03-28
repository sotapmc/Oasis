<template>
  <div class="admin">
    <md-app md-mode="reveal">
      <md-app-toolbar class="md-primary">
        <md-button @click="$router.go(-1)" class="md-icon-button">
          <md-icon class="mdi mdi-arrow-left" />
        </md-button>
        <span class="md-title">Management</span>
      </md-app-toolbar>
      <md-app-content>
        <router-view />
      </md-app-content>
    </md-app>
  </div>
</template>

<script>
import Vue from "vue";
import MdApp from "vue-material/dist/components/MdApp";
import MdToolbar from "vue-material/dist/components/MdToolbar";
import MdContent from "vue-material/dist/components/MdContent";
import MdIcon from "vue-material/dist/components/MdIcon";

Vue.use(MdApp)
  .use(MdToolbar)
  .use(MdContent);

export default {};
</script>

