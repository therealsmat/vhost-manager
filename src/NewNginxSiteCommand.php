<?php namespace therealsmat;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use therealsmat\contracts\SiteInterface;

class NewNginxSiteCommand extends CommandStructure implements SiteInterface {

    /**
     * Available sites directory
     * @var string
     */
    protected $sites_available_dir = '/etc/nginx/sites-available/';

    /**
     * Enabled site directory
     * @var string
     */
    protected $sites_enabled_dir = '/etc/nginx/sites-enabled/';

    /**
     * Default extension to be used
     * @var string
     */
    private $ext = '.conf';

    /**
     * Default port directory
     * @var int
     */
    private $port = 80;

    public function configure()
    {
        $this->setName('new:nginx-site')
            ->setDescription('Creates a new Nginx Server configuration for a new site');

    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $site_name = $this->ask('Name of Website - ', $input, $output);

        $this->verifySiteDoesNotExist($site_name);

        $domain_dir = $this->getDomainDirectory();

        $domain_dir = $this->ask('Enter root directory for this site - ', $input, $output, $domain_dir);

        $domain_dir = trim($domain_dir);

        $this->ensureDirectoryExists($domain_dir);

        $public_dir = $this->ask('Site public directory - ', $input, $output);

        $this->ensureDirectoryExists($domain_dir.'/'.$public_dir);

        $output->writeln("<info>Creating {$site_name} in {$domain_dir}/{$public_dir} at port {$this->port}...</info>");

        $this->createSite($site_name, $domain_dir, $public_dir);

        $this->runCommand("sudo a2ensite {$site_name}.conf && systemctl reload nginx", TRUE);

        $this->addToHosts($site_name);

        $output->writeln("====================================================");

        $output->writeln("<info>Site {$site_name} created successfully!</info>");

        $output->writeln("====================================================");
    }

    private function getDomainDirectory()
    {
        return $this->runCommand("pwd");
    }

    private function verifySiteDoesNotExist($name){
        $sites = $this->getAvailableSites();
        $available_sites = explode(PHP_EOL, $sites);
        $vhost_name = $name.''.$this->ext;

        if (!in_array($vhost_name, $available_sites)) {
            return true;
        }
        throw new \RuntimeException('Site Already Exists!');
    }

    public function template($site_name, $domain_dir, $public_dir)
    {
        $document_root = $domain_dir.'/'.$public_dir;

        return "
            listen {$this->port};
            listen [::]:{$this->port};
    
            root {$document_root};
            index index.php index.html index.htm index.nginx-debian.html;
    
            server_name {$site_name} www.{$site_name};
    
            location / {
                    try_files \$uri \$uri/ =404;
            }
        ";
    }

    public function createSite($site_name, $domain_dir, $public_dir)
    {
        $filename = $this->sites_available_dir.''.$site_name.''.$this->ext;
        $content = $this->template($site_name, $domain_dir, $public_dir);
        file_put_contents($filename, $content);
    }

    public function addToHosts($site)
    {
        $hosts = file_get_contents('/etc/hosts');
        $hosts .= "127.0.0.1       {$site}".PHP_EOL;
        file_put_contents('/etc/hosts', $hosts);
    }
}