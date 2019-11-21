<?php declare(strict_types=1);

namespace App\Domain\Magento;

use App\Domain\Fixture\ExtractPimgentoMapping;
use App\Infrastructure\Command\CommandInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class MappingCommand implements CommandInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var string */
    private $importType;
    /** @var array */
    private $mapping;

    public function __construct(
        string $importType,
        array $mapping,
        LoggerInterface $logger = null
    ) {
        $this->importType = $importType;
        $this->mapping = $mapping;
        $this->logger = $logger ?? new NullLogger();
    }

    public function __invoke(\PDO $connection): void
    {
        (new ExtractPimgentoMapping($this->importType))();
    }
}