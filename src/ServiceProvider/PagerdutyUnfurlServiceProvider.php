<?php

namespace PagerdutySlackUnfurl\ServiceProvider;

use PagerdutySlackUnfurl\Event\Subscriber\PagerdutyUnfurler;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\EventListenerProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PagerdutyUnfurlServiceProvider implements ServiceProviderInterface, EventListenerProviderInterface
{
    public function register(Container $app): void
    {
        $app['pagerduty.url'] = getenv('PAGERDUTY_URL');

        $app[PagerdutyUnfurler::class] = static function ($app) {
            $domain = parse_url($app['pagerduty.url'], PHP_URL_HOST);

            return new PagerdutyUnfurler(
                $domain,
            );
        };

        $app[HttpClientInterface::class] = static function () {
            return HttpClient::create();
        };
    }

    public function subscribe(Container $app, EventDispatcherInterface $dispatcher): void
    {
        $dispatcher->addSubscriber($app[PagerdutyUnfurler::class]);
    }
}
