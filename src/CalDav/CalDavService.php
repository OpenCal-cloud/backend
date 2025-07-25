<?php

declare(strict_types=1);

namespace App\CalDav;

use App\Entity\CalDavAuth;
use Sabre\DAV\Client;
use Sabre\VObject\Reader;
use Safe\DateTime;
use SimpleXMLElement;

class CalDavService
{
    /** @var array<Client> */
    private array $clients = [];

    public function __construct(
        private readonly ClientFactory $clientFactory,
    ) {
    }

    /** @return list<array{day: mixed, startTime: mixed, endTime: mixed, etag: string}> */
    public function fetchEventsByAuth(CalDavAuth $auth): array
    {
        $client = $this->getClient(
            $auth->getBaseUri(),
            $auth->getUsername(),
            $auth->getPassword(),
        );

        $requestXML = $this->buildRequestXML();

        /** @var array{body: string, statusCode: int} $response */
        $response = $client->request('REPORT', '', $requestXML, [
            'Depth'        => '1',
            'Content-Type' => 'application/xml',
        ]);

        /** @phpstan-ignore-next-line */
        if (null === $response) {
            return [];
        }

        $calendarEntries = $this->getCalendarEntries($response['body']);

        $calendarEventData = [];

        foreach ($calendarEntries as $entry) {
            $vCalendar = Reader::read($entry['calendar-data']);

            /** @phpstan-ignore-next-line */
            foreach ($vCalendar->VEVENT as $vEvent) {
                $event = [
                    /** @phpstan-ignore-next-line */
                    'day'       => $vEvent->DTSTART->getDateTime()->format('Y-m-d'),
                    /** @phpstan-ignore-next-line */
                    'startTime' => $vEvent->DTSTART->getDateTime()->format('H:i:s'),
                    /** @phpstan-ignore-next-line */
                    'endTime'   => $vEvent->DTEND->getDateTime()->format('H:i:s'),
                    'etag'      => $entry['etag'],
                ];

                $calendarEventData[] = $event;
            }
        }

        return $calendarEventData;
    }

    /**
     * @return list<array{calendar-data: string, etag: string}>
     *
     * @throws \Exception
     */
    private function getCalendarEntries(string $xml): array
    {
        $document = new SimpleXMLElement($xml);
        $document->registerXPathNamespace('dav', 'DAV:');
        $document->registerXPathNamespace('cal', 'urn:ietf:params:xml:ns:caldav');

        $responses = $document->xpath('//dav:response');

        $data = [];

        if (\is_array($responses)) {
            foreach ($responses as $response) {
                $response->registerXPathNamespace('dav', 'DAV:');
                $response->registerXPathNamespace('cal', 'urn:ietf:params:xml:ns:caldav');

                $calendarData = $response->xpath('./dav:propstat/dav:prop/cal:calendar-data');
                $etag         = $response->xpath('./dav:propstat/dav:prop/dav:getetag');

                $iCalString = isset($calendarData[0]) ? (string) $calendarData[0] : '';

                $item = [
                    'calendar-data' => $iCalString,
                    'etag'          => isset($etag[0]) ? \trim((string) $etag[0], '"') : '',
                ];

                $data[] = $item;
            }
        }

        return $data;
    }

    private function buildRequestXML(string $startDateString = 'today 00:00:00'): string
    {
        $startDate = new DateTime($startDateString, new \DateTimeZone('UTC'));
        $endDate   = clone $startDate;
        $endDate->modify('+ 3 months');

        return <<<XML
<c:calendar-query xmlns:d="DAV:" xmlns:c="urn:ietf:params:xml:ns:caldav">
    <d:prop>
        <d:getetag />
        <c:calendar-data />
    </d:prop>
    <c:filter>
        <c:comp-filter name="VCALENDAR">
          <c:comp-filter name="VEVENT">
            <c:time-range start="{$startDate->format('Ymd\THis\Z')}" end="{$endDate->format('Ymd\THis\Z')}"/>
          </c:comp-filter>
        </c:comp-filter>
    </c:filter>
</c:calendar-query>
XML;
    }

    private function getClient(
        string $baseUri,
        string $username,
        string $password,
    ): Client {
        if (!isset($this->clients[$baseUri])) {
            $this->clients[$baseUri] = $this->clientFactory->getClient($baseUri, $username, $password);
        }

        return $this->clients[$baseUri];
    }
}
