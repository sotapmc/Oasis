<template>
  <div class="pagination">
    <md-button v-if="current > 1" class="page-number-button md-icon-button" @click="goto(1)">
      <md-icon class="mdi mdi-chevron-double-left"/>
    </md-button>
    <md-button v-if="current !== 1" class="page-number-button md-icon-button" @click="goto(current - 1)">
      <md-icon class="mdi mdi-arrow-left" />
    </md-button>
    <md-button
      class="page-number-button md-icon-button"
      @click="goto(n)"
      :class="n === current ? 'active' : ''"
      v-for="n in getRange(((max - current) >= 9) ? current : max - 9, ((max - current) >= 9) ? current + 9 : max)"
      :key="n"
    >{{ n }}</md-button>
    <md-button v-if="current !== max" class="page-number-button md-icon-button" @click="goto(current + 1)">
      <md-icon class="mdi mdi-arrow-right" />
    </md-button>
    <md-button v-if="current < max" class="page-number-button md-icon-button" @click="goto(max)">
      <md-icon class="mdi mdi-chevron-double-right"/>
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
      range_max: 0
    };
  },
  methods: {
    getRange,
    goto(page) {
      if (page === this.current || page < 1 || page > this.max || isNaN(page))
        return;
      this.$router.push({
        name: "admin-view-requests",
        params: {
          page
        }
      });
      this.$bus.$emit("reload");
    }
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