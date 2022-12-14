<?php
// src/EventListener/AccessDeniedListener.php
namespace App\EventListener;


use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AccessDeniedListener implements EventSubscriberInterface
{
    const ERROR_NOT_CONNECT = "personne n'est connecté";
    // TODO essayer de faire un message d'erreur quand id est un string
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 2],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof AccessDeniedException) {
            return;
        }

        $response = new JsonResponse([
            'error' => self::ERROR_NOT_CONNECT
        ], 401);
        // optionally set the custom response
        $event->setResponse($response);
    }
}
