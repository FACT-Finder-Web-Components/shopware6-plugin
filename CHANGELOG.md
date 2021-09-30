# Changelog
## [v2.1.0] - 2021.09.30
### Add
 - Category
    - Add `Disable ff-communication/search-immediate` field, which prevents setting `search-immediate=true` in `ff-communication` on selected category page

### Fix
 - Export
    - Custom export types, defined by user, are not visible in the export type selector in export form
    - Fix "Call getMedia On null in `ImageProvider.php`"
 - Page Builder
    - infinite-scrolling is always set to true
    - infinite-debounce-delay is always set to true with incorrect value
 - Configuration
  - Fix Category CustomField `Include in FACT-Finder® CMS Export` is always rendered as selected

## [v2.0.0] - 2021.08.26
### Breaking
 - IMPORTANT! Drop Shopware 6.3 compatibility
 - IMPORTANT! Drop PHP 7.3 compatibility
 - Export:
   - `Omikron\FactFinder\Shopware6\Export\Field\FieldInterface`
        - changed `getValue` method which accepts now a object of type `Shopware\Core\Framework\DataAbstractionLayer\Entity` (was `Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity`)
        - adds method `getCompatibleEntityTypes` which expect to return an array of classes extending `Shopware\Core\Framework\DataAbstractionLayer\Entity` a given Field can be applied to
    * `Omikron\FactFinder\Shopware6\Export\ExportInterface`
        -  adds method `getCoveredEntityType` which expect to return a class extending `Shopware\Core\Framework\DataAbstractionLayer\Entity` which given Exported can work with
        -  adds method `getProducedExportEntityType` which expect to return a class that implements `Omikron\FactFinder\Shopware6\Export\Data\ExportEntityInterface` - a exportable version of Entity 
-  CLI
   - Remove `Omikron\FactFinder\Shopware6\Command\ProductExportCommand` (`factfinder:export:products`) and replace them with universal `Omikron\FactFinder\Shopware6\Command\DataExportCommand` (`factfinder:data:export`)
   
### Add
- Shopping Experiences:
   -  Add `callback` and `dom-updated` fields to ASN and RecordList CMS Elements which allows to pass inject custom scripts
- Export:
   - Add `Omikron\FactFinder\Shopware6\Export\Data\Factory\FactoryInterface` which should be implemented by class which will instantiate object implementing 
- CLI
   - Introduce CMS Export
   - Introduce Manufacturers Export

### Fix
 - Fix Export Settings might cause an error on fresh installation of plugin
 - Fix ASN styles which might have affected mobile viewport
 - Fix communication parameters rendering by applying a default filter with empty array 

## [v1.1.0] - 2021.07.08
### Add
- Add form for run Feed export from the admin panel instead of command line

### Fix
  - Fix FTP upload does not take the channel name from correct sales channel context
  - Event data coming from searchbox element is not URLencoded before redirecting to search result page

## [v1.0.0] - 2021.06.29
Initial module release. Includes Web Components v4.0.3

[v2.1.0]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v2.1.0
[v2.0.0]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v2.0.0
[v1.0.1]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v1.0.1
[v1.0.0]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v1.0.0

