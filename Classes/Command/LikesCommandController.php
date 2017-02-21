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
    public function addCommand($type, $user, $subject)
    {
        $this->likeService->addLike($type, $user, $subject);
        $this->outputLine('Added <b>%s</b> like for user <b>%s</b> and subject <b>%s</b>', [$type, $user, $subject]);
    }

    /**
     * @param string $type
     * @param string $user
     * @param string $subject
     */
    public function revokeCommand($type, $user, $subject)
    {
        $this->likeService->revokeLike($type, $user, $subject);
        $this->outputLine('Revoked <b>%s</b> like for user <b>%s</b> and subject <b>%s</b>', [$type, $user, $subject]);
    }

    /**
     * @param string $type
     * @param string $subject
     */
    public function countSubjectLikesCommand($type, $subject)
    {
        $number = $this->likeService->getNumberOfLikesBySubject($type, $subject);
        $this->outputLine('There are <b>%d</b> <b>%s</b>-like(s) for subject <b>%s</b>', [$number, $type, $subject]);
    }

    /**
     * @param string $type
     * @param string $user
     */
    public function listUserLikesCommand($type, $user)
    {
        $this->outputLine('<b>%s</b>-Likes for user <b>%s</b>:', [$type, $user]);
        $likes = array_map(function(Like $like) {
            return $like->jsonSerialize();
        }, $this->likeService->getLikesBySubjectTypeAndUser($type, $user));
        $this->output->outputTable($likes, ['Subject Type', 'User ID', 'Subject ID']);
    }

    /**
     * @param string $type
     */
    public function listLikesCommand($type)
    {
        $this->outputLine('All <b>%s</b>-Likes:', [$type]);
        $likes = array_map(function(Like $like) {
            return $like->jsonSerialize();
        }, $this->likeService->getLikesBySubjectType($type));
        $this->output->outputTable($likes, ['Subject Type', 'User ID', 'Subject ID']);
    }

}
