![tests](https://github.com/jeyroik/extas-installer-plugins-repositories/workflows/PHP%20Composer/badge.svg?branch=master&event=push)
![codecov.io](https://codecov.io/gh/jeyroik/extas-installer-plugins-repositories/coverage.svg?branch=master)
<a href="https://github.com/phpstan/phpstan"><img src="https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat" alt="PHPStan Enabled"></a>

<a href="https://github.com/jeyroik/extas-installer/" title="Extas Installer v3"><img alt="Extas Installer v3" src="https://img.shields.io/badge/installer-v3-green"></a>
[![Latest Stable Version](https://poser.pugx.org/jeyroik/extas-installer-plugins-repositories/v)](//packagist.org/packages/jeyroik/extas-q-crawlers)
[![Total Downloads](https://poser.pugx.org/jeyroik/extas-installer-plugins-repositories/downloads)](//packagist.org/packages/jeyroik/extas-q-crawlers)
[![Dependents](https://poser.pugx.org/jeyroik/extas-installer-plugins-repositories/dependents)](//packagist.org/packages/jeyroik/extas-q-crawlers)


# Description

Allow to use dynamic repositories in the plugins install section.

# install

`# vendor/bin/extas init`
`# vendor/bin/extas install`

# usage

`extas.json`

```json
{
  "repositories": {
    "name": "any",
    "...": "...",
    "aliases": ["test"] 
  },
  "plugins_install": [
    {
      "repository": "test",
      "name": "some",
      "section": "tests"
    }
  ]
}
```
