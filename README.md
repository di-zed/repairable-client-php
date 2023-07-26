# Repairable PHP Client

## Repairable widget for your own project.

The library will help you easily set up the Repairable widget on your site.

###### Developed and tested on PHP 7.4 and 8.1 versions.

##### Key Features:

- The ability to easily display a GUI for a Repairable form.
- Smooth integration with various types of site engines (CMS, framework, etc.).

### Installation.

```code
composer require dized/repairable-client-php
```

### Adding Repairable form to the project.

To add the Repairable form to the project you have to have special **Public Key** and **Private Key** values.

The keys you can receive from the Repairable support.

```php
<?php
use DiZed\RepairableClient\Client\FormClient;

/* The required public key value. */
$publicKey = 'd1d8f66b2e8b2f0bfdc379b689311b78';

/* The required private key value. */
$privateKey = '3bbd640d9b2e8c9639771cd6c45d79d3';

/* Optional configuration information. */
/* Can be sent to the Repairable server to specifically customize your form. */
$config = [];

$formClient = new FormClient($publicKey, $privateKey, $config);

/* Optional configuration information. */
/* Will be added to $config when requesting a Repairable server. */
$params = [];

echo $formClient->getFormHtml($params);

```
