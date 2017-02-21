<?php
namespace Wwwision\Likes\Projection\Like;

use Neos\EventSourcing\Projection\Doctrine\AbstractDoctrineProjector;
use Neos\Flow\Annotations as Flow;
use Wwwision\Likes\Domain\Event\LikeWasAdded;
use Wwwision\Likes\Domain\Event\LikeWasRevoked;

/**
 * @Flow\Scope("singleton")
 */
final class LikeProjector extends AbstractDoctrineProjector
{
    public function whenLikeWasAdded(LikeWasAdded $event)
    {
        $like = new Like($event->getSubjectType(), $event->getUserId(), $event->getSubjectId());
        $this->add($like);
    }

    public function whenLikeWasRevoked(LikeWasRevoked $event)
    {
        $like = $this->get(['subjectType' => $event->getSubjectType(), 'userId' => $event->getUserId(), 'subjectId' => $event->getSubjectId()]);
        if ($like === null) {
            return;
        }
        $this->remove($like);
    }
}