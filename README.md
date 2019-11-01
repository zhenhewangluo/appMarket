#  安卓市场代码

> 一套完整的安卓市场代码，最早在2012年发布。 现在发布出来供大家学习使用。



## 目录结构：
 
 * www PC端代码，支持 PHP 5.6 使用 dedeCMS 与 ThinkPHP 开发
 * api 安卓开端，接口文件 
 * mysql 完整的 MySQL 数据库文件，直接下载来放在 MySQL 目录中就可以使用
 * android 安卓客户端代码，版本比较老仅仅支持安卓5.1以下


## 接口 Nginx 配置

由于版本比较，数据库等配置直接放在配置文件

```
server {

    listen 80;
    server_name api.market.com;
    index index.html index.php;
    root /var/www/html/api.oi3g.com;

    access_log  /var/log/nginx/api.market.com.access.log main;
    error_log   /var/log/nginx/api.market.com.error.log;  

    location ~ /\.ht {
        deny  all;
    }


   location / {
   
        if (!-e $request_filename){  
            rewrite ^/(.*) /index.php last;  
        }
    }

    location ~ \.php$
    {
	    fastcgi_pass fpm-5.3:9000;
        fastcgi_index index.php;
        include fastcgi.conf;
	    fastcgi_param DB_HOST "172.18.0.1";
        fastcgi_param DB_USER "root";
        fastcgi_param DB_PASSWORD "Qq7731226!@#";
        fastcgi_param DB_NAME "_v2_android";
        fastcgi_param COMMON_DB_HOST "172.18.0.1";
        fastcgi_param COMMON_DB_USER "root";
        fastcgi_param COMMON_DB_PASSWORD "Qq7731226!@#";
        fastcgi_param COMMON_DB_NAME "_v2_common";
        fastcgi_param AM_SITE_ROOT "/var//www/html/api.oi3g.com/";
        fastcgi_param AM_SITE_URL "http://down.market.com/";
        fastcgi_param AM_SITE_DOWN_URL "http://down.market.com/";
        fastcgi_param REPO_ROOT "http://down.market.com/repo/";
        fastcgi_param MEMCACH_HOST "127.0.0.1";
        fastcgi_param MEMCACH_PORT "11211";	
    }

    location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$
    {
        expires      30d;
    }
    
    location ~ .*\.(js|css)?$
    {
        expires      1h;
    }
    
    #error_page  404              /404.html;
    
    # redirect server error pages to the static page /50x.html
    #
    error_page   500 502 503 504  /50x.html;
    location = /50x.html {
        root   html;
    }


}

```

## PC 配置如下：

```
server {
    listen       80;
    server_name   www.market.com;
    root   /var/www/html/www.market.com;
    index  index.php index.html index.htm;

    access_log  /var/log/nginx/www.market.com.access.log main;
    error_log   /var/log/nginx/www.market.com.error.log;   

    location / {
        index index.php;
        if (!-e $request_filename) {
            rewrite ^/(.*)$ /index.php?s=$1 last;
            break;
        }
    }
     
    #charset koi8-r;
    #access_log  /var/log/nginx/log/host.access.log  main;

    #error_page  404              /404.html;

    # redirect server error pages to the static page /50x.html
    #
    error_page   500 502 503 504  /50x.html;
    location = /50x.html {
        root   /usr/share/nginx/html;
    }

    # proxy the PHP scripts to Apache listening on 127.0.0.1:80
    #
    #location ~ \.php$ {
    #    proxy_pass   http://127.0.0.1;
    #}

    # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
    #
    location ~ \.php$ {
        fastcgi_pass   fpm-5.6:9000;
        fastcgi_index  index.php;
        include        fastcgi_params;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
    }

 
}

```

