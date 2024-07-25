# README

A simple PHP / MySQL app for logging blood pressure for multiple users. 

* User may add multiple aliases (e.g. your parents or other members of your family)
* Readings Today
* Daily Average (Last 30 days)
* Weekly Average (1 year)

# Usage

You can try it or use it for free on https://bp.10kilobyte.com

# Install

Works on:  `PHP >= 8.1`

Clone the source code: 

    git clone https://github.com/diversen/blood-pressure-log
    cd blood-pressure-app

Install composer packages:

    composer install

Create a `config-locale` dir that will override settings in `config`.

    mkdir config-locale

## Load MySQL DB

Create a database and change the settings in `config-locale/DB.php`

    cp config/DB.php config-locale/DB.php

Check if you can connect to the server and then create the database:

    ./cli.sh db --server-connect
    create database bp;
    exit

You can look at the other `config/` files, but you don't need to change these in order to run the system local now: 

Load the SQL files found in `migration` into a database. 

    ./cli.sh migrate --up

## Run

Run the built-in PHP server:

    ./serv.sh