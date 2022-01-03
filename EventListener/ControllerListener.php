<?php

declare(strict_types=1);

namespace Shopping\ApiTKDtoMapperBundle\EventListener;

use Symfony\Component\HttpKernel\Event\ControllerEvent;

/**
 * Class ControllerListener.
 *
 * Remember, what controller got called in this request, so we can get the corresponding annotation in the ResponseView.
 *
 * @package Shopping\ApiTKDtoMapperBundle\EventListener
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
    private $calledController;

    /**
     * @param ControllerEvent $event
     */
    public function onKernelController(ControllerEvent $event): void
    {
        // only transform on original action
        if (!$this->masterRequest) {
            return;
        }
        $this->masterRequest = false;

        $this->calledController = $event->getController();
    }

    public function getCalledController(): ?callable
    {
        return $this->calledController;
    }
}
