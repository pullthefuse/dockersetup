version: "<?= $config['docker_version'] ?>"
services:
  nginx_proxy:
    image: nginx:latest
    container_name: nginx_proxy
    volumes:
      - <?= $config['root_directory'] ?>/nginx/proxy/sites-enabled:/etc/nginx/sites-enabled
      - <?= $config['root_directory'] ?>/nginx/proxy/nginx.conf:/etc/nginx/nginx.conf
      - <?= $config['root_directory'] ?>/tls:/etc/tls
    ports:
      - 80:80
      - 443:443
    networks:
      - <?= $config['network'] ?><?= "\n" ?>

networks:
  <?= $config['network'] ?>:
