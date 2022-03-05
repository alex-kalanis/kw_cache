# kw_cache

Cache content across the KWCMS. Use storage interfaces or directly volume. Contains a simple support
for semaphores as remote flags for cache re-generating.

## PHP Installation

```
{
    "require": {
        "alex-kalanis/kw_cache": "1.0"
    }
}
```

(Refer to [Composer Documentation](https://github.com/composer/composer/blob/master/doc/00-intro.md#introduction) if you are not
familiar with composer)


## PHP Usage

1.) Use your autoloader (if not already done via Composer autoloader)

2.) Set storage(s) which will be used by cache.

3.) Connect the "kalanis\kw_cache\*" into your app. Extends it for setting your case.

4.) Just call it
