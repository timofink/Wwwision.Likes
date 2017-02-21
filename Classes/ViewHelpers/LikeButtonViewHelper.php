<?php
namespace Wwwision\Likes\ViewHelpers;

use Neos\Flow\Annotations as Flow;
use Neos\FluidAdaptor\Core\Widget\AbstractWidgetViewHelper;

class LikeButtonViewHelper extends AbstractWidgetViewHelper
{

    /**
     * @Flow\Inject
     * @var Controller\LikeButtonController
     */
    protected $controller;

    protected $ajaxWidget = true;

    protected $storeConfigurationInSession = false;

    public function render(string $subjectType, string $userId, string $subjectId)
    {
        return $this->initiateSubRequest();
    }
}