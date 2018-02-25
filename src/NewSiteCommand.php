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

    private $port = 80;

    private $sites_available_dir = '/etc/apache2/sites-available/';

    private $sites_enabled_dir = '/etc/apache2/sites-enabled/';

    public function configure()
    {
        $this->setName('new:site')
            ->setDescription('Creates a new virtual host configuration for a new site');

    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $site_name = $this->ask('Name of Website - ', $input, $output);

        $this->verifySiteDoesNotExist($site_name);

        $domain_dir = $this->getDomainDirectory();

        $domain_dir = trim($domain_dir);
        
        $public_dir = $this->ask('Site public directory - ', $input, $output);

        $output->writeln("<info>Creating {$site_name} in {$domain_dir}/{$public_dir} at port {$this->port}...</info>");

        $this->createSite($site_name, $domain_dir, $public_dir);

        $this->runCommand("sudo a2ensite {$site_name}.conf && systemctl reload apache2", TRUE);

        $this->addToHosts($site_name);

        $output->writeln("----------------------------------------------------");

        $output->writeln("<info>Site {$site_name} created successfully!</info>");

        $output->writeln("----------------------------------------------------");
    }

    private function getDomainDirectory()
    {
        return $this->runCommand("pwd");
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

    private function template($site_name, $domain_dir, $public_dir)
    {
        $document_root = $domain_dir.'/'.$public_dir;
        return "<VirtualHost *:{$this->port}>

                    ServerName www.{$site_name}
                    ServerAlias {$site_name}
                    DocumentRoot {$document_root}

                    <Directory {$document_root}>
                        Options Indexes FollowSymLinks
                        AllowOverride All
                        Require all granted
                    </Directory>

                    # Available loglevels: trace8, ..., trace1, debug, info, notice, warn,
                    # error, crit, alert, emerg.
                    # It is also possible to configure the loglevel for particular
                    # modules, e.g.
                    #LogLevel info ssl:warn

                    ErrorLog {$domain_dir}/error.log
                    CustomLog {$domain_dir}/access.log combined

                </VirtualHost>

                # vim: syntax=apache ts=4 sw=4 sts=4 sr noet

                ";
    }

    private function createSite($site_name, $domain_dir, $public_dir)
    {
        $filename = $this->sites_available_dir.''.$site_name.''.$this->ext;
        $content = $this->template($site_name, $domain_dir, $public_dir);
        file_put_contents($filename, $content);
    }

    private function addToHosts($site)
    {
        $hosts = file_get_contents('/etc/hosts');
        $hosts .= "127.0.0.1    {$site}";
        file_put_contents('/etc/hosts', $hosts);
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
        $command = "cd {$this->sites_enabled_dir} && ls";
        $process = new Process($command);
        $process->run();

        return $process->getOutput();
    }

    private function runCommand($command, $showRealTimeOutput = false)
    {
        $process = new Process($command);

        if($showRealTimeOutput) {
            $process->run(function($type, $buffer){
                echo $buffer;
            });
            return;
        }

        $process->run();

        return $process->getOutput();
    }
}