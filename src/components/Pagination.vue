<template>
  <div v-if="active" class="pagination">
    <md-button v-if="current > 1" class="page-number-button md-icon-button" @click="goto(1)">
      <md-icon class="mdi mdi-chevron-double-left" />
    </md-button>
    <md-button
      v-if="current !== 1"
      class="page-number-button md-icon-button"
      @click="goto(current - 1)"
    >
      <md-icon class="mdi mdi-arrow-left" />
    </md-button>
    <md-button
      class="page-number-button md-icon-button"
      @click="goto(n)"
      :class="n === current ? 'active' : ''"
      v-for="n in getRange(((max_page - current) > 9) ? current : max_page - 8, ((max_page - current) > 9) ? current + 9 : max_page)"
      :key="n"
    >{{ n }}</md-button>
    <md-button
      v-if="current !== max_page"
      class="page-number-button md-icon-button"
      @click="goto(current + 1)"
    >
      <md-icon class="mdi mdi-arrow-right" />
    </md-button>
    <md-button v-if="current < max_page" class="page-number-button md-icon-button" @click="goto(max_page)">
      <md-icon class="mdi mdi-chevron-double-right" />
    </md-button>
  </div>
</template>

<script>
import Vue from "vue";
import MdButton from "vue-material/dist/components/MdButton";
import MdIcon from "vue-material/dist/components/MdIcon";
import { getRange } from "../functions";

Vue.use(MdButton).use(MdIcon);

export default {
  props: ["max", "current"],
  data() {
    return {
      range_max: 0,
      max_page: 0,
      active: false,
    };
  },
  methods: {
    getRange,
    goto(page) {
      if (page === this.current || page < 1 || page > this.max_page || isNaN(page))
        return;
      this.$router.push({
        name: "admin-view-requests",
        params: {
          page
        }
      });
      this.$bus.$emit("reload");
    }
  },
  mounted() {
    // automatically get max page count from server if not specified.
    this.$server.post("get-max-page", {}, r => {
      this.max_page = this.max !== false ? this.max : r.data;
      this.active = true;
    });
  }
};
</script>

<style lang="less" scoped>
.pagination {
  .page-number-button {
    border: 1px solid rgba(0, 0, 0, 0.1);
    margin-left: 16px;
    margin-right: 16px;

    &.active {
      background-color: var(--md-theme-default-primary);
      color: white;
      border: none;
    }
  }
  display: block;
  margin: auto;
  text-align: center;
}
</style>