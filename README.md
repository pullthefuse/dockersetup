# Docker Setup
Create all the files you need to run your applications from docker files in your dev environment. You can create new Symfony or Laravel projects when setting up a domain so everything is ready for you to start coding your application. It uses a reverse proxy to allow you to run several containers at the same time. 

***This software has only been tested on MacOs and Linux*

## Instructions 
From the directory you wish to install the code run the following command to auto run the domain setup. Follow the instructions to setup your hosts file and up your docker-compose files when its finished.

```
docker run -it --rm --env DIR=${PWD} -v ${PWD}:${PWD} pullthefuse/dockersetup:latest
```

The following command will run the container and open bash which will allow you to setup, delete and use various other commands.

```
docker run -it --rm --env DIR=${PWD} -v ${PWD}:${PWD} pullthefuse/dockersetup:latest /bin/bash
```

Use ```bin/console docker``` to see the available commands. To setup a domain from here run ```bin/console docker:setup yourdomain.com```

### MacOS
You will need to install the docker client and in the preferences set the directory you are using.

#### MacOS Performance
For performance you will need to setup nfs

Add or create the ``` /etc/exports ``` file and put in the following:
 
 Catalina or higher
 ``` /System/Volumes/Data -alldirs -mapall=0:0,501:20 localhost ```

Pre Catalina
 ``` /Users -alldirs -mapall=0:0,501:20 localhost ```

You will be prompted by the MacOS to make this change accept the change

Add ``` nfs.server.mount.require_resv_port = 0 ``` to your ``` /etc/nfs.conf ```

Then restart nfsd
``` sudo nfsd restart ```

In the docker directory that's created edit the .env file as follows:

```
NFS_CODE_DIRECTORY=/System/Volumes/Data/Users/{ YOUR USERNAME }/{ DIRECTORY PATH OF YOUR CODE }
CODE_PERFORMANCE_OPTIONS=:cached
DOCKER_PERFORMANCE_OPTIONS=:ro
``` 

** For older versions of MacOS use ``` /Users ```  instead of ``` /System/Volumes/Data/Users ``` for your NFS_CODE_DIRECTORY

You will need to run ``` docker-compose -f MacOS.yml up -d ``` to create the nfs volume that's required. Once the volume is created you will not need to run it again.

### Customize Settings
Nginx settings - Currently after the files are created you will have to go in and update the settings you wish to change.
Docker images/settings - You can go to the docker-compose files and change the images or any of the settings before running docker-compose up.

### Performance Settings
To improve performance you can add the following to the .env that is locatied with the docker-compose files.

```
CODE_PERFORMANCE_OPTIONS=:cached,delegated
DOCKER_PERFORMANCE_OPTIONS=:ro
```

### Multiple Database Versions
We support multiple database versions. If you use the current version on the initial install it will be on the normal port so 3306 for mysql.

If you use version lower it will drop a port number so mysql will be on port 3305 and so on.

### Docker Images
All the Dockerfiles used to create the custom images can be found [here](https://github.com/pullthefuse/docker)
