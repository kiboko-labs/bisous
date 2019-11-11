Akeneo Fixtures Generation Toolbox
==================================

This toolbox helps generating CSV fixtures consumed by Akeneo's InstallerBundle from Magento 1.9CE or 1.14EE catalog data.

This package is here to help you import your Magento catalog into a fresh new Akeneo instance. It is not aimed at synchronising on a daily basis Akeneo and Magento together. 

Be aware that all your existing Akeneo product data will be reset by this tool, and lost.

Supported attribute types 
---

| Magento Type | Akeneo Type        | Not Localizable, not Scopable | Localizable, not Scopable | Not Localizable, Scopable | Localizable, Scopable |
| ------------ | ------------------ | --- | --- | --- | --- |
| Gallery      | Asset Collection   | ❌ | ❌ | ❌ | ❌ |
| Gallery Item | Image              | ✅ | ✅ | ✅ | ✅ |
| Datetime     | Date               | ✅ | ✅ | ✅ | ✅ |
| File         | File               | ❌ | ❌ | ❌ | ❌ |
| SKU          | Identifier         | ✅ | ❌ | ❌ | ❌ |
| Image        | Image              | ✅ | ✅ | ✅ | ✅ |
| Decimal      | Metric             | ❌ | ❌ | ❌ | ❌ |
| Multiselect  | Multi select       | ✅ | ❌ | ❌ | ✅ |
| Select       | Simple select      | ✅ | ❌ | ✅ | ✅ |
| Number       | Number             | ❌ | ❌ | ❌ | ❌ |
| Price        | Price              | ❌ | ❌ | ❌ | ❌ |
| Status       | Simple select      | ❌ | ❌ | ❌ | ✅ |
| -            | Ref. multi select  | ❌ | ❌ | ❌ | ❌ |
| -            | Ref. simple select | ❌ | ❌ | ❌ | ❌ |
| Text         | Text area          | ✅ | ❌ | ❌ | ✅ |
| Varchar      | Text               | ✅ | ❌ | ❌ | ✅ |
| Visibility   | Simple select      | ❌ | ❌ | ❌ | ✅ |
| YesNo        | Yes No             | ❌ | ❌ | ❌ | ❌ |

How to start
---

### Install using Composer

You will primarily need to install the tool in your environment:

`composer global require kiboko/bisous`

Once you are done, open a terminal in your Akeneo environement.
You will need to create an `.env` file, with the following environment variables properly set:

* `APP_DSN=mysql:host=mysql;dbname=magento`, Magento's database connection DSN, see [PDO MySQL Data Source Name](https://www.php.net/manual/en/ref.pdo-mysql.connection.php)
* `APP_USERNAME=root`, Magento's MySQL user name
* `APP_PASSWORD=password`, Magento's MySQL password

