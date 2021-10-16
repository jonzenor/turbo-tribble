# Back-End Developer Test

## Overview
Your client has tasked you with making a RESTful JSON API to support the ultimate Moviegoers Guide suite of applications.
They are asking for an API that:

* Lists movies
  * The user should be able to search the movies by title
  * The user should be able to filter the movies by rating
  * The user should be able to filter the movies by category
* Gets Details about a specific movie
* Creates a movie

We have provided a mysql database of movie info for you.
We have also provided a starter PHP application using the SLIM framework.

## Things to have

* Unit and/or Integration tests
* Separation of concerns (We've included a couple directories already to split
  up business logic and data access)
* Security mitigations

## Keep in mind the following questions

* What design patterns did you use?
* What ways could you structure the code to make it easy to understand and maintain?
* What other considerations and tradeoffs did you make when building the application?

## Requirements

### Docker
You will need Docker installed.
Here are the instructions to install Docker for ([Mac](https://docs.docker.com/docker-for-mac/install/)) or ([Windows](https://docs.docker.com/docker-for-windows/install/)). For older systems that do not meet the docker requirements, use the [Docker Toolbox](https://docs.docker.com/toolbox/overview) installation instructions.

_NOTE if using Docker Toolbox_: To verify you may have to use the ip address of the docker machine. In the _verify installation_ section you will want to use the ip address returned by `docker-machine ip` instead of localhost.

### Composer

You will also need to install Composer if you plan to use our starter PHP application.
Here are the instructions to install [Composer](https://getcomposer.org/download)

## Running The API

```
cd developer_test_server/src
composer install
cd ../
docker-compose up
```

In a browser navigate to http://localhost:8080/movies. You should see a json
array of movies returned

### Database Connection Information

__host__: 127.0.0.1<br />
__username__: sakila<br />
__password__: sakila<br />
__database__: sakila<br />
__port__: 3306

## Submitting Your App
When you have completed your app, please post it in a public repository and send us a link - GitHub, GitLab, BitBucket etc.
