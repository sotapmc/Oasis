# Oasis

![](https://img.shields.io/badge/backend-PHP-purple?logo=php)
![](https://img.shields.io/badge/front-Vue.js-brightgreen?logo=vue.js)
![](https://img.shields.io/badge/poweredby-sotapmc-blue)


![Oasis.png](https://i.loli.net/2020/03/23/pBbJlVmMsuNFkcg.png)

**Oasis** 是一个多功能的表单创建与处理 Web 应用程序，使用 Vue.js 编写。

## 功能

![](https://img.shields.io/badge/coverage-40%25-yellow)

Oasis 由表单和管理后台组成，表单可通过*某些方法*进行定制，以达到不同的收集效果。管理后台则可以查看所有的表单并给予一定的回应。同时，为了响应请求，我们还增加了自定义评分、邮件发送、内容归档、数据统计以及导出为表格等功能。这些功能也将在未来被实现。如果您有任何好的建议，欢迎在 Issues 提出。

目前我们通过了一些简单的方式实现了如下功能，此列表会持续更新

- [x] **高度自定义。** 详见 [Wiki](https://github.com/sotapmc/Oasis/wiki)。
- [x] **自定义主题色。** 目前有蓝、绿、黄三种颜色可选，在将来会添加更多颜色甚至自定义主题。

## ⚠ 非公众化软体

虽然 Oasis 是开源自由软件，但目前它的逻辑代码设计均倾向于 SoTap 定制，因此如果您想要将 Oasis 应用到自己的业务场景内，则可能需要等待一段时间。公众化重写将会在不远的未来开始执行，届时 Oasis 可应用于所有场景。

## 贡献

Oasis 目前在如下环境测试成功：

|软体名称|版本|注释|
|:-:|:-:|:-:|
|Windows 10|1903 `18362.720`|-|
|Google Chrome|`80.0.3987.149`|-|
|PHP|7.4|`mbstring` and `mysqli` extension required|
|Apache|2.4.41|`mod_rewrite` enabled with php support|
|MySQL|10.4.11-MariaDB|please use MariaDB instead of Oracle MySQL|
|Vue.js|2.6|-|
|Nodejs|12.14.1|-|

在贡献之前，先 Fork 本项目后 `clone`。

```bash
$ git clone git@github.com:YourName/Oasis
```

安装依赖

```bash
$ npm i
```

安装以后，您需要手动运行 `/backend/setup.php` 进行数据库部署。部署后，则可以开始更改代码以后提交。如果一切就绪，则可以向本项目提交 Pull Request。

如果没有什么太大的需求（例如地址重写），则可以选择使用 php 的内置服务器。

```bash
php -S 127.0.0.1:2333
```

> ❌ 请勿使用 `localhost:2333` 的写法，否则可能导致 Vue 的 devserver 无法 proxy 到后端的正确目录。Vue 的 proxy 设置位于 `/vue.config.js`。

## 协议

**M**ozilla **P**ublic **L**icense *Version 2.0*
