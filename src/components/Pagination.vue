<template>
  <div v-if="active" class="pagination">
    <md-button v-if="current > 1" class="page-number-button md-icon-button" @click="goto(1)">
      <md-icon class="mdi mdi-arrow-collapse-left" />
    </md-button>
    <md-button
      v-if="current !== 1"
      class="page-number-button md-icon-button"
      @click="goto(current - 1)"
    >
      <md-icon class="mdi mdi-arrow-left" />
    </md-button>
    <md-button
      @click="pageSelectionDialog = true"
      class="page-number-button md-icon-button"
      v-if="!isPC()"
    >
      <md-icon class="mdi mdi-apps" />
    </md-button>
    <div style="display: inline-block" v-if="isPC()">
      <md-button
        class="page-number-button md-icon-button"
        @click="goto(n)"
        :class="n === current ? 'active' : ''"
        v-for="n in getRange(((max_page - current) > 9) ? current : max_page - 8, ((max_page - current) > 9) ? current + 9 : max_page)"
        :key="n"
      >{{ n }}</md-button>
    </div>
    <md-button
      v-if="current !== max_page"
      class="page-number-button md-icon-button"
      @click="goto(current + 1)"
    >
      <md-icon class="mdi mdi-arrow-right" />
    </md-button>
    <md-button
      v-if="current < max_page"
      class="page-number-button md-icon-button"
      @click="goto(max_page)"
    >
      <md-icon class="mdi mdi-arrow-collapse-right" />
    </md-button>
    <md-dialog v-if="!isPC()" :md-active.sync="pageSelectionDialog">
      <md-dialog-title>跳转到...</md-dialog-title>
      <md-dialog-content>
        <md-field :class="jumptoInvalid">
          <md-icon class="mdi mdi-numeric" />
          <label>页码</label>
          <md-input type="number" min="1" :max="max_page" step="1" v-model="jumpto" />
          <span class="md-helper-text">正整数，不应超过 {{max_page}}</span>
          <span class="md-error">无效页码</span>
        </md-field>
      </md-dialog-content>
      <md-dialog-actions>
        <md-button @click="pageSelectionDialog = false" class="md-primary">取消</md-button>
        <md-button
          @click="goto(jumpto); pageSelectionDialog = false"
          :disabled="jumptoInvalid === 'md-invalid'"
          class="md-raised md-primary"
        >前往第 {{ isPagenumValid() ? jumpto : 1 }} 页</md-button>
      </md-dialog-actions>
    </md-dialog>
  </div>
</template>

<script>
import Vue from "vue";
import MdButton from "vue-material/dist/components/MdButton";
import MdIcon from "vue-material/dist/components/MdIcon";
import MdDialog from "vue-material/dist/components/MdDialog";
import MdField from "vue-material/dist/components/MdField";
import { getRange, isPC } from "../functions";

Vue.use(MdButton)
  .use(MdIcon)
  .use(MdDialog)
  .use(MdField);

export default {
  props: ["max", "current"],
  data() {
    return {
      range_max: 0,
      max_page: 0,
      active: false,
      // only on mobile
      jumpto: 1,
      pageSelectionDialog: false,
      jumptoInvalid: ""
    };
  },
  methods: {
    getRange,
    goto(page) {
      if (
        page === this.current ||
        page < 1 ||
        page > this.max_page ||
        isNaN(page)
      )
        return;
      this.$router.push({
        name: "admin-view-requests",
        params: {
          page
        }
      });
      this.$bus.$emit("reload");
    },
    isPC,
    isPagenumValid() {
      if (isPC()) {
        return true;
      } else {
        return (
          this.jumpto >= 1 &&
          this.jumpto <= this.max_page &&
          // is float?
          this.jumpto % 1 === 0 &&
          this.jumpto.toString().indexOf(".") === -1
        );
      }
    }
  },
  mounted() {
    // automatically get max page count from server if not specified.
    this.$server.post("get-max-page", {}, r => {
      this.max_page = this.max !== false ? this.max : r.data;
      this.active = true;
    });
  },
  watch: {
    jumpto(v, ov) {
      if (this.isPagenumValid()) {
        this.jumptoInvalid = "";
      } else {
        this.jumptoInvalid = "md-invalid";
      }
    }
  }
};
</script>

<style lang="less" scoped>
.pagination {
  .page-number-button {
    border: 1px solid rgba(0, 0, 0, 0.1);
    @media screen and (min-width: 1024px) {
      margin-left: 16px;
      margin-right: 16px;
    }
    margin-left: 8px;
    margin-right: 8px;

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