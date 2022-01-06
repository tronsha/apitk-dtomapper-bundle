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
use FOS\RestBundle\Controller\Annotations as Rest;
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

### Turning arrays into Collections

If you return an array in the controller it will be serialized like this. If you do not want to work
with an array or can not work with arrays due to technological constraints (protobuf) you can instruct
the bundle to turn arrays into collection-classes instead.

To turn an array returned from a controller into a collection, implement the MapperCollectionInterface 
additionally to the MapperInterface into your mapper.

Example: 
```php
use Shopping\ApiTKDtoMapperBundle\DtoMapper\MapperCollectionInterface;
use Shopping\ApiTKDtoMapperBundle\DtoMapper\MapperInterface;

class UserV1Mapper implements MapperInterface, MapperCollectionInterface
{
    /**
     * @param Dto\UserV1[] $items
     */
    public function mapCollection(array $items): Dto\UserV1Collection {
        $collection = new UserV1Collection();
        $collection->setItems($items);

        return $collection;
    }

    /**
     * @param Dto\UserV1 $data
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

This will cause the bundle to call `mapCollection` as soon as all items have been mapped via `map`. 
You can initialize your collection class within the `mapCollection` method. The object returned 
here will replace the controller response's content.

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

### Serialized Protobuf view
If you wish to return serialized Protobuf object.

```yaml
//fos_rest.yaml
fos_rest:
    view:
        mime_types:
            proto: ['application/x-protobuf'] # You can specify whatever mime type you want, just map it to "proto".
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
            - ['registerHandler', ['proto', ['@Shopping\ApiTKDtoMapperBundle\Handler\ProtobufViewHandler', 'createResponse']]]
```
