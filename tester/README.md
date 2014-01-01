# How chroot tester works

## System requirement

* This is a Unix-like OS
* You have root privilege (Use only at setup)
* You have access to a directory whose parent directories and itself are all owned by root
* You have installed Jailkit

## Setup

1. Create a chroot environment with `./create_shj_chroot.sh`
	* Usage: `./create_shj_chroot.sh -d <chroot_dir> [-h] [-l <lang_list eg. python,java>]`
		* -d the path to the chroot
		* -h use hardlink instead of copying files to chroot
	* You may have to modify `jk_init.ini` to suit your environment
	* You must install jailkit first
	* chroot\_dir and its all parent dirs must be owned by root
	* lang\_list can be found in jk\_init.ini
2. Create a blank directory `shj_jail` at the root of chroot environment
3. Set owner of `shj_jail` be the user running webserver (usually www-data, http or nobody) `# chown http:http /path/to/chroot/shj_jail`
4. Run sudo visudo and add this line at the end of sudoers file: `<user_running_webserver> ALL=(ALL) NOPASSWD: ALL`

These procedures will be included in a auto-install script later

## How does it work?

* Chroot to the limited environment to act as a sandbox
* Although `chroot_dir/shj_jail` is owned by the webserver, untrusted code can still write to it as untrusted code is run as the user running the webserver. So, `sudo -u` is used so that the untrused code runs as other user to make it unable to write to any place.

