# EnPHP Decoder

[![Build Status](https://travis-ci.com/ganlvtech/php-enphp-decoder.svg?branch=master)](https://travis-ci.com/ganlvtech/php-enphp-decoder)

[EnPHP](https://github.com/djunny/enphp) Decoder written in PHP. Powered by [PHP-Parser](https://github.com/nikic/PHP-Parser).

## Try it online

Check the link in repository description.

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

### Decode by Web UI

```bash
cd public/
php -S 127.0.0.1:8000
```

Visit <https://127.0.0.1:8000/> on Browser. You can select a file to upload, and you will download a decoded file.

### Decode One File

```bash
php bin/decode.php input.php output.php
```

Call `bin/decode.php` decode `input.php` and save it to `output.php`.

### Decode All Files in A Directory

```bash
php bin/decodeRecursive.php dir/
```

Call `bin/decodeRecursive.php` decode all php files in `dir/` recursively and save it to its original path.

You can use absolute path like `/path/to/your/dir/`.

**CAUTION: This will OVERWRITE all php files! If any error happened with the decoder, your files MAY NOT BE RECOVERED! Please backup your files!**

## About EnPHP Bugs

See [docs/enphp_bugs.md](docs/enphp_bugs.md).

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
