<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Context;

interface SessionContextStorageInterface
{
    public function setSessionContext(SessionContext $sessionContext): void;

    public function getSessionContext(): SessionContext;

    public function invalidateSessionContext(): void;
}
