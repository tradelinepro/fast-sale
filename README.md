# TradelineproFastSale

## Description

Fast sale

Based on Shopware ~6.5.0

The “Fast Sale” function offers users two additional, quick ordering options:

* Quick search for product and article number with direct option to add the product to the shopping cart
* Import product lists in CSV or XLSX format

## Plugin Configuration

* This setting allows you to activate or deactivate the plugin in the frontend. If this setting is not active, the plugin's functions are not visible.
* This setting allows you to configure whether or not product images are visible in the results list for the quick order search. If you activate this setting, the matching products will be displayed without an image.
* A sample file for the shopping cart import can be stored in the settings, in CSV or XLSX format. This file is available for the customer to download in the frontend so that they can create their own import file in the correct format.

## How To Use:

### Quick order search in the frontend
The quick order search in the frontend can be found in the customer account under the entry “Quick order search”, as well as via a rocket icon in the header. There you will find an input field in which you can search for products. The search terms that can be used here are product name, product number and EAN. As soon as you enter a search term, the search is started automatically:

The results are presented very condensed and allow you to select the quantity directly. You can put the desired products into your shopping cart straight away.

### Shopping Cart Import
The shopping cart import is located in the customer account under the entry “Shopping cart import”. Here you have the option to upload a CSV or XLSX file, which you can then import. The importer processes the products from the file and puts them in the shopping cart in the stored quantity.

The import automatically detects errors in the files and can provide appropriate assistance. For example, empty lines are mentioned, or lines with unknown products. All other correct lines are still imported and the products are put in the shopping cart.

The example files that have been stored in the plugin configuration can also be found here. The user can download these and thus has a template for their own file. They only have to enter the desired products with the corresponding quantities.


## Setup & Development

### Install
```
composer require tradelinepro/fast-sale
```

### Setup
```
bin/console plugin:install --activate TradelineproFastSale
```

## Compatibility

| Plugin version | Shopware version | PHP version | 
|----------------|------------------|-------------|
| `0.9.*`        | ~6.5.0           | 8.2         |


### Licence

The MIT License (MIT). Please see License File for more information.

## Links/Reference (optional)

* [Documentation][WIKI]
* [Shopware 6.5][SW]

## Known Issues

## Troubleshooting


[WIKI]:https://docs.tradelinepro.de/schnellbestellung
[SW]:https://docs.shopware.com/en/shopware-6-en/getting-started
