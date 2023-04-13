# Changelog
## [v4.2.3] - 2023.04.13
### Fix
- SSR
  - Cache field roles
- Fix problem with new map due to SecurityExtension
- Export
  - Fix problem with PriceCurrency during export
- Security patch

## [v4.2.2] - 2023.03.16
### Add
- Proxy
### Change
- Upgrade Web Components version to v4.2.6
### Fix
- Tracking
  - Fix tracking when `Add To Cart` button has been clicked

## [v4.2.1] - 2023.03.10
### Fix
- Category
  - Fix sorting - removed redundant parameters `order` and `name`
### Change
- Upgrade Web Components version to v4.2.5

## [v4.2.0] - 2023.02.23
### Fix
- SSR
  - fixed problem with not visible search results when SSR enabled
### Change
- Upgrade Web Components version to v4.2.4
- Export
  - Export only active categories
### Add
- Redirect mapping for selected queries

## [v4.1.2] - 2023.02.06
### Fix
- Category
  - add missing attribute `category-page` in `ff-communication` element for Category Page
### Change
- SSR
  - replace FF_SEARCH_RESULT only if no real result will be rendered
### Add
- Tracking
    - Add option to select scenario when `Add To Cart button` has been clicked
## [v4.1.1] - 2023.02.02
### Add
- Tracking
    - Add option to select scenario when `Add To Cart button` has been clicked
### Change
- SSR
  - read fieldRoles using service and set default configuration as backup
  - set SalesChannelId
  - display navigation results when HTTP cache is enabled
  - Fixed Uncaught TypeError: can't access property "find", t.path is undefined
  - Replace FF_SEARCH_RESULT with empty string when page don't have to SSR
  - add support for Mustache template engine
  - set RequestStack as optional to avid fails for CLI-Commands
  - combine masterValues with variantValues in search results
  - prevent error when invalid chanel is set
  - pass all parameters for category pages and search result
  - translate query parameter "p" to "page"
  - add SEO urls and link-rel-prev/next for pagination
### Fix
- SSR
  - BeforeSendResponseEventSubscriber - fix Call to a member function getId() on null
  - solve problem with empty filterAttributes in export
- Export
  - Fix problem with not working export from admin panel

## [v4.1.0] - 2023.01.05
### Add
- Add option to switch between Api Version
- Add export cache which decrease export time.

### Fix
- ClientBuilderConfigurator
  - Fix problem with missing server URL when SDK is activated for first time

## [v4.0.4] - 2022.12.02
### Fix
- Fix problem with missing page in ConfigurationSubscriber

## [v4.0.3] - 2022.11.29
### Add
- Server-Side Rendering (SSR)

## [v4.0.2] - 2022.11.22
### Fix
- Fix error during SDK activation
- Category
    - Fix search-immediate behaviour on Category Page

## [v4.0.1] - 2022.11.21
### Fix
- Fix error during SDK activation

## [v4.0.0] - 2022.11.15
### BREAKING
- `Omikron\FactFinder\Shopware6\Command\DataExportCommand.php`
    - change output from method `getBaseTypeEntityMap`. 

- `Omikron\FactFinder\Shopware6\Export\Data\Entity\CmsPageEntity.php`
    - rename class name from `CategoryEntity` to `CmsPageEntity`

- `Omikron\FactFinder\Shopware6\Export\Field\Brand.php`
    - change output from method `getCompatibleEntityTypes`.

- `Omikron\FactFinder\Shopware6\Export\Field\CategoryPath.php`
    - change output from method `getCompatibleEntityTypes`.

- `Omikron\FactFinder\Shopware6\Export\Field\CustomFields.php`
    - change output from method `getCompatibleEntityTypes`.

- `Omikron\FactFinder\Shopware6\Export\Field\Deeplink.php`
    - change output from method `getCompatibleEntityTypes`.

- `Omikron\FactFinder\Shopware6\Export\Field\Description.php`
    - change output from method `getCompatibleEntityTypes`.

