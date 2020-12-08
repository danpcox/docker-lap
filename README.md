# docker-lap
Linux-Apache-PHP docker container
( On Windows - as administrator do this - "git config --system core.longpaths true" )

( To checkout, git clone  https://github.com/danpcox/docker-lap.git )


#Docker Machine:
docker-machine start

docker-machine env

##MariaDB on Docker - https://hub.docker.com/_/mariadb/
UnInstall it: docker rm mariadb

Install it (first time only):   docker run --name my-mariadb -p 3306:3306 -v /mysql:/var/lib/mysql -e MYSQL_ROOT_PASSWORD=password -d mariadb

Connect to it:   docker exec -i -t mysql /bin/bash

Stop it: docker stop my-mariadb

Start it: docker start my-mariadb

#PHP + Apache on Docker

http://jessesnet.com/development-notes/2015/docker-lamp-stack/

Build and Run an image (called my-lap)

docker build -t my-lap .

docker run -v /www:/www:rw -v -d -p 8080:80 -e MYSQL_PASS=root --name my-lap my-lap

 -v is to mount a volume for local edits

 -d is to run as a daemon

 -p is which port to run as

#Connect to Image
docker exec -i -t my-lap /bin/bash

Connect to MySQL from LAP image: mysql -h 192.168.99.100 -p$MYSQL_PASS

#Stop Image
docker stop my-lap
docker rm my-lap

#MISC
On Ubuntu, To search for a package, use apt-cache search

#Docker Misc
- List all inactive containers : docker ps -f "status=exited"
- Remove all inactive containers: docker rm $(docker ps --filter "status=exited" -q)
