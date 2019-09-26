PHP Starter Pack
================

This is a starter pack for quickly developing a modern PHP project locally with [Vagrant](https://www.vagrantup.com/), which is then easily deployed live with [Ansible](https://docs.ansible.com/ansible/latest/). It installs and configures everything you need to enjoy a self-contained and disposable server VM:
 - **Ubuntu 18.04**
 - **PHP 7.3** and Composer
 - **Nginx** for static files with catch-all proxy to **PHP-FPM**
 - **MySQL** database and user

### Features
- Ansible playbook with configuration for both local development and production
- Easily add [Ansible Galaxy](https://galaxy.ansible.com/) roles for additional infrastructure with `ansible/requirements.yml`
- Automatically installs PHP dependencies from `composer.json`
- Minimal ["no-framework"](https://kevinsmith.io/modern-php-without-a-framework) `index.php` with automatic routing, templating, and exception handling (optional -- see below)

### Makefile Shortcuts
- `make db` - run db.sql to create project tables
- `make start/restart/stop` - start/restart/stop Nginx & PHP-FPM
- `make provision-dev` - run ansible playbook for localhost/dev
- `make provision-prod` - run ansible playbook for production group
- `make install-galaxy-roles` - install galaxy roles from `ansible/requirements.yml`

----

### Local Development

0. You'll need VM software like [VirtualBox](https://www.virtualbox.org/) (Mac/PC) or [Parallels](https://www.parallels.com/) (Mac)

0. Install [Vagrant](https://www.vagrantup.com/) for automating VMs.

0. Clone this repo as your project name: **(This is important, the project folder name will be used for initial configuration of database name, hostname, etc.)**
    ```sh
    git clone git@github.com:paste/php-starter-pack.git my-project-name
    ```

0. Build your Vagrant VM:

    ```sh
    vagrant up
    ```

0. Modify your computer's local `/etc/hosts`:

    ```
    192.168.33.73   my-project-name.local
    ```

0. Visit your app:
    ```
    http://my-project-name.local
    ```

0. Log into the VM via SSH and run db.sql to create example data:
    ```sh
    vagrant ssh
    cd my-project-name
    make db
    ```

0. Visit example page with DB query results:
    ```
    http://my-project-name.local/example/results
    ```

0. **Profit** :heavy_check_mark:

----

### Production

0. You'll need a remote user with `sudo` privileges for Ansible provisioning – don't use `root`.

0. Edit the `prod` group in `ansible/inventory.yml` and set the `ansible_user` and `ansible_host`. This is the SSH user and host to connect to. See here for more details:
https://docs.ansible.com/ansible/latest/user_guide/intro_inventory.html

0. Set the production `host_name` in `ansible/group_vars/prod.yml`. This is the domain that Nginx will use.

0. It's handy to provision from within your Vagrant VM since it already has Ansible installed. Log into the VM and run the Ansible playbook for the production group:
    ```sh
    vagrant ssh
    cd my-project-name
    make provision-prod
    ```

0. **Profit** :heavy_check_mark:


----

### "No-Framework" PHP

The `public/index.php` file contains a minimal ["no-framework"](https://kevinsmith.io/modern-php-without-a-framework) approach which uses [PSR](https://www.php-fig.org/) standards and popular 3rd-party libraries to accomplish much of what a larger framework provides. All of the libraries can be easily swapped using dependency injection. Here's a list of features and how they are implemented:

 - **URL Routing** is provided by [FastRoute](https://github.com/nikic/FastRoute). Nginx is configured with a catch-all rule to forward requests to index.php if there is no matching static file.
 - **Templating** uses the [Twig](https://twig.symfony.com/) template engine to enforce separation of PHP logic and frontend HTML. It is easy to use, well documented, and allows for convenient template inheritance.
 - **Configuration** is handled by [zend-config](https://docs.zendframework.com/zend-config/). This small library supports multiple file formats and provides an OO interface for site configuration.
 - **Database Access** uses the standard built-in [PDO](https://www.php.net/manual/en/book.pdo.php) library for its consistent API and data handling. A singular PDO connection is automatically provided to each controller.
 - **Request/Response** objects are provided to each controller for convenience in handling HTTP headers, redirects, etc. These [Symfony HTTP Components](https://symfony.com/doc/current/components/http_foundation.html) are fully featured and well documented. It is easy to add the [Session](https://symfony.com/doc/current/components/http_foundation/sessions.html) component if needed.
 - **Custom Exception Handling** allows for a better user and developer experience. It is a clean way to return proper 4XX/5XX HTTP error messages to the browser.  

----

### Writing Controllers

The `public/index.php` file will automatically route requests to your custom "controller" classes using the following naming convention:
```php
/mycontroller/mymethod === MyController->mymethod()
```
The default method is `index()`, so you can omit it in the URL:
```php
/mycontroller === MyController->index()
```

Controller methods that are `protected`, `private`, or `static` will not be accessible to the router. You can also write explicit routes and use named parameters, see `public/index.php` and the [FastRoute](https://github.com/nikic/FastRoute) documentation for examples.

Check out the `src/controllers/ExampleController.php` class to see examples of querying the database and rendering templates using the provided `PDO` and `Twig` instances. The abstract `BaseController` class provides a few convenience methods like `render()` and `query()/queryAll()` which should be enough to get started.
