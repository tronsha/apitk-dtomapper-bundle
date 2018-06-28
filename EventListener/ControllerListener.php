<?php

namespace Ofeige\Rfc1Bundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

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