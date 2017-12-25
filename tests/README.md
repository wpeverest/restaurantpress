# RestaurantPress Unit Tests

## Initial Setup

1) Install [PHPUnit](http://phpunit.de/) by following their [installation guide](https://phpunit.de/getting-started.html). If you've installed it correctly, this should display the version:

    ```
    $ phpunit --version
    ```

2) Install WordPress and the WP Unit Test lib using the `install.sh` script. Change to the plugin root directory and type:

    ```
    $ tests/bin/install.sh <db-name> <db-user> <db-password> [db-host]
    ```

Sample usage:

    $ tests/bin/install.sh restaurantpress_tests root root

**Important**: The `<db-name>` database will be created if it doesn't exist and all data will be removed during testing.

## Automated Tests

Tests are automatically run with [Travis-CI](https://travis-ci.org/wpeverest/restaurantpress/) for each commit and pull request.

## Code Coverage

Code coverage is available on [Scrutinizer](https://scrutinizer-ci.com/g/wpeverest/restaurantpress) and [Code Climate](https://codeclimate.com/github/wpeverest/restaurantpress) which receives updated data after each Travis build.
