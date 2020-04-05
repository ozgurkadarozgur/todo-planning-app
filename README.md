**Todo Planning Application**

This application needs todo provider to plan todo list.

You have to create a provider class and it has to implements IProvider interface.
Provider classes should be inserted to $provider_arr variable(in ProviderService class).
```
class ProviderService
{

    private $provider_arr = [
        Provider1::class,
        Provider2::class,
        ...
    ];
    .
    .
    .
```

Run the command below to generate migrations.
```
$ php bin/console doctrine:migrations:migrate
```

Run the command below to fetch data from providers. 
```
$ php bin/console app:fetch-todo-data
``` 

After this steps, you can run this application by running the command below.
```
$ symfony server:start
```

Symfony serves application on <a href="http://127.0.0.1:8000">http://127.0.0.1:8000</a> url.

To view weekly todo plan click <a href="http://127.0.0.1:8000/todo">http://127.0.0.1:8000/todo</a>