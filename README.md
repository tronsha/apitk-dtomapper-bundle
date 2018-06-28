# rfc1-bundle

## Overview
This bundle adds versioned DTO support for RESTful API's.

## Installation
Add this repository to your `composer.json` until it is available at packagist:
```
{
    "repositories": [{
            "type": "vcs",
            "url": "git@github.com:bywulf/rfc1-bundle.git"
        }
    ]
}
```

After that, install the package via composer:
```
composer install ofeige/rfc1-bundle:dev-master
```

## Usage

### Setup
Add this to your services.yaml so the bundle can automatically load and use the mapper services:
```
    App\DtoMapper\:
        resource: '../src/DtoMapper'
        public: true
```

### Writing Mappers

Create a mapper class in the folder `src/DtoMapper` (or whichever you configured) which implements the `MapperInterface` and transforms incoming data into a single DTO:
```
<?php

namespace App\DtoMapper;

use App\Entity\User;

use App\Dto as Dto;
use Ofeige\Rfc1Bundle\DtoMapper\MapperInterface;

class UserV1Mapper implements MapperInterface
{
    /**
     * @param User $data
     * @return Dto\UserV1
     */
    public function map($data)
    {
        $userDto = new Dto\UserV1();
        $userDto->setId($data->getId())
            ->setUsername($data->getUsername())
            ->setEmail($data->getEmail());

        return $userDto;
    }
}
```

In your controller replace the `@Rest\View()` annotation with the corresponding `@Rfc1\View()` mentioning the mapper to use:
```
    /**
     * @Rest\Get("/v1/users")
     * @Rfc1\View(dtoMapper="App\DtoMapper\UserV1Mapper")
     *
     * @param EntityManagerInterface $entityManager
     * @return User[]
     */
    public function getUsersV1(EntityManagerInterface $entityManager)
    {
        $userRepository = $entityManager->getRepository(User::class);

        $users = $userRepository->findAll();

        return $users;
    }
```

The bundle now automatically transform whatever you return in the action with the help of the given mapper into an DTO. When you return an array of data in your controller, the mapper will be called on every single element. You don't have to worry about that.

### Serialized DTO view
If you wish to return the DTOs in a `serialize($dto)` manner instead of json, implement the available dto view handler.

```
//fos_rest.yaml
fos_rest:
    view:
        mime_types:
            dto: ['application/vnd.demo.dto'] # You can specify whatever mime type you want, just map it to "dto".
    service:
        view_handler: app.view_handler
```
```
//services.yaml
services:
    app.view_handler:
        autowire: true
        autoconfigure: false
        public: false
        parent: fos_rest.view_handler.default
        calls:
            - ['registerHandler', ['dto', ['@Ofeige\Rfc1Bundle\Handler\PhpViewHandler', 'createResponse']]]
```

When calling the API with the `Accept: application/vnd.demo.dto` header, you will get the DTO as an unserializable string.
