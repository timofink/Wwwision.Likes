<?php
namespace Wwwision\Likes\Projection\Like;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 *
 * @internal To be used by the LikeService
 */
final class LikeFinder
{

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->queryBuilder = $entityManager->createQueryBuilder()
            ->select('l')
            ->from(Like::class, 'l')
            ->orderBy('l.userId')
            ->addOrderBy('l.subjectType');
    }

    public function findBySubjectType(string $subjectType): array
    {
        $query = $this->queryBuilder
            ->where('l.subjectType = :subjectType')
            ->setParameters([
                'subjectType' => $subjectType,
            ])
            ->getQuery();
        return $query->execute();
    }

    public function findBySubjectTypeAndUserId(string $subjectType, string $userId): array
    {
        $query = $this->queryBuilder
            ->where('l.subjectType = :subjectType')
            ->andWhere('l.userId = :userId')
            ->setParameters(compact('subjectType', 'userId'))
            ->getQuery();
        return $query->execute();
    }

    /**
     * @param string $subjectType
     * @param string $userId
     * @param string $subjectId
     * @return bool
     */
    public function hasOneWithSubjectTypeUserIdAndSubjectId(string $subjectType, string $userId, string $subjectId): bool
    {
        $query = $this->queryBuilder
            ->select('COUNT(l)')
            ->where('l.subjectType = :subjectType')
            ->andWhere('l.userId = :userId')
            ->andWhere('l.subjectId = :subjectId')
            ->setParameters(compact('subjectType', 'userId', 'subjectId'))
            ->getQuery();
        try {
            return (int)$query->getSingleScalarResult() > 0;
        } catch (NoResultException | NonUniqueResultException $e) {
            throw new \RuntimeException(sprintf('Failed to determine number of likes for subject type "%s", user id "%s" and subject id "%s": %s', $subjectType, $userId, $subjectId, $e->getMessage()), 1605880811, $e);
        }
    }

    /**
     * @param string $subjectType
     * @param string $subjectId
     * @return int
     */
    public function countBySubjectTypeAndSubjectId(string $subjectType, string $subjectId): int
    {
        $query = $this->queryBuilder
            ->select('COUNT(l)')
            ->where('l.subjectType = :subjectType')
            ->andWhere('l.subjectId = :subjectId')
            ->setParameters(compact('subjectType', 'subjectId'))
            ->getQuery();
        try {
            return (int)$query->getSingleScalarResult();
        } catch (NoResultException | NonUniqueResultException $e) {
            throw new \RuntimeException(sprintf('Failed to determine number of likes for subject type "%s" and subject id "%s": %s', $subjectType, $subjectId, $e->getMessage()), 1605880845, $e);
        }

    }
}
