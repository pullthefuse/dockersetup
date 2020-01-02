# Docker Setup
Create all the files you need to run your applications from docker files in your dev environment. You can create new Symfony or Laravel projects when setting up a domain so everything is ready for you to start coding your application. It uses a reverse proxy to allow you to run several containers at the same time. 

***This software has only been tested on MacOs and Linux*

## Instructions 
Run the following command to auto run the domain setup. Follow the instructions to setup your hosts file and up your docker-compose files when its finished.

```
docker run -it --rm -v /{YOUR_DIRECTORY}:/data jamescastro/dockersetup:latest
```

The following command will run the container and open bash which will allow you to setup, delete and use various other commands.
```
docker run -it --rm -v /{YOUR_DIRECTORY}:/data jamescastro/dockersetup:latest /bin/bash
```
Use ```bin/console docker``` to see the available commands. To setup a domain from here run ```bin/console docker:setup yourdomain.com```

### MacOS
You will need to install the docker client and set your directory in the client.

### Customize Settings
Nginx settings - Currently after the files are created you will have to go in and update the settings you wish to change.
Docker images/settings - You can go to the docker-compose files and change the images or any of the settings before running docker-compose up.
