<?php namespace therealsmat;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DeleteSiteCommand extends CommandStructure {

    /**
     * Available sites directory
     * @var string
     */
    protected $sites_available_dir = '/etc/apache2/sites-available/';

    /**
     * Enabled site directory
     * @var string
     */
    protected $sites_enabled_dir = '/etc/apache2/sites-enabled/';

    /**
     * Configure the command
     * @return [type] [description]
     */
    public function configure()
    {
        $this->setName('delete:site')
            ->setDescription('Deletes an existing virtual host site');
    }

    /**
     * Execute the command
     * @param  InputInterface  $input  [description]
     * @param  OutputInterface $output [description]
     * @return [type]                  [description]
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $options = $this->availableSitesArray();

        $site = $this->askWithOptions('What site do you want to delete?', $options, $input, $output);

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Continue with this action?', false);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $this->deleteSite($site);

        $output->writeln("=====================================================");

        $output->writeln("<info>Site {$site} deleted successfully!</info>");

        $output->writeln("=====================================================");

    }

    /**
     * Get a list of the available sites
     * @return [type] [description]
     */
    private function availableSitesArray()
    {
        $sites = $this->getAvailableSites();
        return explode(PHP_EOL, $sites);
    }

    /**
     * Delete a virtual host
     * @param  [type] $site [description]
     * @return [type]       [description]
     */
    private function deleteSite($site)
    {
        $this->runCommand("rm {$this->sites_enabled_dir}/{$site}");
        $this->runCommand("rm {$this->sites_available_dir}/{$site}");
    }
}