<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\ApiV1Bundle\Endpoint\EndpointInterface as ApiV1Endpoint;
use App\ApiV1Bundle\RequestHandler as ApiV1RequestHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class RequestSubscriber implements EventSubscriberInterface
{
    private ContainerInterface $container;
    private ApiV1RequestHandler $apiV1RequestHandler;

    public function __construct(ContainerInterface $container, ApiV1RequestHandler $apiV1RequestHandler)
    {
        $this->container = $container;
        $this->apiV1RequestHandler = $apiV1RequestHandler;
    }

    public function handleRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$request->attributes->has('_controller')) {
            return;
        }

        $controllerClassname = explode('::', $request->attributes->get('_controller'))[0];
        $controllerClass = $this->container->get($controllerClassname);

        if (!$controllerClass) {
            return;
        }

        if ($controllerClass instanceof ApiV1Endpoint) {
            $response = $this->apiV1RequestHandler->handle($request, $controllerClass);
            $response->headers->set('origin', $request->headers->get('origin'));
            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'handleRequest',
        ];
    }
}