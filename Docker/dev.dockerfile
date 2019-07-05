FROM baiyuetribe/sspanel:alpine
MAINTAINER azure <https://baiyue.one>
# 本镜像每月自动同步sspanel官方源码
# Docker版问题反馈地址：https://github.com/Baiyuetribe/sspanel/pulls
ENV SOURCE=https://github.com/Anankke/SSPanel-Uim/archive/dev.zip
WORKDIR /app
RUN wget -q ${SOURCE} && unzip dev.zip && rm dev.zip && cd SSPanel-Uim-dev \
    && cp config/.config.example.php config/.config.php \
    && sed -i "s|\['db_host'\]\s*=\s*'.*'|['db_host'] = 'mysql'|" /app/SSPanel-Uim-dev/config/.config.php \
    && composer update     
WORKDIR /sspanel    
COPY deventrypoint.sh /usr/local/bin/
RUN chmod a+x /usr/local/bin/deventrypoint.sh
EXPOSE 80
ENTRYPOINT ["deventrypoint.sh"]
CMD [ "php", "-S", "0000:80", "-t", "/sspanel/public" ]


