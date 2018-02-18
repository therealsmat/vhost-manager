<?php namespace therealsmat;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;

class NewSiteCommand extends command{

    /**
     * Default extention to be used
     * @var string
     */
    private $ext = '.conf';

    public function configure()
    {
        $this->setName('new:site')
            ->setDescription('Creates a new virtual host configuration for a new site');

    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $site_name = $this->ask('Name of Website - ', $input, $output);

        $this->verifySiteDoesNotExist($site_name);

        $output->writeln("<info>Creating {$site_name}...</info>");

    }

    private function verifySiteDoesNotExist($name)
    {
        $sites = $this->getAvailableSites();
        $available_sites = explode(PHP_EOL, $sites);
        $vhost_name = $name.''.$this->ext;

        if (!in_array($vhost_name, $available_sites)) {
            return true;
        }
        throw new \RuntimeException('Site Already Exists!');
    }

    private function ask($question, $input, $output)
    {
        $helper = $this->getHelper('question');

        $question = new Question($question);

        return $helper->ask($input, $output, $question);
    }

    private function getAvailableSites()
    {
        $enabled_sites_dir = '/etc/apache2/sites-enabled';
        $command = "cd {$enabled_sites_dir} && ls";
        $process = new Process($command);
        $process->run();

        return $process->getOutput();
    }
}