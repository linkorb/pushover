# LinkORB\Pushover

Simple API client for www.pushover.net

## Features

* PSR-0 compatible, works with composer and is registered on packagist.org
* Send messages using custom titles, urls and url texts.
* Support for message priorities (-1, 0, 1 and 2)
* Stand-alone library, no external dependencies
* Unit tests

## Installing

Check out [composer](http://www.getcomposer.org) for details about installing and running composer.

Then, add `linkorb/pushover` to your project's `composer.json`:

```json
{
    "require": {
        "linkorb/pushover": "1.*"
    }
}
```
## Contributing

Ready to build and improve on this repo? Excellent!
Go ahead and fork/clone this repo and we're looking forward to your pull requests!
Be sure to update the unit tests in tests/.

If you are unable to implement changes you like yourself, don't hesitate to
open a new issue report so that we or others may take care of it.

## Todo

* Implement receipts / callbacks [https://pushover.net/api#receipt](https://pushover.net/api#receipt)
* Implement methods for user verification [https://pushover.net/api#verification](https://pushover.net/api#verification)
* Improve unit test coverage

## License
Please check LICENSE.md for full license information


## Brought to you by the LinkORB Engineering team

<img src="http://www.linkorb.com/d/meta/tier1/images/linkorbengineering-logo.png" width="200px" /><br />
Check out our other projects at [linkorb.com/engineering](http://www.linkorb.com/engineering).

Btw, we're hiring!
