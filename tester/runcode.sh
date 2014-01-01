#!/bin/bash

# This file runs a command with given limits
# usage: ./runcode.sh extension memorylimit timelimit timelimit_int input_file jail run_as_user command
# If this code is run in chroot, <jail> should be set to a path in chroot
# i.e. if jail is at /path/to/chroot/jail/jail-1234, please set <jail> as /jail/jail-1234
# run_as_user MUST be a UID
# If run_as_user = 0, code will run directly
# If run_as_user <> 0, you should change your sudoers file and allow the user running PHP (e.g. www-data in Ubuntu+Apache) to su to another user
# e.g. In Ubuntu (Apache running under www-data), run visudo and add this line:
# www-data ALL=(username_of_run_as_user) NOPASSWD: ALL

EXT=$1
shift

MEMLIMIT=$1
shift

TIMELIMIT=$1
shift

TIMELIMITINT=$1
shift

IN=$1
shift

JAIL=$1
shift

RUN_AS_USER=$1
shift

if [ "$RUN_AS_USER" != '0' ]; then
	SUDO="sudo -u #$RUN_AS_USER"
else
	SUDO=""
fi

# The Command:
CMD=$@

cd $JAIL

# detecting existence of timeout
TIMEOUT_EXISTS=true
hash timeout 2>/dev/null || TIMEOUT_EXISTS=false

# Imposing memory limit with ulimit
if [ "$EXT" != "java" ]; then
	ulimit -v $((MEMLIMIT+10000))
	ulimit -m $((MEMLIMIT+10000))
	ulimit -s $((MEMLIMIT+10000))
fi

# Imposing time limit with ulimit
ulimit -t $TIMELIMITINT

if $TIMEOUT_EXISTS; then
	# Run the command with REAL time limit of TIMELIMITINT*2
	$SUDO timeout -s9 $((TIMELIMITINT*2)) $CMD <$IN >out 2>err
else
	# Run the command
	$SUDO $CMD <$IN >out 2>err	
fi
EC=$?

# KILL all processes of another_user (A process may still be alive!)
# If you are running codes as another_user, also uncomment this line:
#sudo -u another_user pkill -9 -u another_user

# Return exitcode
exit $EC
