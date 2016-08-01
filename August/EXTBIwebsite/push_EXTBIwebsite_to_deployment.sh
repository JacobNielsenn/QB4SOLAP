#!/bin/bash

SERVER_URL="http://localhost/EXTBIwebsite/"
SERVER_BASE_PATH="C:\xampp\htdocs"
COUNT=0
HELP="usage:\n [-h] \t\t -- This message\n -u\t\t -- AAU user (mandatory)\n -p \t\t -- AAU password (mandatory)\n [-m]\t\t -- Commit message (optional)\n file1 ... \t -- relate path to files/dir (mandatory)"

############ functions definitions ###############3

function log {
	echo "$(timestamp) [$USER] $1 -- $MESSAGE" >> transfers.log
}

function timestamp() {
  date +"%Y-%m-%d_%H-%M-%S"
}





############## Pass input flags ################

while getopts "hu:p:m::" opt; do
  case "$opt" in
    h) echo -e $HELP; exit ;;
    u) USER=$OPTARG ;;
    p) PASSWORD=$OPTARG ;;
	m) MESSAGE=$OPTARG ;;
  esac
done
shift $(( OPTIND - 1 ))


################ Checks ##########################

#Validate that sshpass is installed // I hope everybody have command installed :P
#command -v sshpass >/dev/null 2>&1 || { echo >&2 "I require sshpass but it's not installed.  Aborting."; exit 1; }
#Make sure that there is a connection to the server

#if [[ $(ping -c 1 $SERVER_URL >/dev/null ; echo $?) -ne 0 ]]; then
#	echo "Could not reach $SERVER_URL, please connect to the AAU network (Ping error code $?). Aborting." ; exit 1;
#fi
#Username and password must be supplied
if [[ -z $USER || -z $PASSWORD ]]; then
	echo -e $HELP ; exit 1;
fi
if [[ -z $1 ]]; then
	echo -e $HELP ; exit 1;
fi


############# Pormpt user #################

while true; do
    read -p "Do you wish to overwrite remote files? (Y/N) " yn
    case $yn in
        [Yy]* ) break;;
        [Nn]* ) exit;;
        * ) echo "Please answer yes or no.";;
    esac
done


############### Move files ##################
for file in "$@"; do
	FILE_NAME=$(echo $file | rev | cut -d/ -f1 | rev)
	FILE_BODY=$(echo $file | rev | cut -d/ -f2- | rev)
	if [[ $FILE_NAME == $FILE_BODY ]]; then #file is in root folder
		sshpass -p $PASSWORD scp -r $file $USER@$SERVER_URL:$SERVER_BASE_PATH
		((COUNT++))
		log $file

	elif [[ -d $file ]]; then #Input is a dir
		sshpass -p $PASSWORD ssh $USER@$SERVER_URL "mkdir -p $SERVER_BASE_PATH$FILE_BODY"
		for item in $(ls $file); do
			sshpass -p $PASSWORD scp -r $file$item $USER@$SERVER_URL:$SERVER_BASE_PATH$FILE_BODY
			((COUNT++))
			log $file$item
		done
	else #file is in sub folder
		sshpass -p $PASSWORD ssh $USER@$SERVER_URL "mkdir -p $SERVER_BASE_PATH$FILE_BODY"
		sshpass -p $PASSWORD scp -r $file $USER@$SERVER_URL:$SERVER_BASE_PATH$FILE_BODY
		((COUNT++))
		log $file
	fi

done


echo "$COUNT files was copied to server"

#svn commit transfers.log -m"$COUNT files copied to server"

