# How tester works

## System requirement

* This is a Unix-like OS
* You have root privilege (Use only at setup)
* You have access to a directory whose parent directories and itself are all owned by root
* You have installed Jailkit and fakechroot

## Setup

1. Create a chroot environment with `./create_shj_chroot.sh`
	* Usage: `./create_min_chroot.sh -d <chroot_dir> [-h] [-l <lang_list eg. python,java>]`
		* -d the path to the chroot
		* -h use hardlink instead of copying files to chroot
	* You may have to modify `jk_init.ini` to suit your environment
	* You must install jailkit first
	* chroot\_dir and its all parent dir must be owned by root
	* lang\_list can be found in jk\_init.ini
2. Create a blank directory `shj_jail` at the root of chroot environment
3. Set owner of `jail` be the user running php (usually http or nobody) `# chown http:http /path/to/chroot/jail`
4. Set owner of `setuid_run_cmd` be root:root `# chown root:root setuid_run_cmd`
5. Make `setuid_run_cmd` be a `setuid` program `# chmod +s setuid_run_cmd`

_Procedure 4 and 5 should not be reversed. Otherwise, you have to do procedure 5 again_

These procedures will be included in a auto-install script later

## How does it work?

* Chroot to the limited environment to act as a sandbox
* Use fakechroot so that this can be done with root privilege
* The `chroot_dir/shj_jail` is only writable by the webserver, so that untrusted code cannot write to any place.
* If we fakechroot to the chroot environment directly, untrusted code will run as the webserver user (usually http), and it can still write to `chroot_dir/shj_jail`
* To solve this problem, `setuid` is used to set uid as something else before fakechroot
