<template>
  <div class="main">
    <div class="register" v-if="active === 'register'">
      <Logo class="logo" />
      <md-field :class="username_invalid">
        <md-icon class="mdi mdi-minecraft" />
        <label>正版 ID</label>
        <md-input required v-model="username" type="text" minlength="3" maxlength="18" />
        <span class="md-helper-text">在此填写您的 Minecraft 正版 ID</span>
        <span class="md-error">这不是一个有效的正版 ID</span>
      </md-field>
      <md-field :class="email_invalid">
        <md-icon class="mdi mdi-email" />
        <label>电子邮箱</label>
        <md-input required v-model="email" type="email" />
        <span class="md-helper-text">请填写常用邮箱，我们会将审核结果发送至此</span>
        <span class="md-error">无效的邮箱</span>
      </md-field>
      <md-field :class="age_invalid">
        <md-icon class="mdi mdi-cake" />
        <label>年龄</label>
        <md-input v-model="age" type="number" step="1" max="120" min="13" />
        <span class="md-helper-text">非必填，有助于我们更好地了解您的情况</span>
        <span class="md-error">此年龄不可用</span>
      </md-field>
      <md-field>
        <md-icon class="mdi mdi-gender-male-female" />
        <md-select style="margin-left: 14px;" v-model="gender">
          <md-option value="male">男</md-option>
          <md-option value="female">女</md-option>
          <md-option value="others">其它</md-option>
          <md-option value="secret">保密</md-option>
        </md-select>
        <span class="md-helper-text">您的性别</span>
      </md-field>
      <md-checkbox v-if="agreement_enabled" v-model="agreement">
        我已阅读并同意
        <router-link to="/agreement">{{ agreement_name }}</router-link>
      </md-checkbox>
      <md-button
        @click="active = 'information'"
        :disabled="checkInvalid()"
        class="btn md-primary md-raised"
      >继续 &raquo;</md-button>
    </div>
    <div class="information" v-if="active === 'information'">
      <md-steppers
        class="information-stepper"
        :md-active-step.sync="step"
        :md-linear="isPC()"
        :md-vertical="!isPC()"
      >
        <md-step id="1" md-label="来源" md-description="何处得知 SoTap">
          <h1>您从何处得知 SoTap？</h1>
          <md-field class="select">
            <md-select v-model="come_from">
              <md-option value="mcbbs">MCBBS</md-option>
              <md-option value="douban">豆瓣</md-option>
              <md-option value="nga">NGA 论坛</md-option>
              <md-option value="mcmod">MCMOD 百科</md-option>
              <md-option value="zhihu">知乎</md-option>
              <md-option value="recommended">朋友推荐</md-option>
              <md-option value="other">其它渠道</md-option>
            </md-select>
          </md-field>
          <md-button @click="next()" class="md-primary md-raised">下一步 &raquo;</md-button>
        </md-step>
        <md-step id="2" md-label="LGBT 调查" md-description="您是 LGBT 吗">
          <h1>您是否为 LGBT 群体的一员？</h1>
          <p>
            请如实填写。若不了解 LGBT 可查看百度百科：
            <a
              target="_blank"
              href="https://baike.baidu.com/item/LGBT"
            >https://baike.baidu.com/item/LGBT</a>
          </p>
          <md-field class="select">
            <md-select v-model="lgbt">
              <md-option value="yes">是</md-option>
              <md-option value="no">否</md-option>
            </md-select>
          </md-field>
          <md-button @click="next()" class="md-primary md-raised">下一步 &raquo;</md-button>
        </md-step>
        <md-step id="3" md-label="从事" md-description="正在做的事情">
          <h1>您目前正做些什么？</h1>
          <p>
            可填写您的学历（例如「学生，初三」）、您工作的公司或岗位。
            <span
              style="color: #aaa"
            >SoTap 保证您的隐私不会被用作任何除了审核以外的用途。我们的隐私政策：</span>
            <a target="_blank" href="https://sotap.org/privacy">https://sotap.org/privacy</a>。
          </p>
          <md-field>
            <label>答案</label>
            <md-textarea required v-model="focusing" maxlength="100"></md-textarea>
          </md-field>
          <md-button
            :disabled="focusing.length <= 2"
            @click="next()"
            class="md-primary md-raised"
          >下一步 &raquo;</md-button>
        </md-step>
        <md-step id="4" md-label="自我介绍" md-description="介绍一下自己">
          <h1>简单介绍一下您自己吧。</h1>
          <p>简单阐述一下您喜欢的或不喜欢的事物，擅长的任何事情以及性格特点。当然，如果您愿意，也可以添加更多信息！</p>
          <md-field>
            <label>答案</label>
            <md-textarea required v-model="introduction" maxlength="1000"></md-textarea>
          </md-field>
          <md-button
            :disabled="introduction.length <= 20"
            @click="next()"
            class="md-primary md-raised"
          >下一步 &raquo;</md-button>
        </md-step>
        <md-step id="5" md-label="想要做的事" md-description="想要做些什么">
          <h1>在 Minecraft 领域中，您最想要做到的事情是什么呢？</h1>
          <md-field>
            <label>答案</label>
            <md-textarea required v-model="want_to_do" maxlength="1000"></md-textarea>
          </md-field>
          <md-button
            :disabled="want_to_do.length <= 20"
            @click="next()"
            class="md-primary md-raised"
          >下一步 &raquo;</md-button>
        </md-step>
        <md-step id="6" md-label="其它游戏爱好" md-description="喜欢什么游戏">
          <h1>除了 Minecraft，您是否还玩其它游戏？能否阐述一下您喜欢它的理由呢？</h1>
          <p>若只有 Minecraft，则可以填写喜欢 Minecraft 的理由。</p>
          <md-field>
            <label>答案</label>
            <md-textarea required v-model="preferred_games" maxlength="1000"></md-textarea>
          </md-field>
          <md-button
            :disabled="preferred_games.length <= 20"
            @click="next()"
            class="md-primary md-raised"
          >下一步 &raquo;</md-button>
        </md-step>
        <md-step id="7" md-label="社交网站" md-description="您的社交网站">
          <h1>为了让我们更了解您，请告诉我们一个或多个您的社交账户地址。</h1>
          <p>
            这可以是您的个人博客，或开放的社交网站。例如微博、知乎、推特、Github、Instagram、P 站、A/B 站、或 V2EX 个人主页等。
            <strong>请勿填写 QQ 号或 QQ 空间等。</strong>
          </p>
          <md-field>
            <label>答案</label>
            <md-textarea v-model="links" maxlength="1000"></md-textarea>
          </md-field>
          <md-button
            :disabled="getLinks(links).length < 1"
            @click="submit_dialog = true"
            class="md-primary md-raised"
          >完成 &raquo;</md-button>
        </md-step>
      </md-steppers>
      <md-dialog :md-active.sync="submit_dialog">
        <md-dialog-title>提交申请</md-dialog-title>
        <md-empty-state>
          <md-icon class="md-empty-state-icon mdi mdi-send" />
          <span class="md-empty-state-label">您的申请即将提交</span>
          <span class="md-empty-state-description">如果一切就绪，按下「提交」按钮即可</span>
        </md-empty-state>
        <md-dialog-actions>
          <md-button class="md-primary" @click="submit_dialog = false">取消</md-button>
          <md-button class="md-primary md-raised" @click="submit()">提交</md-button>
        </md-dialog-actions>
      </md-dialog>
      <md-dialog :md-active.sync="info_dialog">
        <md-dialog-title>提交结果</md-dialog-title>
        <md-empty-state class="info-container" v-if="!submitting">
          <md-icon style="color: #2196f3" class="md-empty-state-icon mdi mdi-check" v-if="info_ok" />
          <md-icon class="md-empty-state-icon mdi mdi-close" v-if="info_error" />
          <span class="md-empty-state-label">{{ info_title }}</span>
          <span class="md-empty-state-description">{{ info }}</span>
        </md-empty-state>
        <md-dialog-actions>
          <md-button class="md-primary md-raised" @click="info_dialog = false">好</md-button>
        </md-dialog-actions>
      </md-dialog>
      <md-dialog :md-active.sync="submitting">
        <md-dialog-title>请稍等...</md-dialog-title>
        <md-progress-spinner class="loading" md-mode="indeterminate"></md-progress-spinner>
      </md-dialog>
    </div>
  </div>
