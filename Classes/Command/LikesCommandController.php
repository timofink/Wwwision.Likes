<?php
namespace Wwwision\Likes\Command;

/*
 * This file is part of the Wwwision.Likes package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;
use Wwwision\Likes\LikeService;
use Wwwision\Likes\Projection\Like\Like;

/**
 * @Flow\Scope("singleton")
 */
final class LikesCommandController extends CommandController
{
    /**
     * @Flow\Inject
     * @var LikeService
     */
    protected $likeService;

    /**
     * @param string $type
     * @param string $user
     * @param string $subject
     */
    public function addCommand(string $type, string $user, string $subject): void
    {
        $this->likeService->addLike($type, $user, $subject);
        $this->outputLine('Added <b>%s</b> like for user <b>%s</b> and subject <b>%s</b>', [$type, $user, $subject]);
    }

    /**
     * @param string $type
     * @param string $user
     * @param string $subject
     */
    public function revokeCommand(string $type, string $user, string $subject): void
    {
        $this->likeService->revokeLike($type, $user, $subject);
        $this->outputLine('Revoked <b>%s</b> like for user <b>%s</b> and subject <b>%s</b>', [$type, $user, $subject]);
    }

    /**
     * @param string $type
     * @param string $subject
     */
    public function countSubjectLikesCommand(string $type, string $subject): void
    {
        $number = $this->likeService->getNumberOfLikesBySubject($type, $subject);
        $this->outputLine('There are <b>%d</b> <b>%s</b>-like(s) for subject <b>%s</b>', [$number, $type, $subject]);
    }

    /**
     * @param string $type
     * @param string $user
     */
    public function listUserLikesCommand(string $type, string $user): void
    {
        $this->outputLine('<b>%s</b>-Likes for user <b>%s</b>:', [$type, $user]);
        $likes = array_map(static function(Like $like) {
            return $like->jsonSerialize();
        }, $this->likeService->getLikesBySubjectTypeAndUser($type, $user));
        $this->output->outputTable($likes, ['Subject Type', 'User ID', 'Subject ID']);
    }

}
