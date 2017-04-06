# -*- mode: ruby -*-
# vi: set ft=ruby :

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
Vagrant.configure("2") do |config|

    config.vm.box = "chrislentz/trusty64-lamp"
    #config.vm.network "private_network", ip: "192.168.33.10"
    config.vm.network "public_network"
    config.vm.hostname = "SRzA"
    config.vm.synced_folder ".", "/var/www", :mount_options => ["dmode=777", "fmode=666"]
    #config.vm.synced_folder '.', '/var/www', nfs: true

       config.vm.provider "virtualbox" do |v|
          v.memory = 2048
          v.cpus = 3
        end

    # Optional NFS. Make sure to remove other synced_folder line too
    #config.vm.synced_folder ".", "/var/www", :nfs => { :mount_options => ["dmode=777","fmode=666"] }

   # config.vm.provider "virtualbox" do |v|
   #   host = RbConfig::CONFIG['host_os']
#
   #   # Give VM 1/4 system memory
   #   if host =~ /darwin/
   #     # sysctl returns Bytes and we need to convert to MB
   #     mem = `sysctl -n hw.memsize`.to_i / 1024
   #   elsif host =~ /linux/
   #     # meminfo shows KB and we need to convert to MB
   #     mem = `grep 'MemTotal' /proc/meminfo | sed -e 's/MemTotal://' -e 's/ kB//'`.to_i
   #   elsif host =~ /mswin|mingw|cygwin/
   #     # Windows code via https://github.com/rdsubhas/vagrant-faster
   #     mem = `wmic computersystem Get TotalPhysicalMemory`.split[1].to_i / 1024
   #   end
#
   #   mem = mem / 1024 / 4
   #   v.customize ["modifyvm", :id, "--memory", mem]
   # end


   # config.vm.provision "shell", inline: <<-SHELL
   #     mv /var/www/public /var/www/public_html
   #     sudo sed -i s,/var/www/public,/var/www/public_html,g /etc/apache2/sites-available/000-default.conf
   #     sudo sed -i s,/var/www/public,/var/www/public_html,g /etc/apache2/sites-available/scotchbox.local.conf
   #     sudo service apache2 restart
   # SHELL

  # config.vm.box_check_update = false
  # config.vm.network "forwarded_port", guest: 80, host: 8080
  # config.vm.network "private_network", ip: "192.168.33.10"
  # config.vm.network "public_network"

  # Share an additional folder to the guest VM. The first argument is
  # the path on the host to the actual folder. The second argument is
  # the path on the guest to mount the folder. And the optional third
  # argument is a set of non-required options.
  # config.vm.synced_folder "../data", "/vagrant_data"

  # Provider-specific configuration so you can fine-tune various
  # backing providers for Vagrant. These expose provider-specific options.
  # Example for VirtualBox:
  #
  # config.vm.provider "virtualbox" do |vb|
  #   # Display the VirtualBox GUI when booting the machine
  #   vb.gui = true
  #
  #   # Customize the amount of memory on the VM:
  #   vb.memory = "1024"
  # end
  #
  # View the documentation for the provider you are using for more
  # information on available options.

  # Define a Vagrant Push strategy for pushing to Atlas. Other push strategies
  # such as FTP and Heroku are also available. See the documentation at
  # https://docs.vagrantup.com/v2/push/atlas.html for more information.
  # config.push.define "atlas" do |push|
  #   push.app = "YOUR_ATLAS_USERNAME/YOUR_APPLICATION_NAME"
  # end

  # Enable provisioning with a shell script. Additional provisioners such as
  # Puppet, Chef, Ansible, Salt, and Docker are also available. Please see the
  # documentation for more information about their specific syntax and use.
  # config.vm.provision "shell", inline: <<-SHELL
  #   apt-get update
  #   apt-get install -y apache2
  # SHELL
end
