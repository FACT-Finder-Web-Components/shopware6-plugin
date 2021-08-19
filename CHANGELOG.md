# Changelog
## [v2.0.0] - Unreleased
### Breaking
 - IMPORTANT! Drop Shopware 6.3 compatibility
 - IMPORTANT! Drop PHP 7.3 compatibility
 - Following Public Interfaces has been changed. 
    * Omikron\FactFinder\Shopware6\Export\Field\FieldInterface
        - `getValue` accepts now a object of type Shopware\Core\Framework\DataAbstractionLayer\Entity (was Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity)
        - added method `getCompatibleEntityTypes` which expect to return an array of classes extending Shopware\Core\Framework\DataAbstractionLayer\Entity a given class instance can be applied to
    * Omikron\FactFinder\Shopware6\Export\ExportInterface
        -  added method `getCoveredEntityType` which expect to return a class extending Shopware\Core\Framework\DataAbstractionLayer\Entity which given Exported will be exporting
        -  added method `getProducedExportEntityType` which expect to return a class that implements Omikron\FactFinder\Shopware6\Export\Data\ExportEntityInterface - a exportable version of Entity 
       
### Add
- Add Omikron\FactFinder\Shopware6\Export\Data\Factory\FactoryInterface which should be implemented by class which will instantiate object implementing
- Introduce Category Export
- Introduce Brand Export
- Add callback and dom-updated fields to ASN and RecordList CMS Elements which allows to pass inject javascript interacting with Web Components from the PageBuilder
### Fix
 - Fix Export Settings
 - Fix communication parameters rendering by applying a default filter with empty array 

## [v1.1.0] - 2021.07.08
### Add
- Add form for run Feed export from the admin panel instead of command line

### Fix
  - Fix FTP upload does not take the channel name from correct sales channel context
  - Event data coming from searchbox element is not URLencoded before redirecting to search result page

## [v1.0.0] - 2021.06.29
Initial module release. Includes Web Components v4.0.3

[v1.0.1]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v1.0.1
[v1.0.0]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v1.0.0

