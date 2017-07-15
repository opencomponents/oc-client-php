# oc-client-php

[![Build status][build svg]][build status]
[![Code coverage][coverage svg]][coverage]

PHP client for [OpenComponents][open-components]

For a nodejs implementation check this [link][oc-client-node]. This library is heavily based on it.

## Install

```bash
composer require opencomponents/oc-client-php
```

## Quickstart

```php
use OpenComponents\Client;

// Initializing the client
$client = new Client(array(
    "serverRendering" => 'https://your-components.repository.com/'
));

// Render some component
$components = $client->renderComponents(array(
    array(
        'name' => 'your-amazing-widget',
        'parameters' => array(
            'param1' => 'hello opencomponents!',
            'param2' => 'just show me the component'
        )
    ),
    array(
        'name' => 'one-more-component'
    )
));

// Print the rendered component and volià
echo $components['html'];
```

## Running tests

```bash
composer test
```

[open-components]: https://github.com/opentable/oc
[oc-client-node]: https://github.com/opencomponents/oc-client-node
[build status]: https://travis-ci.org/opencomponents/oc-client-php
[build svg]: https://img.shields.io/travis/opencomponents/oc-client-php/master.svg?style=flat-square

[coverage]: https://codecov.io/gh/opencomponents/oc-client-php
[coverage svg]: https://img.shields.io/codecov/c/github/opencomponents/oc-client-php/master.svg?style=flat-square
