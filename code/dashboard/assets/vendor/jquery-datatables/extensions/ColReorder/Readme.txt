# ColReorder

ColReorder adds the ability for the end user to click and drag column headers to reorder a table as they see fit, to DataTables. Key features include:

* Very easy integration with DataTables
* Tight integration with all other DataTables plug-ins
* The ability to exclude the first (or more) column from being movable
* Predefine a column order
* Save staving integration with DataTables


# Installation

To use ColReorder, first download DataTables  and place the unzipped ColReorder package into a `extensions` directory in the DataTables package. This will allow the pages in the examples to operate correctly. To see the examples running, open the `examples` directory in your web-browser.


# Basic usage

ColReorder is initialised using the `R` option that it adds to DataTables' `dom` option. For example:

```js
$(document).ready( function () {
    $('#example').dataTable( {
        "dom": 'Rlfrtip'
    } );
} );
```


# Documentation / support




# GitHub

If you fancy getting involved with the development of ColReorder and help make it better, please refer to its GitHub repo: https://github.com/DataTables/ColReorder

