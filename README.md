# PHP DataLayer Manager

A helper package that help to generate Google's DataLayer script on pages.

Data can be stored in session, like in a conversion funnel process, and be printed on the last page before being cleared.

## Installation

Require this package with composer.
```shell
composer require Straylightagency/datalayer
```

### Laravel without auto-discovery:

If you do not use auto-discovery, add the ServiceProvider to the providers array in `config/app.php` :

```php
Straylightagency\DataLayer\Laravel\DataLayerServiceProvider::class,
```

Then add this line to your facades in `config/app.php` :
```php
'DataLayer' => Straylightagency\DataLayer\Laravel\DataLayer::class,
```

Finally, add your GTM-ID in `config/datalayer.php`.

```php
<?php
return [
    'gtm_id' => 'GTM-XXXXXXXX'
];
```

## Usage

### Without Laravel

You must create a SessionHandler object. Handler uses session to pass data through pages. Then you can pass the SessionHandler to the DataLayerHandler using the constructor :

```php
use Straylightagency\DataLayer\DataLayerManager;
use Straylightagency\DataLayer\SessionHandler;

$datalayer = new DataLayerManager( 
                new SessionHandler,
                'GTM-XXXXXXXX'
            );
```

A static method is available to create a new instance using the basic session handler

```php
use Straylightagency\DataLayer\DataLayerManager;

$datalayer = DataLayerManager::newUsingBasicSession('GTM-XXXXXXXX');
```

### With Laravel

The package provides by default a Facade for Laravel application. You can call methods directly using the Facade or use the alias instead.
```php
use Straylightagency\DataLayer\Laravel\DataLayer;

DataLayer::with('foo', 'bar');
```

## API documentation

Examples bellow are using Laravel Facade `DataLayer`.

### In your controllers

#### Set one value in the DataLayer

```php
DataLayer::with('foo', 'bar');
```

#### Set an array of data in the DataLayer

```php
DataLayer::withArray([
    'user_name' => 'John Doe',
    'age' => '42',
    'country' => 'Belgium',
]);
```

Both methods can be chained :

```php
DataLayer::with('foo', 'bar')
->withArray([
    'user_name' => 'John Doe',
    'age' => '42',
    'country' => 'Belgium',
])
->with('name', 'value')
```

Do not hesitate to check the prototype of the method to view all possibles options.

### In your views

#### Publish the DataLayer in the view

Just call this method in your app layout before the closing <HEAD> tag.

```php
DataLayer::print();
```

It will print this entire HTML code in your layout :

```html
<script>
    dataLayer = dataLayer || [];
</script>

<script>
    dataLayer.push({foo:'bar',user_name:'John Doe',age:42,country:'Belgium'});
</script>

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-XXXXXXXX');</script>
<!-- End Google Tag Manager -->
```

Do not forget to call `DataLayer::printNoScript()` right after your <BODY> tag :

```php
DataLayer::printNoScript();
```

It will print the following :

```html
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-XXXXXXXX"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
```

You can use named parameters to choose if you do not want to initialise the global JS object, initialise the Google Tag Manager script or to clear data after publish.

```php
DataLayer::print(
    init: false,
    clear: false,
    script: false,
);
```

It will just print this :

```html
<script>
    dataLayer.push({foo:'bar',user_name:'John Doe',age:42,country:'Belgium'});
</script>
```

#### Push an array of data in the DataLayer

```php
DataLayer::pushData([
    'user_name' => 'John Doe',
    'age' => '42',
    'country' => 'Belgium',
], [clear: bool = false]);
```

### Others methods

#### Load the data from session

```php
DataLayer::load();
```

#### Save the data in the session

```php
DataLayer::save();
```

#### Clear the data in the session

```php
DataLayer::clear();
```

#### Get the array data

```php
$data = DataLayer::getData();
$data = DataLayer::data(); # alias of the getData method
```

#### Print the global JS object in the view

```php
echo DataLayer::init();
```

It will print this in the HTML :

```html
<script>
    window.dataLayer = window.dataLayer || [];
</script>
```

#### Print the Google Tag Manager script in the view

The `$gtm_id` parameter is optional. If omitted, it will use the Google ID set in your .env file.

```php
echo DataLayer::script([gtm_id: null|string = null]);
```

```html
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-XXXXXXXX');</script>
<!-- End Google Tag Manager -->
```

Also, do not forget to add the <noscript> tag with noScript :

```php
echo DataLayer::noScript([gtm_id: null|string = null]);
```

... or printNoScript :

```php
DataLayer::printNoScript([gtm_id: null|string = null]);
```

```html
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-XXXXXXXX"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
```

#### Show the content of the DataLayer (debug purpose)

```php
DataLayer::dump();
```

### Requirement

PHP 8.3 or above

## See also

- [Dev Guide](https://developers.google.com/tag-manager/devguide)
- [Quick Start](https://developers.google.com/tag-manager/quickstart)

## Credits

- [Anthony Pauwels](https://github.com/anthonypauwels)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). 