<?php
namespace Wwwision\Likes\Domain\Event;

use Neos\EventSourcing\Event\EventInterface;

final class LikeWasAdded implements EventInterface
{

    /**
     * @var string
     */
    private $subjectType;

    /**
     * @var string
     */
    private $userId;

    /**
     * @var string
     */
    private $subjectId;

    public function __construct(string $subjectType, string $userId, string $subjectId)
    {
        $this->subjectType = $subjectType;
        $this->userId = $userId;
        $this->subjectId = $subjectId;
    }

    public function getSubjectType(): string
    {
        return $this->subjectType;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getSubjectId(): string
    {
        return $this->subjectId;
    }

}