<?php

namespace PagerdutySlackUnfurl\Event\Subscriber;

use SlackUnfurl\Event\Events;
use SlackUnfurl\Event\UnfurlEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PagerdutyUnfurler implements EventSubscriberInterface
{
    /** @var string */
    private $domain;

    public function __construct(
        string $domain
    ) {
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
