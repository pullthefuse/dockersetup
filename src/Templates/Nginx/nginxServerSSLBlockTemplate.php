server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;

    server_name <?= $domain ?>;

    ssl_certificate /etc/tls/<?= $domain ?>.crt;
    ssl_certificate_key /etc/tls/<?= $domain ?>.key;

    root /var/www/<?= $domain ?><?= $publicDir ?>;

    index index.php index.html index.htm app_dev.php;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass web_<?= \App\Helper\Str::slug($domain) ?>:9000;
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

    server_name <?= $domain ?>;

    return 301 https://$host$request_uri;
}