</template>

<script>
import Vue from "vue";
import MdIcon from "vue-material/dist/components/MdIcon";
import MdField from "vue-material/dist/components/MdField";
import MdButton from "vue-material/dist/components/MdButton";
import MdMenu from "vue-material/dist/components/MdMenu";
import MdList from "vue-material/dist/components/MdList";
import MdCheckbox from "vue-material/dist/components/MdCheckbox";
import Logo from "../components/Logo";
import MdSteppers from "vue-material/dist/components/MdSteppers";
import { isPC, getLinks } from "../functions";
import MdDialog from "vue-material/dist/components/MdDialog";
import MdEmptyState from "vue-material/dist/components/MdEmptyState";
import MdProgress from "vue-material/dist/components/MdProgress";

Vue.use(MdIcon)
  .use(MdField)
  .use(MdButton)
  .use(MdMenu)
  .use(MdList)
  .use(MdCheckbox)
  .use(MdSteppers)
  .use(MdDialog)
  .use(MdEmptyState)
  .use(MdProgress);

export default {
  data() {
    return {
      // reg
      username: "",
      username_invalid: "",
      age: "",
      age_invalid: "",
      gender: "male",
      email: "",
      email_invalid: "",
      agreement: false,
      //information
      step: "1",
      come_from: "mcbbs",
      lgbt: "no",
      focusing: "",
      introduction: "",
      want_to_do: "",
      preferred_games: "",
      links: "",
      // controller
      active: "register",
      i: 0,
      submit_dialog: false,
      info_dialog: false,
      submitting: false,
      info_ok: true,
      info_error: false,
      info_title: "",
      info: "",
      //agreement
      agreement_name: "",
      agreement_enabled: false
    };
  },
  methods: {
    next(completed) {
      this.step = (Number(this.step) + 1).toString();
    },
    isPC,
    getLinks,
    // check if the results are invalid
    // if true that means something went wrong
    checkInvalid() {
      return (
        this.username.length === 0 ||
        this.email.length === 0 ||
        this.username_invalid === "md-invalid" ||
        this.age_invalid === "md-invalid" ||
        this.email_invalid === "md-invalid" ||
        (this.agreement !== true && this.agreement !== "not_enabled")
      );
    },
    submit() {
      this.submit_dialog = false;
      this.submitting = true;
      let data = {
        username: this.username,
        age: this.age,
        gender: this.gender,
        email: this.email,
        agreement: this.agreement,
        come_from: this.come_from,
        lgbt: this.lgbt,
        focusing: this.focusing,
        introduction: this.introduction,
        want_to_do: this.want_to_do,
        preferred_games: this.preferred_games,
        links: this.links
      };

      this.$server.post("SUBMIT", "application", data, r => {
        console.log(r);
        this.submitting = false;
        let data = r.data;
        if (data === "ok") {
          this.info_ok = true;
          this.info_title = "成功";
          this.info = "您的申请已提交，将在 24 小时内得到回复";
        } else {
          this.info_ok = false;
          this.info_error = true;
          switch (data) {
            case "pending":
              this.info_title = "正在审核中";
              this.info = "您的申请正在审核中，请耐心等待";
              break;

            case "too_much":
              this.info_title = "申请过多";
              this.info = "您提交的申请数量超出了我们的限额";
              break;

            case "blacklisted":
              this.info_title = "黑名单";
              this.info = "您的所有申请已被标记为黑名单";
              break;

            case "too_short":
              this.info_title = "内容缺失";
              this.info = "您填写的内容因过短而无效";
              break;

            case "bad_agreement":
              this.info_title = "协议失效";
              this.info = "您必须同意我们的协议才可提交申请";
              break;

            default:
              this.info_title = "失败";
              this.info = "您填写的数据无效";
          }
        }
        this.info_dialog = true;
      });
    }
  },
  watch: {
    username(ov, v) {
      if (/^[a-zA-Z0-9_]{3,18}$/.test(ov)) {
        this.username_invalid = "";
      } else {
        this.username_invalid = "md-invalid";
      }
    },
    age(ov, v) {
      if (ov >= 13 && ov <= 120) {
        let num = Number(ov);
        if (num !== NaN) {
          this.age_invalid = "";
          return;
        }
      } else if (ov.length === 0) {
        this.age_invalid = "";
        return;
      }
      this.age_invalid = "md-invalid";
    },
    email(ov, v) {
      if (
        /^[A-Za-z0-9]+([_\.][A-Za-z0-9]+)*@([A-Za-z0-9\-]+\.)+[A-Za-z]{2,6}$/.test(
          ov
        ) &&
        ov.length !== 0
      ) {
        this.email_invalid = "";
      } else {
        this.email_invalid = "md-invalid";
      }
    }
  },
  components: {
    Logo
  },
  mounted() {
    this.$server.getcfg(
      ["oasis.agreement-enabled", "oasis.agreement-name"],
      r => {
        this.agreement_enabled = r[0];
        this.agreement_name = r[1];
        if (this.agreement_enabled === false) {
          this.agreement = "not_enabled";
        }
      }
    );
  }
};
</script>

