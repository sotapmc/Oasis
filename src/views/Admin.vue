<template>
  <div class="container admin-main">
    <h1 class="main-title">管理界面</h1>
    <div class="info-container">
      <div class="info-item">
        <span class="subtitle">请求总量</span>
        <span class="number">{{ total_requests }}</span>
      </div>
      <div class="info-item">
        <span class="subtitle">通过总量</span>
        <span class="number">{{ passed_requests }}</span>
      </div>
      <div class="info-item">
        <span class="subtitle">通过概率</span>
        <span class="number">{{ pass_percentage }}</span>
        <span style="font-size: 20px">&emsp;%</span>
      </div>
    </div>
    <div class="card-container">
      <div class="card-group">
        <div
          @click="$router.push({name: 'admin-view-requests'})"
          class="function-card paper-card red"
        >
          <md-icon class="mdi mdi-magnify" />
          <h1>查看申请</h1>
          <p>查看待处理的申请，并进行审查和操作。</p>
        </div>
        <div class="function-card paper-card orange">
          <md-icon class="mdi mdi-chart-bar" />
          <h1>数据统计</h1>
          <p>根据最近的申请信息，进行数据方面的系统化分析。</p>
        </div>
        <div class="function-card paper-card amber">
          <md-icon class="mdi mdi-delete" />
          <h1>回收站</h1>
          <p>查看已经被标记为「删除」的所有申请。</p>
        </div>
      </div>
      <div class="card-group">
        <div class="function-card paper-card green">
          <md-icon class="mdi mdi-history" />
          <h1>历史记录</h1>
          <p>查看自 Oasis 部署以来所有的申请数据及状态。</p>
        </div>
        <div class="function-card paper-card blue">
          <md-icon class="mdi mdi-cog" />
          <h1>系统设置</h1>
          <p>高度定制 Oasis 的功能和自动计划任务。</p>
        </div>
        <div class="function-card paper-card purple">
          <md-icon class="mdi mdi-brush" />
          <h1>表单个性化</h1>
          <p>创建、管理表单内容，增加或修改已有信息。</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      total_requests: 0,
      passed_requests: 0,
      pass_percentage: 0
    };
  },
  created() {
    this.$server.post(
      "get",
      ["total-requests", "passed-requests", "passed-percentage"],
      r => {
        let data = r.data;
        this.total_requests = data[0];
        this.passed_requests = data[1];
        this.pass_percentage = data[2];
      }
    );
  }
};
</script>

<style lang="less" scoped>
.main-title {
  font-size: 42px;
  margin-bottom: 64px;
}

.admin-main {
  .info-container {
      -webkit-box-shadow: 0 3px 1px -2px rgba(0, 0, 0, 0.2),
    0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12);
  box-shadow: 0 3px 1px -2px rgba(0, 0, 0, 0.2), 0 2px 2px 0 rgba(0, 0, 0, 0.14),
    0 1px 5px 0 rgba(0, 0, 0, 0.12);
    border-radius: 2px;
    padding: 16px;
    margin-bottom: 16px;
    position: relative;
    width: 100%;

    .info-item {
      width: 33.3%;
      text-align: center;
      line-height: 1.5;
      .subtitle {
        font-size: 16px;
        color: #aaa;
        display: block;
      }

      .number {
          font-weight: bolder;
          font-size: 48px;
      }
      display: inline-block;
    }
  }
}

.paper-card {
  -webkit-box-shadow: 0 3px 1px -2px rgba(0, 0, 0, 0.2),
    0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12);
  box-shadow: 0 3px 1px -2px rgba(0, 0, 0, 0.2), 0 2px 2px 0 rgba(0, 0, 0, 0.14),
    0 1px 5px 0 rgba(0, 0, 0, 0.12);
  border-radius: 2px;
  padding: 16px;
  width: calc(33.3% - 11px);
}

.card-container {
  position: relative;
  width: 100%;

  .card-group {
    display: flex;
    .function-card {
      display: inline-block;
      margin-right: 8px;
      margin-left: 8px;
      color: white;
      cursor: pointer;
      position: relative;

      &:first-child {
        margin-left: 0;
      }

      &:last-child {
        margin-right: 0;
      }

      & > .mdi {
        color: white;
        opacity: 0.4;
        position: absolute;
        right: 42px;
        bottom: 42px;

        &::before {
          font-size: 90px;
        }
      }

      &.red {
        background-color: #e53935;
      }

      &.orange {
        background-color: #ff9800;
      }

      &.amber {
        background-color: #ffc107;
      }

      &.green {
        background-color: #4caf50;
      }

      &.blue {
        background-color: #2196f3;
      }

      &.purple {
        background-color: #673ab7;
      }
    }
    margin-top: 16px;

  }
}
</style>