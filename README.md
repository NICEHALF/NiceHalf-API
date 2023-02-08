# NiceHalf-API

## Introduction

NiceHalf API is a RESTful API that allows you Authentication, Authorization, and CRUD operations on the NiceHalf database.

## Getting Started

### Prerequisites

- [PHP](https://www.php.net/downloads.php) >= 8.0.0
- [Composer](https://getcomposer.org/download/)

### Installation

1. run this command to install the dependencies

```bash
composer require nicehalf/api
```

2. create a `index.php` file in the root directory of your project and add the following code

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use NiceHalf\Api\NiceHalf;

$api = new NiceHalf();
```

3. run this command to start the server

```bash
php -S localhost:8000
```

## Usage

### Authentication

#### Login

```bash
$api->auth();
```

#### Logout

```bash
$api->logout();
```

### Users

#### Get User Data

```bash
$api->getUser();
```