<style lang="less">
.logo {
  display: block;
  margin: auto;
}
</style>

<style lang="less" scoped>
.main {
  @media screen and (min-width: 1024px) {
    min-width: 350px;
  }
  
  display: flex;
  align-items: center;
  height: 100%;
  position: relative;
}

.logo {
  @media screen and (max-width: 1024px) {
    width: 130px;
  }

  @media screen and (min-width: 1024px) {
    width: 200px;
  }
}

.btn {
  display: block;
  margin: auto;
  margin-top: 32px;
}

.register {
  padding: 32px;
  @media screen and (min-width: 1024px) {
    padding-left: 64px;
    padding-right: 64px;
    background-color: white;
    box-shadow: 0 3px 1px -2px rgba(0, 0, 0, 0.2),
      0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12);
  }
  display: block;
  margin: auto;
}

.information {
    max-width: 80%;
    display: block;
    margin: auto;
    
    .information-stepper {
      max-width: 1024px;
      position: relative;
      height: 480px;

      .md-stepper {
          height: 100%;
      }
    }

    .md-field.select {
        @media screen and (min-width: 1024px) {
            max-width: 320px;
        }
    }
}

h1 {
  @media screen and (max-width: 1024px) {
    line-height: 1.2;
  }
}

.loading {
  display: block;
  margin: auto;
  padding: 64px;
}
</style>