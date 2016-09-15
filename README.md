# docker-lap
Linux-Apache-PHP docker container

#Docker Machine:
docker-machine start

docker-machine env

##MySQL on Docker - https://hub.docker.com/_/mysql/
UnInstall it: docker rm mysql

Run it:   docker run -p 3306:3306 --name mysql -e MYSQL_ROOT_PASSWORD=root -d mysql:latest

Connect to it:   docker exec -i -t mysql /bin/bash

#PHP + Apache on Docker

http://jessesnet.com/development-notes/2015/docker-lamp-stack/

Build and Run an image (called my-lap)

docker build -t my-lap .

docker run --link mysql -v /Users/dcox/src/docker-lap/src/:/www:rw  -d -p 8080:80 --name my-lap my-lap

 -v is to mount a volume for local edits

 -d is to run as a daemon

 -p is which port to run as

#Connect to Image
docker exec -i -t my-lap /bin/bash

Connect to MySQL from LAP image: mysql -h 192.168.99.100 -proot

#Stop Image
docker stop my-lap
docker rm my-lap

#MISC
On Ubuntu, To search for a package, use apt-cache search
