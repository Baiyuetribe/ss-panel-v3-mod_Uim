## SSPanel DOCKER版（项目来源：Anankke/SSPanel-Uim）
![logo_img_sc.png](https://i.loli.net/2019/07/13/5d2984bc1f5e481107.png)


| SSPanel版本                                                  | 镜像                                                         | 状态                                                         |
| ------------------------------------------------------------ | ------------------------------------------------------------ | ------------------------------------------------------------ |
| 稳定版（master分支）[![](https://images.microbadger.com/badges/version/baiyuetribe/sspanel:stable.svg)](https://microbadger.com/images/baiyuetribe/sspanel:stable "Get your own version badge on microbadger.com") | [![](https://images.microbadger.com/badges/image/baiyuetribe/sspanel:stable.svg)](https://microbadger.com/images/baiyuetribe/sspanel:stable "Get your own image badge on microbadger.com") | ![Docker Cloud Build Status](https://img.shields.io/docker/cloud/build/baiyuetribe/sspanel.svg?style=flat-square) |
| 开发版（dev分支）[![](https://images.microbadger.com/badges/version/baiyuetribe/sspanel:dev.svg)](https://microbadger.com/images/baiyuetribe/sspanel:dev "Get your own version badge on microbadger.com") | [![](https://images.microbadger.com/badges/image/baiyuetribe/sspanel:dev.svg)](https://microbadger.com/images/baiyuetribe/sspanel:dev "Get your own image badge on microbadger.com") | ![Docker Cloud Build Status](https://img.shields.io/docker/cloud/build/baiyuetribe/sspanel.svg?style=flat-square) |
| 基础镜像（运行环境）[![](https://images.microbadger.com/badges/version/baiyuetribe/sspanel:alpine.svg)](https://microbadger.com/images/baiyuetribe/sspanel:alpine "Get your own version badge on microbadger.com") | [![](https://images.microbadger.com/badges/image/baiyuetribe/sspanel:alpine.svg)](https://microbadger.com/images/baiyuetribe/sspanel:alpine "Get your own image badge on microbadger.com") | ![Docker Cloud Build Status](https://img.shields.io/docker/cloud/build/baiyuetribe/sspanel.svg?style=flat-square) |

特点：

- 镜像模式类似wordpress、typoehco、nextcloud等，抛弃臃肿的LNMP，镜像极简。
- 更轻量、更快、也更安全。
- 完整镜像体积仅仅257MB，源码可挂载本地

特殊优势：

- 前端和后端节点可共存于一台服务器
- 去中心化，搭配swarm和k8s可部署容器集群

## 部署方法

> 博客文章地址：[在 Debian 9、Centos7、Ubuntu 搭建 SSPanel 魔改版【Docker版一键脚本】](https://baiyue.one/archives/503.html)

### 方法1：一键脚本（推荐）

集成docker环境和docker-compose环境检测及安装，适配Centos、Debian、Ubuntu等系统。
提供两种版本：

- 稳定版每月同步master分支
- 开发版每月同步dev分支

```
bash <(curl -L -s https://raw.githubusercontent.com/Baiyuetribe/ss-panel-v3-mod_Uim/dev/sspanel.sh)
```

脚本结束后会提示如下内容：

- sspanel主程序：http://ip:666
- kodexplore文件管理器：http://ip:999
- 默认源码路径：`/opt/sspanel/code`
- 默认数据库路径：`/opt/sspanel/mysql`

修改宿主机源码，可实时同步容器内文件。

![](https://img.baiyue.one/upload/2019/07/5d21bb61ae931.png)

### 方法2：手动部署

稳定版（master分支）：

```
wget https://raw.githubusercontent.com/Baiyuetribe/ss-panel-v3-mod_Uim/dev/Docker/master/docker-compose.yml
docker-compose up -d
```

开发版（dev分支）：

```
wget https://raw.githubusercontent.com/Baiyuetribe/ss-panel-v3-mod_Uim/dev/Docker/docker-compose.yml
docker-compose up -d
```

部署完成后，主要内容如下：

- sspanel主程序：http://ip:666
- kodexplore文件管理器：http://ip:999
- 默认源码路径：`/opt/sspanel/code`
- 默认数据库路径：`/opt/sspanel/mysql`

之后执行剩下的相关命令：

```
docker exec -it sspanel sh		#进入sspanel容器
php xcat createAdmin		#创建管理员账户
php xcat syncusers		#同步用户
php xcat initQQWry		#下载ip解析库
php xcat resetTraffic		#重置流量
php xcat initdownload		#下载客户端安装包
exit		#退出
```

执行 `crontab -e` 命令, 添加以下四条（定时任务配置）：

```
30 22 * * * docker exec -t sspanel php xcat sendDiaryMail
0 0 * * * docker exec -t sspanel php -n xcat dailyjob
*/1 * * * * docker exec -t sspanel php xcat checkjob
*/1 * * * * docker exec -t sspanel php xcat syncnode
```

## 备注：

稳定版与开发版不可共存。

- 卸载命令：`docker-compose down`
- 删除本地缓存源码：`rm -rf /opt/sspanel`

脚本作者：azure 更多优质web资源，请参考：[佰阅部落](https://baiyue.one)

## 最终效果：

![](https://i.loli.net/2019/06/14/5d03a311b46dd70196.png)

![Snipaste_2019-07-19_16-44-54.png](https://i.loli.net/2019/07/19/5d3183487ea1628740.png)

## 维护日志

- 2019.07.19 修复数据库问题导致商品名称显示“???”字符问题
- 2019.06.14 受`yangxuan8282`启发，使用alpine基础镜像重构镜像，体积更小、加入源码数据持久化、宿主机现在可以正常编辑源码
- 2019.04 使用LNMP编写镜像，可实现无需宝塔快速部署sspanel面板（与宝塔兼容）。


## 问题及建议渠道：

可在GitHub issue区提问或者到博客文章底部留言评论。

## 免责声明

本程序由 MIT License 授权。**不提供任何担保**。使用本程序即表明，您知情并同意：程序开发者不对此程序导致的任何服务中断、数据损失或任何少见未列出的事故负责。

## 更多优质web资源

[佰阅部落]（https://baiyue.one）---- 专注分享优质开源项目