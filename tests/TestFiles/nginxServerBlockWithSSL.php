server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;

    server_name dev.example.com;

    ssl_certificate /etc/tls/dev.example.com.crt;
    ssl_certificate_key /etc/tls/dev.example.com.key;

    root /var/www/dev.example.com/public;

    index index.php index.html index.htm app_dev.php;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass web_dev_example_com:9000;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        internal;
    }

    location ~ \.php$ {
        return 404;
    }

    error_log /var/log/nginx/project_error.log;
    access_log /var/log/nginx/project_access.log;
}

server {
    listen 80;
    listen [::]:80;

    server_name dev.example.com;

    return 301 https://$host$request_uri;
}
