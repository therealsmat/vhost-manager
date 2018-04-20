<?php namespace therealsmat;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NewSiteCommand extends CommandStructure {

    public function configure()
    {
        $this->setName('new:site')
            ->setDescription('Creates a new virtual host configuration for a new site')
            ->addOption('server', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Your server', ['nginx', 'apache']);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $option = $input->getParameterOption('--server');
        if ($option == 'nginx') {
            $command = $this->getApplication()->find('new:nginx-site');
            $args = [
                'command' => 'new:nginx-site'
            ];
            $commandInput = new ArrayInput($args);
            $output->writeln("============================================");
            $output->writeln("Nginx Server Block Wizard");
            $output->writeln("============================================");
            return $command->run($commandInput, $output);
        } else {
            $command = $this->getApplication()->find('new:apache-site');
            $args = [
                'command' => 'new:apache-site'
            ];
            $commandInput = new ArrayInput($args);
            $output->writeln("============================================");
            $output->writeln("Apache Virtual Host Wizard");
            $output->writeln("============================================");
            return $command->run($commandInput, $output);
        }
    }
}