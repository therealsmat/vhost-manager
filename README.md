# vhost-manager
Create and manage your virtual hosts on your command line, seamlessly!

Vhost manager is a php command line tool to help you create virtual hosts for your new site.

## Requirements
You must have php installed on your machine to use the tool.

## Installation
Download the tool using composer

`composer global require therealsmat/vhost-manager`

## Usage
To list all available sites, run `vhost sites`

To create a new site, run `vhost new:site`. You will be prompted to

- Enter the site domain name. This is the url you want to use to access your app e.g `site1.local`
- Enter a public directory. This is the directory inside which you want your apllication to be served from.

Do not confuse the public directory with the root directory. By default, the directory you are running the command from will be used as the default root directory. The public directory is the directory you want web requests to be served from. e.g `site/public`. Here, our root directory is `site` since it will contain all of our application code. However, we want all requests to be served from the `public` directory, so `public` will be our public directory.

## Todo's
These features will be shipping soon;

- Rename site
- Change public directory
- Use a different root directory
- Serve site on a different port
- Delete a site (vhost)

## License
MIT
