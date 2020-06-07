# Admin Menu Design Plugin

Allows you to organize the Admin Navigation Main Menu entries by sections.


## Installation

1. Copy the AdminMenuDesign folder into the "plugins" folder of the root Omeka installation. (see [Installing a Plugin](https://omeka.org/classic/docs/Admin/Adding_and_Managing_Plugins/))

2. Add your custom sections in "sections.php".
    - Example: 

```
$sections = array(
                'Section 1' => array(),
                'Section 2' => array(),
                'Section 3' => array(),
                'Section 4' => array(),
                'Section 5' => array(),
                # ....
            );
```

3. In the Omeka administrative interface, click on the "Settings" button at the top right of the screen, go to the "Plugins" tab, and click the "Install" button next to the listing for "Admin Navigation Main Menu Design ".

## Usage

1. In the Omeka administrative interface, click on the "Settings" button at the top right of the screen, go to the "Plugins" tab, and click the "Configure" button next to the listing for "Admin Navigation Main Menu Design ".

2. Match the plugins you want with the sections and click on the "Save Changes button".

![Config page](./dm-img/config.png)

## View

![View](./dm-img/view.gif)

