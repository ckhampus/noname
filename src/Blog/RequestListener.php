<?php

namespace Blog;

use Symfony\Component\HttpKernel\Event\GetResponseEvent,
    Symfony\Component\EventDispatcher\EventSubscriberInterface,
    Symfony\Component\HttpKernel\KernelEvents;

class RequestListener implements EventSubscriberInterface
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $request->setFormat('png', 'image/png');
        $request->setFormat('jpg', 'image/jpeg');
        $request->setFormat('gif', 'image/gif');
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 32)),
        );
    }
}