You will then need to create a `catalog.yml` file in this directory, describing your catalog structure. See [The `catalog.yml` file](#the-catalogyml-file)

### Download the phar from github

Go to [the latest version download page](https://github.com/kiboko-labs/bisous/releases/latest) and download the `bisous.phar` and `bisous.phar.pubkey` files.

Alternatively you can install the files this way:

```bash
curl https://github.com/kiboko-labs/bisous/releases/download/v1.0.0/bisous.phar --output /usr/local/bin/bisous
curl https://github.com/kiboko-labs/bisous/releases/download/v1.0.0/bisous.phar.pubkey --output /usr/local/bin/bisous.pubkey
chmod 0755 /usr/local/bin/bisous
```

Run the tool
---

Once properly installed, run `bisous magento <akeneo-directory>/src/InstallerBundle/Resources/fixtures/default`.

This command will create fixtures file required by Akeneo, with your Magento catalog data and structure.

The `catalog.yml` file
---

The `catalog.yml` file has a root node named `catalog:`, and 5 sub-nodes described in the following paragraphs:

### The `attributes:` section

This section is useful for describing your attribute list. It is an array of configuration fields, with the following fields:

* `code` (string): Your attribute code, as seen in Akeneo
* `type` (string): The attribute's type (valid values are `identifier`, `text`, `text-area`, `rich-text`, `status`, `visibility`, `simple-select`, `multiple-select`, `datetime`, `metric`, `image`)
* `strategy` (string): The import strategy, following the next possible values:
  * `ad-hoc`: the attribute will be created in Akeneo in the same way it was created in Magento
  * `aliased`: the attrib ute will be created in Akeneo with another code than the one existing in Magento
  * `ex-nihilo`: the attribute will be created in Akeneo without taking into account any attribute present in Magento
* `group` (string): the attribute group in which the attribute will be assigned in Akeneo
* `source` (string) (for strategy `aliased` only): the attribute code in Magento
* `scoped` (bool): to specify it the attribute is scopable (only applies to types `text`, `text-area`, `rich-text`, `status`, `visibility`, `simple-select`, `multiple-select`, `datetime`, `metric`, `image`, will produce an error in Akeneo if used on a variant axis attribute) 
* `localised` (bool): to specify it the attribute is localizable (only applies to types `text`, `text-area`, `rich-text`, `status`, `visibility`, `simple-select`, `multiple-select`, `datetime`, `metric`, `image`, will produce an error in Akeneo if used on a variant axis attribute) 

Example:

```yaml
catalog:
  attributes:
    - code: sku
      type: identifier
      strategy: ad-hoc
      group: general
    - code: name
      type: text
      strategy: ad-hoc
      group: marketing
      scoped: true
      localised: true
    - code: variation_name
      type: text
      strategy: ex-nihilo
      group: marketing
      scoped: true
      localised: true
    - code: weight
      type: metric
      strategy: ad-hoc
      group: logistics
      metric:
        family: Weight
        unit: KILOGRAM
    - code: image
      type: image
      strategy: ad-hoc
      group: marketing
```

### The `groups:` section

This section describes the attribute groups that will be created in Akeneo, with the following fields:

* `code` (string): it will contain the attribute group code in Akeneo
* `label` (array): it contains a map of the labels of this group, having key as locale ISO code and value as actual label.

Example:

```yaml
catalog:
  groups:
    - code: general
      label:
        fr_FR: Général
        en_GB: General
    - code: marketing
      label:
        fr_FR: Général
        en_GB: General
```

### The `families:` section

Example:

```yaml
catalog:
  families:
    - code: jeans
      attributes: [ name, description, short_description, meta_title, meta_description, status, visibility, image, variation_name, variation_image, variation_description, news_to_date, news_from_date, length, width, color, size ]
      label: name
      image: image
      requirements:
        - scope: america
          attributes: [ name, description, image ]
        - scope: europe
          attributes: [ name, description, image ]
        - scope: france
          attributes: [ name, description, image ]
        - scope: japan
          attributes: [ name, description, image ]
        - scope: china
          attributes: [ name, description, image ]
        - scope: asia
          attributes: [ name, description, image ]
        - scope: amazon
          attributes: [ name, description, image ]
        - scope: ebay
          attributes: [ name, description, image ]
      variations:
        - code: jeans_by_size_and_color
          skuPattern: '{{ parent }}:{{ length }}:{{ width }}'
          level-1:
            axis: [ length, width ]
            attributes: [ variation_name, variation_image, variation_description, news_from_date, news_to_date ]
          level-2:
            axis: [ color ]
            attributes: [ sku, status, visibility ]
        - code: jeans_by_size
          level-1:
            axis: [ size ]
            attributes: [ sku, status, visibility, variation_name, variation_image, variation_description, news_from_date, news_to_date ]

```

### The `locales:` section

Example:

```yaml
catalog:
  locales:
    - code: fr_FR
      currency: EUR
      store: 15
    - code: en_GB
      currency: GBP
      store: 21
```

### The `scopes:` section

Example:

```yaml
catalog:
  scopes:
    - code: europe
      store: 1
      locales:
        - code: fr_FR
          store: 1
        - code: de_DE
          store: 4
        - code: es_ES
          store: 3
        - code: it_IT
          store: 2
    - code: america
      store: 5
      locales:
        - code: en_US
          store: 5
        - code: en_CA
          store: 8
        - code: fr_CA
          store: 6
```

### The `codes-mapping:` section

Example:

```yaml
catalog:
  codes-mapping:
    - from: '"'
      to: 'inches'
    - from: 'â'
      to: 'a'
    - from: 'é'
      to: 'e'
    - from: 'è'
      to: 'e'
    - from: '/'
      to: '_'
```