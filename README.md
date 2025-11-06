# Slipstream technical test on Laravel 11

## Included a docker installation to easily boot up the project

## How to Install

- In the `project/`, rename `.env.example` to `.env`

- Move to the root folder and run `cd projects && docker-compose up -d`

- Go inside the docker container: `docker exec -it app sh`

- Once inside, run `composer install && php artisan migrate:fresh && php artisan db:seed`

- Open your browser and go to [http://localhost:8000](http://localhost:8000)

## What is in here?

- Customer and Contacts with summary tables and detail forms

## What is not in here?

- Authentication. Left out authentication for demonstration purposes.