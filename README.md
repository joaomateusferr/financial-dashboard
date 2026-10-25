# Financial Dashboard

A simple project that aims to create a dashboard to help automate finances and investments.

## Preparing the development environment

### Installing python dependencies in ubuntu
```shell
sudo apt-get update && apt-get -y install python3 python3-pip && python3 -m pip install requests yfinance --break-system-packages
```

### Update yfinance
```shell
sudo python3 -m pip install  yfinance --upgrade --break-system-packages
```

### Installing PHP dependencies in ubuntu

```shell
sudo apt-get update && sudo apt-get install -y php composer php-curl php-mysql php-mbstring php-zip php-dom php-gd php-redis
```

### Install composer dependencies

```shell
composer install
```
### Create all necessary containers and data structures

```shell
composer run-script --timeout=120 prepare-environment-dev
```

### Start the development server

```shell
PHP_CLI_SERVER_WORKERS=2 php -S localhost:8888 -t ./public/
```

### Run all tests

Running this command will **delete all** local databases to initialize the test environment.

```shell
composer run-script --timeout=120 run-all-tests
```

### Clear all application containers

```shell
composer run-script --timeout=60 environment-clear
```