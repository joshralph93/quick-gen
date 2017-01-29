## (Work in progress)

Only currently actively tested with Laravel 5.4. Previous version support to come...

Contribution welcome.

# Installation

Pull in the package using composer

    $ composer require joshralph/quick-gen
    
    
Include the service provider within ```/config/app.php```

```php
QuickGen\Providers\GeneratorServiceProvider::class,
```


# Getting Started

You will need to run the command below to publish the stub files that ship with the package.

```
php artisan vendor:publish --tag=stubs
```

To run the generator run the below command. The example resource name given below should be replaced as you need.

```
php artisan quick-gen:generate cars
```

# Templates

The package ships with a couple of standard templates that you can use to generate basic crud functionality.

To specify which template you wish to use, add the template flag to the command.

```
php artisan quick-gen:generate cars --template=crud-module
```

### crud
This template will generate the following files for you within the standard laravel directory structure:

- Controller
- Model
- Views
    - index.blade.php
    - create.blade.php
    - edit.blade.php
    - partials/list.blade.php
    - partials/form.blade.php
    
    
### crud-module
The only difference with this template, is that your namespaced files (as above) will be placed into a new folder within the ```app``` directory.

e.g. ```app/YourModuleName/Http/Controllers```...


## Custom Templates

Of course you will likely want to create your own template files that are in keeping with your current view structure, and coding style.

To create a new template simply create a new folder within the ```resources/stubs/``` directory. **The folder name should be used when setting the  ```--template``` argument.**

### Stub Syntax
#### Variables

The following variables are made available to stub files (both content and filename):
 
```name``` - The name of the resource as specified in the generate command

```baseNamespace``` - The namespace where the generated files will reside.

...and should be wrapped in the following way:

**Stub Contents** 

```php
namespace <<baseNamespace>>\Http\Controllers\Admin;
```

**Filename**

```
__name__Controller.php.stub
```

#### Filters

You may wish to transform the case and formatting of variables within stub files. These can be used both within the file contents and the filename.

Filters can be added to variables using a ```.``` delimiter.

```blade
@foreach ($<<name.camel.plural>> as $<<name.camel.singular>>)
```

The following filters are available:

```camel``` - convert the variable to camel case.

```studly``` - convert the variable to studly case.

```snake``` - convert the variable to snake case.

```plural``` - convert the variable to plural.

```singular``` - convert the variable to singular.

```ucwords``` - upper case the first letter of each word in the variable (see ```words```).

```words``` - convert the variable to space delimited words