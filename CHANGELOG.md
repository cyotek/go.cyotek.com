Change Log
==========

1.3.0
-----
### Added
* Added new migration page to anonymise existing data
* Added new configuration viewer page

### Fixed
* Fixed syntax error in `UPGRADE_1_1_0_TO_1_2_0.sql`

1.2.0
-----
### Added
* Added new `AnonymizeAddresses` setting. When set to `true` (default), IP addresses will be anonymised to a /24 subnet (IPv4) or a /64 subnet (IPv6)

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