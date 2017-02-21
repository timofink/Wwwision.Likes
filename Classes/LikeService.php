<?php
namespace Wwwision\Likes;

use Neos\EventSourcing\Event\EventPublisher;
use Neos\Flow\Annotations as Flow;
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
     * @var EventPublisher
     */
    private $eventPublisher;

    /**
     * @var LikeFinder
     */
    private $likeFinder;

    public function __construct(EventPublisher $eventPublisher, LikeFinder $likeFinder)
    {
        $this->eventPublisher = $eventPublisher;
        $this->likeFinder = $likeFinder;
    }

    public function addLike(string $subjectType, string $userId, string $subjectId)
    {
        if ($this->likeExists($subjectType, $userId, $subjectId)) {
            throw new \RuntimeException(sprintf('User "%s" already likes subject "%s" (type "%s")', $userId, $subjectId, $subjectType), 1487677445);
        }
        $streamName = sprintf('Wwwision.Likes:%s-User-%s', $subjectType, $userId);
        $event = new LikeWasAdded($subjectType, $userId, $subjectId);
        $this->eventPublisher->publish($streamName, $event);
    }

    public function revokeLike(string $subjectType, string $userId, string $subjectId)
    {
        if (!$this->likeExists($subjectType, $userId, $subjectId)) {
            throw new \RuntimeException(sprintf('Can\'t revoke non-existing like for user "%s" and subject "%s" (type "%s")', $userId, $subjectId, $subjectType), 1487677455);
        }
        $streamName = sprintf('Wwwision.Likes:%s-User-%s', $subjectType, $userId);
        $event = new LikeWasRevoked($subjectType, $userId, $subjectId);
        $this->eventPublisher->publish($streamName, $event);
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
        return $this->likeFinder->findBySubjectType($subjectType)->toArray();
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
        return $this->likeFinder->findBySubjectTypeAndUserId($subjectType, $userId)->toArray();
    }

}
