# Laravel 5 Json Transformer Package


[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nilportugues/laravel5-json-transformer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nilportugues/laravel5-json-transformer/?branch=master) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/aa4fca95-5720-4d07-b35f-3d26e9f0e9e4/mini.png?)](https://insight.sensiolabs.com/projects/aa4fca95-5720-4d07-b35f-3d26e9f0e9e4) 
[![Latest Stable Version](https://poser.pugx.org/nilportugues/laravel5-json/v/stable?)](https://packagist.org/packages/nilportugues/laravel5-json) 
[![Total Downloads](https://poser.pugx.org/nilportugues/laravel5-json/downloads?)](https://packagist.org/packages/nilportugues/laravel5-json) 
[![License](https://poser.pugx.org/nilportugues/laravel5-json/license?)](https://packagist.org/packages/nilportugues/laravel5-json) 



## Installation

Use [Composer](https://getcomposer.org) to install the package:

```
$ composer require nilportugues/laravel5-json
```


## Laravel 5 / Lumen Configuration

**Step 1: Add the Service Provider**

Open up `bootstrap/app.php`and add the following lines before the `return $app;` statement:

```php
$app->register('NilPortugues\Laravel5\JsonSerializer\Laravel5JsonSerializerServiceProvider');
$app['config']->set('json_mapping', include('json.php'));
```

**Step 2: Add the mapping**

Create a `json.php` file in `bootstrap/` directory. This file should return an array returning all the class mappings.

An example as follows:


**Step 3: Usage**

For instance, lets say the following object has been fetched from a Repository , lets say `PostRepository` - this being implemented in Eloquent or whatever your flavour is:

```php
use Acme\Domain\Dummy\Post;
use Acme\Domain\Dummy\ValueObject\PostId;
use Acme\Domain\Dummy\User;
use Acme\Domain\Dummy\ValueObject\UserId;
use Acme\Domain\Dummy\Comment;
use Acme\Domain\Dummy\ValueObject\CommentId;

//$postId = 9;
//PostRepository::findById($postId); 

$post = new Post(
  new PostId(9),
  'Hello World',
  'Your first post',
  new User(
      new UserId(1),
      'Post Author'
  ),
  [
      new Comment(
          new CommentId(1000),
          'Have no fear, sers, your king is safe.',
          new User(new UserId(2), 'Barristan Selmy'),
          [
              'created_at' => (new \DateTime('2015/07/18 12:13:00'))->format('c'),
              'accepted_at' => (new \DateTime('2015/07/19 00:00:00'))->format('c'),
          ]
      ),
  ]
);
```

And a series of mappings, placed in `bootstrap/json.php`, that require to use *named routes* so we can use the `route()` helper function:

```php
<?php
//bootstrap/json.php
return [
    [
        'class' => 'Acme\Domain\Dummy\Post',
        'alias' => 'Message',
        'aliased_properties' => [
            'author' => 'author',
            'title' => 'headline',
            'content' => 'body',
        ],
        'hide_properties' => [

        ],
        'id_properties' => [
            'postId',
        ],
        'urls' => [
            'self' => route('get_post'),
            'comments' => route('get_post_comments'),
        ],
    ],
    [
        'class' => 'Acme\Domain\Dummy\ValueObject\PostId',
        'alias' => '',
        'aliased_properties' => [],
        'hide_properties' => [],
        'id_properties' => [
            'postId',
        ],
        'urls' => [
            'self' => 'self' => route('get_post'),
        ],
    ],
    [
        'class' => 'Acme\Domain\Dummy\User',
        'alias' => '',
        'aliased_properties' => [],
        'hide_properties' => [],
        'id_properties' => [
            'userId',
        ],
        'urls' => [
            'self' => route('get_user'),
            'friends' => route('get_user_friends'),
            'comments' => route('get_user_comments'),
        ],
    ],
    [
        'class' => 'Acme\Domain\Dummy\ValueObject\UserId',
        'alias' => '',
        'aliased_properties' => [],
        'hide_properties' => [],
        'id_properties' => [
            'userId',
        ],
        'urls' => [
            'self' => route('get_user'),
            'friends' => route('get_user_friends'),
            'comments' => route('get_user_comments'),
        ],
    ],
    [
        'class' => 'Acme\Domain\Dummy\Comment',
        'alias' => '',
        'aliased_properties' => [],
        'hide_properties' => [],
        'id_properties' => [
            'commentId',
        ],
        'urls' => [
            'self' => route('get_comment'),
        ],
    ],
    [
        'class' => 'Acme\Domain\Dummy\ValueObject\CommentId',
        'alias' => '',
        'aliased_properties' => [],
        'hide_properties' => [],
        'id_properties' => [
            'commentId',
        ],
        'urls' => [
            'self' => route('get_comment'),
        ],
    ],
];

```

The named routes belong to the `app/Http/routes.php`. Here's a sample for the routes provided mapping:

```php
$app->get(
  '/post/{postId}',
  ['as' => 'get_post', 'uses' => 'PostController@getPostAction']
);

$app->get(
  '/post/{postId}/comments',
  ['as' => 'get_post_comments', 'uses' => 'CommentsController@getPostCommentsAction']
);

//...
``` 

All of this set up allows you to easily use the `Serializer` service as follows:

```php
<?php

namespace App\Http\Controllers;

use Acme\Domain\Dummy\PostRepository;
use NilPortugues\Api\Json\Http\Message\Response;
use NilPortugues\Serializer\Serializer;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;


class PostController extends \Laravel\Lumen\Routing\Controller
{
    /**
     * @var PostRepository
     */
    private $postRepository;

    /**
     * @param PostRepository $postRepository
     * @param Serializer $jsonSerializer
     */
    public function __construct(PostRepository $postRepository, Serializer $jsonSerializer)
    {
        $this->postRepository = $postRepository;
        $this->serializer = $jsonSerializer;
    }

    /**
     * @param int $postId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getPostAction($postId)
    {
        $post = $this->postRepository->findById($postId);

        /** @var \NilPortugues\Api\Json\JsonTransformer $transformer */
        $transformer = $this->serializer->getTransformer();
        $transformer->setSelfUrl(route('get_post', ['postId' => $postId]));
        $transformer->setNextUrl(route('get_post', ['postId' => $postId+1]));

        return (new HttpFoundationFactory())->createResponse(new Response($this->serializer->serialize($post)));
    }
}
```

**Output:**

```
HTTP/1.1 200 OK
Cache-Control: private, max-age=0, must-revalidate
Content-type: application/json; charset=utf-8
```

```json

```

#### Response objects

The following PSR-7 Response objects providing the right headers and HTTP status codes are available:

- `NilPortugues\Api\Json\Http\Message\ErrorResponse($json)`
- `NilPortugues\Api\Json\Http\Message\ResourceCreatedResponse($json)`
- `NilPortugues\Api\Json\Http\Message\ResourceDeletedResponse($json)`
- `NilPortugues\Api\Json\Http\Message\ResourceNotFoundResponse($json)`
- `NilPortugues\Api\Json\Http\Message\ResourcePatchErrorResponse($json)`
- `NilPortugues\Api\Json\Http\Message\ResourcePostErrorResponse($json)`
- `NilPortugues\Api\Json\Http\Message\ResourceProcessingResponse($json)`
- `NilPortugues\Api\Json\Http\Message\ResourceUpdatedResponse($json)`
- `NilPortugues\Api\Json\Http\Message\Response($json)`
- `NilPortugues\Api\Json\Http\Message\UnsupportedActionResponse($json)`

Due to the current lack of support for PSR-7 Requests and Responses in Laravel,  `symfony/psr-http-message-bridge` will bridge between the PHP standard and the Response object used by Laravel automatically, as seen in the Controller example code provided.


<br>
## Quality

To run the PHPUnit tests at the command line, go to the tests directory and issue phpunit.

This library attempts to comply with [PSR-1](http://www.php-fig.org/psr/psr-1/), [PSR-2](http://www.php-fig.org/psr/psr-2/), [PSR-4](http://www.php-fig.org/psr/psr-4/) and [PSR-7](http://www.php-fig.org/psr/psr-7/).

If you notice compliance oversights, please send a patch via [Pull Request](https://github.com/nilportugues/laravel5-json-transformer/pulls).


<br>
## Contribute

Contributions to the package are always welcome!

* Report any bugs or issues you find on the [issue tracker](https://github.com/nilportugues/laravel5-json-transformer/issues/new).
* You can grab the source code at the package's [Git repository](https://github.com/nilportugues/laravel5-json-transformer).


<br>
## Support

Get in touch with me using one of the following means:

 - Emailing me at <contact@nilportugues.com>
 - Opening an [Issue](https://github.com/nilportugues/laravel5-json-transformer/issues/new)
 - Using Gitter: [![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/nilportugues/laravel5-json-transformer?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)


<br>
## Authors

* [Nil Portugués Calderó](http://nilportugues.com)
* [The Community Contributors](https://github.com/nilportugues/laravel5-json-transformer/graphs/contributors)


## License
The code base is licensed under the [MIT license](LICENSE).
