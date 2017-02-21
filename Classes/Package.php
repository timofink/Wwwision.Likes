<?php
namespace Wwwision\Likes;

use Neos\EventSourcing\Event\EventInterface;
use Neos\EventSourcing\Event\EventPublisher;
use Neos\Flow\Cli\CommandRequestHandler;
use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Http\HttpRequestHandlerInterface;
use Neos\Flow\Package\Package as BasePackage;

final class Package extends BasePackage
{

    public function boot(Bootstrap $bootstrap)
    {
        $dispatcher = $bootstrap->getSignalSlotDispatcher();


        // Add metadata to all Wwwision.Likes events
        $dispatcher->connect(EventPublisher::class, 'beforePublishingEvent', function (EventInterface $event, array &$metadata) use ($bootstrap) {
            if ($bootstrap->getObjectManager()->getPackageKeyByObjectName(get_class($event)) !== $this->packageKey) {
                return;
            }
            if (isset($metadata['Wwwision.Likes:Client'])) {
                return;
            }
            $requestHandler = $bootstrap->getActiveRequestHandler();
            if ($requestHandler instanceof CommandRequestHandler) {
                $metadata['Wwwision.Likes:Client'] = '(cli)';
                return;
            }
            if (!$requestHandler instanceof HttpRequestHandlerInterface) {
                return;
            }
            $httpRequest = $requestHandler->getHttpRequest();
            $metadata['Wwwision.Likes:Client'] = [
                'url' => (string)$httpRequest->getUri(),
                'method' => $httpRequest->getMethod(),
                'clientIpAddress' => $httpRequest->getClientIpAddress(),
                'userAgent' => $httpRequest->getHeader('User-Agent')
            ];
        });

    }
}
