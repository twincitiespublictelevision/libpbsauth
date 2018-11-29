# libpbsauth
[![CircleCI](https://circleci.com/gh/twincitiespublictelevision/libpbsauth/tree/master.svg?style=svg)](https://circleci.com/gh/twincitiespublictelevision/libpbsauth/tree/master)

`libpbsauth` is a small package containing a few classes for structured
usage of PBS Sign In auth data.

---

## Overview

Provides classes for `Owner`, `VPPA`, and `Token` objects, along with a wrapper class
`PBSAuth`. Creating a record returns a `*Result` class. The result encapsulates either 
the created object or the error depending on the success of call.

## Usage

Documentation can be found at [https://twincitiespublictelevision.github.io/libpbsauth/](https://twincitiespublictelevision.github.io/libpbsauth/)

Result classes provide a return style for capturing the success or failure of a
given operation in a single return value. The value or error can then be extracted
from the result by the calling code and be conditionally used. An **ok** value
represents the success of an operation, whereas an **err** value represents the
failure of an operation.

When attempting to parse an array, stdClass, or string an ok will be returned if
the entire parsing of the record succeeds. If any of the steps fail then an err
is returned containing the failure.

An example of generic usage of a Result:

```php
$resultA = Result::ok("foo");
echo $resultA->value(); // foo

$resultB = Result::err(new \Exception("Bar error");
echo $resultB->value(); // PHP Fatal error:  Uncaught exception ...
```

To safely handle a result and extract its value the caller can use either
conditionals or try / catch syntax

```php
$resultA = Result::ok("foo");

if ($resultA->isOk()) {
  echo $result->value(); // foo
} else {
  // ...
}

$resultB = Result::err(new \Exception("Bar error");

try {
  echo $resultB->value();
} catch (\Exception $e) {
  echo $e->getMessage(); // Bar error
}
```

## Requirements

* PHP >= 7.1

## Install

1. Add to the **repositories** key of your **composer.json** file:
```
{
  "type": "vcs",
  "url": "https://github.com/twincitiespublictelevision/libpbsauth.git"
}
```

2. Run `composer require twincitiespublictelevision/libpbsauth` to pull in the package