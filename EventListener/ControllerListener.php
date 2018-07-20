<?php

namespace Shopping\ApiDtoMapperBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class ControllerListener
 *
 * Remember, what controller got called in this request, so we can get the corresponding annotation in the ResponseView.
 *
 * @package Shopping\ApiDtoMapperBundle\EventListener
 */
class ControllerListener
{
    /**
     * @var bool
     */
    private $masterRequest = true;

    /**
     * @var callable|null
     */
    private $calledController = null;

    public function onKernelController(FilterControllerEvent $event)
    {
        //Only transform on original action
        if (!$this->masterRequest) {
            return;
        }
        $this->masterRequest = false;

        if (is_array($event->getController())) {
            $this->calledController = $event->getController();
        }
    }

    public function getCalledController(): ?callable
    {
        return $this->calledController;
    }
}