<?php
function parse_size($size)
{
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
    $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
    if ($unit) {
        // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
        return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    } else {
        return round($size);
    }
}

/**
 * Returns a file size limit in bytes based on the PHP upload_max_filesize and post_max_size
 *
 * @link https://stackoverflow.com/questions/13076480/php-get-actual-maximum-upload-size
 *
 * @return int
 */
function file_upload_max_size()
{
    static $max_size = -1;

    if ($max_size < 0) {
        // Start with post_max_size.
        $post_max_size = parse_size(ini_get('post_max_size'));
        if ($post_max_size > 0) {
            $max_size = $post_max_size;
        }

        // If upload_max_size is less, then reduce. Except if upload_max_size is
        // zero, which indicates no limit.
        $upload_max = parse_size(ini_get('upload_max_filesize'));
        if ($upload_max > 0 && $upload_max < $max_size) {
            $max_size = $upload_max;
        }
    }
    return $max_size;
}
?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP EnPHP Decoder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.5/css/bulma.min.css">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <style>
        .field .button {
            padding-left: 2em;
            padding-right: 2em;
        }
    </style>
</head>
<body>
    <section class="hero is-light has-text-centered">
        <div class="hero-body">
            <div class="container">
                <h1 class="title">PHP EnPHP Decoder</h1>
                <p>
                    <a href="https://github.com/djunny/enphp">EnPHP</a> Decoder written in PHP. Powered by <a href="https://github.com/nikic/PHP-Parser">PHP-Parser</a>.
                </p>
            </div>
        </div>
    </section>
    <section class="section">
        <div class="container">
            <iframe name="target_iframe" id="target-iframe" style="display: none;"></iframe>
            <form action="decode.php" method="POST" enctype="multipart/form-data" target="target_iframe" id="form">
                <input type="hidden" id="max-file-size-input" name="MAX_FILE_SIZE" value="<?php echo file_upload_max_size(); ?>">
                <div class="field">
                    <div class="file is-centered has-name is-large is-boxed">
                        <label class="file-label">
                            <input class="file-input" type="file" id="file" name="file" accept=".php">
                            <span class="file-cta">
                                <span class="file-icon"><i class="fas fa-upload"></i></span>
                                <span class="file-label">Select a PHP File…</span>
                            </span>
                            <span class="file-name has-text-centered" id="file-name">No file selected.</span>
                        </label>
                    </div>
                    <p class="help has-text-centered">Max file size is <span id="max-file-size"></span>.</p>
                </div>
                <div class="field is-grouped is-grouped-centered">
                    <div class="control">
                        <button type="submit" class="button is-large is-primary"><span class="file-icon"><i class="fas fa-download"></i></span>Decode</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
    <script>
        function humanReadableSize(size) {
            if (size > 1024 * 1024 * 1024) {
                return Math.round(size / 1024 / 1024 / 1024 * 10) / 10 + 'GiB';
            } else if (size > 1024 * 1024) {
                return Math.round(size / 1024 / 1024 * 10) / 10 + 'MiB';
            } else if (size > 1024) {
                return Math.round(size / 1024 * 10) / 10 + 'KiB';
            }
            return size + 'B';
        }

        var maxFileSize = parseInt(document.querySelector('#max-file-size-input').value);
        document.querySelector('#max-file-size').textContent = humanReadableSize(maxFileSize);
        document.querySelector('#file').addEventListener('change', function (e) {
            var file = e.target.files[0];
            if (file.size > maxFileSize) {
                swal({
                    icon: 'error',
                    text: 'This file may be too large.',
                });
            } else {
                if (!file.name.endsWith('.php')) {
                    swal({
                        icon: 'warning',
                        text: 'This file may not be a php file. But you can still try to submit.',
                    });
                }
                document.querySelector('#file-name').textContent = file.name;
            }
        });
        document.querySelector('#target-iframe').addEventListener('load', function () {
            var html = document.querySelector('#target-iframe').contentDocument.body.innerHTML;
            if (html.length > 0) {
                var div = document.createElement('div');
                div.innerHTML = html;
                swal({
                    content: div,
                    icon: 'error'
                });
            }
        });
    </script>
    <a href="https://github.com/ganlvtech/php-enphp-decoder" class="github-corner" aria-label="View source on GitHub"><svg width="80" height="80" viewBox="0 0 250 250" style="fill:#151513; color:#fff; position: absolute; top: 0; border: 0; right: 0;" aria-hidden="true"><path d="M0,0 L115,115 L130,115 L142,142 L250,250 L250,0 Z"></path><path d="M128.3,109.0 C113.8,99.7 119.0,89.6 119.0,89.6 C122.0,82.7 120.5,78.6 120.5,78.6 C119.2,72.0 123.4,76.3 123.4,76.3 C127.3,80.9 125.5,87.3 125.5,87.3 C122.9,97.6 130.6,101.9 134.4,103.2" fill="currentColor" style="transform-origin: 130px 106px;" class="octo-arm"></path><path d="M115.0,115.0 C114.9,115.1 118.7,116.5 119.8,115.4 L133.7,101.6 C136.9,99.2 139.9,98.4 142.2,98.6 C133.8,88.0 127.5,74.4 143.8,58.0 C148.5,53.4 154.0,51.2 159.7,51.0 C160.3,49.4 163.2,43.6 171.4,40.1 C171.4,40.1 176.1,42.5 178.8,56.2 C183.1,58.6 187.2,61.8 190.9,65.4 C194.5,69.0 197.7,73.2 200.1,77.6 C213.8,80.2 216.3,84.9 216.3,84.9 C212.7,93.1 206.9,96.0 205.4,96.6 C205.1,102.4 203.0,107.8 198.3,112.5 C181.9,128.9 168.3,122.5 157.7,114.1 C157.9,116.9 156.7,120.9 152.7,124.9 L141.0,136.5 C139.8,137.7 141.6,141.9 141.8,141.8 Z" fill="currentColor" class="octo-body"></path></svg></a><style>.github-corner:hover .octo-arm{animation:octocat-wave 560ms ease-in-out}@keyframes octocat-wave{0%,100%{transform:rotate(0)}20%,60%{transform:rotate(-25deg)}40%,80%{transform:rotate(10deg)}}@media (max-width:500px){.github-corner:hover .octo-arm{animation:none}.github-corner .octo-arm{animation:octocat-wave 560ms ease-in-out}}</style>
</body>
</html>