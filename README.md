Akeneo Fixtures Generation Toolbox
==================================

This classes and templates toolbox helps generating CSV fixtues from Magento 1.9CE or 1.14EE catalog.

Example
-------

```php
#!/ur/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

use Kiboko\Bridge\Akeneo\Magento\Attribute;
use Kiboko\Bridge\Akeneo\Magento\AttributeRenderer;
use Kiboko\Bridge\Akeneo\Magento\FieldResolver;
use Kiboko\Bridge\Akeneo\Magento\MagentoStore;
use Kiboko\Bridge\Akeneo\Magento\Locale;
use Kiboko\Bridge\Akeneo\Magento\Scope;
use Kiboko\Bridge\Akeneo\Magento\Renderer;
use Kiboko\Bridge\Akeneo\Magento\TwigExtension;

$twig = new \Twig\Environment(
    new Twig\Loader\FilesystemLoader([
        __DIR__ . '/../templates'
    ])
);
$twig->addExtension(new TwigExtension());

$locales = [
    $fr_FR = new Locale\Locale('fr_FR', new MagentoStore(1)),
    $de_DE = new Locale\Locale('de_DE', new MagentoStore(2)),
    $en_GB = new Locale\Locale('en_GB', new MagentoStore(2)),
    $en_US = new Locale\Locale('en_US', new MagentoStore(4)),
    $ja_JP = new Locale\Locale('ja_JP', new MagentoStore(5)),
    $fr_CA = new Locale\Locale('fr_CA', new MagentoStore(8)),
];

$scopes = [
    new Scope\Scope(
        'europe',
        new MagentoStore(1),
        $fr_FR,
        $de_DE,
        $en_GB
    ),
    new Scope\Scope(
        'america',
        new MagentoStore(4),
        $en_US,
        $fr_CA,
        new Locale\LocaleMapping($en_US, new MagentoStore(9)) // Additional locale mapping for Canada
    ),
    new Scope\Scope(
        'asia',
        new MagentoStore(5),
        $ja_JP,
        new Locale\LocaleMapping($en_GB, new MagentoStore(6)) // Additional locale mapping for Hong Kong
    ),
];

$globalized = new FieldResolver\Globalised();
$localized = new FieldResolver\Localized(...$locales);
$scoped = new FieldResolver\Scoped(...$scopes);
$scopedAndLocalized = new FieldResolver\ScopedAndLocalized(...$scopes);
$axis = new FieldResolver\VariantAxis();

$renderer = new Renderer(
    'initialize.sql.twig',
    'finalize-product-parents.sql.twig',
    ['configurable'],
    // 1st level Product Models
    new AttributeRenderer\Image(
        new Attribute\AdHoc('image'),
        $scoped
    ),
    new AttributeRenderer\Varchar(
        new Attribute\AdHoc('name'),
        $scopedAndLocalized
    ),
    new AttributeRenderer\Text(
        new Attribute\AdHoc('description'),
        $scopedAndLocalized
    ),
    new AttributeRenderer\Text(
        new Attribute\AdHoc('short_description'),
        $scopedAndLocalized
    ),
    new AttributeRenderer\Varchar(
        new Attribute\AdHoc('meta_title'),
        $scopedAndLocalized
    ),
    new AttributeRenderer\Text(
        new Attribute\AdHoc('meta_description'),
        $scopedAndLocalized
    ),
    new AttributeRenderer\SimpleSelect(
        new Attribute\AdHoc('model'),
        $scoped
    ),
    new AttributeRenderer\SimpleSelect(
        new Attribute\AdHoc('manufacturer'),
        $scoped
    )
);

$renderer(fopen('products_models1.sql', 'w'), $twig);

$renderer = new Renderer(
    'initialize.sql.twig',
    'finalize-product-children.sql.twig',
    [],
    // 2nd level Product Models
    new AttributeRenderer\SimpleSelect(
        new Attribute\AdHoc('color'),
        $axis
    )
);

$renderer(fopen('products_models2.sql', 'w'), $twig);

$renderer = new Renderer(
    'initialize.sql.twig',
    'finalize-products.sql.twig',
    ['simple', 'virtual'],
    // Product & Product variants
    new AttributeRenderer\Status(
        new Attribute\AdHoc('status'),
        $scopedAndLocalized
    ),
    new AttributeRenderer\Visibility(
        new Attribute\AdHoc('visibility'),
        $scopedAndLocalized
    ),
    new AttributeRenderer\SimpleSelect(
        new Attribute\AdHoc('size'),
        $axis
    ),
    new AttributeRenderer\Image(
        new Attribute\Aliased('image', 'variation_image'),
        $scoped
    ),
    new AttributeRenderer\Varchar(
        new Attribute\Aliased('name', 'variation_name'),
        $scopedAndLocalized
    ),
    new AttributeRenderer\Text(
        new Attribute\Aliased('description', 'variation_description'),
        $scopedAndLocalized
    ),
    new AttributeRenderer\Datetime(
        new Attribute\AdHoc('news_from_date'),
        $scopedAndLocalized
    ),
    new AttributeRenderer\Datetime(
        new Attribute\AdHoc('news_to_date'),
        $scopedAndLocalized
    )
);

$renderer(fopen('products.sql', 'w'), $twig);
```

Supported attribute types 
---

| Magento Type | Akeneo Type | Not Localizable, not Scopable | Localizable, not Scopable | Not Localizable, Scopable | Localizable, Scopable |
| --- | --- | --- | --- | --- | --- |
| Gallery | Asset Collection | ❌ | ❌ | ❌ | ❌ |
| Datetime | Date | ✅ | ❌ | ❌ | ✅ |
| File | File | ❌ | ❌ | ❌ | ❌ |
| SKU | Identifier | ✅ | ❌ | ❌ | ❌ |
| Image | Image | ✅ | ❌ | ❌ | ✅ |
| Decimal | Metric | ❌ | ❌ | ❌ | ❌ |
| Multiselect | Multi select | ❌ | ❌ | ❌ | ❌ |
| Select | Simple select | ✅ | ❌ | ✅ | ✅ |
| Number | Number | ❌ | ❌ | ❌ | ❌ |
| Price | Price | ❌ | ❌ | ❌ | ❌ |
| Status | Simple select | ✅ | ❌ | ❌ | ❌ |
| - | Reference data multi select | ❌ | ❌ | ❌ | ❌ |
| - | Reference data simple select | ❌ | ❌ | ❌ | ❌ |
| Text | Text area | ✅ | ❌ | ❌ | ✅ |
| Varchar | Text | ✅ | ❌ | ❌ | ✅ |
| Visibility | Simple select | ✅ | ❌ | ❌ | ❌ |
| YesNo | Yes No | ❌ | ❌ | ❌ | ❌ |
