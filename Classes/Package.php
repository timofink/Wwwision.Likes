<?php
namespace Wwwision\Likes;

use Neos\EventSourcing\Event\EventInterface;
use Neos\EventSourcing\Event\EventPublisher;
use Neos\Flow\Cli\CommandRequestHandler;
use Neos\Flow\Configuration\ConfigurationManager;
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

            $configurationManager = $bootstrap->getObjectManager()->get(ConfigurationManager::class);
            $settings = $configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, $this->getPackageKey());

            $metadataParts = [];
            if (isset($settings['eventMetadata']['url']) && $settings['eventMetadata']['url'] === true) {
                $metadataParts['url'] = (string)$httpRequest->getUri();
            }
            if (isset($settings['eventMetadata']['method']) && $settings['eventMetadata']['method'] === true) {
                $metadataParts['method'] = $httpRequest->getMethod();
            }
            if (isset($settings['eventMetadata']['clientIpAddress']) && $settings['eventMetadata']['clientIpAddress'] === true) {
                $metadataParts['clientIpAddress'] = $httpRequest->getClientIpAddress();
            }
            if (isset($settings['eventMetadata']['userAgent']) && $settings['eventMetadata']['userAgent'] === true) {
                $metadataParts['userAgent'] = $httpRequest->getHeader('User-Agent');
            }
            if ($metadataParts !== []) {
                $metadata['Wwwision.Likes:Client'] = $metadataParts;
            }
        });

    }
}
