<?php

namespace PagerdutySlackUnfurl\Event\Subscriber;

use PagerdutySlackUnfurl\PagerdutyClient;
use SlackUnfurl\Event\Events;
use SlackUnfurl\Event\UnfurlEvent;
use SlackUnfurl\Traits\SlackEscapeTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PagerdutyUnfurler implements EventSubscriberInterface
{
    use SlackEscapeTrait;

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
            $unfurl = $this->getIncidentUnfurl($url);
            if ($unfurl) {
                $event->addUnfurl($url, $unfurl);
            }
        }
    }

    private function getIncidentUnfurl(string $url): ?array
    {
        $details = $this->getIncidentDetails($url);
        if (!$details) {
            return null;
        }


        return [
            'title' => $this->createLink($url, "${details['type']}: {$details['title']} ({$details['status']})"),
        ];
    }

    private function getIncidentDetails(string $url): ?array
    {
        if (!preg_match("#^https?://\Q{$this->domain}\E/incidents/(?P<incidentId>[\w\d]+)#", $url, $m)) {
            return null;
        }

        return $this->client->getIncident($m['incidentId'])['incident'] ?? null;
    }
}
