## SSPanel DOCKER版

特点：

- 镜像模式类似wordpress、typoehco\nextcloud等，抛弃臃肿的LNMP，镜像极简。
- 更轻量、更快、也更安全。
- 完整镜像体积仅仅275MB，源码可挂载本地

## 部署方法

### 一键脚本（推荐）

集成docker环境和docker-compose环境检测及安装，适配Centos、Debian、Ubuntu等系统。
提供两种版本：

- 稳定版每月同步master分支
- 开发版每月同步dev分支

```
bash 。。。待制作
```

脚本结束后会提示如下内容：

- sspanel主程序：http://ip:666
- kodexplore文件管理器：http://ip:999
- 默认源码路径：`/opt/sspanel/code`
- 默认数据库路径：`/opt/sspanel/mysql`

修改宿主机源码，可实时同步容器内文件。

### 手动部署

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

