<?php
namespace Wwwision\Likes\ViewHelpers\Controller;

use Neos\Flow\Annotations as Flow;
use Neos\FluidAdaptor\Core\Widget\AbstractWidgetController;
use Neos\FluidAdaptor\Core\Widget\Exception as WidgetException;
use Wwwision\Likes\LikeService;

class LikeButtonController extends AbstractWidgetController
{

    /**
     * @Flow\Inject
     * @var LikeService
     */
    protected $likeService;

    public function indexAction()
    {

    }

    public function endpointAction()
    {
        if (!isset($this->widgetConfiguration['subjectType'])) {
            throw new WidgetException('Invalid configuration. Missing "subjectType"', 1487684912);
        }
        if (!isset($this->widgetConfiguration['userId'])) {
            throw new WidgetException('Invalid configuration. Missing "userId"', 1487684913);
        }
        if (!isset($this->widgetConfiguration['subjectId'])) {
            throw new WidgetException('Invalid configuration. Missing "subjectId"', 1487684914);
        }
        $subjectType = $this->widgetConfiguration['subjectType'];
        $userId = $this->widgetConfiguration['userId'];
        $subjectId = $this->widgetConfiguration['subjectId'];
        switch ($this->request->getHttpRequest()->getMethod()) {
            case 'GET':
                return json_encode($this->likeService->likeExists($subjectType, $userId, $subjectId));
            case 'POST':
                $this->likeService->addLike($subjectType, $userId, $subjectId);
                return json_encode(true);
            case 'DELETE':
                $this->likeService->revokeLike($subjectType, $userId, $subjectId);
                return json_encode(true);
            default:
                throw new WidgetException(sprintf('Unsupported Request Method %s', $this->request->getHttpRequest()->getMethod()), 1487684915);
        }
    }
}