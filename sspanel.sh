#!/bin/bash                                                                                               
#===================================================================#
#   System Required:  CentOS 7                                      #
#   Description: Install sspanel for CentOS7                        #
#   Author: Azure <2894049053@qq.com>                               #
#   github: @baiyutribe <https://github.com/baiyuetribe>            #
#   Blog:  佰阅部落 https://baiyue.one                               #
#===================================================================#
#
#  .______        ___       __  ____    ____  __    __   _______      ______   .__   __.  _______ 
#  |   _  \      /   \     |  | \   \  /   / |  |  |  | |   ____|    /  __  \  |  \ |  | |   ____|
#  |  |_)  |    /  ^  \    |  |  \   \/   /  |  |  |  | |  |__      |  |  |  | |   \|  | |  |__   
#  |   _  <    /  /_\  \   |  |   \_    _/   |  |  |  | |   __|     |  |  |  | |  . `  | |   __|  
#  |  |_)  |  /  _____  \  |  |     |  |     |  `--'  | |  |____  __|  `--'  | |  |\   | |  |____ 
#  |______/  /__/     \__\ |__|     |__|      \______/  |_______|(__)\______/  |__| \__| |_______|
#
#一键脚本
#
#
# 设置字体颜色函数
function blue(){
    echo -e "\033[34m\033[01m $1 \033[0m"
}
function green(){
    echo -e "\033[32m\033[01m $1 \033[0m"
}
function greenbg(){
    echo -e "\033[43;42m\033[01m $1 \033[0m"
}
function red(){
    echo -e "\033[31m\033[01m $1 \033[0m"
}
function redbg(){
    echo -e "\033[37;41m\033[01m $1 \033[0m"
}
function yellow(){
    echo -e "\033[33m\033[01m $1 \033[0m"
}
function white(){
    echo -e "\033[37m\033[01m $1 \033[0m"
}
#            
# @安装docker
install_docker() {
    docker version > /dev/null || curl -fsSL get.docker.com | bash 
    service docker restart 
    systemctl enable docker  
}
install_docker_compose() {
	curl -L "https://github.com/docker/compose/releases/download/1.23.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
	chmod +x /usr/local/bin/docker-compose
}


# 单独检测docker是否安装，否则执行安装docker。
check_docker() {
	if [ -x "$(command -v docker)" ]; then
		blue "docker is installed"
		# command
	else
		echo "Install docker"
		# command
		install_docker
	fi
}
check_docker_compose() {
	if [ -x "$(command -v docker-compose)" ]; then
		blue "docker-compose is installed"
		# command
	else
		echo "Install docker-compose"
		# command
		install_docker_compose
	fi
}


# check docker


# 以上步骤完成基础环境配置。
echo "恭喜，您已完成基础环境安装，可执行安装程序。"


# 输出结果本地clone
local_clone(){
    white "本地配置文件准备"
    cd /opt/sspanel
    git clone -b master https://github.com/Anankke/ss-panel-v3-mod_Uim.git
    mkdir -p /opt/sspanel/public/images
    cp -r /opt/sspanel/ss-panel-v3-mod_Uim/public/images /opt/sspanel/public/images
    cp -r /opt/sspanel/ss-panel-v3-mod_Uim/config /opt/sspanel/config
    cp -r /opt/sspanel/ss-panel-v3-mod_Uim/sql /opt/sspanel/sql
    cp /opt/sspanel/ss-panel-v3-mod_Uim/config/.config.php.example /opt/sspanel/config/.config.php
    sed -i "s/localhost/mysql/g" /opt/sspanel/config/.config.php
    sed -i "s|\['db_password'\]\s*=\s*'.*'|['db_password'] = '${rootpwd}'|" /opt/sspanel/config/.config.php
    sed -i "s|\['appName'\]\s*=\s*'.*'|['appName'] = '${APP_NAME}'|" /opt/sspanel/config/.config.php
    rm -rf /opt/sspanel/ss-panel-v3-mod_Uim
    greenbg "本地初始化完成"
}
mysql_init(){
    redbg "程序正在等待数据库完成初始化约3s"
    sleep 3s
    cd /opt/sspanel
    docker-compose exec mysql mysql -uroot -p$rootpwd sspanel < /root/sql/glzjin_all.sql 
    sleep 3s
}
# docker exec some-mysql sh -c 'exec mysqldump --all-databases -uroot -p"$MYSQL_ROOT_PASSWORD"' > /some/path/on/your/host/all-databases.sql

