<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class AddCustomHeaderListener
{
    public function addHeader(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        $response->headers->add(['X-DEVELOPED-BY' => 'Monsieur X']);
    }
}