<?php
namespace Wwwision\Likes\Projection\Like;

use Neos\EventSourcing\Projection\Doctrine\AbstractDoctrineFinder;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\QueryInterface;
use Neos\Flow\Persistence\QueryResultInterface;

/**
 * @Flow\Scope("singleton")
 *
 * @internal To be used by the LikeService
 */
final class LikeFinder extends AbstractDoctrineFinder
{
    /**
     * @var array
     */
    protected $defaultOrderings = [
        'userId' => QueryInterface::ORDER_ASCENDING,
        'subjectType' => QueryInterface::ORDER_ASCENDING
    ];

    public function findBySubjectType(string $subjectType): QueryResultInterface
    {
        $query = $this->createQuery();
        return $query->matching(
                $query->equals('subjectType', $subjectType)
            )
            ->execute();
    }

    public function findBySubjectTypeAndUserId(string $subjectType, string $userId): QueryResultInterface
    {
        $query = $this->createQuery();
        return $query->matching(
                $query->logicalAnd(
                    $query->equals('subjectType', $subjectType),
                    $query->equals('userId', $userId)
                )
            )
            ->execute();
    }

    public function hasOneWithSubjectTypeUserIdAndSubjectId(string $subjectType, string $userId, string $subjectId): bool
    {
        $query = $this->createQuery();
        $like = $query->matching(
                $query->logicalAnd(
                    $query->equals('subjectType', $subjectType),
                    $query->equals('userId', $userId),
                    $query->equals('subjectId', $subjectId)
                )
            )
            ->execute()
            ->getFirst();
        return $like !== null;
    }

    public function countBySubjectTypeAndSubjectId(string $subjectType, string $subjectId): int
    {
        $query = $this->createQuery();
        $queryBuilder = $query->getQueryBuilder();
        $queryBuilder
            ->select('COUNT(e)')
            ->where('e.subjectType = :subjectType')
            ->andWhere('e.subjectId = :subjectId')
            ->setParameters([
                'subjectType' => $subjectType,
                'subjectId' => $subjectId]
            );
        return (int)$queryBuilder->getQuery()->getSingleScalarResult();
    }
}