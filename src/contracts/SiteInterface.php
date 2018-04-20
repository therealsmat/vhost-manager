<?php

namespace therealsmat\contracts;

interface SiteInterface {

    public function template($site_name, $domain_dir, $public_dir);
    public function createSite($site_name, $domain_dir, $public_dir);
    public function addToHosts($site);
}