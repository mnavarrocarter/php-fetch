PHP Fetch
=========

A simple, type-safe, zero dependency port of the javascript `fetch` WebApi for PHP.


<h3 align="center">
    <img style="alignment: center" src="https://media0.giphy.com/media/xlYKItjhiDsY/giphy.gif?cid=ecf05e474io66b5jt2mrufubg3otjzq26qgtqd0cb0w71fiu&rid=giphy.gif"/>
</h3>

> NOTE: This library is in `< 1.0.0` version and as per the Semantic Versioning Spec, breaking
> changes might occur in minor releases before reaching `1.0.0`. Specify your constraints 
> carefully.

## Installation

```bash
composer require mnavarrocarter/php-fetch
```

## Basic Usage

A simple `GET` request can be done just calling fetch passing the url:

```php
<?php

use function MNC\Http\fetch;

$response = fetch('https://mnavarro.dev');

// Emit the response to stdout
while (($chunk = $response->body()->read()) !== null) {
    echo $chunk;
}
```

## Advanced Usage

Like in the browser's `fetch` implementation, you can pass a map of options
as a second argument:

```php
<?php

use function MNC\Http\buffer;
use function MNC\Http\fetch;

$response = fetch('https://some-domain.com/some-form', [
    'method' => 'POST',
    'headers' => [
        'Content-Type' => 'application/json',
        'User-Agent' => 'PHP Fetch'
    ],
    'body' => json_encode(['data' => 'value'])
]);

// Emit the response to stdout
while (($chunk = $response->body()->read()) !== null) {
    echo $chunk;
}
```

At the moment, the only options supported are:

- `method`: Sets the request method
- `body`: The request body. It can be a `resource`, a `string` or `null`.
- `headers`: An associative array of header names and values.

### Getting response information

You can get all the information you need from the response using
the available api.

```php
<?php

use function MNC\Http\fetch;

$response = fetch('https://mnavarro.dev');

echo $response->status()->protocolVersion();  // 1.1
echo $response->status()->code();   // 200
echo $response->status()->reasonPhrase(); // OK
echo $response->headers()->has('content-type'); // true
echo $response->headers()->contains('content-type', 'html'); // true
echo $response->headers()->get('content-type'); // text/html;charset=utf-8
echo $response->body()->read(''); // Outputs some bytes from the response body
```

### Exception Handling

A call to `fetch` can throw two exceptions, which are properly documented.

A `MNC\Http\SocketError` is thrown when a TCP connection cannot be established
with the server. Common scenarios where this may happen include:

- The server is down
- The domain name could not be resolved to an ip address (dns)
- The server took too long to produce a response (timeout)
- The SSL handshake failed (non trusted certificate)

A `MNC\Http\ProtocolError` occurs when a connection could be established, and a
response was produced by the server, but this response was an error according to
the HTTP protocol specification (a status code in the 400 or 500 range). This exception
contains the `MNC\Http\Response` object that the server produced.

The distinction between these two kind of errors is really important since
you most likely will be reacting in different ways to each one of them.

### Body Buffering

When you call the `MNC\Http\Response::body()` method you get an instance of
`MNC\Http\Io\Reader`, which is a very simple interface inspired in golang's
`io.Reader`. This interface allows you to read a chunk of bytes until you reach
`EOF` in the data source.

Often times, you don't want to read byte per byte, but get the whole contents 
of the body as a string at once. This library provides the `buffer` function
as a convenience for that:

```php
<?php

use function MNC\Http\buffer;
use function MNC\Http\fetch;

$response = fetch('https://mnavarro.dev');

echo buffer($response->body()); // Buffers all the contents in memory and emits them.
````

Buffering is a very good convenience, but it needs to be used with care, since it could
increase your memory usage up to the size of the file your are fetching. Keep in mind that
and use the reader when you are fetching big files.

### Handling Common Encodings

Some libraries make their response implementations aware of the content type of a body in a
very unreliable way.

For example, Symfony's HTTP client response object contains a `toArray()` method that
returns an array if the body of the response is a json.

Apart from being a leaky abstraction, it is not a good one, since it can fail miserably
in content types like `text/plain`. However, there is big gain in user experience
when we provide helpers like these in our apis.

This library provides an approach a bit more safe. If the response headers contain the
`application/json` content type, the `MNC\Http\Io\Reader` object of the body is internally
decorated with a `MNC\Http\Encoding\Json` object. This object implements both the
`Reader` and `JsonDecoder` interfaces. Checking for the former is the safest way of
handling json payloads:

```php
<?php

use MNC\Http\Encoding\JsonDecoder;
use function MNC\Http\fetch;

$response = fetch('https://api.github.com/users/mnavarrocarter', [
    'headers' => [
        'User-Agent' => 'PHP Fetch 1.0' // Github api requires user agent
    ]
]);

$body = $response->body();

