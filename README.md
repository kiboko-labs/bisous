Akeneo Fixtures Generation Toolbox
==================================

This toolbox helps generating CSV fixtures consumed by Akeneo's InstallerBundle from Magento 1.9CE or 1.14EE catalog data.


Supported attribute types 
---

| Magento Type | Akeneo Type        | Not Localizable, not Scopable | Localizable, not Scopable | Not Localizable, Scopable | Localizable, Scopable |
| ------------ | ------------------ | --- | --- | --- | --- |
| Gallery      | Asset Collection   | ❌ | ❌ | ❌ | ❌ |
| Datetime     | Date               | ✅ | ❌ | ❌ | ✅ |
| File         | File               | ❌ | ❌ | ❌ | ❌ |
| SKU          | Identifier         | ✅ | ❌ | ❌ | ❌ |
| Image        | Image              | ✅ | ❌ | ❌ | ✅ |
| Decimal      | Metric             | ❌ | ❌ | ❌ | ❌ |
| Multiselect  | Multi select       | ❌ | ❌ | ❌ | ❌ |
| Select       | Simple select      | ✅ | ❌ | ✅ | ✅ |
| Number       | Number             | ❌ | ❌ | ❌ | ❌ |
| Price        | Price              | ❌ | ❌ | ❌ | ❌ |
| Status       | Simple select      | ✅ | ❌ | ❌ | ❌ |
| -            | Ref. multi select  | ❌ | ❌ | ❌ | ❌ |
| -            | Ref. simple select | ❌ | ❌ | ❌ | ❌ |
| Text         | Text area          | ✅ | ❌ | ❌ | ✅ |
| Varchar      | Text               | ✅ | ❌ | ❌ | ✅ |
| Visibility   | Simple select      | ✅ | ❌ | ❌ | ❌ |
| YesNo        | Yes No             | ❌ | ❌ | ❌ | ❌ |
