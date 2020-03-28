<template>
  <div class="admin">
    <md-empty-state class="empty-state-full-screen" v-if="empty">
      <md-icon class="md-empty-state-icon mdi mdi-help-circle-outline" />
      <span class="md-empty-state-label">空页面</span>
      <span class="md-empty-state-description">当前页面没有任何内容</span>
      <md-button @click="$router.go(-1)" class="md-primary md-raised">&laquo; 后退</md-button>
    </md-empty-state>
    <md-table class="admin-table" v-if="error === false && !empty" v-model="applications" md-card>
      <md-table-toolbar>
        <h1 class="md-title">审核请求</h1>
      </md-table-toolbar>

      <md-progress-spinner style="display: block; margin: auto" v-if="loading" class="loading" md-mode="indeterminate"/>

      <md-table-row v-if="loading === false" slot="md-table-row" slot-scope="{ item }">
        <md-table-cell md-label="ID" md-numeric>{{ item.id }}</md-table-cell>
        <md-table-cell md-label="用户名">{{ item.username }}</md-table-cell>
        <md-table-cell md-label="邮箱">{{ item.email }}</md-table-cell>
        <md-table-cell md-label="LGBT">{{ item.lgbt }}</md-table-cell>
        <md-table-cell md-label="状态">{{ item.status }}</md-table-cell>
        <md-table-cell md-label="操作">
          <md-button
            @click="target_id = item.id; setDialog('delete-confirm')"
            class="md-icon-button"
          >
            <md-icon class="mdi mdi-delete" />
          </md-button>
          <md-button @click="target_id = item.id" class="md-icon-button">
            <md-icon class="mdi mdi-magnify" />
          </md-button>
        </md-table-cell>
      </md-table-row>
    </md-table>
    <Pagnition :current="Number(page)" :max="Number(max_page)"/>
    <md-dialog
      v-if="error !== false"
      :md-close-on-esc="false"
      :md-click-outside-to-close="false"
      :md-active.sync="error_dialog"
    >
      <md-dialog-title>错误</md-dialog-title>
      <md-empty-state>
        <md-icon class="md-empty-state-icon mdi mdi-close" />
        <span class="md-empty-state-label">{{ getErrorTitle() }}</span>
        <span class="md-empty-state-description">{{ getErrorDesc() }}</span>
      </md-empty-state>
      <md-dialog-actions>
        <md-button class="md-primary md-raised" @click="$router.go(-1)">后退</md-button>
      </md-dialog-actions>
    </md-dialog>
    <md-dialog :md-active.sync="dialog">
      <md-dialog-title>{{ dialog_title }}</md-dialog-title>
      <md-empty-state>
        <md-icon class="md-empty-state-icon mdi" :class="dialog_icon" />
        <span class="md-empty-state-label">{{ dialog_label }}</span>
        <span class="md-empty-state-description">{{ dialog_desc }}</span>
      </md-empty-state>
      <md-dialog-actions>
        <md-button v-if="dialog_cancel" class="md-primary" @click="dialog = false">取消</md-button>
        <md-button class="md-primary" @click="dialog_confirm_action">确定</md-button>
      </md-dialog-actions>
    </md-dialog>
  </div>
</template>

<script>
import Vue from "vue";
import MdTable from "vue-material/dist/components/MdTable";
import MdCard from "vue-material/dist/components/MdCard";
import MdContent from "vue-material/dist/components/MdContent";
import MdDialog from "vue-material/dist/components/MdDialog";
import MdEmptyState from "vue-material/dist/components/MdEmptyState";
import MdIcon from "vue-material/dist/components/MdIcon";
import MdTooltip from "vue-material/dist/components/MdTooltip";
import MdRipple from "vue-material/dist/components/MdRipple";
import Pagnition from '../components/Pagination.vue';

Vue.use(MdTable)
  .use(MdCard)
  .use(MdContent)
  .use(MdEmptyState)
  .use(MdIcon)
  .use(MdTooltip)
  .use(MdRipple);

export default {
  data() {
    return {
      page: 1,
      max_page: -1,
      applications: [],
      error: false,
      error_dialog: false,
      target_id: 0,
      empty: false,
      // custom dialog
      dialog: false,
      dialog_title: "",
      dialog_label: "",
      dialog_desc: "",
      dialog_icon: "",
      dialog_confirm_action: "",
      dialog_cancel: true,
      //loading
      loading: true,
    };
  },
  methods: {
    setDialog(type) {
      // set back to default
      this.dialog = false;
      this.dialog_cancel = true;
      switch (type) {
        case "delete-confirm":
          this.dialog_title = "删除确认";
          this.dialog_label = "是否确认？";
          this.dialog_desc = "将会把此申请移至回收站";
          this.dialog_icon = "mdi-delete";
          this.dialog_confirm_action = () => {
            this.deleteApplication();
            this.dialog = false;
          };
        break;

        case "complete":
          this.dialog_title = "成功";
          this.dialog_label = "成功";
          this.dialog_desc = "您的操作成功完成";
          this.dialog_icon = "mdi-check";
          this.dialog_cancel = false;
          this.dialog_confirm_action = () => {
            this.dialog = false;
            this.$bus.$emit("reload");
          };
        break;

        default:
          return;
      }
      this.dialog = true;
    },
    getErrorTitle() {
      if (!this.error) return;
      let errorTitleDict = {
        invalid_page: "无效页码"
      };
      return errorTitleDict[this.error];
    },
    getErrorDesc() {
      if (!this.error) return;
      let errorDescDict = {
        invalid_page: "您所输入的页码不存在"
      };
      return errorDescDict[this.error];
    },
    setError(error) {
      if (error === false) {
        this.error = false;
        this.error_dialog = false;
      } else {
        this.error = error;
        this.error_dialog = true;
      }
    },
    deleteApplication() {
      this.$server.post(
        "remove-application",
        {
          id: this.target_id
        },
        r => {
          if (r.data === "ok") {
            this.setDialog("complete");
          }
        }
      );
    }
  },
  components: {
    Pagnition
  },
  created() {
    this.page = this.$route.params.page ? this.$route.params.page : 1;
    if (this.page <= 0 || isNaN(Number(this.page))) {
      this.setError("invalid_page");
      return;
    }
    this.$server.post("get-max-page", {}, r => {
      if (r.data === 0) {
        this.empty = true;
        return;
      }
      if (this.page > r.data) {
        
        this.setError("invalid_page");
      } else {
        this.max_page = r.data;
        this.$server.post(
          "get-applications",
          {
            page: this.page
          },
          r => {
            this.loading = false;
            this.applications = r.data;
          }
        );
      }
    });
  }
};
</script>

<style lang="less" scoped>
.admin {
  height: 100%;
  position: relative;

  .admin-table {
      @media screen and (min-width: 1024px) {
          margin: 64px;
      }   
  }
}
</style>