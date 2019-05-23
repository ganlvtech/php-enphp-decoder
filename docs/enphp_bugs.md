# About EnPHP Bugs

## explode delimiter with apostrophe

tests/bug_samples/Rush.php

```php
explode('|\'|%|\'', 'post.|\\\'|%|\\\'site_config|\\\'|%|\\\'设置成功|\\\'|%|\\\'API|\\\'|%|\\\'api_config|\\\'|%|\\\'taobao|\\\'|%|\\\'key|\\\'|%|\\\'taobaoAppkey|\\\'|%|\\\'site');
```

This is EnPHP's bug. Won't Fix!
