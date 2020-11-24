<?php
namespace Wwwision\Likes\Domain\Event;

use Neos\EventSourcing\Event\DomainEventInterface;

interface LikeEventInterface extends DomainEventInterface
{

    public function getSubjectType(): string;

    public function getUserId(): string;

}
