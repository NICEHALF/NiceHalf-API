# NiceHalf-API V1.1.1 - 2023-02-10

## Introduction

NiceHalf API is a RESTful API that allows you Authentication, Authorization, and CRUD operations on the NiceHalf database.

## Getting Started

### Prerequisites

- [PHP](https://www.php.net/downloads.php) >= 8.0.0
- [Composer](https://getcomposer.org/download/)
- [Laravel](https://laravel.com/docs/9.x/installation) >= 9.0.0 / Only if you want to use the AuthAPI class (optional)

### Installation

1. run this command to install the dependencies

```bash
composer require nicehalf/api
```

2. create a `index.php` file in the root directory of your project and add the following code

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use NiceHalf\Api\AuthAPI;
use NiceHalf\Api\CrudAPI;
use NiceHalf\Api\LicenseAPI;

$auth = new AuthAPI();
$license = new LicenseAPI();
$crud = new CrudAPI();
```

3. run the following command to start the server

```bash
php -S localhost:8000
```

## Usage

### AuthAPI

#### Login

```php
$auth->auth();
```

#### Logout

```php
$auth->logout();
```

#### Get User

```php
$auth->getUser();
```

### LicenseAPI

#### Check API Connection

```php
$license->check_connection();
```

#### Activate License

```php
$license->activate_license($license_key, $client_username);
```

#### Verify License

```php
$license->verify_license($time_based_check = false, $license_key = null, $client_username = null);
```

#### Deactivate License

```php
$license->deactivate_license($license_key, $client_username);
```

#### Check For Updates

```php
$license->check_update();
```

### CrudAPI

#### Get Rows

```php
$crud->get_rows($table_name, $where = [], $order_by = null, $limit = null, $offset = null);
```

#### Get Row

```php
$crud->get_row($table_name, $where = []);
```

#### Insert Row

```php
$crud->insert($table_name, $data);
```

#### Update Row

```php
$crud->update($table_name, $data, $where = []);
```

#### Delete Row

```php
$crud->delete($table_name, $where = []);
```

## License

This project is licensed under the MIT License - You can do whatever you want with it.

## Acknowledgments

- [PHP](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [Laravel](https://laravel.com/)

## Copyright

NiceHalf API is © 2023 NiceHalf. All rights reserved.

## Contact

- [Website](https://nicehalf.com/)
- [Email](mailto:contact@nicehalf.com)
- [Instagram](https://www.instagram.com/bablil_ayoub/)