if ($body instanceof JsonDecoder) {
    var_dump($body->decode()); // Dumps the json as an array
} else {
    // The response body is not json encoded
}
```

This makes the code more maintainable and evolvable, as we can support more encodings 
in the future, like `csv` or `xml` without harming the base api and making more assumptions
about our content types than we should.

This way of doing things (small interfaces that encourage composability) is another principle
that we have taken from golang's idiosyncrasies.

### Working with Standard Headers

HTTP is a very generic protocol in terms of structure. An HTTP response really is just
metadata in the form of key value pairs (headers) and the contents of that response itself.

However, there is a set of standardized headers across multiple RFC's that is not good to
ignore. They are not part of the HTTP protocol specification, but they are so widespread
and commonly used that a good implementation of the protocol should acknowledge them.

This library keeps the protocol pure but provides better apis over standard headers by
using the `MNC\Http\StandardHeaders` class.

The `MNC\Http\Response::headers()` method returns an instance of `MNC\Http\Headers`.
This object is just a bag of string keys and string values. Names when fetching headers
should be provided by you, and as per protocol spec they are case-insensitive.

By using the `MNC\Http\StandardHeaders` class, you can decorate a `MNC\Http\Headers`
object to provide an api over some standardized and useful headers.

```php
<?php

use MNC\Http\StandardHeaders;
use function MNC\Http\fetch;

$response = fetch('https://mnavarro.dev');

$stdHeaders = StandardHeaders::from($response);
$lastModified = $stdHeaders->getLastModified()->diff(new DateTimeImmutable(), true)->h;
echo sprintf('This html content was last modified %s hours ago...', $lastModified) . PHP_EOL;
```

You can use these headers information to handle caching or avoiding reading the whole
stream body if is not necessary.

Since these standards headers may not be present in a certain responses, they
all can return `null`.
 
### Function Composition

As a function, `fetch` can be really verbose if you do not use it with the
appropriate patterns. One of these appropriate patterns is composition.

For example, you can compose functions the same way you can
compose objects. Wrapping `fetch` in anonymous functions that define
some common default options is, in fact, the recommended way of using `fetch`, not
only in this library but also in the browser one.

For example, the following code defines a function that takes a token as an
argument and then returns another function that calls fetch with a simplified
api, using the token internally.

```php
<?php

use MNC\Http\Encoding\JsonDecoder;
use function MNC\Http\fetch;

$authenticate = static function (string $token) {
    return static function (string $method, string $path, array $contents = null) use ($token): ?array {
        $url = 'https://my-api-service.com' . $path;
        $response = fetch($url, [
            'method' => $method,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ],
            'body' => is_array($contents) ? json_encode($contents) : ''
        ]);

        $body = $response->body();
        if ($body instanceof JsonDecoder) {
            return $body->decode();
        }
        return null;
    };
};

$client = $authenticate('your-api-token');

$ordersArray = $client('GET', '/orders');
$createdOrderArray = $client('POST', '/orders', ['id' => '1234556']);
```

Note how the `$client` function does not expose any details of how fetch works
and reduces the interaction with the client classes to PHP primitive types
only. Of course, this example lacks exception handling, but the idea is the same.

You can pass that `$client` variable anywhere in your application and you won't
be tying your code to this library, but to a callable with the same signature.

### Dependency Injection

Following the previous example, we do not recommend you call fetch directly in
your code. At least, not if you are too worried about coupling with a specific
HTTP client library that you might replace in the future.

A common pattern I personally use, is that I create an interface for the
api client that I need to use.

```php
<?php

use MNC\Http\Encoding\JsonDecoder;
use function MNC\Http\fetch;

// We start with an interface, a well defined contract.
interface ApiClient
{
    public function getOrder(string $id): array;

    public function createOrder(string $id): array;

    public function deleteOrder(string $id): void;
}

// Then, we can have an implementation that uses this library.
final class FetchApiClient implements ApiClient
{
    /**
     * @var callable
     */
    private $client;

