Reference (plugin for Omeka)
============================

[Reference] is a plugin for [Omeka] that allows to serve an alphabetized index
of links to records or to searches for all item types and metadata of all items
of an Omeka instance, or an expandable hierarchical list of specified subjects.
These lists can be displayed in any page via a helper or a shortcode.

This plugin is upgradable to [Omeka S] via the plugin [Upgrade to Omeka S], that
installs the module [Reference for Omeka S].

---
Customizations
--------------

### Forked Repo

Might need to sync if there are changes to source... otherwise, the following files have been customized.

Should be able to download zip.

[Reference/views/public/common/reference-list.php](views/public/common/reference-list.php)

[Reference/views/public/css/reference.css](/views/public/css/reference.css)

[Reference/views/public/index/browse.php](/views/public/index/browse.php)

---

Installation
------------

Uncompress files and rename plugin folder "Reference".

Then install it like any other Omeka plugin and follow the config instructions.


Usage
-----

The plugin adds secondary links in the secondary navigation bar:
* "Browse by Reference" (http://www.example.com/references).
* "Hierarchy of Subjects" (http://www.example.com/subjects/tree).

For the list view, the references are defined in the config page.

For the tree view, the subjects are set in the config form with the hierarchical
list of subjects, formatted like:
```
Europe
- France
- Germany
- United Kingdom
-- England
-- Scotland
-- Wales
Asia
- Japan
```

So, the format is the config page for the tree view is:

- One subjet by line.
- Each subject is preceded by zero, one or more "-" to indicate the hierarchy
level.
- Separate the "-" and the subject with a space.
- A subject cannot begin with a "-" or a space.
- Empty lines are not considered.

These contents can be displayed on any page via the helper `reference()`:

```php
$slug = 'subject';
$references = $this->reference()->getList($slug);
echo $this->reference()->displayList($references, array(
    'skiplinks' => true,
    'headings' => true,
    'strip' => true,
    'raw' => false,
));
```

For tree view:
```php
$subjects = $this->reference()->getTree();
echo $this->reference()->displayTree($subjects, array(
    'expanded' => true,
    'strip' => true,
    'raw' => false,
));
```

All arguments are optional and the default ones are set in the config page, but
they can be overridden in the theme. So a simple `echo $this->reference();`
is enough. For list, the default is the "Dublin Core : Subject".

The shortcodes "reference" and "subjects" can be used too, in particular in
exhibits and in simple pages:

```
[reference]
[reference slug=date skiplinks=true headings=true raw=false]
[subjects]
[subjects expanded=true raw=false]
```

Arguments that are not set use the default values.


Warning
-------

Use it at your own risk.

It’s always recommended to backup your files and your databases and to check
your archives regularly so you can roll back if needed.


Troubleshooting
---------------

See online issues on the [plugin issues] page on GitHub.


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


The plugin uses a jQuery library for the tree view, released under the [MIT]
licence.


Contact
-------

Current maintainers:

* Daniel Berthereau (see [Daniel-KM] on GitHub, release [Reference])

This plugin incorporates earlier work done by William Mayo (see [pobocks] on
GitHub) in [Subject Browse], with some ideas from [Metadata Browser] and
[Category Browse], that have been upgraded for Omeka 2.x too ([Subject Browse (2.x)],
[Metadata Browser (2.x)], and [Category Browse (2.x)]). They are no longer
maintained.

Upgrade and improvements have been made for [Jane Addams Digital Edition].


Copyright
---------

* Copyright William Mayo, 2011
* Copyright Philip Collins, 2013 ([jQuery tree view])
* Copyright Daniel Berthereau, 2014-2018
* Copyright Daniele Binaghi, 2020-2021

[Omeka]: https://omeka.org
[Reference]: https://github.com/Daniel-KM/Omeka-plugin-Reference
[Omeka S]: https://omeka.org/s
[Upgrade to Omeka S]: https://github.com/Daniel-KM/Omeka-plugin-UpgradeToOmekaS
[Reference for Omeka S]: https://github.com/Daniel-KM/Omeka-S-module-Reference
[plugin issues]: https://github.com/Daniel-KM/Omeka-plugin-Reference/issues
[GNU/GPL]: https://www.gnu.org/licenses/gpl-3.0.html "GNU/GPL v3"
[MIT]: http://http://opensource.org/licenses/MIT
[pobocks]: https://github.com/pobocks
[Subject Browse]: https://github.com/pobocks/SubjectBrowse
[Metadata Browser]: https://github.com/kevinreiss/Omeka-MetadataBrowser
[Category Browse]: https://github.com/kevinreiss/Omeka-CategoryBrowse
[Subject Browse (2.x)]: https://github.com/Daniel-KM/Omeka-plugin-Reference/tree/subject_browse
[Metadata Browser (2.x)]: https://github.com/Daniel-KM/Omeka-plugin-MetadataBrowser
[Category Browse (2.x)]: https://github.com/Daniel-KM/Omeka-plugin-CategoryBrowse
[Jane Addams Digital Edition]: http://digital.janeaddams.ramapo.edu
[Daniel-KM]: https://github.com/Daniel-KM "Daniel Berthereau"
[jQuery tree view]: https://github.com/collinsp/jquery-simplefolders
