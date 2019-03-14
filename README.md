# EnPHP Decoder

## Installation

### Get It By Git Clone

You can get the code by git clone. And then install the dependencies by yourserlf with [Composer](http://getcomposer.org/).

```bash
git clone https://github.com/ganlvtech/php-enphp-decoder.git
cd php-enphp-decoder
composer install
```

### Download From GitHub Release

You can also download it from [GitHub Release](https://github.com/ganlvtech/php-enphp-decoder/releases).

Download the `zip` file and unzip them into a folder. All dependencies have been installed.

## Usage

```bash
php bin/decode.php input.php output.php
```

Call `bin/decode.php` decode `input.php` and save it to `output.php`.

## About EnPHP Bugs

EnPHP has bugs. The obfuscated files cannot even run properly. You shouldn't ask a decoder to revert a broken file to a normal file.

Here are some known EnPHP bugs. They **WON'T** be fixed.

### Class Static Call

```php
class Foo
{
    public static function baz()
    {
        echo 'baz';
    }
}

class Bar extends Foo
{
    public function __construct()
    {
        parent::baz();
    }
}
```

class Bar will be obfuscated like this

```php
class Bar extends Foo
{
    public function __construct()
    {
        parent::$GLOBALS[GLOBAL_VAR_KEY][0x0]();
    }
}
```

This means `(parent::$GLOBALS)[GLOBAL_VAR_KEY][0x0]();` instead of what we expected `parent::{$GLOBALS[GLOBAL_VAR_KEY][0x0]}();`.

### Class Method Call

```php
class Foo
{
    public function bar()
    {
        echo 'bar';
    }

    public function __construct()
    {
        $this->bar();
    }
}
```

The constructor will be encoded like this

```php
class Foo
{
    public function __construct()
    {
        $v0 = &$GLOBALS[GLOBAL_VAR_KEY];
        $this->$v0[0x0]();
    }
}
```

This means `($this->$v0)[0x0]()` instead of what we expected `$this->{$v0[0x0]}()`.

## License

GNU GENERAL PUBLIC LICENSE Version 3

    EnPHP Decoder
    Copyright (C) 2019  Ganlv

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <https://www.gnu.org/licenses/>.