- `Omikron\FactFinder\Shopware6\Export\Field\FilterAttributes.php`
    - change output from method `getCompatibleKEntityTypes`.

- `Omikron\FactFinder\Shopware6\Export\Field\ImageUrl.php`
    - change output from method `getCompatibleEntityTypes`.

- `Omikron\FactFinder\Shopware6\Export\Field\Layout.php`
    - change output from method `getCompatibleEntityTypes`.

- `Omikron\FactFinder\Shopware6\Export\Field\Price.php`
    - change output from method `getCompatibleEntityTypes`.

- `Omikron\FactFinder\Shopware6\Export\Field\PriceCurrency.php`
    - change output from method `getCompatibleEntityTypes`.

- `Omikron\FactFinder\Shopware6\Export\ExportBrands.php`
    - remove method `getCoveredEntityType`.

- `Omikron\FactFinder\Shopware6\Export\ExportCmsPages.php`
    - rename class name from `ExportCategories` to `ExportCmsPages`
    - change output from method `getProducedExportEntityType`.
    - remove method `getCoveredEntityType`.

- `Omikron\FactFinder\Shopware6\Export\ExportInterface.php`
    - remove method `getCoveredEntityType`.

- `Omikron\FactFinder\Shopware6\Export\ExportProducts.php`
    - remove method `getCoveredEntityType`.

### Change
- Upgrade Web Components version to v4.2.3
- extended the cookie consent manager with cookies added in release [v3.3.2](https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v3.3.2)

### Add
 - category export feed for category, enrich category suggestion with deeplinks

### Fix
 - ff-communication
   - add configuration parameters when error page 404
 - removed `query` parameter from pages different than search result

## [v3.3.2] - 2022.10.24
### Change
- Upgrade Web Components version to v4.2.2
- introduce new way of user login tracking event
## [v3.3.1] - 2022.08.01
### Improve
 - Product Detail Page
  - Initialize sliders that display similar, recommendation or campaign products only after they are fully rendered 
 - SearchResult Page, Category Page
  - add facet name to the `ff-filter-cloud` template
- SearchResult Page
  - Add "You search for" feature 
- ff-communication
 - added a block that wraps the element, that allows extending in child blocks

## [v3.3.0] - 2022.07.06
### Add
 - Page Builder
  - add ff-campaign-pushed-products support to the campaigns block
  - add flag configuration for ff-campaign-feedbacktext
 - SearchResult Page
  - add ff-campaign-pushed-products to default template
  
### Change
 - src/Resources/views/storefront/components/factfinder/campaign-pushed-products.html.twig
  - template takes new argument recordListTemplate set by default to src/Resources/views/storefront/components/factfinder/record-list.html.twig  
   
### Fix
 - Page Builder
  - fix enableCampaignFeedbackText,enableCampaignAdvisor and enableCampaignRedirect are enabled by default but does not show their configuration  
 - SearchResult Page, Category Page
  - fix filters are not showing off on mobile mode
     
## [v3.2.0] - 2022.05.30
### Add
 - Configuration
  - Add new Update Field Roles functionality. More information in documentation

