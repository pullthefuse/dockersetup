server {
    listen 80;
    listen [::]:80;

    server_name dev.example.com;

    resolver 127.0.0.11 valid=5s;
    set $upstream http://nginx_dev_example_com;

    location / {
        proxy_set_header Host $host;
        proxy_pass $upstream;
    }
}