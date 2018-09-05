# libpbsauth

`libpbsauth` is a small package containing a few classes for structured
usage of PBS Sign In auth data.

---

## Overview

Provides classes for `Owner`, `VPPA`, and `Token` objects, along with a wrapper class
`PBSAuth`. Creating a record returns a `*Result` class. The result encapsulates either 
the created object or the error depending on the success of call.

## Requirements

* PHP >= 7.1

## Installing

1. Add to the **repositories** key of your **composer.json** file:
```
{
  "type": "vcs",
  "url": "https://github.com/twincitiespublictelevision/libpbsauth.git"
}
```

2. Run `composer require twincitiespublictelevision/libpbsauth:dev-master` to pull in the package