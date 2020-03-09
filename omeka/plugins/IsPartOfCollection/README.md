# IsPartOfCollection

A plugin for Omeka designed to work with the Dublin Core Extended plugin. Whenever an item is saved, the plugin will update the item's Dublin Core "Is Part Of" metadata with the identifier of collection associated.

WARNING: By default this plugin will OVERWRITE any element texts in 'Is Part Of'. Anytime the collection is changed, the 'Is Part Of' fields will likewise be changed. It is recommended that this be used in combination with the Hide Elements plugin so that the 'Is Part Of' fields are hidden in editing to avoid loss of data.