admin_set(){
    greenbg "请设置管理员账号"
    cd /opt/sspanel
    docker-compose exec php php xcat createAdmin
    redbg "一切已就绪，欢迎使用sspanel一键脚本"
}
notice(){
    green "=================================================="
    green "搭建成功，现在您可以直接访问了"
    green "---------------------------"
    green " 首页地址： http://ip:$port"
    green " 后台地址：http://ip:$port/login"
    green " 网站配置文件： /opt/sspanel/confing/config.php"
    green " 网站图片修改： /opt/sspanel/public/images"
    green "---------------------------"
    white "其他信息"
    white "已配置的端口：$port  数据库root密码：$pwd "
    yellow "如需正式运作：请参考脚本说明地址，设置定时任务"
    green "=================================================="
    white "开发者：Nimaqu   Dcocker by 佰阅部落  "
    white "项目地址： https://github.com/Anankke/sspanel"
    yellow "脚本说明地址：https://baiyue.one"
    admin_set 
}
# 开始安装sspanel
install_main(){
    blue "获取配置文件"
    mkdir -p /opt/sspanel && cd /opt/sspanel
    rm -f docker-compose.yml
    #wget http://23.94.24.115:10080/baiyue/ss/raw/master/docker-compose.yml  
    wget https://raw.githubusercontent.com/Baiyuetribe/ss-panel-v3-mod_Uim/docker/docker-compose.yml    
    blue "配置文件获取成功"
    sleep 2s
    white "请仔细填写参数，部署完毕会反馈已填写信息"
    green "访问端口：如果想通过域名访问，请设置80端口，其余端口可随意设置"
    read -e -p "请输入访问端口(默认端口2020)：" port
    [[ -z "${port}" ]] && port="2020"
    green "设置数据库ROOT密码"
    read -e -p "请输入ROOT密码(默认baiyue.one)：" rootpwd
    [[ -z "${rootpwd}" ]] && rootpwd="baiyue.one" 
    blue "给网站起个名字（默认sspanel[该名字后期可改]）" 
    read -e -p "给网站起个名字:" APP_NAME
    [[ -z "${APP_NAME}" ]] && APP_NAME="sspanel"     
    green "请选择安装版本"
    yellow "1.[sspanel](master稳定版)"
    yellow "2.[sspanel](dev开发版)"
    echo
    read -e -p "请输入数字[1~3](默认1)：" vnum
    [[ -z "${vnum}" ]] && vnum="1" 
	if [[ "${vnum}" == "1" ]]; then
        greenbg "开始安装sspanel稳定版"
        yellow "part1:本地配置文件clone"
        local_clone
        yellow "part2:镜像拉取"
        sed -i "s/数据库密码/$rootpwd/g" /opt/sspanel/docker-compose.yml
        sed -i "s/访问端口/$port/g" /opt/sspanel/docker-compose.yml
        greenbg "已完成配置部署"
        greenbg "程序将下载镜像，请耐心等待下载完成"
        cd /opt/sspanel
        greenbg "首次启动会拉取镜像，国内速度比较慢，请耐心等待完成"
        docker-compose up -d
        mysql_init
        notice
	elif [[ "${vnum}" == "2" ]]; then
        white "该项目请联系QQ：2894049053有偿搭建。。。"
        white ":)"
	fi   
   
}

# 停止服务
stop_sspanel(){
    cd /opt/sspanel
    docker-compose kill
}

# 重启服务
restart_sspanel(){
    cd /opt/sspanel
    docker-compose restart
}

# 卸载
remove_all(){
    cd /opt/sspanel
    docker-compose down
	echo -e "\033[32m已完成卸载\033[0m"
}

backend_docking_set(){
    white "本骄脚本支持 green "webapi" 和 green "数据库对接" 两种对接方式"
    green "请选择对接方式(随机可选，无区别)"
    yellow "1.webapi对接"
    yellow "2.数据库对接"
    echo
    read -e -p "请输入数字[1~2](默认1)：" vnum
    [[ -z "${vnum}" ]] && vnum="1" 
	if [[ "${vnum}" == "1" ]]; then
        greenbg "当前对接模式：webapi"
        greenbg "使用前请准备好 redbg "节点ID、前端网站ip或url、前端token" "
        green "请输入网址：示例http://baiyue.one 或http://23.94.13.115 (ip地址)"
        read -p "请输入网址:" web_url
        green "请输入网站webapi_token:如未修改默认的NimaQu,可直接回车下一步"
        read -e -p "请输入webapi_token(默认值NimaQu)：" webapi_token
        [[ -z "${webapi_token}" ]] && webapi_token="NimaQu"
        green "节点ID：示例3"
        read -p "请输入节点ID:" node_id
        yellow "配置已完成，正在部署后端。。。。"
        docker run -d --name=ssrmu -e NODE_ID=$node_id -e API_INTERFACE=modwebapi -e WEBAPI_URL=$web_url -e WEBAPI_TOKEN=$webapi_token --network=host --log-opt max-size=50m --log-opt max-file=3 --restart=always fanvinga/docker-ssrmu
        greenbg "恭喜，已搭建成功"        
	elif [[ "${vnum}" == "2" ]]; then
        greenbg "当前对接模式：数据库对接"
        greenbg "使用前请准备好 redbg "节点ID、前端网站ip、数据库ROOT密码" "
        green "请输入前端网网站IP：23.94.13.115 (ip数字)"
        read -p "请输入ip:" web_ip
        green "请输入前端数据库密码"
        read -p "请输入前端数据库密码:" root_pwd
        green "节点ID：示例3"
        read -p "请输入节点ID:" node_id
        yellow "配置已完成，正在部署后端。。。。"
        docker run -d --name=ssrmu -e NODE_ID=$node_id -e API_INTERFACE=glzjinmod -e MYSQL_HOST=$web_ip -e MYSQL_USER=root -e MYSQL_DB=sspanel -e MYSQL_PASS=$root_pwd --network=host --log-opt max-size=50m --log-opt max-file=3 --restart=always fanvinga/docker-ssrmu
        greenbg "恭喜，已搭建成功"   
	fi       
}


