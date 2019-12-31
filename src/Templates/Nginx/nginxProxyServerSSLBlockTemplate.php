server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;

    ssl_certificate /etc/tls/<?= $domain ?>.crt;
    ssl_certificate_key /etc/tls/<?= $domain ?>.key;

    server_name <?= $domain ?>;

    resolver 127.0.0.11 valid=5s;
    set $upstream https://nginx_<?= \App\Helper\Str::slug($domain) ?>;

    location / {
        proxy_set_header Host $host;
        proxy_pass $upstream;
    }
}

server {
    listen 80;
    listen [::]:80;

    server_name <?= $domain ?>;

    return 301 https://$host$request_uri;
}
