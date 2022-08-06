# Blog voyage (portfolio)

This repository contains a travel blog that is part of my portfolio.
The website is build with the cms Sulu in its version 2.2.

## Limitations

The docker environnement who contains the website can only work on localhost with ssl certificate.
If you want to use another domain you need to change the virtual host in the apache folder and generate a new ssl certificate corresponding to your new domain by your own way.

I'm a french freelancer who target mainly client in france so the website will be only in french.

## Requirements

You need to have Docker with compose.

## Installation

### Clone the project

Clone the project in the directory you want to execute it.

```sh
cd /path/to/myfolder
git clone https://github.com/yanb94/voyage.git
```

### Create an environnement file for the docker environnement

Create an environnent file for docker who contains the password for the database and the possibility to enable or not opcache revalidation of file base on timestamp.

```env
#.env
DB_PASSWORD=<your_database_password>
OPCACHE_REVALIDATE=1 #Recommended to leave on 1 for the first utilisation or if you plane to change the content
```

### Create an environnement file for the website

Create a local environnement file for the website who contains:

-   The environnement to execute the website (prod recommended)
-   The app secret for symfony (You can generate one [here](https://coderstoolbox.online/toolbox/generate-symfony-secret))
-   The url of the database
-   The mailer url (See symfony documentation for [more informations](https://symfony.com/doc/current/mailer.html))
-   The trusted proxies
-   The admin email

```env
#web/.env.local
APP_ENV=prod
APP_SECRET=<your_app_secret>
DATABASE_URL=mysql://root:<your_database_password>@mysql/voyage
MAILER_URL=<your_mailer_url>
TRUSTED_PROXIES=127.0.0.1,REMOTE_ADDR
SULU_ADMIN_EMAIL=<your_admin_email>
```

### Start docker container

```sh
cd /path/to/myfolder
docker-compose up
```

### Open a new terminal in php-fpm docker container

```
cd /path/to/myfolder
docker-compose exec php-fpm bash
```

You will have to type all the following installation command in this opened terminal.

### Install the composer dependencies

```sh
composer install
```

### Install the javacript dependencies

```sh
yarn install --force
```

### Download the languages assets

```sh
php bin/console sulu:admin:download-language
```

### Setup the database

```sh
php bin/adminconsole sulu:build dev
```

### Init the adminstration interface assets

```sh
php bin/adminconsole sulu:admin:update-build
```

### Init the other assets

```sh
yarn encore prod
```

### Load the fixtures

```sh
php bin/console doctrine:fixtures:load --append --env initfixtures
php bin/console sulu:document:fixtures:load --append --env initfixtures
```

## Usage

### Add a new article

Firstly you have to logged in the backoffice. For doing this you can use the default admin user create by the installation.

```
username: admin
password: admin
```

#### Add illustration image of the article

-   Click on the blue button on the top left to open the navbar
-   Click on "Media" to manage the different immage
-   Click on the collection "Articles"
-   Now click on the button "Upload" on the top and add the image you want to illustrate the article

#### Create the article

-   Return to the navbar an click on "Webspaces"
-   You should see a three columns, when you pass your cursor on the third one you should see two button on top, click on the button with a plus icon
-   On the top you should see a bar with four buttons click on "Default" and select "Article"
-   Now type the title, choose the image you had previously and add the content of your article
-   On the top click on "Save" and select "Save as draft"
-   Now select "SEO" tab and type the title and the description of the article. After save as draft
-   Now select "Excerpt & Taxonomies" tab and type the title and the description of the article and choose the same image in the field "Images"
-   Now on top select "Save and publish"

## Troubleshooting

In the first start of the docker environnement the mysql container can take a long time to completely start.
This can create an issue where you can see during the installation the error message "Connection Refused".
The solution to this issue is to wait until the complete initialization of the container data and retry.
You can see when the container is ready in the docker log associate when you see this line:

```
[System] [MY-010931] [Server] /usr/sbin/mysqld: ready for connections. Version: '8.0.29'  socket: '/var/run/mysqld/mysqld.sock'  port: 3306  MySQL Community Server - GPL
```
