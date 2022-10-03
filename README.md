# Description

VeryFastApi is a project created to build apis in php as fast as possible,
it has everything the author of the project needs to run an API and to fetch and insert data yo a MySQL Database.

Because it is intended to donwload and deploy as fast as possible, there are many features not included intenitionally, like connections to other databases, or dependency usage via Composer.

## Requirements

* PHP last version
* Driver for mysql installed
* Extension for mod_rewrite.c installed
* Apache with AllowOverride activated


## Installation

You only need to download the project in a folder that can be found by your apache.

in envorionments/ folder, you have and example of the parameters of the database, please coy that file
inte a new file called environment.php and fill the empty fields

## Usage

Ther will be a manual of how the project works, for now, this document explains the easy ways to create entities ans controllers

### For creating entities

```bash

php ./artisan/create/createEntity.php -e {entityName}

```

### For creating controllers

```bash

php ./artisan/create/createController.php -c {controllerName}

```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)