<?php declare(strict_types=1);

namespace App\Infrastructure\Console\Command;

use Humbug\SelfUpdate\Updater;
use PHAR;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SelfUpdateCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'self-update';
    /** @var Updater */
    private $updater;

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this
            ->setDescription(sprintf(
                'Update %s to most recent stable build.',
                $this->getLocalPharName()
            ));
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->updater = new Updater('bin/bisous.phar');
        $this->updater->setStrategy(Updater::STRATEGY_GITHUB);
        $this->updater->getStrategy()->setPackageName('kiboko/bisous');
        $this->updater->getStrategy()->setPharName('bisous.phar');
        $this->updater->getStrategy()->setCurrentLocalVersion(
            $this->getApplication()->getVersion()
        );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $result = $this->updater->update();

        if ($result) {
            $io->success(
                sprintf(
                    'Your PHAR has been updated from "%s" to "%s".',
                    $this->updater->getOldVersion(),
                    $this->updater->getNewVersion()
                )
            );
        } else {
            $io->success('Your PHAR is already up to date.');
        }

        return 0;
    }

    private function getLocalPharName(): string
    {
        return basename(PHAR::running());
    }
}