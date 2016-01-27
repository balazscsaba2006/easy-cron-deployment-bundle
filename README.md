[![Code Climate](https://codeclimate.com/github/MadrakIO/easy-cron-deployment-bundle/badges/gpa.svg)](https://codeclimate.com/github/MadrakIO/easy-cron-deployment-bundle)
[![Packagist](https://img.shields.io/packagist/v/MadrakIO/easy-cron-deployment-bundle.svg)]()
[![Packagist](https://img.shields.io/packagist/dt/MadrakIO/easy-cron-deployment-bundle.svg)]()
[![Packagist](https://img.shields.io/packagist/l/MadrakIO/easy-cron-deployment-bundle.svg)]()

Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require madrakio/easy-cron-deployment-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new MadrakIO\EasyCronDeploymentBundle\MadrakIOEasyCronDeploymentBundle(),
        );

        // ...
    }

    // ...
}
```

Step 3: Create cron.yml file
-------------------------

Create `app/config/cron.yml` and follow this example:

```yaml
madrak_io_easy_cron_deployment:
    jobs:
        -
            minute: 0
            hour: 0
            day: 1
            month: 1
            task: 'php somescript.php'
            hosts: ['node-1', 'node-2']
            disabled: true
        -
            minute: 0
            task: 'php someotherscript.php'
```

Step 4: Import cron.yml into `app/config/config.yml`
-------------------------

```yaml
imports:
    - { resource: cron.yml }
```

Possible Commands
-------------------------

`app/console madrakio:cron:deploy`
Deploy cron based on cron.yml

`app/console madrakio:cron:disable`
Disable all current cron tasks by adding # before each line

`app/console madrakio:cron:enable`
Remove all #s before cron tasks

`app/console madrakio:cron:verify`
Verify that cron tasks match the ones in the cron.yml file