# About EnPHP Bugs

**EnPHP is for php 5. There may be some problem is you use the obfuscated files on php 7.**

**php 5.5 5.6 7.0 is no longer supported.** See [PHP: Supported Versions](http://php.net/supported-versions.php).

EnPHP has bugs. The obfuscated files cannot even run properly on php 7. You shouldn't ask a decoder to recover a broken file to a normal file.

See [PHP 5.x to 7.x: Changes to the handling of indirect variables, properties, and methods](http://php.net/manual/en/migration70.incompatible.php#migration70.incompatible.variable-handling.indirect).

Here are some known EnPHP bugs running on php 7. They **WON'T** be fixed.

## Class Static Call

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

## Class Method Call

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
