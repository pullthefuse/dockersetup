server {
    listen 80 http2;
    listen [::]:80 http2;

    server_name <?= $domain ?>;

    resolver 127.0.0.11 valid=5s;
    set $upstream https://nginx_<?= \App\Helper\Str::slug($domain) ?>;

    location / {
        proxy_set_header Host $host;
        proxy_pass $upstream;
    }
}
