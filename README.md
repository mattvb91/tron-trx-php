# tron-trx-php
[![Build Status](https://travis-ci.com/mattvb91/tron-trx-php.svg?branch=master)](https://travis-ci.com/mattvb91/tron-trx-php)
[![Coverage Status](https://coveralls.io/repos/github/mattvb91/tron-trx-php/badge.svg?branch=master)](https://coveralls.io/github/mattvb91/tron-trx-php?branch=master)

PHP Library for interacting with the Tron blockchain through Tron-Grid


## Overview

This library aims to integrate with the Tron-Grid while removing some of the dangerous aspects of its API.
One of the biggest issues currently is private keys being posted over a network to various TRON nodes while leaving the node
configuration up to developers. This causes
great potential for man in the middle attacks to take place and steal private keys. Even with trustworthy developers
who know how to setup their networking & authentication layers mistakes do happen and fact is your private key may be
getting posted over a network and being exposed.

##### This library integrates with Tron-Grid while removing some of these dangerous parts and doing them locally instead

So in a nutshell. This library allows you to use the harmless endpoints such as ```/wallet/getaccountnet``` etc.. while
any actions such as generating addresses/private keys or signing are all done locally without your private key getting posted over a network.  

### Prerequisites

Your PHP installation requires bcmath & gmp extensions to be enabled.

### Installing

```
composer require mattvb91/trontrx
```

### Docker

There is a [Dockerfile](/build/Dockerfile) available that you can use to build a working image to get started quickly.

```bash
cd build
docker build -t tron-trx-php .
cd ../
docker run -it -v $(pwd):/app -u 1000 tron-trx-php /bin/bash
composer install
./vendor/bin/phpunit

```

## Available interface

- TODO describe available interface

## Built With

* [ionux/phactor](https://github.com/ionux/phactor) - PHP implementation of the elliptic curve based on secp256k1
* [php-keccak](https://github.com/kornrunner/php-keccak) - Pure PHP implementation of Keccak (SHA-3)

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/mattvb91/tron-trx-php/tags). 

## Contributors

- 

See also the list of [contributors](https://github.com/mattvb91/tron-trx-php/contributors) who participated in this project.

## License
```
MIT License

Copyright (c) 2018 Matthias von Bargen

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

## Acknowledgments

* Support files from [iexbase/tron-api](https://github.com/iexbase/tron-api)
