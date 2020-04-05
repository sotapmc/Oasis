<template>
  <div class="container application-review">
    <md-empty-state v-if="!isValidPage() && !empty">
      <md-icon class="md-empty-state-icon mdi mdi-alert" />
      <span class="md-empty-state-label">无效页面</span>
      <span class="md-empty-state-description">您输入的请求 ID 无效</span>
      <md-button class="md-primary md-raised" @click="$router.go(-1)">&laquo; 后退</md-button>
    </md-empty-state>
    <md-empty-state v-if="isValidPage() && empty">
      <md-icon class="md-empty-state-icon mdi mdi-help-circle-outline" />
      <span class="md-empty-state-label">空页面</span>
      <span class="md-empty-state-description">此编号的请求不存在</span>
      <md-button class="md-primary md-raised" @click="$router.go(-1)">&laquo; 后退</md-button>
    </md-empty-state>
    <div class="loading-container" v-if="!load">
      <md-progress-spinner class="loading" md-mode="indeterminate" />
    </div>
    <div v-if="isValidPage() && load && !empty" class="content">
      <div class="header">
        <h1>来自玩家 {{ username }} 的申请</h1>
        <span class="application-number">#{{ id }}</span>
        <span class="label" :class="status">
          {{ status }}
          <md-tooltip>{{ dataTranslate("status-tooltip", status) }}</md-tooltip>
        </span>
      </div>
      <div class="toolbar">
        <md-button
          :class="removed ? 'md-raised' : ''"
          class="md-icon-button removed-mark"
          @click="toggleRemoveApplication()"
        >
          <md-icon class="mdi mdi-delete" />
          <md-tooltip>删除</md-tooltip>
        </md-button>
        <md-button class="md-icon-button">
          <md-icon class="mdi mdi-information" />
          <md-tooltip>详细信息</md-tooltip>
        </md-button>
        <md-button
          :class="isSpam ? 'md-raised' : ''"
          class="md-icon-button spam-mark"
          @click="toggleSpam('spam')"
        >
          <md-icon class="mdi mdi-alert" />
          <md-tooltip>Spam</md-tooltip>
        </md-button>
        <md-button
          :class="overallRank + basicRank >= 16 ? 'md-raised' : ''"
          class="md-icon-button high-quality-mark"
          @click="highQualityTipDialog = true"
        >
          <md-icon class="mdi mdi-check-decagram" />
          <md-tooltip>高质量</md-tooltip>
        </md-button>
      </div>
      <div class="questions">
        <md-table class="basic-information-table" md-card>
          <md-table-row>
            <md-table-head>年龄</md-table-head>
            <md-table-head>性别</md-table-head>
            <md-table-head>电子邮箱</md-table-head>
            <md-table-head>LGBT</md-table-head>
            <md-table-head>来源</md-table-head>
          </md-table-row>
          <md-table-row>
            <md-table-cell>{{ age }}</md-table-cell>
            <md-table-cell>{{ gender }}</md-table-cell>
            <md-table-cell>{{ email }}</md-table-cell>
            <md-table-cell>{{ lgbt }}</md-table-cell>
            <md-table-cell>{{ come_from }}</md-table-cell>
          </md-table-row>
        </md-table>

        <div class="rank-section">
          <span @click="openRankDialog('basic')" class="rank">
            <span class="given">{{ basicRank }}</span>
            /
            <span class="full">3</span>
          </span>
        </div>

        <h1 style="margin-bottom: 32px">主观问答题</h1>
        <div class="question-item">
          <h2>您目前正做些什么？</h2>
          <p>{{ focusing }}</p>
        </div>
        <div class="question-item">
          <h2>简单介绍一下您自己吧。</h2>
          <p>{{ introduction }}</p>
        </div>
        <div class="question-item">
          <h2>在 Minecraft 领域中，您最想要做到的事情是什么呢？</h2>
          <p>{{ want_to_do }}</p>
        </div>
        <div class="question-item">
          <h2>除了 Minecraft，您是否还玩其它游戏？能否阐述一下您喜欢它的理由呢？</h2>
          <p>{{ preferred_games }}</p>
        </div>
        <div class="question-item">
          <h2>为了让我们更了解您，请告诉我们一个或多个您的社交账户地址。</h2>
          <p>{{ links }}</p>
        </div>

        <div class="rank-section">
          <span @click="openRankDialog('overall')" class="rank">
            <span class="given">{{ overallRank }}</span>
            /
            <span class="full">17</span>
          </span>
        </div>
        <div class="end">
          <div class="divider" />
          <div class="rank-section">
            <span class="rank">
              <span class="given">{{ Number(overallRank) + Number(basicRank) }}</span>
              /
              <span class="full">20</span>
            </span>
          </div>
          <div class="commented-area" v-if="commented">
            <h1>评价</h1>
            <p>{{ comment }}</p>
            <md-button class="md-primary" @click="commented = false">更改</md-button>
          </div>
          <div class="comment-area" v-else>
            <md-field>
              <label>评价...</label>
              <md-textarea type="text" v-model="comment" maxlength="1000" />
            </md-field>
            <md-button @click="submit()" class="md-primary md-raised">提交</md-button>
          </div>
        </div>
        <md-dialog :md-active.sync="rankDialog">
          <md-dialog-title>评分</md-dialog-title>
          <md-dialog-content>
            <md-field :class="rankInvalid">
              <md-icon class="mdi mdi-star" />
              <label>输入评分</label>
              <md-input type="number" v-model="rank" />
              <span class="md-helper-text">应不大于 {{ currentMaxRank }}</span>
              <span class="md-error">无效的分数</span>
            </md-field>
          </md-dialog-content>
          <md-dialog-actions>
            <md-button @click="rankDialog = false" class="md-primary">取消</md-button>
            <md-button
              :disabled="rankInvalid === 'md-invalid'"
              @click="setRank(); rankDialog = false"
              class="md-primary md-raised"
            >确定</md-button>
          </md-dialog-actions>
        </md-dialog>
        <md-dialog :md-active.sync="highQualityTipDialog">
          <md-dialog-content>
            <md-empty-state>
              <md-icon
                :style="overallRank + basicRank >= 16 ? 'color: #4caf50' : ''"
                class="md-empty-state-icon mdi mdi-check-decagram"
              />
              <span class="md-empty-state-label">高质量申请</span>
              <p class="md-empty-state-description">高于 16 分的申请将会被标记为高质量，便于日后进行统计</p>
            </md-empty-state>
          </md-dialog-content>
        </md-dialog>
        <md-snackbar
          md-position="center"
          :md-duration="3000"
          md-persistant
          :md-active.sync="snackbar"
        >
          <span>{{ snackbarMsg }}</span>
        </md-snackbar>
      </div>
    </div>
  </div>
