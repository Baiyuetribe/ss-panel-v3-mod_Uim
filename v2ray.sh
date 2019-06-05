#!/bin/bash                                                                                               
#===================================================================#
#   System Required:  CentOS 7                                 #
#   Description: Install v2ray for CentOS7 #
#   Author: Azure <2894049053@qq.com>                               #
#   github: @baiyutribe <https://github.com/baiyuetribe>                     #
#   Blog:  佰阅部落 https://baiyue.one                           #
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
# bash <(curl -L -s https://raw.githubusercontent.com/Baiyuetribe/v2ray_docker/master/v2ray.sh)             
# @安装docker
install_docker() {
    docker version > /dev/null || curl -fsSL get.docker.com | bash 
    service docker restart 
    systemctl enable docker  
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



# check docker


# 以上步骤完成基础环境配置。
echo "恭喜，您已完成基础环境安装，可执行安装程序。"


# 开始安装人人影视客户端
install_v2ray(){
    docker run -d --name v2ray --restart always --network host jrohy/v2ray   
}
stop_v2ray(){
    docker stop v2ray
}
start_v2ray(){
    docker restart v2ray
}


# 删除
remove_all(){
    docker rm -f v2ray
    docker rmi -f jrohy/v2ray
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
    echo -e "\033[43;42m ====================================\033[0m"
    echo -e "\033[43;42m 介绍：v2ray一键脚本Docker版               \033[0m"
    echo -e "\033[43;42m 作者：Azure  基于jrohy/v2ray镜像制作      \033[0m"
    echo -e "\033[43;42m 网站：https://baiyue.one             \033[0m"
    echo -e "\033[43;42m Youtube/B站：佰阅部落                 \033[0m"
    echo -e "\033[43;42m =====================================\033[0m"
    echo
    echo -e "\033[0;33m 1. 安装 \033[0m"
    echo -e "\033[0;33m 2. 查看配置信息\033[0m"
    echo -e "\033[0;33m 3. 重启\033[0m"
    echo -e "\033[37;41m 4. 卸载\033[0m"
    echo " 0. 退出脚本"
    echo
    read -p "请输入数字:" num
    case "$num" in
    1)
	check_docker
	install_v2ray
    docker exec v2ray bash -c "v2ray info"	#查看配置信息
    echo -e "\033[32m====================================\033[0m"	
    echo -e "\033[32m 恭喜，v2ray已经安装成功                         "
    echo -e "\033[32m====================================\033[0m"	
	;;
	2)
    docker exec v2ray bash -c "v2ray info"
	;;
	3)
    start_v2ray
	echo -e "\033[32m====================================\033[0m"
	echo -e "\033[32m已启动v2ray\033[0m"
    echo -e "\033[32m====================================\033[0m"
	;;
	4)
    remove_all
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