### Change
 - upgrade Web Components to version [4.0.10](https://github.com/FACT-Finder-Web-Components/ff-web-components/releases/tag/4.0.10)
 
## [v3.1.1] - 2022.04.15
### Fix
 - Export
  - fix ImageUrl has not been exported for variants
 - Upload
  - fix uploaded feed file does not contain type of feed in its name which cause feeds of different type overrides each other  
    
## [v3.1.0] - 2022.03.28
### Add 
 - Export
  - Added new field provider `Omikron\FactFinder\Shopware6\Export\Field\Layout` applicable to CMS Export
  
### Change
 - Category
  - hide `<ff-asn-group>` responsible for rendering category filters as this breaks the Web Components navigation mode.
   User should navigate between categories using shop main navigation
 
### Fix
 - Category, SearchResult
  - fix `ff-asn-group` does not collapse if user clicks outside it or on another `ff-asn-group`
    
## [v3.0.1] - 2022.03.10
### Fix
 - `Omikron\FactFinder\Shopware6\Subscriber\CategoryView`
  - fix category path is not encoded correctly 

### Change
 - upgrade Web Components to version [4.0.8](https://github.com/FACT-Finder-Web-Components/ff-web-components/releases/tag/4.0.8)
 
## [v3.0.0] - 2022.01.25
### BREAKING
 - `Omikron\FactFinder\Shopware6\Subscriber\CategoryView`
    - rename class name from CategoryView  to CategoryPageSubscriber
    - change argument name from `$initialNavigationParams` to `$categoryPageAddParams`
    
 - `Omikron\FactFinder\Shopware6\Subscriber\ConfigurationSubscriber`
    - add new constructor argument string[] $configurationAddParams
    - `$initialNavigationParams` to `$categoryPageAddParams`
    
 - `src/Resources/services.xml`
  - rename `factfinder.navigation.initial_params` to `factfinder.category_page.add_params`
  - add new parameters collection `factfinder.configuration.add_params` which is bound to argument name $configurationAddParams
  
### Add
 - introduce setting `add-params` for ConfigurationSubscriber. Arguments from both ConfigurationSubscriber and CategoryPageSubscriber are merged

### Change
 - Upgrade Web Components to version 4.0.6
  
## [v2.2.2] - 2021.12.22
## Add
 - Category
   - Implement `ff-communication/category-page` attribute which serves to correctly filter products on category pages 
 - Export
   - Call SEO URL indexer to reindex when no SEO URL is available for given product during the export 

 ### Change
  - Upgrade Web Components to version 4.0.5
  
### Fix
  - Export
    - Fix Entity types custom fields are causing export to throw an error while reading from options of the field 
    - Fix Entity multi selection custom fields are causing export while formatting output value
  - Tracking
    - Fix cart tracking is not sending mandatory field id if field roles are not correctly set 

## [v2.2.1] - 2021.11.08
### Add 
 - Upload
    - Added Root Directory Field which allows to specify where uploaded file has to be saved

### Fix
  - Upload
    - Fix Type Error occurs when upload directory does not exist
  - Category
    - Fix force disable search-immediate on home page wasn't work as expected
   
## [v2.2.0] - 2021.10.25
### Add 
 - Upload
    - Added SFTP support

### Change
 - Category
    - Force search-immediate="false" on Home Page

### Fix
  - Configuration
    - Configuration is not taking sales channel id argument into account when load the FACT-Finder channel
  - Setup
    - Fix is not removing its Custom Field Sets

## [v2.1.1] - 2021.10.05
### Fix
 - Export
  - Price column is not exported
  
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

[v4.2.3]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v4.2.3
[v4.2.2]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v4.2.2
[v4.2.1]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v4.2.1
[v4.2.0]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v4.2.0
[v4.1.2]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v4.1.2
[v4.1.1]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v4.1.1
[v4.1.0]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v4.1.0
[v4.0.4]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v4.0.4
[v4.0.3]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v4.0.3
[v4.0.2]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v4.0.2
[v4.0.1]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v4.0.1
[v4.0.0]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v4.0.0
[v3.3.2]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v3.3.2
[v3.3.1]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v3.3.1
[v3.3.0]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v3.3.0
[v3.2.3]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v3.2.3
[v3.2.3]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v3.2.3
[v3.2.0]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v3.2.0
[v3.1.1]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v3.1.1
[v3.1.0]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v3.1.0
[v3.0.0]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v3.0.0
[v2.2.2]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v2.2.2
[v2.2.1]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v2.2.1
[v2.2.0]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v2.2.0
[v2.1.1]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v2.1.1
[v2.1.0]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v2.1.0
[v2.0.0]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v2.0.0
[v1.0.1]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v1.0.1
[v1.0.0]: https://github.com/FACT-Finder-Web-Components/shopware6-plugin/releases/tag/v1.0.0