#开始菜单
start_menu(){
    clear
	echo "


  ██████╗  █████╗ ██╗██╗   ██╗██╗   ██╗███████╗    ██████╗ ███╗   ██╗███████╗
  ██╔══██╗██╔══██╗██║╚██╗ ██╔╝██║   ██║██╔════╝   ██╔═══██╗████╗  ██║██╔════╝
  ██████╔╝███████║██║ ╚████╔╝ ██║   ██║█████╗     ██║   ██║██╔██╗ ██║█████╗  
  ██╔══██╗██╔══██║██║  ╚██╔╝  ██║   ██║██╔══╝     ██║   ██║██║╚██╗██║██╔══╝  
  ██████╔╝██║  ██║██║   ██║   ╚██████╔╝███████╗██╗╚██████╔╝██║ ╚████║███████╗
  ╚═════╝ ╚═╝  ╚═╝╚═╝   ╚═╝    ╚═════╝ ╚══════╝╚═╝ ╚═════╝ ╚═╝  ╚═══╝╚══════╝                                                            
    "
    greenbg "==============================================================="
    greenbg "简介：sspanel一键安装脚本                                        "
    greenbg "系统：Centos7、Ubuntu等                                         "
    greenbg "脚本作者：Azure  QQ群：635925514                                 "
    greenbg "程序开发者：Nimaqu Github:nimaqu/sspanel                        "
    greenbg "网站： https://baiyue.one                                       "
    greenbg "主题：专注分享优质web资源                                        "
    greenbg "Youtube/B站： 佰阅部落                                          "
    greenbg "==============================================================="
    echo
    yellow "使用前提：脚本会自动安装docker，国外服务器搭建只需1min~2min"
    yellow "国内服务器下载镜像稍慢，请耐心等待"
    blue "备注：非80端口可以用caddy反代，自动申请ssl证书，到期自动续期"
    echo
    white "—————————————程序安装——————————————"
    white "1.安装sspanel"
    white "—————————————杂项管理——————————————"
    white "2.停止sspanel"
    white "3.重启sspanel"
    white "4.卸载sspanel"
    white "5.清除本地缓存（仅限卸载后或失败重装操作）"
    white "—————————————域名访问——————————————" 
    white "6.Caddy域名反代一键脚本(可以实现非80端口使用域名直接访问)"
    white "—————————————后端节点对接——————————————"
    green "7.后端节点bbr加速"
    green "8.后端节点对接（Docker版）一键脚本" 
    blue "0.退出脚本"
    echo
    echo
    read -p "请输入数字:" num
    case "$num" in
    1)
	check_docker
    check_docker_compose
    install_main
	;;
	2)
    stop_sspanel
    green "sspanel程序已停止运行"
	;;
	3)
    restart_sspanel
    green "sspanel程序已重启完毕"
	;;
	4)
    redbg "正在卸载sspanel"
    remove_all
	;;
	5)
    rm -fr /opt/sspanel
    green "清除完毕"
	;;    
	6)
    bash <(curl -L -s https://raw.githubusercontent.com/Baiyuetribe/codes/master/caddy/caddy.sh)
	;;
	7)
    yellow "bbr加速选用94ish.me的轮子"
    bash <(curl -L -s https://raw.githubusercontent.com/chiakge/Linux-NetSpeed/master/tcp.sh)
	;;  
	8)
    greenbg "此脚本适用于centos7、ubutun,其它系统按理也可以成功,未实测"
    yellow "检测是否安装docker环境"
    check_docker
    backend_docking_set
	;;          
	0)
	exit 1
	;;
	*)
	clear
	echo "请输入正确数字[0~8]"
	sleep 5s
	start_menu
	;;
    esac
}

start_menu

