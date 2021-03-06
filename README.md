# FACT-Finder® Web Components for Shopware 6

[![Build status](https://github.com/FACT-Finder-Web-Components/shopware6-plugin/workflows/build/badge.svg)](https://github.com/FACT-Finder-Web-Components/shopware6-plugin/actions)

This document helps you to integrate the FACT-Finder® Web Components SDK into your Showpare Shop. In addition, it gives
a concise overview of its primary functions. The first chapter *Installation* walks you through the suggested
installation process. The second chapter *Settings* explains the customisation options in the Showpare backend. The
final chapter *Exporting Feed* describes how to use provided console command to export the feed.

- [Requirements](#requirements)
- [Installation](#installation)
- [Activating the Module](#activating-the-module)
- [Settings](#settings)
- [Export Settings](#export-settings)
    - [Price Columns Format](#price-columns-format)
- [Category Pages](#category-pages)
    - [Element Settings](#element-settings)
    - [Blocks and Elements Templates](#block-and-elements-templates)
    - [Assigning Layout to Category](#assigning-layout-to-category)
- [Exporting Feed](#exporting-feed)
    - [CLI](#cli)
    - [Exporting from Admin Panel](#exporting-from-admin-panel)
- [Web Components Integration](#web-components-integration)
    - [Web Components Documentation](#web-components-documentation)
    - [Including Scripts](#including-scripts)
    - [Communication](#communication)
    - [Templates](#templates)
    - [Full List of Implemented Web Components](#full-list-of-implemented-web-components)
- [Modification Examples](#modifications-examples)
    - [Adding New Column to Feed](#adding-new-column-to-feed)
    - [Export Fields Stored in Variants](#export-fields-stored-in-variants)
    - [Extending Specific Web Component Template](#extending-specific-web-component-template)
- [Contribute](#contribute)
- [License](#license)

## Requirements

- Shopware 6.3 or higher
- PHP version 7.3 or higher

## Installation

To install the plugin, open your terminal and run the command:

    composer require omikron/shopware6-factfinder

After plugin is successfully installed it will be visible in the extensions list. Depending on the Shopware 6 versions
the view could be different.

![Module Enabled](docs/assets/extension-disabled.png "Extension disable")

**Note:**
Plugin can only be installed only via composer, unpacking plugin source code directly into Shopware 6 project directory
will not work as the plugin contains external dependencies which also needs to be installed.

## Activating the Module

To activate the module, please use the switcher located on the left side of the row with extension info.
![Module Enabled](docs/assets/extension-enabled.png "Extension enabled")
After it is installed, its configuration is available under the three dots icon.
![Module Enabled](docs/assets/extension-config.png "Extension configuration")
All sections will be covered in the following paragraphs.

## Settings

![Main Settings](docs/assets/main-settings.png "Main settings")

This section contains a plugin configuration, which is required in order for the module to work. All fields are
self-explained. Configuration set here is used by both Web Components and during the server side communication with
FACT-Finder® instance. Credentials you will be given should be placed here. Please remember that you have to clear the
store cache in order the new applied configuration to start working.

* Server URL - FACT-Finder® instance url   
  **Note:** Server URL should contain a used protocol: (e.g. `https://`) and should end with an
  endpoint ( `fact-finder` )
* Channel - Channel you want to serve data from
* Username
* Password  
  **Note:** Module supports FACT-Finder® NG only.

### Export Settings

Settings placed in this section for customizing the export

* Select Filter attributes which should be ignored be feed exporter - product properties selected here will not be
  exported. By default, all properties are being exported.
  **Note:** Variant products properties which are part of the configuration will ignore that settings.
* Select Custom fields which should be ignored be feed exporter - custom fields selected here will not be exported. By
  default, all custom fields are being exported.
* Export prices in all currencies - if disabled, export will contain only one column `Price`. If enabled, all product
  price will be exported in all currency configured for a given sales channel.

#### Price Columns Format

If `Export prices in all currencies` setting is set to true, price columns will be exported in followed pattern
`Price_[currency ISO code]` i.e. `Price_De`

### Upload Options

Following settings are used for uploading already exported feed to a given FTP server.

**Note:** The default port setting is 21. If your FTP is listening on different port, please change it accordingly.

* Server URL
* Port
* username
* password
* Enable Automatic Import for - define import types which should be triggered. Possible types are: Suggest, Search and
  Recommendation

## Category Pages

**Note:**
This feature is experimental, and it is highly possible that it will be significantly modified in a near future.

Plugin offers a way to use FACT-Finder® Web Components on category pages using page builder Shopping Experiences. There
is two CMS blocks offered:

* Listing
    * ff-record-list
    * ff-asn + including ff-filter-cloud
    * ff-pagination
    * ff-sortbox
* Campaigns
    * ff-campaign-feedbacktext
    * ff-campaign-advisor
    * ff-campaign-redirect

![Main Settings](docs/assets/page-builder-cms-blocks.png "Page Builder CMS Blocks")
![Main Settings](docs/assets/page-builder-listing.png "Page Builder CMS Preview")

### Element Settings

Each of the element of given block contains dedicated configuration which allows to configure them without necessity of adding hardcoded values in the templates.
![Main Settings](docs/assets/page-builder-element-config.png "Page Builder CMS Element Configuration")

If you do not want to render a specific element, just change the `subscribe` option to `false`. This will make element will not subscribe to the
FACT-Finder® response, hence they will not render any HTML.

### Blocks and Elements Templates

Each of the block and element has it own templates which could be found, according to the Shopware 6 convention:

* for blocks - `Resources/views/storefront/block`
* for element - `Resources/views/storefront/element`
  which could be extended using Shopware `sw_extends` tag

### Assigning Layout to Category

Once the page layout is done, you need to assign layout to selected categories.
![Main Settings](docs/assets/page-builder-assigning.png "Page Builder Page Assigning")

We strongly recommend not creating many layouts as currently there's still only few possibilities offered anyway.
Future development will bring more blocks and elements will be provided here.

**Note:**
Offered Cms Blocks and Elements are designed to work on pages of type `LandingPage`.
There is a type `CategoryPage` but the builtin validation will not allow saving that prepared page, unless it contains at least one default Product Listing Block.
The block `FACTFinder Web Components Listing` is unfortunately not taken into account.

## Exporting Feed

### CLI
Feed export is available in the Shopware CLI application. You can run it by executing:

    php [SHOPWARE_ROOT]/bin/console factfinder:export:products

The command can be run with an optional argument indicating the sales channel ID that you are targeting. The ID is an
string value.

    php [SHOPWARE_ROOT]/bin/console factfinder:export:products SALES_CHANNEL_ID

If a specific language needs to be specified, theres is a second argument which allows that.

    php [SHOPWARE_ROOT]/bin/console factfinder:export:products SALES_CHANNEL_ID LANGUGAGE_ID

There are two additional options:

* `-u` uploads the feed to the configured FTP server after feed is generated.
* `-i` runs the FACT-Finder® import with previously uploaded feed  
  **Note:** This option works only in a combination with `-u`
  
by default export outputs data in the STDOUT. It could be easily redirected using Linux way of redirecting output.

    php [SHOPWARE_ROOT]/bin/console factfinder:export:products > export.csv

### Exporting from Admin Panel

There is a possibility to run whole integration: generating feed, uploading it to FTP server and trigger FACT-Finder® import.
A dedicated form can be found under `Extensions` section
![Admin Panel Export](docs/assets/admin-panel-export.png "Admin Panel Export")


![Admin Panel Export Form](docs/assets/admin-panel-export-form.png "Admin Panel Export Form")
Select fields allows you to select sales channel and languague parameter for which an integration shall be run.

`Run Integration` Send a message to a bus which then might be consumed automatically by an admin worker (if enabled)
or by CLI worker. More information about messaging you can find in official Shopware [documentation](https://developer.shopware.com/docs/guides/hosting/infrastructure/message-queue)


## Web Components Integration

**Note:** Please note that plugin right now is supporting only classic storefronts, rendered using Twig templating
system.

### Web Components Documentation

Full FACT-Finder® Web Components documentation you can found [here](https://web-components.fact-finder.de/api/4.x/ff-communication)


### Including Scripts

The module is shipped with script including FACT-Finder® Web Components.
[Including Scripts](https://web-components.fact-finder.de/documentation/4.x/include-scripts) step is implemented in the
module. No additional action is required.

* Resources/public/ff-web-components/vendor/custom-elements-es5-adapter.js
* Resources/public/ff-web-components/vendor/webcomponents-loader.js
* Resources/public/ff-web-components/ff-web-components/bundle.js

All these files are included in `Resources/views/storefront/layout/meta.html.twig`. That file extends the default
theme [meta.html.twig](https://github.com/shopware/platform/blob/trunk/src/Storefront/Resources/views/storefront/layout/meta.html.twig)
.
**Note:** Including these scripts is obligatory for Web Components to work. Make sure you include it or if your
storefront does not use that file, include all scripts in mentioned order on your own.

### Communication

Main configuration element `ff-communication` is added in file `src/Resources/views/storefront/base.html.twig`
. Same as with `meta.twig.html`, it extends
the [base.html.twig](https://github.com/shopware/platform/blob/trunk/src/Storefront/Resources/views/storefront/base.html.twig)
file defined in default Storefront. This element is populated automatically with the data, configured in module
configuration.

**Note:** If your theme doesn't extend the default Storefront, make sure you implement `ff-communication` element as it
is mandatory and FACT-Finder® Web Components will not work without it.

### Templates

Plugin templates could be found in `Resources/views/storefront/`. Just as with the previous sections, all templates are
extending default Storefront wherever it is possible. You can use these templates if you are extending it
using `sw_extends` which offers a support for a multi inheritance.

### Tracking
Plugin offers a following way of tracking customer actions
 * login - logged automatically via ff-communication element when `user-id` is set
 * click on product - implemented using ff-record template [directives](https://web-components.fact-finder.de/documentation/4.x/tracking-guide) (see Click Tracking)
 * add to cart - implemented in a js [plugin](src/Resources/app/storefront/src/plugin/tracking.plugin.js)
 * purchase - implemented using [ff-checkout-tracking element](https://web-components.fact-finder.de/documentation/4.x/tracking-guide) (see Checkout Tracking)

### Full List of Implemented Web Components
Plugin implements a list of given Web Components:

* Page Header
    * ff-communication
    * ff-searchbox
    * ff-suggest

* Search Result & Navigation Page
    * ff-record-list
    * ff-asn
    * ff-sortbox
    * ff-paging
    * ff-filter-cloud
    * ff-campaign-advisor
    * ff-campaign-feedbacktext
    * ff-campaign-redirect 

* Product Detail Page
    * ff-campaign-product
    * ff-campaign-pushed-products
    * ff-recommendation
    * ff-similar-products

* Checkout Success Page
    * ff-checkout-tracking


## Modifications Examples

### Adding New Column to Feed

The standard feed contains all data FACT-Finder® requires to work. However, you may want to export additional
information which is relevant for your project and not part of a default Shopware installation. This section shows you
how to extend the feed with additional column.

Start with creating field provider - a class
implementing `Omikron\FactFinder\Shopware6\Export\FieldFieldInterface` which will be used to export your data.

```php
interface FieldInterface
{
    public function getName(): string;

    public function getValue(Product $product): string;
}
```

The method `getValue` contains your field logic and receives the article detail currently being exported.

```php
class CustomColumn implements FieldInterface
{
     public function getName(): string
     {
        return 'MyColumnName'; // Will be used as column header in the CSV feed
     }

    public function getValue(Product $product): string
    {
        // Implement logic to fetch and transform data for a given article detail  
    }
}
```

Once your additional column is defined, register it as a service using Symfony DI (you can find more information
[here][1]) and set them to be [auto-configured][2]. By doing this, your fields will be tagged
as `factfinder.export.field` and can be picked up automatically. Of course, autoconfiguration is
just a convenience we offer, you can still assign the tag to your service manually.

### Export Fields Stored in Variants
By default, only these fields are exported from variants:
    * CustomFields
    * ImageUrl
If your setup requires more field to be exported from variants, you need to tag the desired Field with a `factfinder.export.variant_field` tag in `services.xml file`.

    <service id="Omikron\FactFinder\Shopware6\Export\Field\CustomFields">
        <tag name="factfinder.export.variant_field"/>
    </service>
However,exporting data from variants may require additional joins during the data fetch.
To define custom association (join) set them in under a parameter of that name

    <parameter key="factfinder.export.associations" type="collection">
        <parameter key="variant_cover">children.cover</parameter>
    </parameter>

All parameters in that collection will be used in a following expression

    $criteria->addAssociation($association);
where `$association` would be a `children.cover` in that example.

**Note:**
Please note that adding more and more associations will have an impact on overall export performance.

### Extending Specific Web Component Template
All the Web Components rendering HTML, contains it inside the Twig block.
If you need to change it just extend the parent file and override the block which contains the template.
For example if you need to define own template for the ff-record element which is responsible for rendering record tiles on search result page

    {% sw_extends '@Parent/storefront/components/factfinder/record-list.html.twig' %}

    {% block component_factfinder_record %}
        {# here comes the template #}
    {% endblock %}

As we see we use `sw_extends` to extend the parent file and then define the re-define `component_factfinder_record` block.
It is contained in the plugin `Resources/views/storefront/components/factfinder/record-list.html.twig` file.
With this code you override only this block and rest of the parent file HTML will be inherited.

**Note:**
Template in that section stands for the HTML rendered by Web Components, not the `.html.twig` file


## Contribute

We welcome contribution! For more information, click [here](.github/CONTRIBUTING.md)

## License

FACT-Finder® Web Components License. For more information see the [LICENSE](LICENSE) file.

[1]: https://developer.shopware.com/docs/guides/plugins/plugins/plugin-fundamentals/dependency-injection

[2]: https://symfony.com/doc/3.3/service_container.html#the-autoconfigure-option

