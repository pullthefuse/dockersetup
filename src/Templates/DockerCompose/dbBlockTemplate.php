db_<?= $database['type'] ?>:
    image: <?= $docker['services']['db'][$database['type']][$database['version']]['image'] ?><?= "\n" ?>
    container_name: db_<?= $database['type'] ?><?= "\n" ?>
    command: <?= $docker['services']['db'][$database['type']][$database['version']]['command'] ?><?= "\n" ?>
    ports:
      - <?= $docker['services']['db'][$database['type']][$database['version']]['port'] ?>:<?= $docker['services']['db'][$database['type']][$database['version']]['port'] ?><?= "\n" ?>
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_ROOT_HOST: db
      MYSQL_DATABASE: test
      MYSQL_USER: user
      MYSQL_PASSWORD: root
    volumes:
      - <?= $config['root_directory'] ?>/<?= $database['type'] ?>:/var/lib/<?= $database['type'] ?><?= "\n" ?>
    networks:
      - <?= $docker['network'] ?><?= "\n" ?>