</template>

<script>
import Vue from "vue";
import MdTable from "vue-material/dist/components/MdTable";
import MdCard from "vue-material/dist/components/MdCard";
import MdButton from "vue-material/dist/components/MdButton";
import MdField from "vue-material/dist/components/MdField";
import MdDialog from "vue-material/dist/components/MdDialog";
import MdTooltip from "vue-material/dist/components/MdTooltip";
import MdProgress from "vue-material/dist/components/MdProgress";
import MdSnackbar from "vue-material/dist/components/MdSnackbar";

Vue.use(MdTable)
  .use(MdCard)
  .use(MdButton)
  .use(MdField)
  .use(MdDialog)
  .use(MdTooltip)
  .use(MdProgress)
  .use(MdSnackbar);

export default {
  data() {
    return {
      id: 0,
      username: "",
      age: "",
      gender: "",
      email: "",
      come_from: "",
      lgbt: "",
      focusing: "",
      introduction: "",
      want_to_do: "",
      preferred_games: "",
      links: "",
      status: "",
      // controller variable
      load: false,
      empty: false,
      isSpam: false,
      removed: false,
      // rank
      rank: 0,
      basicRank: 0,
      overallRank: 0,
      currentMaxRank: 0,
      // dialog
      rankDialog: false,
      rankType: "",
      rankInvalid: "",
      highQualityTipDialog: false,
      // comment
      comment: "",
      commented: false,
      // snackbar
      snackbar: false,
      snackbarMsg: ""
    };
  },
  created() {
    // 获取请求内容
    this.$server.post(
      "get-application",
      {
        id: this.$route.params.id
      },
      r => {
        this.load = true;
        if (r.data !== null) {
          Object.keys(r.data).forEach(k => {
            this[k] = this.dataTranslate(k, r.data[k]);
          });
          this.removed = r.data.removed === "yes";
          // 获取对请求的审核内容
          this.$server.post(
            "get-review",
            {
              id: this.$route.params.id
            },
            r => {
              let data = r.data;
              if (data !== null) {
                // 审核数据处理
                this.commented = data.comment != "";
                this.comment = data.comment;
                this.basicRank = data.basic_rank;
                this.overallRank = data.overall_rank;
                this.isSpam = data.spam === "yes";
              } else {
                // do nothing
              }
            }
          );
        } else {
          this.empty = true;
        }
      }
    );
  },
  methods: {
    dataTranslate(type, data) {
      switch (type) {
        case "age":
          if (data !== "") {
            this.basicRank += 1;
          }
          return data === "" ? "未填写" : data;

        case "gender":
          return {
            male: "男",
            female: "女",
            others: "其它",
            secret: "保密"
          }[data];

        case "come_from":
          return {
            mcbbs: "MCBBS",
            douban: "豆瓣",
            nga: "NGA 论坛",
            mcmod: "MCMOD 百科",
            zhihu: "知乎",
            recommended: "朋友推荐",
            other: "其它渠道"
          }[data];

        case "lgbt":
          if (data === "yes") {
            this.basicRank += 2;
          } else {
            this.basicRank += 1;
          }
          return data === "yes" ? "是" : "否";

        case "status-tooltip":
          return {
            pending: "待审核",
            passed: "已通过",
            invalid: "无效申请"
          }[data];

        default:
          return data;
      }
    },
    isValidPage() {
      let page = Number(this.$route.params.id);

      return Number.isInteger(page) && page > 0;
    },
    openRankDialog(type) {
      this.rankType = type;
      this.rank = 0;
      if (type === "basic") {
        this.currentMaxRank = 3;
      } else if (type === "overall") {
        this.currentMaxRank = 17;
      }
      this.rankDialog = true;
    },
    setRank() {
      this[this.rankType + "Rank"] = this.rank;
    },
    submit() {
      this.$server.post(
        "submit-review",
        {
          basic_rank: Number(this.basicRank),
          overall_rank: Number(this.overallRank),
          comment: this.comment,
          id: this.$route.params.id,
          username: this.username
        },
        r => {
          if (r.data === "ok") {
            let total_rank = Number(this.basicRank) + Number(this.overallRank);
            this.$server.post(
              "update-application-status",
              {
                status: total_rank >= 12 ? "passed" : "invalid"
              },
              r => {
                if (r.data === "ok") {
                  this.snackbarMsg =
                    (total_rank >= 12
                      ? "已通过该申请"
                      : "已将该申请标记为无效") + "，页面将自动刷新";
                  this.snackbar = true;
                  setTimeout(() => {
                    this.$router.go(0);
                  }, 3000);
                }
              }
            );
          }
        }
      );
    },
    toggleSpam() {
      this.$server.post(
        "toggle-spam",
        {
          id: this.$route.params.id
        },
        r => {
          if (r.data === "ok") {
            this.snackbarMsg =
              this.isSpam === true
                ? "已取消标记为 Spam"
                : "已将此申请标记为 Spam";
            this.snackbar = true;
            this.isSpam = !this.isSpam;
          }
        }
      );
    },
    toggleRemoveApplication() {
      this.$server.post("toggle-application", {
        id: this.$route.params.id,
        action: this.removed ? "recover" : "remove",
      }, r => {
        if (r.data === "ok") {
          this.snackbarMsg = this.removed ? "成功还原此申请" : "成功移除此申请";
          this.snackbar = true;
          this.removed = !this.removed;
        }
      })
    }
  },
  watch: {
    rank(v) {
      if (
        Number.isInteger(Number(this.rank)) &&
        this.rank >= 0 &&
        this.rank.length >= 1
      ) {
        if (this.rankType === "basic") {
          if (this.rank <= 3) {
            this.rankInvalid = "";
          } else {
            this.rankInvalid = "md-invalid";
          }
        } else if (this.rankType === "overall") {
          if (this.rank <= 17) {
            this.rankInvalid = "";
          } else {
            this.rankInvalid = "md-invalid";
          }
        }
      } else {
        this.rankInvalid = "md-invalid";
      }
    }
  }
};
</script>

