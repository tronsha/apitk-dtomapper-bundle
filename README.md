# apitk-dtomapper-bundle - DTO handling

## Overview
This bundle adds versioned DTO support for RESTful API's.

## Installation
Install the package via composer:
```bash
composer require check24/apitk-dtomapper-bundle
```

## Usage

### Setup
Add this to your services.yaml so the bundle can automatically load and use the mapper services:
```yaml
services:
    App\DtoMapper\:
        resource: '../src/DtoMapper'
        public: true
```

### Writing Mappers

Create a mapper class in the folder `src/DtoMapper` (or whichever you configured) which implements the
`MapperInterface` and transforms incoming data into a single DTO:
```php
use Shopping\ApiTKDtoMapperBundle\DtoMapper\MapperInterface;

class UserV1Mapper implements MapperInterface
{
    /**
     * @param User $data
     * @return Dto\UserV1
     */
    public function map($data): Dto\UserV1
    {
        $userDto = new Dto\UserV1();
        $userDto->setId($data->getId())
            ->setUsername($data->getUsername())
            ->setEmail($data->getEmail());

        return $userDto;
    }
}
```

In your controller replace the `@Rest\View()` annotation with the corresponding `@Dto\View()` mentioning
the mapper to use:
```php
use Shopping\ApiTKDtoMapperBundle\Annotation as DtoMapper;

/**
 * @Rest\Get("/v1/users")
 * @DtoMapper\View(dtoMapper="App\DtoMapper\UserV1Mapper")
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

The bundle now automatically transform whatever you return in the action with the help of the given 
mapper into an DTO. When you return an array of data in your controller, the mapper will be called on 
every single element. You don't have to worry about that.

You can throw a `UnmappableException` if you want to skip some elements of the array.

Also the bundle auto generates a swagger response with code 200 and the corresponding DTO scheme 
(respectively an array of DTOs), so you don't have to add the redundant `@SWG\Response()`. For this 
to work, just take care that your Mapper has a correct return typehint (f.e. 
`public function map($data): FoobarDto`) and that your controller action has a return annotation, 
which states if an array or object is returned (f.e. `* @return Foobar[]`). You can still overwrite 
this by your own `@SWG\Response()` annotation.

### Serialized DTO view
If you wish to return the DTOs in a `serialize($dto)` manner instead of json, implement the available 
dto view handler.

```yaml
//fos_rest.yaml
fos_rest:
    view:
        mime_types:
            dto: ['application/vnd.dto'] # You can specify whatever mime type you want, just map it to "dto".
    service:
        view_handler: app.view_handler
    exception:
        serializer_error_renderer: true
```
```yaml
//services.yaml
services:
    app.view_handler:
        autowire: true
        autoconfigure: false
        public: false
        parent: fos_rest.view_handler.default
        calls:
            - ['registerHandler', ['dto', ['@Shopping\ApiTKDtoMapperBundle\Handler\PhpViewHandler', 'createResponse']]]
```

When calling the API with the `Accept: application/vnd.dto` header, you will get the DTO as an 
unserializable string.

Exceptions will also be serialized. Stack Traces, filenames, line numbers and previous exceptions will be omitted
when `kernel.debug` is set to `false` (= in productive environments) to avoid leaking potentially sensitive information.
