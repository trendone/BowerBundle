<?php

namespace Toa\Bundle\BowerBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * BowerComponentsInstallCommand
 *
 * @author Enrico Thies <enrico.thies@gmail.com>
 */
class BowerComponentsInstallCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('toa:bower:components:install')
            ->setDescription('Installs bower components into a bundle')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command installs bower components
into the "Resources/Public" directory of a bundle.

<info>php %command.full_name% web</info>

If a "bower.json" file exists inside the "Resources/public" directory of a bundle,
Bower installs the components.

<info>php %command.full_name% web</info>

EOT
            );
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException When the target directory does not exist or symlink cannot be used
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new ExecutableFinder;

        $bin = $finder->find('bower');
        if (!$bin) {
            throw new \RuntimeException('The Bower executable could not be found.');
        }

        foreach ($this->getContainer()->get('kernel')->getBundles() as $bundle) {
            if (is_dir($originDir = $bundle->getPath() . '/Resources/public')) {
                $file = $originDir . '/bower.json';

                if (file_exists($file)) {
                    $output->writeln(sprintf('Installing bower components for <comment>%s</comment>', $bundle->getNamespace()));

                    $process = new Process($bin . ' install', $originDir);
                    $process->setTimeout(3600);
                    $process->run(
                        function ($type, $buffer) use ($output) {
                            if ($output->getVerbosity() > $output::VERBOSITY_NORMAL) {
                                 $output->write($buffer);
                            }
                        }
                    );

                    if (!$process->isSuccessful()) {
                        throw new \RuntimeException(sprintf('An error occurred when executing the "%s" command.', $this->getName()));
                    }
                }
            }
        }
    }
}
