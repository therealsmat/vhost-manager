<?php namespace therealsmat;

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
            ->addOption('nginx', null, InputOption::VALUE_OPTIONAL, 'Your server', true);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $option = $input->getParameterOption('--nginx');
        $output->write($option);
    }
}