<?php

namespace App\Event;

use App\ConsoleStyle;
use App\Exception\DockerSetupException;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ConsoleErrorSubscriber implements EventSubscriberInterface
{
    /**
     * @var bool
     */
    private bool $setExitCode = false;

    /**
     * Catch errors and display error messages.
     *
     * @param ConsoleErrorEvent $event
     */
    public function onConsoleError(ConsoleErrorEvent $event): void
    {
        if ($event->getError() instanceof DockerSetupException) {
            $io = new ConsoleStyle($event->getInput(), $event->getOutput());
            $io->errorText($event->getError()->getMessage());

            $event->setExitCode(0);
            $this->setExitCode = true;
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::ERROR => 'onConsoleError'
        ];
    }
}
