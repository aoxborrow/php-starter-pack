PHP Starter Pack
================

### PHP-MySQL-Ansible-Vagrant *Starter Pack*

This is a template for quickly developing a modern PHP project locally with Vagrant, which is then easily deployed with Ansible. It installs and configures all the infrastructure you'll need:
 - **Vagrant** VM based on **Ubuntu 18.04**
 - **PHP 7.3** and Composer
 - **Nginx** for static files with catch-all proxy to **PHP-FPM**
 - **MySQL** database and user

### Features
- Disposable environment is fully self-contained within the Vagrant VM
- Ansible playbook for both local development and production
- Easily add Ansible Galaxy roles for additional infrastructure `ansible/requirements.yml`
- Automatically installs PHP dependencies from `composer.json`
- Minimal "no-framework" `index.php` with routing and exception handling (optional)

### Shortcuts
- `make db` - run db.sql to create project tables
- `make start/restart/stop` - start/restart/stop Nginx & PHP-FPM 
- `make provision-dev` - run ansible playbook for localhost/dev
- `make provision-prod` - run ansible playbook for production group
- `install-galaxy-roles` - install galaxy roles from *(ansible/requirements.yml)*
 
 
----

### Local Development

0. Install [Vagrant](https://www.vagrantup.com/)

0. Clone this repo as your project name: **(This is important, the project folder name will be used for configuring database name, hostname, etc.)**
    ```sh
    git clone git@github.com:paste/php-starter-pack.git my-project-name
    ```

0. Modify your computer's local `/etc/hosts`:

    ```
    192.168.33.73   my-project-name.local
    ```

0. Build your Vagrant VM:

    ```sh
    vagrant up
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

0. Set the production host_name in `ansible/group_vars/prod.yml`. This is the `server_name` that Nginx will listen for.

0. You'll need a remote user with `sudo` privileges for Ansible to provision with. Don't use `root` user.

0. Edit the `prod` group in `ansible/inventory.yml` and set the `ansible_user` and `ansible_host`. See here for more details:
https://docs.ansible.com/ansible/latest/user_guide/intro_inventory.html

0. It's handy to provision from within your Vagrant VM since it already has Ansible installed. Log into the VM via SSH and run the Ansible playbook for the production group:
    ```sh
    vagrant ssh
    cd my-project-name
    make provision-prod
    ```

0. **Profit** :heavy_check_mark:
