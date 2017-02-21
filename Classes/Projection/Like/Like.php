<?php
namespace Wwwision\Likes\Projection\Like;

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;
use Neos\EventSourcing\Annotations as ES;

/**
 * @Flow\Entity
 * @ES\ReadModel
 * @ORM\Table(name="wwwision_likes_like")
 */

final class Like implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @var string
     */
    protected $subjectType;

    /**
     * @ORM\Id
     * @var string
     */
    protected $userId;

    /**
     * @ORM\Id
     * @var string
     */
    protected $subjectId;

    public function __construct(string $subjectType, string $userId, string $subjectId)
    {
        $this->subjectType = $subjectType;
        $this->userId = $userId;
        $this->subjectId = $subjectId;
    }

    /**
     * @return string
     */
    public function getSubjectType()
    {
        return $this->subjectType;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getSubjectId()
    {
        return $this->subjectId;
    }

    public function jsonSerialize(): array
    {
        return [
            'subjectType' => $this->subjectType,
            'userId' => $this->userId,
            'subjectId' => $this->subjectId
        ];
    }
}