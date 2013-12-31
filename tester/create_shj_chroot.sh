#!/bin/bash

# Usage: ./create_shj_chroot.sh -d <chroot_dir> [-h] [-l <lang_list eg. python,java>]

# You may have to modify this script to suit your environment
# You must install jailkit first
# chroot_dir and its all parent dir must be owned by root
# lang_list can be found in jk_init.ini

while getopts ":hl:d:" opt;
do
	case $opt in
		h)
			hardlink=1
			;;
		
		l)
			langs=$OPTARG
			;;

		d)
			chroot_dir=$OPTARG
			;;
	esac
done;

cp jk_init.ini temp_jk_init.ini

cat << EOF >> temp_jk_init.ini
[temp]
comment = temp
includesections = uidbasics, $langs
EOF

if [ $hardlink -eq 1 ]; then
	jk_init -c temp_jk_init.ini -k -j $chroot_dir temp
else
	jk_init -c temp_jk_init.ini -j $chroot_dir temp
fi

rm temp_jk_init.ini

