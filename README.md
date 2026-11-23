# AI-TLP PHPDoc tags

Use `@ai-tlp` in PHPDoc comments to record the level of AI involvement in
your code. The notation follows the [AI Traffic Light Protocol](https://nlkw.de/en/blog/ai-tlp/).

Use the extension with phpDocumentor 3.10 to validate the tags and, with the
provided template override, render colored badges. PhpStorm highlighting is
configured separately.

## 1. Add tags to your project

Put a tag in a PHPDoc block above a class, method, or other documented element:

```php
/**
 * Processes an incoming webhook.
 *
 * @ai-tlp amber AI-generated; reviewed line by line.
 */
function processWebhook(): void
{
    // ...
}
```

The syntax is `@ai-tlp <level> [description]`. Choose the level that reflects
the actual human involvement:

| Level | Meaning |
| --- | --- |
| `white` | Written independently, without significant AI involvement. |
| `green` | AI-assisted; the author drove the work. |
| `amber` | AI-generated and reviewed line by line. |
| `orange` | AI-generated at scale; architecture and hot spots reviewed. |
| `red` | AI-generated and checked only for plausibility. |
| `yolo` | AI-generated and unreviewed. |

Full labels such as `AI:AMBER` and mixed case are also supported by the
validator. The description is optional. See `examples/src/AIExample.php` for
more examples.

## 2. Highlight tags in PhpStorm

PhpStorm does not load highlighting rules from this phpDocumentor package.
You can use its built-in TODO patterns to give each level a distinct color.
This works without installing the package or a plugin.

1. Open **Settings/Preferences → Editor → TODO**.
2. Under **Patterns**, use **+** to add the regular expressions below, one
   at a time. Leave the existing TODO and FIXME patterns in place.
3. For each pattern, configure its own foreground or background color in the
   pattern's appearance settings instead of using the default TODO colors.
   Choose colors that are readable with your editor theme; for example, use
   a light background with dark text for white and amber.
4. Apply the settings and open a PHP file containing the example above. The
   matching tag should be highlighted; occurrences also appear in PhpStorm's
   **TODO** tool window.

| Suggested color | Pattern |
| --- | --- |
| White/gray | `(?i)@ai-tlp[ \t]+(?:ai:)?white(?![A-Za-z0-9_-])` |
| Green | `(?i)@ai-tlp[ \t]+(?:ai:)?green(?![A-Za-z0-9_-])` |
| Amber | `(?i)@ai-tlp[ \t]+(?:ai:)?amber(?![A-Za-z0-9_-])` |
| Orange | `(?i)@ai-tlp[ \t]+(?:ai:)?orange(?![A-Za-z0-9_-])` |
| Red | `(?i)@ai-tlp[ \t]+(?:ai:)?red(?![A-Za-z0-9_-])` |
| Purple for YOLO | `(?i)@ai-tlp[ \t]+(?:ai:)?yolo(?![A-Za-z0-9_-])` |

These are comment-matching patterns, not semantic PHPDoc support: they do not
validate levels or enforce review policies, and they may also match ordinary
comments. They recognize both `amber` and `AI:AMBER`, regardless of case.
The TODO tool window will list the matches as TODO items, even though they are
not tasks. Appearance options and labels can vary by PhpStorm version.

If PhpStorm reports an unknown PHPDoc tag, place the caret on `@ai-tlp` and
check **Alt+Enter** for an option to register or ignore the tag. Alternatively,
adjust the relevant PHPDoc inspection in **Settings/Preferences → Editor →
Inspections**. This affects the inspection, not level validation or the TODO
colors.

## 3. Install the phpDocumentor extension

The extension requires PHP 8.1+ and phpDocumentor 3.10. The instructions
below use the official phpDocumentor 3.10.0 PHAR; it runs independently of
your project's Composer dependencies. Download `phpDocumentor.phar` from the
[3.10.0 release](https://github.com/phpDocumentor/phpDocumentor/releases/tag/v3.10.0)
and keep it at a convenient location outside your project.

In the consuming project's root, add this repository as a Composer VCS source
and require the extension:

```sh
composer config repositories.phpdoc-extension-tlp-ai vcs https://github.com/leibniz-psychology/phpDoc-extension-tlp-ai.git
composer require --dev 'leibniz-psychology/phpdoc-extension-tlp-ai:dev-main'
```

Run these commands in the consuming project's root and commit its updated
`composer.json` and `composer.lock`. `dev-main` tracks an unstable branch;
pin a released version or commit when one is appropriate for your project.
The package does not install phpDocumentor itself. The PHAR discovers the
installed extension automatically when invoked from the project root.

## 4. Generate documentation with badges

Create `phpdoc.dist.xml` in your project root, adjusting `src` and the output
paths to suit your project:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpdocumentor configVersion="3" xmlns="http://phpdoc.org/xsl/config">
    <title>My Project</title>
    <paths>
        <output>build/docs</output>
        <cache>build/cache</cache>
    </paths>
    <version number="1.0.0">
        <api format="php">
            <source dsn=".">
                <path>src</path>
            </source>
        </api>
    </version>
</phpdocumentor>
```

To add the badges to phpDocumentor's **default** HTML template, copy the
provided override next to your configuration file:

```sh
mkdir -p .phpdoc/template/components
cp vendor/leibniz-psychology/phpdoc-extension-tlp-ai/templates/overrides/components/tags.html.twig .phpdoc/template/components/tags.html.twig
```

This replaces phpDocumentor's `components/tags.html.twig`; merge the AI-TLP
changes into your existing override instead if you already have one. Review
the override when upgrading phpDocumentor or the extension. Other templates
need their own integration.

From the project root, run the PHAR, replacing the path with its location:

```sh
php /path/to/phpDocumentor.phar --force
```

The console should show `Loaded extensions` and
`leibniz-psychology/phpdoc-extension-tlp-ai`. Open `build/docs/index.html`
and navigate to a tagged class or method to see its badge and description.
Invalid or missing levels are recorded in `build/docs/reports/errors.html`;
phpDocumentor can still exit successfully, so check that report when enforcing
validity. Without the override, the default template displays tags without
the level badge. Without the extension loaded, phpDocumentor treats `@ai-tlp`
as an ordinary tag and does not validate its level.

For a quick demo, use `examples/phpdoc.dist.xml` and `examples/src/AIExample.php`
as the configuration and source in a consuming project. See the
[phpDocumentor 3.10 extension guide](https://github.com/phpDocumentor/phpDocumentor/blob/v3.10.0/docs/features/extensions/index.rst)
for details on extension discovery.