<style lang="less" scoped>
.rank-style {
  margin-top: 16px;
  .rank {
    cursor: pointer;

    .given {
      font-weight: bold;
      font-size: 24px;
      color: var(--md-theme-default-primary);
    }

    .full {
      color: #aaa;
    }
  }
}

.application-review {
  padding: 32px;
  background-color: white;
  border-radius: 2px;
  -webkit-box-shadow: 0 3px 1px -2px rgba(0, 0, 0, 0.2),
    0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12);
  box-shadow: 0 3px 1px -2px rgba(0, 0, 0, 0.2), 0 2px 2px 0 rgba(0, 0, 0, 0.14),
    0 1px 5px 0 rgba(0, 0, 0, 0.12);
  position: relative;
}

.toolbar {
  position: absolute;
  top: 32px;
  right: 32px;

  .high-quality-mark.md-raised {
    background-color: #4caf50;
  }

  .spam-mark.md-raised {
    background-color: #f44336;
  }

  .removed-mark.md-raised {
    background-color: #ffc107;
  }

  .md-raised .mdi::before {
    color: white;
  }
}

.header {
  display: flex;
  align-items: flex-end;
  h1 {
    display: inline;
    margin-right: 16px;
    font-size: 36px;
    // nzsnmxsmgh
    margin-bottom: 0;
    line-height: 1;
  }

  .application-number {
    color: #aaa;
    font-size: 24px;
    font-weight: 300;
  }
}

.questions {
  margin-top: 32px;

  .basic-information-table {
    margin: 0;
  }

  .divider {
    margin-top: 32px;
    margin-bottom: 16px;
  }

  .rank-section {
    text-align: right;
    .rank-style;
  }
}

.end {
  margin-top: 32px;
  position: relative;

  .comment-area,
  .commented-area {
    .md-button {
      margin: auto;
      display: block;
    }

    margin-top: 16px;
  }

  /* .overall-rank {
    display: block;
    margin: auto;
    text-align: center;

    .given {
      font-size: 32px !important;
    }
    .rank-style;

    margin-bottom: 32px;
  }*/

  .rank-section {
    text-align: right;
    .rank-style;

    .given {
      font-size: 36px !important;
    }
  }
}
</style>