    /**
     * @param string $token
     * @return FetchApiClient
     */
    public static function authenticate(string $token): FetchApiClient
    {
        $client = static function (string $method, string $path, array $contents = null) use ($token): ?array {
            $url = 'https://my-api-service.com' . $path;
            $response = fetch($url, [
                'method' => $method,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token
                ],
                'body' => is_array($contents) ? json_encode($contents) : ''
            ]);

            $body = $response->body();
            if ($body instanceof JsonDecoder) {
                return $body->decode();
            }
            return null;
        };
        return new self($client);
    }

    /**
     * FetchApiClient constructor.
     * @param callable $client
     */
    public function __construct(callable $client)
    {
        $this->client = $client;
    }

    public function getOrder(string $id): array
    {
        return ($this->client)('GET', '/orders/'.$id);
    }

    public function createOrder(string $id): array
    {
        return ($this->client)('POST', '/orders', [
            'id' => $id
        ]);
    }

    public function deleteOrder(string $id): void
    {
        ($this->client)('DELETE', '/orders/'.$id);
    }
}
```

You can use the interface in all the services that depend on this connecting
to the api service it implements.

## Why another HTTP Client?

Maybe you are wondering "Is another HTTP client for PHP necessary"? I think this one is.

Before building it, I did an honest review of the currently available options and enumerated
the things that, for me, were lacking in them. I also listed the desired features I would have
loved to have by looking at other languages and implementations.

At the end, I came up with a list of 4 principles/reasons for building this client that,
**considered in combination**, are only met in this client.

### You don't need PSR-18 HTTP client in your apps

I can only have words of praise for the PHP-FIG and all the standards they have produced
for PHP. I'm personally a big fan of all things PSR-7 and I'm always hoping the community
sees it's benefits and starts moving to them. 

But, when I'm developing my own application and I just need to do a simple HTTP request, 
I would try to avoid at all costs the verbosity and the bloatness of PSR-18 and their
implementations. This is why I made php fetch: for the 90% of simple use cases. If you
need an HTTP client to do web scraping, don't use this (you need redirect following,
multiplexing, cookie support, plugins for bypassing csrf, javascript engine embedded, etc).
But if you need a simple http client to make some requests to an api, you'll find
using this library more than enough.

"But what about interoperability and vendor lock-in?" Well, truth is that if you are a
responsible programmer, you should be building the code that makes requests to a http
endpoint behind a proper abstraction, like an interface. Think something like: `ApiService`
with these possible implementations: `GuzzleApiService`, `CurlApiService` or `FetchApiService`.
If you do this, there is no vendor lock-in to be afraid of. On the contrary, if you don't keep
your dependencies hidden behind interfaces that serve your own contract and requirements,
you will suffer not only when doing HTTP, but with pretty much anything else.

PSR-18 was made for libraries mainly, to avoid dependency conflicts. This does not mean that
it cannot be used in applications; many people do, and it works!
What it does mean is that its reason to be is to serve libraries, like HTTP SDKS or others. 
If you are familiar with the whole Guzzle fiasco from a few years ago, you'll
know that HTTPPlug (the inspiration for PSR-18) was made with the sole purpose of
becoming protection from dependency conflicts in some libraries, mainly caused by a 
very aggressive release policy from Guzzle, and a very relaxed release policy from Amazon.

So, the simplicity of this library is more than enough for most of my applications.

### Most HTTP clients are too bloated

Again, this is not a defect of HTTP clients per se. A client that has many features will 
have a lot of code and dependencies. The question is whether you need those features for
your use case or not. In my experience, most of the time I don't need them, and I
always end up doing simple HTTP requests with PHP streams. I built this library so
I don't have to do that anymore for simple use cases.

Again, if your use case is more complex, you might want to consider using a more feature
rich HTTP client. [Symfony Panther](https://github.com/symfony/panther) is my go-to
recommendation for web scraping, for example.

### No HTTP client is just a function

One thing I love about working with javascript is its more functional friendly approach. Even
though is light-years away of being a pure functional language, the declarative nature
of most of it's apis make it really nice to work with (oh if only had proper typing and
encapsulation!).

The `fetch` api is one of my favourites, and I always wanted to have something like that in
PHP. I searched for it, but to no avail, hence this library. Sometimes, most of our single
method classes could perfectly be functions.

Some people in PHP are starting to grasp this and using more functions, especially since
functions can be namespaced now (please never add functions in the global namespace!).

### Immutability

Well, PSR-18 favours immutability in the form of reference clonation. This library does it in
the form of read-only state. There is nothing you can change in an already constructed
response. Everything is read only.

> Well, you could change it using nasty php tricks like closure scope binding; but don't
> do that, okay?

There is no reason why you should need to change a response from a server. The only thing
you can do with the response is compose it into other types: nothing more.

### Other Nit-Pickyness

It really annoys the freak out of me when a library that implements a protocol does not
throw exceptions when a protocol error happens. Like, which SMTP client library gives you
a `Response` containing an error when no target address has been specified instead of throwing
an exception? How are you supposed to know that an error happened?

The purpose of implementing a protocol in a language is to mimic the idiosyncrasies of the
protocol in the available language constructs. If there is status codes defined for errors, the
language construct for errors should be used: in this case, an exception should be raised.

[This is one of the biggest problems for me with PSR-18](https://www.php-fig.org/psr/psr-18/#error-handling).
 I think it was a terrible design decision that harms user experience.
 
### In Closing

This client is simple, small, functional, immutable, type-safe, well designed and achieves a
good balance between protocol strictness and convenience. I think there is nothing like that
in the PHP ecosystem right now, so there might be a user base for this.

Hope you enjoy using it as much as I enjoyed building it.
