# Is Part Of Collection Plugin

An Omeka plugin designed to work with the [Dublin Core Extended](https://github.com/omeka/plugin-DublinCoreExtended) plugin. Whenever an item is saved, the plugin will update the item's Dublin Core "Is Part Of" metadata with the identifier of collection associated.

**WARNING**: By default this plugin will OVERWRITE any element texts in 'Is Part Of'. Anytime the collection is changed, the 'Is Part Of' fields will likewise be changed. It is recommended that this be used in combination with the Hide Elements plugin so that the 'Is Part Of' fields are hidden in editing to avoid loss of data.

-----

## Installation

1. Copy the IsPartOfCollection folder into the "plugins" folder of the root Omeka installation. (see [Installing a Plugin](https://omeka.org/classic/docs/Admin/Adding_and_Managing_Plugins/))

2. In the Omeka administrative interface, click on the "Settings" button at the top right of the screen, go to the "Plugins" tab, and click the "Install" button next to the listing for "Is Part Of Collection".
