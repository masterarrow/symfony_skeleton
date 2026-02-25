<?php

namespace App\Logger;

use Monolog\Level;
use Monolog\LogRecord;
use App\Entity\AuditLog;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\AbstractProcessingHandler;

class DatabaseLogHandler extends AbstractProcessingHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        int|string|Level $level = Level::Info
    ) {
        parent::__construct($level);
    }

    protected function write(LogRecord $record): void
    {
        $log = new AuditLog();
        $log->setLevel($record->level->getName());
        $log->setMessage($record->message);
        $log->setContext($record->context);

        $this->em->persist($log);
        $this->em->flush();
    }
}
