CSV Import+ (plugin for Omeka)
==============================


[CSV Import+] is a plugin for [Omeka] that allows users to import or update
items from a simple CSV (comma separated values) file, and then map the CSV
column data to multiple elements, files, and/or tags. Each row in the file
represents metadata for a single item.
This plugin is useful for exporting data from one database and importing that
data into an Omeka site.

This fork adds some improvments:
- more options for import;
- import of metadata of collections and files;
- update of collections, items and files;
- batch edit form to reorder files by filename;
- import extra data of records that are not managed via standard elements but
via specific tables.

It can be installed simultaneously with the upstream [CSV Import].

The similar tool [XML Import] can be useful too, depending on your types of
data.


Installation
------------

Uncompress files and rename plugin folder "CsvImportPlus".

Then install it like any other Omeka plugin and follow the config instructions.

If you want to use local files inside the file system, the allowed base path or
a parent should be defined before in the file "security.ini" of the plugin.

Set the proper settings in config.ini like so:

```
plugins.CsvImportPlus.columnDelimiter = ","
plugins.CsvImportPlus.enclosure = '"'
plugins.CsvImportPlus.memoryLimit = "128M"
plugins.CsvImportPlus.requiredExtension = "txt"
plugins.CsvImportPlus.requiredMimeType = "text/csv"
plugins.CsvImportPlus.maxFileSize = "10M"
plugins.CsvImportPlus.fileDestination = "/tmp"
plugins.CsvImportPlus.batchSize = "1000"
```

All of the above settings are optional.  If not given, [CSV Import+] uses the
following default values:

```
memoryLimit = current script limit
requiredExtension = "txt" or "csv"
requiredMimeType = "text/csv"
maxFileSize = current system upload limit
fileDestination = current system temporary dir (via sys_get_temp_dir())
batchSize = 0 (no batching)
```

Set a high memory limit to avoid memory allocation issues with imports.
Examples include 128M, 1G, and -1. This will set PHP's memory_limit setting
directly, see PHP's documentation for more info on formatting this number. Be
advised that many web hosts set a maximum memory limit, so this setting may be
ignored if it exceeds the maximum allowable limit. Check with your web host for
more information.

Note that 'maxFileSize' will not affect 'post_max_size' or 'upload_max_filesize'
as is set in 'php.ini'. Having a maxFileSize that exceeds either will still
result in errors that prevent the file upload.

'batchSize': Setting for advanced users.  If you find that your long-running
imports are using too much memory or otherwise hogging system resources, set
this value to split your import into multiple jobs based on the number of CSV
rows to process per job.

For example, if you have a CSV with 150000 rows, setting a batchSize of 5000
would cause the import to be split up over 30 separate jobs.
Note that these jobs run sequentially based on the results of prior jobs,
meaning that the import cannot be parallelized.  The first job will import
5000 rows and then spawn the next job, and so on until the import is completed.

_Important_

On some servers, in particular with shared hosts, an option should be changed in
the application/config/config.ini file:

```
jobs.dispatcher.longRunning = "Omeka_Job_Dispatcher_Adapter_BackgroundProcess"
```

by

```
jobs.dispatcher.longRunning = "Omeka_Job_Dispatcher_Adapter_Synchronous"
```

Note that this change may limit the number of lines imported by job. If so, you
can increase the time limit for process in the server or php configuration.

_Note about local paths_

For security reasons, to import files from local file system is forbidden.
Nevertheless, it can be allowed for a specific path. This allowed base path or a
parent should be defined in the file "security.ini" of the plugin.


Examples
--------

Since release 2.2-full, only the "Manage" format is available. Some tests are
incompatible with this one, so change their headers to process them. Generally,
to set the "Dublin Core : Title" of "Dublin Core : Identifier" as the required
identifier is enough to process a test.

Fifteen examples of csv files are available in the csv_files folder. They are
many because a new one is built for each new feature. The last ones uses all of
them.

Some files may be updated with a second file to get full data. This is just to
have some examples.

They use free images of [Wikipedia], so import speed depends on the connection.

The first three tests use the same items from Wikipedia, so remove them between
tests.

1. `test.csv`

    A basic list of three books with images of Wikipedia, with non Dublin Core
    tags. To try it, you just need to check `Item metadata`, to use the default
    delimiters `,` and enclosure `"`. The identifier field is "Dublin Core : Title"
    and extra data are "Perhaps", so a manual mapping will be done, where the
    special value "Identifier" will be set to the title.

