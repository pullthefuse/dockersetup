server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;

    ssl_certificate /etc/tls/dev.example.com.crt;
    ssl_certificate_key /etc/tls/dev.example.com.key;

    server_name dev.example.com;

    resolver 127.0.0.11 valid=5s;
    set $upstream https://nginx_dev_example_com;

    location / {
        proxy_set_header Host $host;
        proxy_pass $upstream;
    }
}

server {
    listen 80;
    listen [::]:80;

    server_name dev.example.com;

    return 301 https://$host$request_uri;
}
