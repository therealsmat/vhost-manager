<?php namespace therealsmat;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ListCommand extends command{

    public function configure()
    {
        $this->setName('sites')
            ->setDescription('List all your enabled sites');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $enabled_sites_dir = '/etc/apache2/sites-enabled';
        $command = "cd {$enabled_sites_dir} && ls";
        $process = new Process($command);
        $process->run(function ($type, $line) use ($output){
            $output->write($line);
        });
    }
}