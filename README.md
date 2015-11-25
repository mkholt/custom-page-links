# custom-page-links

A WordPress plugin to set a custom list of links on a page.
The links are listed using a ShortCode.

## Description

The Custom Page Links plugin allows you to specify a list of links to be shown on a specific page.
This allows the user to easily specify links that, grouped together, can then be listed on the page.

The plugin was created from the need to add a list of links to a widget, which is not easily achievable using existing functionality and plugins.

When a link is clicked, the user is taken through a landing page to the end location.
Currently, no tracking is done on this landing page. In the future some tracking is planned, but only to be stored locally, and never to be shared with 3rd parties.

Each link specified includes an image, which is shown next to the link in the listing.

The editor allows easy linking to other pages / posts on the WordPress installation, as well as easy linking to media.

## Installation

1. Upload the contents of this directory to the `/wp-content/plugins/custom-page-links` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Start adding links through each page.
4. Wherever you want the list of links, use the ShortCode `[cpl]`

## Frequently Asked Questions

### How do I insert the links?

Use the ShortCode `[cpl]` wherever you want the list of links.
Note that the link will be wrapped in a *div*, with the class `cpl-link`.

## Changelog

### 1.1
* Cleaned up the code.
* Added support for translation.

Bug fixes:

* [Issue #1](https://github.com/mkholt/custom-page-links/issues/1)

Translations added:

* Danish (da_DK)

### 1.0.1
* Updated to adhere to WordPress style guide for plugins.
  Removed usage of PHP short tags, including `<?=` shorthand.

### 1.0
* Initial version. Supports adding / editing / removing links.

## Upgrade Notice

### 1.1
* The code. It is cleaner.
* Bug fixes

### 1.0.1
* Adheres to the WordPress requirements for Plugins. No functional changes are done.

### 1.0
* Initial version.