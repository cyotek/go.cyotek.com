Change Log
==========

1.1.0
-----

### Added
* Added ability to record 400 and 404 errors to the database
* Added new `errors.php` management page for viewing the error log
* Added version information to page footers

### Fixed
* Calling `index.php` without providing an redirect ID now returns `200` and displays a slightly more friendly/usable page defined using the **Title** and **HomePage** configuration values.

1.0.0
-----
* Initial release