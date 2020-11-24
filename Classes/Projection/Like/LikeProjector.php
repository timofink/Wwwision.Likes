<?php
namespace Wwwision\Likes\Projection\Like;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Neos\EventSourcing\Projection\ProjectorInterface;
use Neos\Flow\Annotations as Flow;
use Wwwision\Likes\Domain\Event\LikeWasAdded;
use Wwwision\Likes\Domain\Event\LikeWasRevoked;

/**
 * @Flow\Scope("singleton")
 */
final class LikeProjector implements ProjectorInterface
{
    /**
     * @var Connection
     */
    private $dbal;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->dbal = $entityManager->getConnection();
    }

    /**
     * @param LikeWasAdded $event
     * @throws DBALException
     */
    public function whenLikeWasAdded(LikeWasAdded $event): void
    {
        try {
            $this->dbal->insert('wwwision_likes_like', [
                'subjecttype' => $event->getSubjectType(),
                'userid' => $event->getUserId(),
                'subjectid' => $event->getSubjectId(),
            ]);
        } catch (\Exception $e) {
            // ignore unique constraint violations
            if (!$e instanceof UniqueConstraintViolationException) {
                throw $e;
            }
        }
    }

    /**
     * @param LikeWasRevoked $event
     * @throws DBALException | InvalidArgumentException
     */
    public function whenLikeWasRevoked(LikeWasRevoked $event): void
    {
        $this->dbal->delete('wwwision_likes_like', [
            'subjecttype' => $event->getSubjectType(),
            'userid' => $event->getUserId(),
            'subjectid' => $event->getSubjectId(),
        ]);
    }

    /**
     * @throws DBALException
     */
    public function reset(): void
    {
        $this->dbal->executeUpdate('TRUNCATE wwwision_likes_like');
    }
}