2. `test_automap.csv`

    The same list with some Dublin Core attributes in order to automap the
    columns with the Omeka fields. To try it, use the same parameter than the
    previous file. The plugin will try to get matching columns if field names
    are the same in your file and in the drop-down list.

3. `test_special_delimiters.csv`

    A file to try any delimiters. Special delimiters of this file are:
    - Column delimiter: tabulation
    - Enclosure: quotation mark "
    - Element delimiter: custom ^^ (used by Csv Report)
    - Tag delimiter: double space
    - File delimiter: semi-colon

    Extra data can be set to "Perhaps". If set to "No", then the second step
    will be skipped.

4. `test_files_metadata_full.csv` and `test_files_metadata_update.csv`

    A file used to import metadata of files. The first is autonomous, so the
    previous files didn't need to be imported. To try the second, you should
    import items before with any of the previous csv files. Then, select
    `tabulation` as column delimiter, no enclosure, and `|` as element, file and
    tag delimiters, and Dublin Core:Identifier as default identifier. Then, you
    can import it manually or automatically. If manually, set "Perhaps" for
    extra data, then the special values "Identifier field" to the identifier
    field and "Identifier" to the filename. There is no extra data.

5. `test_mixed_records.csv`

    A file used to show how to import metadata of item and files simultaneously,
    and to import files one by one to avoid server overloading or timeout. To
    try it, check `Mixed records` in the form and choose `tabulation` as column
    delimiter, no enclosure, and `|` as element, file and tag delimiters.

    Note: in the csv file, the file rows should always be after the item to
    which they are attached, else they are skipped.

    This file is not compatible with the release 2.2 for an automatic import.

6. `test_mixed_records_update.csv`

    A file used to show how to update metadata of item and files. To try it,
    import `test_mixed_recods.csv` above first, then choose this file and check
    `Update records` in the form.

    This file is not compatible with the release 2.2 for an automatic import.

7. `test_collection.csv`

    Add two items into a new collection. A created collection is not removed if
    an error occurs during import. Parameters are `tabulation` as column
    delimiter, no enclosure and `|` as element, file and tag delimiters. The
    identifier is "Dublin Core : Identifier".

8. `test_collection_update.csv`

    Update metadata of a collection.

    Parameters are the same as in the previous file.

9. `test_collection_update_bis.csv`

    Insert a new item in a collection selected in the form.

    Parameters are the same as in the previous file, but set a default
    collection.

10. `test_extra_data.csv`

    Show import of extra data that are not managed as elements, but as data in
    a specific table. The mechanism processes data as post, so it can uses the
    default hooks, specially `after_save_item`.

    To try this test file, install [Geolocation] first. Set `tabulation` as
    column delimiter, no enclosure, and `|` as element, file and tag delimiters.
    You should set the required identifier to "Dublin Core : Identifier", the
    option "Contains extra data" to "Yes" too (or "Perhaps"  to check manually).
    Use the update below to get full data for all items.

    The last row of this file shows an example to import one item with attached
    files on one row (unused columns, specially Identifier and Record Type, can
    be removed). This simpler format can be used if you don't need files
    metadata or if you don't have a lot of files attached to each item.

11. `test_extra_data_manual.csv`

    This file has the same content than the previous, but header are not set, so
    you should set "Contains extra data" to "Perhaps" to map them to the Omeka
    metadata. Note that extra data should kept their original headers.

12. `test_extra_data_update.csv`

    Show update of extra data. To test it, you need to import one of the two
    previous files first, then this one, with the same parameters.

13. `test_manage_one.csv`

14. `test_manage_two.csv`

15. `test_manage_script.csv`

    These files show how to use the "Manage" process. They don't use a specific
    column, but any field. So, each row is independant from others.
    The first allows to import some data and the second, similar, has got new
    and updated content, because there are errors in the first. The third is
    like a script where each row is processed one by one, with a different
    action for each row.

    To try them, you may install [Geolocation] and to use `tabulation` as column
    delimiter, no enclosure, `|` as element, file, and tag delimiters, and
    `Dublin Core:Identifier` as the field identifier.
    If you import them manually, the special value "Identifier" should be set
    too for the Dublin Core:Identifier, so this column will be used as
    identifier and as a metadata. The third should be imported after the first
    and the second to see changes.


