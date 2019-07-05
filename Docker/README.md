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

bash 
```
脚本结束后会提示如下内容：
- sspanel主程序：http://ip:666
- kodexplore文件管理器：http://ip:999

- 默认源码路径：`/opt/sspanel/code`
- 默认数据库路径：`/opt/sspanel/mysql`

修改宿主机源码，可实时同步容器内文件。

### 手动部署

稳定版：
```
wget 

```
开发版：
```
wget 
docker-compose up -d
```
