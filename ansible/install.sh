#!/usr/bin/env bash
if [ ! -f /etc/ansible/hosts ];
then
    echo "Installing Ansible..."
    sudo DEBIAN_FRONTEND=noninteractive apt-get -y install software-properties-common
    sudo apt-add-repository -y ppa:ansible/ansible
    sudo apt-get update -y
    sudo DEBIAN_FRONTEND=noninteractive apt-get -y install -y ansible make
fi
echo "Ansible is installed."
