#!/bin/bash

PS3='Please enter your choice: '
options=("nginx" "php"  "EXIT")
select opt in "${options[@]}"
do
    case $opt in
        "nginx")
            docker exec -ti nginx sh
            break
            ;;
        "php")
            docker exec -ti php sh
            break
            ;;
        "EXIT")
            break
            ;;
        *) echo "invalid option $REPLY";;
    esac
done


