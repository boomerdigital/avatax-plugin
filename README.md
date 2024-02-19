
# Avalara Avatax for Marketplaces

  This plugin will calculate the product tax based upon the vendor’s location. Basically, plugin will interact with woocommerce and third party plugin (Dokan). The plugin’s functionality will works as per settings defined in “Custom Avatax” page menu under Admin panel.


## Requirements
+ Wordpress version 4.0
+ PHP version 5.6 or Higher
+ [Woocomerce](https://wordpress.org/plugins/woocommerce/#description)
+ [Avalara account’s “Account number” and “License key”](https://www.avalara.com/us/en/index.html)

## Installation
+ Upload \"test-plugin.php\" to the \"/wp-content/plugins/\" directory
+ Activate the plugin through the \"Plugins\" menu in WordPress.


## Configuration
+ Click on “settings” under woocommerce tab in LSB.
+ Click on Tax  tab  and then click on “Custom Avatax”.

## Features

### _Tax calculation_
**Enable\Disable:** if this checkbox is checked then plugin will get all vendor from system and it will create company for each vendor(IF not already created). After creating company, plugin will hit transaction API(“/api/v2/transactions/create”) . This API will calculate the tax based upon vendor’s location and update tax rate for all products corresponding to  each vendor’s into database.
### _Record Calculations_
It will hit Transaction create API  when order status will be completed.
### _Commit Transactions_
It will hit transaction commit API after “Recorded Calculation”.
### _Supported Locations_
List of supported countries.
### _Company Code_
Company code  set in Avalara Account will be display here.
### _Origin Address_
Here we’ll show the address saved in Avalara account.

### _Default Product Tax Code_
If any product doesn’t have tax code, then this tax code will be applied in API’s.
### Default Shipping Tax Code
If any product doesn’t have shipping  tax code, then this tax code will be applied in API’s.

### _Cart Calculation_

   


    
## Contributors

Let people know how they can dive into the project, include important links to things like issue trackers, irc, twitter accounts if applicable.

## License

A short snippet describing the license ([MIT](http://opensource.org/licenses/mit-license.php), [Apache](http://opensource.org/licenses/Apache-2.0), etc.)
