<?php

namespace PagerdutySlackUnfurl\Event\Subscriber;

use PagerdutySlackUnfurl\PagerdutyClient;
use SlackUnfurl\Event\Events;
use SlackUnfurl\Event\UnfurlEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PagerdutyUnfurler implements EventSubscriberInterface
{
    /** @var PagerdutyClient */
    private $client;
    /** @var string */
    private $domain;

    public function __construct(
        PagerdutyClient $client,
        string $domain
    ) {
        $this->client = $client;
        $this->domain = $domain;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::SLACK_UNFURL => ['unfurl', 10],
        ];
    }

    public function unfurl(UnfurlEvent $event): void
    {
        foreach ($event->getMatchingLinks($this->domain) as $link) {
            $url = $link['url'];
            $unfurl = [];
            $event->addUnfurl($url, $unfurl);
        }
    }
}