CSV Format
----------

Since the version 2.2-full, only one format is available. Use the upstream
release for the other formats, or the release tagged "2.1.5-full", that is the
last with all formats (but fixed bug aren't backported).

Anyway, this format, previously named `Manage records`, allows to manage
creation, update and deletion of all records with the same file, or different
ones if you want. See below for possible actions.

Be warned that if you use always the same csv file and that you update records
from the Omeka admin board too, they can be desynchronized and overwritten.

Each row is independant from the other. So a file can be imported before an item
and an item in a collection that doesn't exist yet.

Three columns may be used to identify records between the csv file and Omeka. If
they are not present, the default values will be used.

- `Identifier`
All records should have a unique identifier. According to `IdentifierField`
column or the default parameter, it can be an internal id or any other metadata
field. It can be a specific identifier of the current file too, but in that
case, the identifier is available only for the current import.
When the identifier field is a metadata, this column is optional as long as this
metadata has got a column.
If it is empty and identifier is not set in a metadata column, the only
available action is "Create". If the record doesn't exist when updating, the row
will be skipped.
Note: When the mapping is done manually and when the field is a metadata, the
column should be mapped twice, one as a metadata and the second as a special
value "Identifier".

- `Identifier Field`
This column is optional: by default, the identifier field is set in the main
form. It should be unique, else only the first existing record will be updated.
It can be the "internal id" of the record in Omeka. Recommendation is to use a
specific field, in particular "Dublin Core:Identifier" or an added internal
field. Files can be identified by their "original filename", Omeka "filename"
and "md5" authentication sum too.

- `Record Type`
The record type can be "Collection", "Item" or "File". "Any" can be used only
when identifier is not the internal id and when the identifier is unique accross
all records. If empty, the record type is determined according to other columns
when possible. If not, the record is an item. This column is recommended to
avoid useless processing.

The column "Item" is required to identify the item to which the file is
attached. It contains the same identifier as above.

To import metadata of files alone, the column "Identifier Field" and "File" are
required.


Notes
-----

* Columns

  - Columns can be ordered in any order.
  - Columns names are case sensitive.
  - Spaces can be added before or after the default column name separator `:`,
  except for extra data and the identifier field, when they are imported
  automatically.
  - Manual mapping is slower, conducive to careless mistakes, more boring than
  automatic import, but it allows to map some columns with more than one field.
  For example, a column of tags can be mapped as a Dublin Core Subject too.
  Furthermore, it allows too to set each element as an html one or not.
  - Item type can be changed, but not unset.
  - Tags can be added to an item, but not removed.

* Characters encoding

Depending of your environment and database, if you imports items with encoded
urls, they should be decoded when you import files. For example, you can import
an item with the file `Edmond_Dant%C3%A8s.jpg`, but you may import your file
metadata with the filename `Edmond_Dantès.jpg`. Furthermore, filenames may be or
not case sensitive.

* Update of attached files

Files that are attached to an item can be fully updated. If the url is not the
same than existing ones, the file will be added. If it is the same, the file
will be reimported. To reimport a file with the same url, you should remove it
first. This process avoids many careless errors. To update metadata of a file,
the column for the url ("File") should be removed.
Files are ordered according to the list of files.
Note : This process works only when original filenames are unique. So, the
simplest is to set a unique identifier for files too.

* Status page

The status page indicates situation of previous, queued and current imports. You
can make an action on any import process.

Note that you can't undo an update, because previous metadata are overwritten.

The column "Skipped rows" means that some imported lines were non complete or
with too many columns, so you need to check your import file.

The column "Skipped records" means that an item or a file can't be created,
usually because of a bad url or a bad formatted row. You can check `error.log`
for information.

The count of imported records can be different from the number of rows, because
some rows can be update ones. Furthermore, multiple records can be created with
one row. Files attached directly to items are not counted.

* Available actions

The column `Action` allows to set the action to do for the current row. This
parameter is optional and can be set in the first step of import.

The actions can be (not case sensitive):
    - Empty or "Update else create" (default): Update the record if it exists, else
    create a new one.
    - "Create": Insert a new record if the identifier doesn't exist yet.
    - "Update": Update fields of the record, so remove values of all fields that
    are imported before inserting the new values.
    - "Add": Add values to fields.
    - "Replace": Remove only values of imported fields whose values are not
    empty, then update fields with new values.
    - "Delete": Remove the record (and files, if the record is an item).
    - "Skip": Skip the row and record from any process.

_Important_

This mode doesn't apply to extra data, because the way the plugins manage
updates of their data varies. So existing data may be needed in the update file
in order to not be overwritten (this is the case for the [Geolocation] plugin).

* Management of extra data

Extra data are managed by plugins, so some differences should be noted.
    - The `Contains extra data` parameter should be set to "Yes" or "Manual".
    - The header of each extra data column should be the name used in the manual
    standard form.
    - Columns can't be mapped manually to a specifc plugin, so they should be
    named like the post fields in the hooks `before_save_*` or `after_save_*`.
    If the plugin does not use these hooks, they can be set in a specific
    plugin.
    - All needed columns should exists in the file, according to the model of
    record and the hooks. For example, the import of data for the [Geolocation]
    plugin implies to set not only "latitude" and "longitude", but "zoom_level"
    too. "map_type" and "address" can be needed too in a next release of the
    plugin. Their values can be set to empty or not.
    - If the model allows the data to be multivalued, the column name should be
    appended with a ':'.
    - For update, as the way the plugins manage updates of their data varies,
    the `updateMode` is not used for extra data. So existing data may be needed
    in the update file in order to not be overwritten (this is the case for the
    [Geolocation] plugin).
    - As Omeka jobs don't manage ACL, if a plugin uses it (usually no), the jobs
    displatcher should be the synchronous one and be set in config.ini, so the
    ACL will use the one of the current user:
    ```
    jobs.dispatcher.longRunning = "Omeka_Job_Dispatcher_Adapter_Synchronous"
    ```

* Order of files

In some cases, in particular when the item is saved in another process while the
import job is still working in background, order of files can be broken. In that
case, simply reorder them. A batch edit form can be do it automatically (select
items in items/browse and click the main button "Edit", then check the box for
CSV Import+ / Order files by filename).


Warning
-------

Use it at your own risk.

It’s always recommended to backup your files and your databases and to check
your archives regularly so you can roll back if needed.


Troubleshooting
---------------

See online [CSV Import issues] and [CSV Import+ issues].


License
-------

This plugin is published under [GNU/GPL].

This program is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT
ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
details.

You should have received a copy of the GNU General Public License along with
this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.


Contact
-------

Current maintainers:

* [Center for History & New Media] [CSV Import]
* Daniel Berthereau (see [Daniel-KM]) [CSV Import+]

This plugin has been built by [Center for History & New Media]. Next, the
release 1.3.4 has been forked for [University of Iowa Libraries] and upgraded
for [École des Ponts ParisTech] and [Pop Up Archive]. The fork of this plugin
has been upgraded for Omeka 2.0 for [Mines ParisTech].


Copyright
---------

* Copyright Center for History and New Media, 2008-2016
* Copyright Shawn Averkamp, 2012
* Copyright Matti Lassila, 2016
* Copyright Daniel Berthereau, 2012-2017


[Omeka]: https://omeka.org
[CSV Import+]: https://github.com/Daniel-KM/Omeka-plugin-CsvImportPlus
[CSV Import]: https://github.com/omeka/plugin-CsvImport
[XML Import]: https://github.com/Daniel-KM/Omeka-plugin-XmlImport
[Wikipedia]: https://www.wikipedia.org
[Geolocation]: https://omeka.org/add-ons/plugins/geolocation
[CSV Import issues]: https://omeka.org/forums/forum/plugins
[CSV Import+ issues]: https://github.com/Daniel-KM/Omeka-plugin-CsvImportPlus/issues
[GNU/GPL]: https://www.gnu.org/licenses/gpl-3.0.html
[Center for History & New Media]: http://chnm.gmu.edu
[Daniel-KM]: https://github.com/Daniel-KM "Daniel Berthereau"
[saverkamp]: https://github.com/saverkamp "saverkamp"
[mjlassila]: https://github.com/mjlassila "Matti Lassila"
[University of Iowa Libraries]: http://www.lib.uiowa.edu
[École des Ponts ParisTech]: http://bibliotheque.enpc.fr
[Pop Up Archive]: http://popuparchive.org
[Mines ParisTech]: http://bib.mines-paristech.fr
