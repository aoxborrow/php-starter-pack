# -*- mode: ruby -*-
# vi: set ft=ruby :

# lowercase project name, no spaces
# this automatically gets project name from parent folder
PROJECT_NAME = File.basename(Dir.pwd)

VAGRANTFILE_API_VERSION = "2"
Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "bento/ubuntu-18.04"
  config.vm.network :private_network, ip: "192.168.33.73"
  config.vm.synced_folder ".", "/home/vagrant/" + PROJECT_NAME, type: "nfs", mount_options: ['actimeo=1']
  config.ssh.insert_key = false
  config.ssh.shell = "bash"

  # install Ansible within the VM and run our playbook
  # unfortunately installing ansible_local automatically is broken currently due to this issue:
  # https://github.com/hashicorp/vagrant/issues/10914
  config.vm.provision "shell", inline: "sudo /home/vagrant/" + PROJECT_NAME + '/ansible/install.sh'
  config.vm.provision "ansible_local" do |ansible|
    ansible.compatibility_mode = "2.0"
    ansible.provisioning_path = "/home/vagrant/" + PROJECT_NAME + "/ansible"
    ansible.galaxy_role_file = "requirements.yml"
    ansible.galaxy_command = "ansible-galaxy install --role-file=%{role_file} --roles-path=%{roles_path}"
    ansible.playbook = "playbook.yml"
    ansible.limit = "dev"
  end

  config.vm.provider "parallels" do |prl|
    prl.name = PROJECT_NAME
    prl.linked_clone = true
    prl.memory = 1024
    prl.cpus = 2
  end

  config.vm.provider "vmware_fusion" do |vf|
    vf.gui = true
    vf.vmx['displayname'] = PROJECT_NAME
    vf.vmx["memsize"] = 1024
    vf.vmx["numvcpus"] = 2
  end

  config.vm.provider "vmware_desktop" do |vd|
    vd.gui = true
    vd.vmx['displayname'] = PROJECT_NAME
    vd.vmx["memsize"] = 1024
    vd.vmx["numvcpus"] = 2
  end

  config.vm.provider "virtualbox" do |vb|
    vb.gui = true
    vb.name = PROJECT_NAME
    vb.memory = 1024
    vb.cpus = 2
  end
end
