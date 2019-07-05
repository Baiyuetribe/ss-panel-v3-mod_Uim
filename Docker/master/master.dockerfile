FROM baiyuetribe/sspanel:alpine
MAINTAINER azure <https://baiyue.one>
# 本镜像每月自动同步sspanel官方源码
# Docker版问题反馈地址：https://github.com/Baiyuetribe/sspanel/pulls
ENV SOURCE=https://github.com/Anankke/SSPanel-Uim/archive/master.zip
WORKDIR /app
RUN wget -q ${SOURCE} && unzip master.zip && rm master.zip && cd SSPanel-Uim-master \
    && cp config/.config.example.php config/.config.php \
    && sed -i "s|\['db_host'\]\s*=\s*'.*'|['db_host'] = 'mysql'|" /app/SSPanel-Uim-master/config/.config.php \
    && composer update     
WORKDIR /sspanel    
COPY entrypoint.sh /usr/local/bin/
RUN chmod a+x /usr/local/bin/entrypoint.sh
EXPOSE 80
ENTRYPOINT ["entrypoint.sh"]
CMD [ "php", "-S", "0000:80", "-t", "/sspanel/public" ]


