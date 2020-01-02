<?php

namespace App\Server;

use App\Config;

class NginxServer extends AbstractServer
{
    /**
     * @inheritDoc
     */
    public function isSetupComplete(): bool
    {
        return $this->fileManager->exists("nginx/nginx.conf");
    }

    /**
     * @inheritDoc
     */
    public function setup(): ServerInterface
    {
        $this->createNginxConfFile();
        $this->createProxyConfFile();

        return $this;
    }

    public function deleteDomain(string $domain): void
    {
        $this->fileManager->addToTrash(Config::get('rootDirectory').'/nginx/proxy/sites-enabled/'.$domain.'.conf');
        $this->fileManager->addToTrash(Config::get('rootDirectory').'/nginx/sites-enabled/'.$domain.'.conf');
    }

    /**
     * @inheritDoc
     */
    public function setupDomain($domain): void
    {
        $ssl = $this->getSSL() ? 'SSL' : '';

        $content = $this->twig->render("server/nginx/server{$ssl}Block.html.twig", [
            'domain' => $domain,
            'publicDir' => Config::get('publicDirectory')
        ]);
        $this->fileManager->createFileContent("nginx/sites-enabled/{$domain}.conf", $content);

        $content = $this->twig->render("server/nginx/proxyServer{$ssl}Block.html.twig", [
            'domain' => $domain,
            'publicDir' => Config::get('publicDirectory')
        ]);
        $this->fileManager->createFileContent("nginx/proxy/sites-enabled/{$domain}.conf", $content);
    }

    /**
     * Create Nginx conf file.
     */
    private function createNginxConfFile(): void
    {
        $content = $this->twig->render('server/nginx/httpBlock.html.twig', ['config' => Config::get('http')]);

        $this->fileManager->createFileContent("nginx/nginx.conf", $content);
    }

    /**
     * Create Nginx proxy conf file.
     */
    private function createProxyConfFile(): void
    {
        $config = Config::get('http');
        $config['events']['use'] = 'epoll';

        $config['http'] = array_merge($config['http'], [
            'proxy_buffer_size' => '128k',
            'proxy_buffers' => [4, '256k'],
            'proxy_busy_buffers_size' => '256k',
            'proxy_read_timeout' => '300s'
        ]);

        $content = $this->twig->render('server/nginx/httpBlock.html.twig', ['config' => $config]);
        
        $this->fileManager->createFileContent("nginx/proxy/nginx.conf", $content);
    }
}
