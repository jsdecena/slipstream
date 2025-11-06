# Slipstream technical test on Laravel 11

## Included a docker installation to easily boot up the project

## Requirements
- Docker
- NodeJs on host machine

## How to Install

- Go to the project folder and run `docker-compose up -d`

- Go inside the docker container: `docker exec -it app sh`

- Once inside the container, run `composer install && php artisan migrate`

- Exit the container, and on host maching build frontend: `cd project && npm i && run build`

- Open your browser and go to [http://localhost:8000](http://localhost:8000)

## What is in here?

- Customer and Contacts with summary tables and detail forms
- Included the .env in the commit for demo purposes

## Screenshots

[Screenshot 1](https://i.imgur.com/WfWlS3X)

[Screenshot 2](https://i.imgur.com/nuOrM0A)

[Screenshot 3](https://i.imgur.com/SaTKFlR.png)

[Screenshot 4](https://i.imgur.com/lrP0S85.png)