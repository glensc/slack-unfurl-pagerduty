<?php

namespace PagerdutySlackUnfurl\Test;

use PagerdutySlackUnfurl\Event\Subscriber\PagerdutyUnfurler;
use PagerdutySlackUnfurl\PagerdutyClient;
use PagerdutySlackUnfurl\ServiceProvider\PagerdutyUnfurlServiceProvider;
use Pimple\Container;

class PagerdutyTest extends TestCase
{
    /**  @var PagerdutyClient */
    private $client;
    /** @var PagerdutyUnfurler */
    private $unfurler;

    public function setUp(): void
    {
        $app = new Container();
        $app->register(new PagerdutyUnfurlServiceProvider());
        $this->client = $app[PagerdutyClient::class];
        $this->unfurler = $app[PagerdutyUnfurler::class];
    }

    public function testGetIncident(): void
    {
        $id = '123';
        $incident = $this->client->getIncident($id);
        dd($incident);
    }

    public function testUnfurlIncident(): void
    {
        $event = $this->createUnfurlEvent('https://example.pagerduty.com/incidents/123');
        $this->unfurler->unfurl($event);
        dump($event->getUnfurls());
    }
}
