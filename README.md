# Laravel 5 Json Transformer Package


[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nilportugues/laravel5-json-transformer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nilportugues/laravel5-json-transformer/?branch=master) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/a3933b7b-13ec-44cf-aef9-0dce25e44ef5/mini.png)](https://insight.sensiolabs.com/projects/a3933b7b-13ec-44cf-aef9-0dce25e44ef5) 
[![Latest Stable Version](https://poser.pugx.org/nilportugues/laravel5-json/v/stable)](https://packagist.org/packages/nilportugues/laravel5-json) 
[![Total Downloads](https://poser.pugx.org/nilportugues/laravel5-json/downloads)](https://packagist.org/packages/nilportugues/laravel5-json) 
[![License](https://poser.pugx.org/nilportugues/laravel5-json/license?)](https://packagist.org/packages/nilportugues/laravel5-json) 



## Installation

Use [Composer](https://getcomposer.org) to install the package:

```
$ composer require nilportugues/laravel5-json
```


## Laravel 5 / Lumen Configuration

**Step 1: Add the Service Provider**

**Laravel**

Open up `config/app.php` and add the following line under `providers` array:

```php
'providers' => [

    //...
    \NilPortugues\Laravel5\JsonSerializer\Laravel5JsonSerializerServiceProvider::class,
],
```

**Lumen**

Open up `bootstrap/app.php`and add the following lines before the `return $app;` statement:

```php
$app->register(\NilPortugues\Laravel5\JsonSerializer\Laravel5JsonSerializerServiceProvider::class);
$app->configure('json');
```

Also, enable Facades by uncommenting:

```php
$app->withFacades();
```

**Step 2: Add the mapping**

Create a `json.php` file in `config/` directory. This file should return an array returning all the class mappings.

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

And a series of mappings, placed in `config/json.php`, that require to use *named routes* so we can use the `route()` helper function:

```php
<?php
//config/json.php
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
            'self' => 'get_post',
            'comments' => 'get_post_comments',
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
            'self' => 'self' => 'get_post',
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
            'friends' => 'get_user_friends',
            'comments' => 'get_user_comments',
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
            'friends' => 'get_user_friends',
            'comments' => 'get_user_comments',
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
            'self' => 'get_comment',
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
            'self' => 'get_comment',
        ],
    ],
];

```


The named routes belong to the `app/Http/routes.php`. Here's a sample for the routes provided mapping:

**Laravel**

```php
Route::get(
  '/post/{postId}',
  ['as' => 'get_post', 'uses' => 'PostController@getPostAction']
);

Route::get(
  '/post/{postId}/comments',
  ['as' => 'get_post_comments', 'uses' => 'CommentsController@getPostCommentsAction']
);

//...
```

**Lumen**

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

All of this set up allows you to easily use the `JsonSerializer` service as follows:

```php
<?php

namespace App\Http\Controllers;

use Acme\Domain\Dummy\PostRepository;
use NilPortugues\Laravel5\JsonSerializer\JsonSerializer;
use NilPortugues\Laravel5\JsonSerializer\JsonResponseTrait;

class PostController extends \Laravel\Lumen\Routing\Controller
{
    use JsonResponseTrait;

    /**
     * @var PostRepository
     */
    private $postRepository;

    /**
     * @var JsonSerializer
     */
    private $serializer;

    /**
     * @param PostRepository $postRepository
     * @param JsonSerializer $jsonSerializer
     */
    public function __construct(PostRepository $postRepository, JsonSerializer $jsonSerializer)
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

        return $this->response($this->serializer->serialize($post));
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
{
    "post_id": 9,
    "headline": "Hello World",
    "body": "Your first post",
    "author": {
        "user_id": 1,
        "name": "Post Author"
    },
    "comments": [
        {
            "comment_id": 1000,
            "dates": {
                "created_at": "2015-07-18T12:13:00+00:00",
                "accepted_at": "2015-07-19T00:00:00+00:00"
            },
            "comment": "Have no fear, sers, your king is safe.",
            "user": {
                "user_id": 2,
                "name": "Barristan Selmy"
            }
        }
    ],
    "links": {
        "self": {
            "href": "http://localhost:8000/post/9"
        },
        "next": {
            "href": "http://localhost:8000/post/10"
        },
        "comments": {
            "href": "http://localhost:8000/post/9/comments"
        }
    }
}
```

#### Response objects (JsonResponseTrait)

The following `JsonResponseTrait` methods are provided to return the right headers and HTTP status codes are available:

```php
    private function errorResponse($json);
    private function resourceCreatedResponse($json);
    private function resourceDeletedResponse($json);
    private function resourceNotFoundResponse($json);
    private function resourcePatchErrorResponse($json);
    private function resourcePostErrorResponse($json);
    private function resourceProcessingResponse($json);
    private function resourceUpdatedResponse($json);
    private function response($json);
    private function unsupportedActionResponse($json);
```


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
