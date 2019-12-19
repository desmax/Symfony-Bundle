<?php
/**
 * This file is part of the SymfonyCronBundle package.
 *
 * (c) Dries De Peuter <dries@nousefreak.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cron\CronBundle\Command;

use Cron\Cron;
use Cron\CronBundle\Cron\Manager;
use Cron\CronBundle\Cron\Resolver;
use Cron\Executor\ExecutorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
class CronRunCommand extends Command
{
    private $executor;
    private $resolver;
    private $manager;

    public function __construct(ExecutorInterface $executor, Resolver $resolver, Manager $manager)
    {
        $this->executor = $executor;
        $this->resolver = $resolver;
        $this->manager = $manager;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cron:run');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cron = new Cron();
        $cron->setExecutor($this->executor);
        $cron->setResolver($this->resolver);

        $time = microtime(true);
        $dbReport = $cron->run();

        while ($cron->isRunning()) {}

        $output->writeln('time: ' . (microtime(true) - $time));

        $this->manager->saveReports($dbReport->getReports());

        return 0;
    }
}
