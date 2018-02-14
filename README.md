# MercadoLivre's Official PHP SDK

Still in development.

For moderators: the idea is to give support for resources such as Category, User, Item, etc, removing from developers the development of basic things involving the ML's API like validating/dealing with requests, category validation, items validation(pictures, listing types, buying modes, shipping modes, etc), getting data(category, user info, item info, etc) and more.

Quick comments:

- Uses `GuzzleHttp` for requests, its safer and better
- Every Resource(Category.php, User.php) inherits `Resource.php`, which gives some cool methods like `faker()` for generating a generic `Faker` instance, `validate()` for validating the current object, `load()` and `fill()` for dealing with the current object properties, etc
- Every Resource receives a `MeliRequestInterface`(explained below) for making requests and a array for filling the current object
- Using `Meli` as a class for making requests is optional, in case of the developer already using other custom class for requests involving DB and etc, the developer just needs to pass a class that implements the `MeliRequestInterface`.
