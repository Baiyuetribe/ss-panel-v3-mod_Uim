#!/bin/bash                                                                                               
#===================================================================#
#   System Required:  CentOS 7 、Unbutu、Debian、Fedora、Raspberry Pi#
#   Description: Install rrshare for CentOS7                        #
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
		echo "docker is installed"
		# command
	else
		echo "Install docker"
		# command
		install_docker
	fi
}
check_docker_compose() {
	if [ -x "$(command -v docker-compose)" ]; then
		echo "docker-compose is installed"
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

restart_sspanel(){
    cd /opt/sspanel
    docker-compose restart
}




notice2(){
    green "=================================================="
    green "搭建成功，现在您可以直接访问了"
    green "---------------------------"
    green " sspanel地址：http://ip:666   源码路径：/code/code"      
    green " 文件管理器地址：http://ip:999   源码路径：/code/code"  
    green " phpadmin地址：http://ip:8080"
    green "---------------------------"
    white "其他信息(宿主机挂载路径)"
    white "网页源文件路径：/opt/sspanel/code"
    white "数据库存储路径：/opt/sspanel/mysql"
    green "=================================================="
    white "Dcocker by 佰阅部落  https://baiyue.one"
    white "Docker版问题反馈地址：https://baiyue.one/archives/503.html"
}

# 开始安装sspanel
install_main(){
    blue "获取配置文件"
    start=$(date "+%s")
    mkdir -p /opt/sspanel && cd /opt/sspanel
    rm -f docker-compose.yml  
    wget https://raw.githubusercontent.com/Baiyuetribe/ss-panel-v3-mod_Uim/dev/Docker/master/docker-compose.yml     
    blue "配置文件获取成功"
    greenbg "首次启动会拉取镜像，国内速度比较慢，请耐心等待完成"
    docker-compose up -d
    notice2
    end=$(date "+%s")
    echo 安装总耗时:$[$end-$start]"秒"
    echo
 
}

install_main2(){
    blue "获取配置文件"
    start=$(date "+%s")
    mkdir -p /opt/sspanel && cd /opt/sspanel
    rm -f docker-compose.yml
    docker rmi -f baiyuetribe/sspanel:dev  
    wget https://raw.githubusercontent.com/Baiyuetribe/ss-panel-v3-mod_Uim/dev/Docker/docker-compose.yml     
    blue "配置文件获取成功"
    greenbg "首次启动会拉取镜像，国内速度比较慢，请耐心等待完成"
    docker-compose up -d
    notice2
    end=$(date "+%s")
    echo 安装总耗时:$[$end-$start]"秒"
    echo
 
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
    greenbg "简介：sspanel一键安装脚本（Docker版）                               "
    greenbg "适用系统：CentOS 7、Unbutu、Debian、Fedora、Raspberry Pi等     "
    greenbg "脚本作者：Azure  QQ群：635925514                                 "
    greenbg "程序开发者：Anankke Github:Anankke/SSPanel-Uim                     "
    greenbg "说明：程序集成SSPanel（主程序）+mysql（数据库）+kodexplore(文件管理器)  "
    greenbg "Youtube/B站： 佰阅部落                                          "
    greenbg "==============================================================="
    echo
    yellow "使用前提：脚本会自动安装docker，国外服务器搭建只需1min~2min"
    yellow "注意：稳定版与开发版不共存"
    blue "备注：非80端口可以用caddy反代，自动申请ssl证书，到期自动续期"
    echo
    green "—————————————程序安装——————————————"
    white "1.安装SSPanel（稳定版-master分支）"
    blue "2.安装SSPanel（开发版-dev分支）"    
    green "—————————————杂项管理——————————————"
    white "3.停止SSPanel"
    white "4.重启SSPanel"
    white "5.卸载SSPanel"
    white "6.清除本地缓存（仅限卸载后操作）"
    green "—————————————域名访问——————————————" 
    white "7.Caddy域名反代一键脚本(可以实现非80端口使用域名直接访问)"
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
	check_docker
    check_docker_compose
    install_main2
	;;
	3)
    stop_sspanel
    green "sspanel程序已停止运行"
	;;
	4)
    restart_sspanel
    green "sspanel程序已重启完毕"
	;;
	5)
    remove_all
	;;
	6)
    rm -fr /opt/sspanel
    green "清除完毕"
	;;    
	7)
    bash <(curl -L -s https://raw.githubusercontent.com/Baiyuetribe/codes/master/caddy/caddy.sh)
	;;
	0)
	exit 1
	;;
	*)
	clear
	echo "请输入正确数字"
	sleep 5s
	start_menu
	;;
    esac
}

start_menu
