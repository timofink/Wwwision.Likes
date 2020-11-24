<?php
namespace Wwwision\Likes;

use Neos\EventSourcing\Event\DecoratedEvent;
use Neos\EventSourcing\Event\DomainEvents;
use Neos\EventSourcing\EventStore\EventStore;
use Neos\EventSourcing\EventStore\StreamName;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandRequestHandler;
use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Http\HttpRequestHandlerInterface;
use Neos\Flow\Http\ServerRequestAttributes;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerException;
use Wwwision\Likes\Domain\Event\LikeEventInterface;
use Wwwision\Likes\Domain\Event\LikeWasAdded;
use Wwwision\Likes\Domain\Event\LikeWasRevoked;
use Wwwision\Likes\Projection\Like\Like;
use Wwwision\Likes\Projection\Like\LikeFinder;

/**
 * @Flow\Scope("singleton")
 */
final class LikeService
{

    /**
     * @Flow\Inject
     * @var EventStore
     */
    protected $eventStore;

    /**
     * @Flow\Inject
     * @var LikeFinder
     */
    protected $likeFinder;

    /**
     * @Flow\Inject
     * @var Bootstrap
     */
    protected $bootstrap;

    /**
     * @Flow\InjectConfiguration(package="Wwwision.Likes", path="eventMetadata")
     * @var array
     */
    protected $metadataSettings;

    public function addLike(string $subjectType, string $userId, string $subjectId): void
    {
        if ($this->likeExists($subjectType, $userId, $subjectId)) {
            throw new \RuntimeException(sprintf('User "%s" already likes subject "%s" (type "%s")', $userId, $subjectId, $subjectType), 1487677445);
        }
        $this->commit(new LikeWasAdded($subjectType, $userId, $subjectId));
    }

    public function revokeLike(string $subjectType, string $userId, string $subjectId): void
    {
        if (!$this->likeExists($subjectType, $userId, $subjectId)) {
            throw new \RuntimeException(sprintf('Can\'t revoke non-existing like for user "%s" and subject "%s" (type "%s")', $userId, $subjectId, $subjectType), 1487677455);
        }
        $this->commit(new LikeWasRevoked($subjectType, $userId, $subjectId));
    }

    public function likeExists(string $subjectType, string $userId, string $subjectId): bool
    {
        return $this->likeFinder->hasOneWithSubjectTypeUserIdAndSubjectId($subjectType, $userId, $subjectId);
    }

    /**
     * @param string $subjectType
     * @return Like[]
     */
    public function getLikesBySubjectType(string $subjectType): array
    {
        return $this->likeFinder->findBySubjectType($subjectType);
    }

    public function getNumberOfLikesBySubject(string $subjectType, string $subjectId): int
    {
        return $this->likeFinder->countBySubjectTypeAndSubjectId($subjectType, $subjectId);
    }

    /**
     * @param string $subjectType
     * @param string $userId
     * @return Like[]
     */
    public function getLikesBySubjectTypeAndUser(string $subjectType, string $userId): array
    {
        return $this->likeFinder->findBySubjectTypeAndUserId($subjectType, $userId);
    }

    private function commit(LikeEventInterface $event): void
    {
        $streamName = StreamName::fromString(sprintf('Wwwision.Likes:%s-User-%s', $event->getSubjectType(), $event->getUserId()));

        $eventWithMetadata = DecoratedEvent::addMetadata($event, $this->getMetadata());
        try {
            $this->eventStore->commit($streamName, DomainEvents::withSingleEvent($eventWithMetadata));
        } catch (SerializerException $e) {
            throw new \RuntimeException(sprintf('Failed to serialize event while commiting: %s', $e->getMessage()), 1605880598, $e);
        }
    }

    private function getMetadata(): array
    {
        $requestHandler = $this->bootstrap->getActiveRequestHandler();
        if ($requestHandler instanceof CommandRequestHandler) {
            return ['Wwwision.Likes:Client' => '(cli)'];
        }
        if (!$requestHandler instanceof HttpRequestHandlerInterface) {
            return [];
        }
        $httpRequest = $requestHandler->getHttpRequest();

        $metadataParts = [];
        if (isset($this->metadataSettings['url']) && $this->metadataSettings['url'] === true) {
            $metadataParts['url'] = (string)$httpRequest->getUri();
        }
        if (isset($this->metadataSettings['method']) && $this->metadataSettings['method'] === true) {
            $metadataParts['method'] = $httpRequest->getMethod();
        }
        if (isset($this->metadataSettings['clientIpAddress']) && $this->metadataSettings['clientIpAddress'] === true) {
            $metadataParts['clientIpAddress'] = $httpRequest->getAttribute(ServerRequestAttributes::CLIENT_IP) ?? $httpRequest->getServerParams()['REMOTE_ADDRs'] ?? '?';
        }
        if (isset($this->metadataSettings['userAgent']) && $this->metadataSettings['userAgent'] === true) {
            $metadataParts['userAgent'] = $httpRequest->getHeader('User-Agent');
        }
        return ['Wwwision.Likes:Client' => $metadataParts];
    }


}
