# kw_cache

[![Build Status](https://travis-ci.org/alex-kalanis/kw_cache.svg?branch=master)](https://travis-ci.org/alex-kalanis/kw_cache)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alex-kalanis/kw_cache/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alex-kalanis/kw_cache/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/alex-kalanis/kw_cache/v/stable.svg?v=1)](https://packagist.org/packages/alex-kalanis/kw_cache)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.3-8892BF.svg)](https://php.net/)
[![Downloads](https://img.shields.io/packagist/dt/alex-kalanis/kw_cache.svg?v1)](https://packagist.org/packages/alex-kalanis/kw_cache)
[![License](https://poser.pugx.org/alex-kalanis/kw_cache/license.svg?v=1)](https://packagist.org/packages/alex-kalanis/kw_cache)
[![Code Coverage](https://scrutinizer-ci.com/g/alex-kalanis/kw_cache/badges/coverage.png?b=master&v=1)](https://scrutinizer-ci.com/g/alex-kalanis/kw_cache/?branch=master)

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
