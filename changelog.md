# Change Log

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## 6.1.1 - 2023-12-27

### Changed

- Rule overview is lower in height.

### Removed

- Removed fixed sidebar again

### Fixed

- Nullpointer in rule trigger render code
- [Issue 8272](https://github.com/firefly-iii/firefly-iii/issues/8272) The sum for expected bills in a group includes unexpected bills as well
- [Issue 8273](https://github.com/firefly-iii/firefly-iii/issues/8273) Frontpage preferences indicate all accounts are shown on the frontpage, even when not true
- [Issue 8274](https://github.com/firefly-iii/firefly-iii/issues/8274) Semi specific dates do not work correctly with the "Transaction date is.." rule trigger
- [Issue 8277](https://github.com/firefly-iii/firefly-iii/issues/8277) Expected bill next month, but shown as not expected
- [Issue 8278](https://github.com/firefly-iii/firefly-iii/issues/8278) Net worth is empty in the dashboard due to division by zero
- [Issue 8281](https://github.com/firefly-iii/firefly-iii/issues/8281) Database CPU utilization after v6.1.0 upgrade
- [Issue 8291](https://github.com/firefly-iii/firefly-iii/issues/8291) Multiple "Any tag is" (negated or not) rule triggers don't all apply in strict mode

### Security

- HTML Injection Vulnerability in webhooks code, discovered by @stefan-schiller-sonarsource from Sonar. Thanks!

### API

- [Issue 8282](https://github.com/firefly-iii/firefly-iii/issues/8282) Update transaction via API does not update the "updated_at" parameter

## 6.1.0 - 2023-12-17

> ⚠️⚠️ This release required **PHP 8.3.0** and will not work on earlier releases of PHP ⚠️⚠️

### Added

- [Issue 7571](https://github.com/firefly-iii/firefly-iii/issues/7571) More tag search options
- [Issue 7781](https://github.com/firefly-iii/firefly-iii/issues/7781) Nice wrapper script for artisan commands
- UI also supports time for transactions

### Changed

- ⚠️⚠️ Requires PHP8.3 ⚠️⚠️
- [Issue 8148](https://github.com/firefly-iii/firefly-iii/issues/8148) Slovenian language updates
- [Issue 8023](https://github.com/firefly-iii/firefly-iii/issues/8023) Top bar is now fixed in place
- Completely rewrote the documentation.

### Deprecated

- ⚠️⚠️ Removed support for PHP 8.2 ⚠️⚠️

### Fixed

- [Issue 8106](https://github.com/firefly-iii/firefly-iii/issues/8106) [issue 8195](https://github.com/firefly-iii/firefly-iii/issues/8195) [issue 8163](https://github.com/firefly-iii/firefly-iii/issues/8163) Various changes and fixes to bill date calculation
- [Issue 8137](https://github.com/firefly-iii/firefly-iii/issues/8137) Fix uneven amount error from cron job
- [Issue 8192](https://github.com/firefly-iii/firefly-iii/issues/8192) No matching transactions found.Rule with trigger NOT Transaction is reconciled returns
- [Issue 8207](https://github.com/firefly-iii/firefly-iii/issues/8207) Broken links, thanks @Maxco10!
- [Issue 8138](https://github.com/firefly-iii/firefly-iii/issues/8138) Reconciled transactions can't be "store(d) as new"
- [Issue 7716](https://github.com/firefly-iii/firefly-iii/issues/7716) Removed bar in budget overview
- [Issue 8251](https://github.com/firefly-iii/firefly-iii/issues/8251) Removing a budget would not remove available budget

### API

- [Issue 8022](https://github.com/firefly-iii/firefly-iii/issues/8022) API chart expansions
- [Issue 8106](https://github.com/firefly-iii/firefly-iii/issues/8106) API reports empty string instead of NULL

## 6.0.30 - 2023-10-29

### Fixed

- Missing method after refactoring.

## 6.0.29 - 2023-10-29

### Fixed

- Null pointer in bill overview

## 6.0.28 - 2023-10-29

### Added

- [Issue 8076](https://github.com/firefly-iii/firefly-iii/issues/8076) Added a "Clone and edit"-button
- [Issue 7204](https://github.com/firefly-iii/firefly-iii/issues/7204) Added the ability to customize the URL protocol types Firefly III accepts 
- [Issue 8098](https://github.com/firefly-iii/firefly-iii/issues/8098) More tests in the navigation class, thanks @tonicospinelli!

### Changed

- Refactored the Actions of GitHub
- The transaction currencies are now linked to users, and can be enabled per user
- A few upgrade commands are refactored
- You can no longer edit vital parts of reconciled transactions

### Deprecated

- Remove old v3 layout.

### Fixed

- Bad math in the order of piggy banks
- [Issue 8084](https://github.com/firefly-iii/firefly-iii/issues/8084) @JoSchrader fixed an issue with a duplicate button
- [Issue 8103](https://github.com/firefly-iii/firefly-iii/issues/8103) Bulk edit would not accept transaction descriptions longer than 255 characters
- [Issue 8099](https://github.com/firefly-iii/firefly-iii/issues/8099) The bill index would never skip the number of periods you indicated
- [Issue 8069](https://github.com/firefly-iii/firefly-iii/issues/8069) Rule descriptions would always "1" as description. Thanks @Maxco10!

### API

- API will no longer accept changes to amount and account fields for reconciled transactions

## v6.0.27 - 2023-10-16

### Added

- [Issue 8004](https://github.com/firefly-iii/firefly-iii/issues/8004) Warning in entrypoint script for missing variables.

### Changed

- Experimental database validation command.
- Add some values to the debug form.
- Better debug logs at various places

### Fixed

- [Issue 8020](https://github.com/firefly-iii/firefly-iii/issues/8020), [issue 8028](https://github.com/firefly-iii/firefly-iii/issues/8028) Liability calculation edge case found by @tieu1991
- [Issue 7655](https://github.com/firefly-iii/firefly-iii/issues/7655), [issue 8026](https://github.com/firefly-iii/firefly-iii/issues/8026) Bill date calculation edge case found by @devfaz
- [Issue 8051](https://github.com/firefly-iii/firefly-iii/issues/8051) Null pointer when deleting account
- [Issue 8041](https://github.com/firefly-iii/firefly-iii/issues/8041) Confusing chart is no longer confusing
- [Issue 8050](https://github.com/firefly-iii/firefly-iii/issues/8050) Path is normal for page 2.
- [Issue 8057](https://github.com/firefly-iii/firefly-iii/issues/8057) negative query parameters are handled correctly.

### API (v2.0.10)

- All endpoints (v1 and v2) should now respect the `?limit=` param.

## 6.0.26 - 2023-09-24

### Fixed

- [Issue 7986](https://github.com/firefly-iii/firefly-iii/issues/7986), [issue 7992](https://github.com/firefly-iii/firefly-iii/issues/7992) Fix exception when calling specific end points
- [Issue 7990](https://github.com/firefly-iii/firefly-iii/issues/7990) Remove unused translations

## 6.0.25 - 2023-09-24

### Changed

- v2 index has better overview of bills (now called subscriptions)

### Deprecated

- My attempt to build the `v3`-layout is now officially deprecated and removed. To see the new layout (in beta), use `FIREFLY_III_LAYOUT=v2`.

### Fixed

- [Issue 7970](https://github.com/firefly-iii/firefly-iii/issues/7970) Bad redirect for mass edit/delete forms.
- [Issue 7983](https://github.com/firefly-iii/firefly-iii/issues/7983) Bad math in the calculation of liabilities
- [Issue 7973](https://github.com/firefly-iii/firefly-iii/issues/7973) Bad account validation broke certain imports
- [Issue 7981](https://github.com/firefly-iii/firefly-iii/issues/7981) Menu had a bad link, thanks @Maxco10!
- Slack alerts now use the correct URL
- Better htaccess files thanks to Softaculous.

### Security

- Change htaccess rules so certain files can't be accessed.

### API

- [Issue 7972](https://github.com/firefly-iii/firefly-iii/issues/7972) The API needed start
  *and* end parameters for transactions, this is no longer the case.
- New APIs for user group and rights management. Not yet documented.

## 6.0.24 - 2023-09-16

### Fixed

- [Issue 7920](https://github.com/firefly-iii/firefly-iii/issues/7920) Issues with automatic budgets
- [Issue 7940](https://github.com/firefly-iii/firefly-iii/issues/7940) Edge cases in the data import routine
- [Issue 7963](https://github.com/firefly-iii/firefly-iii/issues/7963) Fix audit items for rules
- Fixed all issues with relative URLs (which I caused myself)

### API

- [Issue 7944](https://github.com/firefly-iii/firefly-iii/issues/7944) Make sure all IDs are strings in the API

## 6.0.23 - 2023-09-04

### Changed

- New debug information tables are in HTML

### Fixed

- Remove extra slashes from paths, breaking CSS

## 6.0.22 - 2023-09-02

### API

- [Issue 7917](https://github.com/firefly-iii/firefly-iii/issues/7917) Fixed an API issue where submitting an account name would not be accepted.

## 6.0.21 - 2023-09-02

### Added

- Rules will now report failures if a Slack/Discord notification channel is configured
- Notifications can be sent to Discord
- Beta layout `v2`, activate with `FIREFLY_III_LAYOUT=v2`

### Changed

- Audit log settings are changed, refer to the `.env.example`-file.
- Many URLs are new rendered as relative URLs.

### Fixed

- [Issue 7853](https://github.com/firefly-iii/firefly-iii/issues/7853) Left to spend on main page shows incorrect value
- [Issue 7883](https://github.com/firefly-iii/firefly-iii/issues/7883) Missing translation
- [Issue 7910](https://github.com/firefly-iii/firefly-iii/issues/7910) Type format error
- Home page respects account order
- JS errors for users using Firefly III in a subdir.

### API

- Bumped to v2.0.6 but only so the docs match again.

## 6.0.20 - 2023-08-13

### Fixed

- [Issue 7787](https://github.com/firefly-iii/firefly-iii/issues/7787) Possible issue when deleting multiple tags from a transaction.
- [Issue 7792](https://github.com/firefly-iii/firefly-iii/issues/7792) Search for tags was broken in rules
- [Issue 7803](https://github.com/firefly-iii/firefly-iii/issues/7803) @zqye fixed an issue where the cron job would fire when not necessary.
- [Issue 7771](https://github.com/firefly-iii/firefly-iii/issues/7771) Unclear use of language in rule trigger
- [Issue 7818](https://github.com/firefly-iii/firefly-iii/issues/7818) Amount was negative instead of positive.
- [Issue 7810](https://github.com/firefly-iii/firefly-iii/issues/7810) Bad math
- Asset accounts will correctly show transaction groups

### API

- Lots of new, undocumented v2 API endpoints.
- [Issue 7845](https://github.com/firefly-iii/firefly-iii/issues/7845) Could not reconcile over API

## 6.0.19 - 2023-07-29

### Fixed

- [Issue 7577](https://github.com/firefly-iii/firefly-iii/issues/7577) Firefly III can't search for backward slashes in identifiers
- [Issue 7762](https://github.com/firefly-iii/firefly-iii/issues/7762) User can't create access token

## 6.0.18 - 2023-07-19

### Fixed

- Slack messages would fail if not configured.
- Bill display would include transactions from the previous period.
- Debug information left in bill overview.
- [Issue 7694](https://github.com/firefly-iii/firefly-iii/issues/7694) Missing CSS info in dark mode.
- [Issue 7706](https://github.com/firefly-iii/firefly-iii/issues/7706) Deleting a budget would not reset the available amount.
- [Issue 7749](https://github.com/firefly-iii/firefly-iii/issues/7749) Account overview would show just 1 transaction from a split of multiple.

## 6.0.17 - 2023-07-16

### Added

- New date calculation code and tests, thanks to @tonicospinelli!

### Removed

- Heroku support

### Fixed

- [Issue 7704](https://github.com/firefly-iii/firefly-iii/issues/7704) Date issues with bills
- Cache issue in budgets
- Fixed the account validation for transfer transactions

### API

- Various fields would not accept `null` values

## 6.0.16 - 2023-06-28

### Changed

- Better IBAN and account validation for new (API) transactions.

### Fixed

- Better transaction split validation in API.
- [Issue 7683](https://github.com/firefly-iii/firefly-iii/issues/7683) Date validation in recurring transaction form.
- [Issue 7686](https://github.com/firefly-iii/firefly-iii/issues/7686) Low contrast in dark mode, thanks @MateusBMP!
- [Issue 7655](https://github.com/firefly-iii/firefly-iii/issues/7655) Bad date display in bills

## 6.0.15 - 2023-06-22

### Fixed

- [Issue 7678](https://github.com/firefly-iii/firefly-iii/issues/7678) Missing argument in postgres maintenance code
  breaks startup.

## 6.0.14 - 2023-06-22

### Added

- Editing some fields will generate audit logs visible when you view a transaction. The number of fields monitored will
  increase over time

### Changed

- Account validation includes IBANs now, this helps the data importer
- Unified and cleaned up all command line output

### Fixed

- [Issue 7630](https://github.com/firefly-iii/firefly-iii/issues/7630) Errors when upgrading using SQLite
- [Issue 7642](https://github.com/firefly-iii/firefly-iii/issues/7642) nn_NO wasn't available for users
- [Issue 7609](https://github.com/firefly-iii/firefly-iii/issues/7609), [issue 7659](https://github.com/firefly-iii/firefly-iii/issues/7659)
  Rule execution form was broken
- [Issue 7677](https://github.com/firefly-iii/firefly-iii/issues/7677) Amount was negative instead of positive in view
- [Issue 7649](https://github.com/firefly-iii/firefly-iii/issues/7649) Bill edit screen would always suggest "daily"
  repeat frequency
- Nullpointer in bill repository class
- Missing param in rule action, thanks @timendum!
- Missing attachment overview in recurring transactions

## v6.0.13 - 2023-06-12

### Fixed

- [Issue 7641](https://github.com/firefly-iii/firefly-iii/issues/7641) Crash with AUTHENTICATION_GUARD=web

## v6.0.12 - 2023-06-12

### Changed

- Command output and logo on the terminal improved.

### Fixed

- [Issue 7557](https://github.com/firefly-iii/firefly-iii/issues/7557) `firefly-iii:upgrade-database` step issue
- [Issue 7572](https://github.com/firefly-iii/firefly-iii/issues/7572) Paid bill shown in wrong currency
- [Issue 7593](https://github.com/firefly-iii/firefly-iii/issues/7593) Fix URLs in .env.example, thanks @josephbadow
- [Issue 7620](https://github.com/firefly-iii/firefly-iii/issues/7620) Issues with light mode
- [Issue 7618](https://github.com/firefly-iii/firefly-iii/issues/7618) Can't log out when using remote auth
- [Issue 7613](https://github.com/firefly-iii/firefly-iii/issues/7613) Can't save piggy bank attachments

### API

- [Issue 7588](https://github.com/firefly-iii/firefly-iii/issues/7588) v1/recurrences not able to handle bills on
  store & update
- [Issue 7589](https://github.com/firefly-iii/firefly-iii/issues/7589) v1/recurrences fails when updating a split
  transaction

## v6.0.11 - 2023-05-28

### Added

- 🇰🇷 Korean translations!
- A new "adjusted" auto-budget type that will correct itself after
  spending. [Read more](https://docs.firefly-iii.org/xfirefly-iii/financial-concepts/organizing/#adjusted-and-correct-for-overspending)
- [Issue 6631](https://github.com/firefly-iii/firefly-iii/issues/6631) Can now link withdrawals and deposits to piggy
  banks, thanks @ChrisWin22!

### Changed

- "Balance" is now called "In + out this period" so it's more clear what it means.

### Removed

- Some superfluous logging.

### Fixed

- An intermittent issue came up where people would suffer from badly rounded numbers.
  The root cause has been fixed. Open a discussion if this affects you, a fix for your data is available.
- The API cron job would not run all available cron commands.
- Debug page would always report midnight
- [Issue 7514](https://github.com/firefly-iii/firefly-iii/issues/7514) DB error when upgrading to 6.0.10
- [Issue 7516](https://github.com/firefly-iii/firefly-iii/issues/7516) Webhook: wrong JSON transaction amount
- [Issue 7522](https://github.com/firefly-iii/firefly-iii/issues/7522) Time related events cause a timeout
- [Issue 7541](https://github.com/firefly-iii/firefly-iii/issues/7541) Login screen display glitch
- [Issue 7549](https://github.com/firefly-iii/firefly-iii/issues/7549) Account creation duplicate checking fails for
  German umlaut
- [Issue 7546](https://github.com/firefly-iii/firefly-iii/issues/7546) Version link doesn't work
- [Issue 7547](https://github.com/firefly-iii/firefly-iii/issues/7547) Rule fails to convert "Withdrawal from X to Y"
  to "Transfer from Y to X"

### API

- [Issue 7505](https://github.com/firefly-iii/firefly-iii/issues/7505) Several API schema dates have been fixed, thanks
  @nagyv!

## v6.0.10 - 2023-05-14

### Added

- The debug screen will also report on the build version of the BASE image.

### Changed

- Health check will also check if the database is up.
- [Issue 7461](https://github.com/firefly-iii/firefly-iii/issues/7461) MFA field will now autofocus, thanks @eandersons!

### Removed

- IBAN check no longer triggers on empty IBANs

### Fixed

- Account validation when you only submit an IBAN.
- [Issue 7478](https://github.com/firefly-iii/firefly-iii/issues/7478) [issue 7457](https://github.com/firefly-iii/firefly-iii/issues/7457)
  Various fixes in budget limit and available amount management.
- [Issue 7446](https://github.com/firefly-iii/firefly-iii/issues/7446) Bills "Next expected match" was incorrect
- [Issue 7456](https://github.com/firefly-iii/firefly-iii/issues/7456) Missing date calculation fields.
- [Issue 7448](https://github.com/firefly-iii/firefly-iii/issues/7448) [issue 7444](https://github.com/firefly-iii/firefly-iii/issues/7444)
  Dark mode bad CSS

## 6.0.9 - 2023-04-29

### Added

- Better length validation for text fields.

### Changed

- Better calculation of available budget

### Fixed

- [Issue 7377](https://github.com/firefly-iii/firefly-iii/issues/7377) Tag search was broken
- [Issue 7389](https://github.com/firefly-iii/firefly-iii/issues/7389) Bug in charts
- [Issue 7394](https://github.com/firefly-iii/firefly-iii/issues/7394) unique iban check was broken
- [Issue 7427](https://github.com/firefly-iii/firefly-iii/issues/7427) API would not accept page 18 and up.
- [Issue 7410](https://github.com/firefly-iii/firefly-iii/issues/7410) Various dark mode color fixes
- Old documentation links fixed by @mindlessroman and @noxonad!

## 6.0.8 - 2023-04-16

### Added

- [Issue 7351](https://github.com/firefly-iii/firefly-iii/issues/7351) Optional command to force the decimal size.
- [Issue 7352](https://github.com/firefly-iii/firefly-iii/issues/7352) Optional command to force the migrations.
- [Issue 7354](https://github.com/firefly-iii/firefly-iii/issues/7354) The new v3 layout will redirect to the index when
  unauthenticated, thanks @corcom!

### Fixed

- [Issue 7349](https://github.com/firefly-iii/firefly-iii/issues/7349) Missing tables in PostgreSQL script.
- [Issue 7358](https://github.com/firefly-iii/firefly-iii/issues/7358) Could not create liabilities with a pre-set
  amount.
- Fix date field in bill warning mail.
- Fix installer script.
- Remove attachment paperclip from transactions with deleted attachments.

### API

- [Issue 7347](https://github.com/firefly-iii/firefly-iii/issues/7347) API made rules would be inactive by default.

## v6.0.7 - 2023-04-09

### Added

- Lots of error catching in DB migrations for smoother upgrades.
- New command `firefly-iii:force-migration` which will force database migrations to run. It will probably also destroy
  your database so don't use it.
- You can now force light/dark mode in your settings.

### Fixed

- [Issue 7137](https://github.com/firefly-iii/firefly-iii/issues/7137) Inconsistent rule test form
- [Issue 7320](https://github.com/firefly-iii/firefly-iii/issues/7320) Standard email values so less errors
- [Issue 7311](https://github.com/firefly-iii/firefly-iii/issues/7311) Fix issue with date validation
- [Issue 7310](https://github.com/firefly-iii/firefly-iii/issues/7310) Better color contrast in dark mode.

### API

- [Issue 7308](https://github.com/firefly-iii/firefly-iii/issues/7308) Could not set current amount for certain piggy
  banks

## v6.0.6 - 2023-04-02

### Changed

- Database migrations are capped at 12 decimals.
- Currency processing is capped at 12 decimals.
- Mail errors no longer crash the app but report the error in logs
- Disabled Sonarcloud runs

### Fixed

- "Change transaction type"-rule actions would create a new expense account instead of finding a liability.
- New users from remote user repositories would not be able to create new asset accounts.
- Firefly III would create "Loan" instead of "Expense account" when faced with unknown accounts during API calls.
- Icons would not show up in the minimized left-hand menu.
- Contrast for dark mode improved.
- Better credit calculation for liabilities in case of complex transactions.

### API

- Fixed: Could not give piggy bank an unlimited amount.
- [Issue 7335](https://github.com/firefly-iii/firefly-iii/issues/7335) Fix upload of attachments, thanks @fengkaijia

## v6.0.5 - 2023-03-19

### Changed

- Mathematical accuracy is set to 12 decimals. This is accurate enough for most currencies and prevents rounding issues
  for systems that don't support more.

### Fixed

- [Issue 7227](https://github.com/firefly-iii/firefly-iii/issues/7227) Could not set webhooks to the correct trigger.
- [Issue 7221](https://github.com/firefly-iii/firefly-iii/issues/7221) Could not see the result of a rule test.

## v6.0.4 - 2023-03-13

### Fixed

- [Issue 7214](https://github.com/firefly-iii/firefly-iii/issues/7214) Import issue blocking multi currency transactions

## v6.0.3 - 2023-03-13

### Fixed

- [Issue 7201](https://github.com/firefly-iii/firefly-iii/issues/7201) Security-related console automatically command
  runs before a database is set, and may error out.

## v6.0.2 - 2023-03-11

### Fixed

- [Issue 7186](https://github.com/firefly-iii/firefly-iii/issues/7186) Fix broken date range
- [Issue 7188](https://github.com/firefly-iii/firefly-iii/issues/7188) Fix broken search
- [Issue 7189](https://github.com/firefly-iii/firefly-iii/issues/7189) Too strict account validation
- [Issue 7142](https://github.com/firefly-iii/firefly-iii/issues/7142) Better contrast in dark mode

## 6.0.1 - 2023-03-11

### Changed

- [Issue 7129](https://github.com/firefly-iii/firefly-iii/issues/7129) Catch common email errors as log errors.

### Fixed

- [Issue 7109](https://github.com/firefly-iii/firefly-iii/issues/7109) Fix CSS in subdirectories, thanks @GaneshKandu
- [Issue 7112](https://github.com/firefly-iii/firefly-iii/issues/7112) Version number parsing
- [Issue 6985](https://github.com/firefly-iii/firefly-iii/issues/6985) Mandrill mail support
- [Issue 7131](https://github.com/firefly-iii/firefly-iii/issues/7131) Fix account sorting, thanks @lflare
- [Issue 7130](https://github.com/firefly-iii/firefly-iii/issues/7130) Fix missing date range parsers
- [Issue 7156](https://github.com/firefly-iii/firefly-iii/issues/7156) Default values for email settings break tokens
- [Issue 7140](https://github.com/firefly-iii/firefly-iii/issues/7140) Header with charset would break API validation
- [Issue 7144](https://github.com/firefly-iii/firefly-iii/issues/7144) Debug page could not handle missing log files
- [Issue 7159](https://github.com/firefly-iii/firefly-iii/issues/7159) Bad parsing in success messages
- [Issue 7104](https://github.com/firefly-iii/firefly-iii/issues/7104) Missing colors in dark mode
- [Issue 7120](https://github.com/firefly-iii/firefly-iii/issues/7120) Missing borders in dark mode
- [Issue 7156](https://github.com/firefly-iii/firefly-iii/issues/7156) Bad HTML parsing in transaction form
- [Issue 7166](https://github.com/firefly-iii/firefly-iii/issues/7166) Rule trigger would trigger on the wrong
  transaction set
- [Issue 7112](https://github.com/firefly-iii/firefly-iii/issues/7112) Content filter would strip emojis
- [Issue 7175](https://github.com/firefly-iii/firefly-iii/issues/7175) Could not delete user invite
- [Issue 7177](https://github.com/firefly-iii/firefly-iii/issues/7177) Missing currency info would break cron job

### API

- [Issue 7127](https://github.com/firefly-iii/firefly-iii/issues/7127) Expand API with new option for "destroy" button.
- [Issue 7124](https://github.com/firefly-iii/firefly-iii/issues/7124) API would not break on missing foreign currency
  information

## 6.0.0 - 2023-03-03

This is release

**6.0.0** of Firefly III.

### Warnings

- The upgrade may not be backwards compatible for people who manage outgoing loans (money borrowed to other people).
  Transactions will be removed or changed. See [this Gist](https://gist.github.com/JC5/909385c5086f9e07ba2c32e047446d68)
  for more information.
- You will need to make a backup of your database.
- You must use PHP 8.2 or use the Docker container.

### Notes

- The new
    *
  *v3
  ** layout is not yet finished, and it should
    *
  *not
  ** be used to edit or add data.

### Added

- Introduce Jetbrains Qodana code scanning for code quality.
- Reintroduced PHPUnit tests
- Added a warning for people using the "remote user guard" in combination with Personal Access Tokens.
- Improved validation across the board.
- First code to validate a user's financial administration
- Dark mode CSS
- New language: Catalan
- "Working" beta of the new layout under `/v3/`
- There is a page for webhooks.
- [Issue 4975](https://github.com/firefly-iii/firefly-iii/issues/4975) Rules can copy/move description to notes and vice
  versa
- [Issue 5031](https://github.com/firefly-iii/firefly-iii/issues/5031) You can invite users to your installation when
  registration is off
- [Issue 5213](https://github.com/firefly-iii/firefly-iii/issues/5213) You can trigger recurring transactions beforehand
- [Issue 5592](https://github.com/firefly-iii/firefly-iii/issues/5592) Transactions have a little history box to show
  how rules changed them
- [Issue 5752](https://github.com/firefly-iii/firefly-iii/issues/5752) Firefly III can send Slack notifications instead
  of emails
- [Issue 5862](https://github.com/firefly-iii/firefly-iii/issues/5862) Search can filter on reconciled transactions
- [Issue 6086](https://github.com/firefly-iii/firefly-iii/issues/6086) All search filters can be negative by putting `-`
  in front of them
- [Issue 6441](https://github.com/firefly-iii/firefly-iii/issues/6441) Buttons to purge deleted data, which is easy for
  data imports
- [Issue 6457](https://github.com/firefly-iii/firefly-iii/issues/6457) Rule trigger 'transaction exists', that will
  always trigger
- [Issue 6526](https://github.com/firefly-iii/firefly-iii/issues/6526) Option to disable rules and/or webhooks when
  saving transactions
- [Issue 6605](https://github.com/firefly-iii/firefly-iii/issues/6605) You can search for external ID values

### Changed

- Completely rewritten documentation at https://docs.firefly-iii.org
- Bad escape in JS code has been fixed.
- Added date validation in routes for better script kiddie protection
- Shorter titles in object groups
- Piggy bank actions are created correctly
- Some bad spelling in a header check
- Various errors no longer throw a 500 but a 422 (validation failed)
- The translations now have a warning in the comments so people don't submit translations.
- [Issue 6824](https://github.com/firefly-iii/firefly-iii/issues/6824) Fix issue with bills.
- [Issue 6828](https://github.com/firefly-iii/firefly-iii/issues/6828) Catch bad number in API
- [Issue 6829](https://github.com/firefly-iii/firefly-iii/issues/6829) Better error catching in API
- [Issue 6831](https://github.com/firefly-iii/firefly-iii/issues/6831) TypeError when using remote authentication
- [Issue 6834](https://github.com/firefly-iii/firefly-iii/issues/6834) Will use IBAN in account names if account exists
  already with a different IBAN
- [Issue 6842](https://github.com/firefly-iii/firefly-iii/issues/6842) Switch from expense to revenue when importing
  data.
- [Issue 6855](https://github.com/firefly-iii/firefly-iii/issues/6855) Do not validate currency if currency is NULL,
  thanks @eps90!
- [Issue 6869](https://github.com/firefly-iii/firefly-iii/issues/6869) Liability created via API is not applying opening
  balance.
- [Issue 6870](https://github.com/firefly-iii/firefly-iii/issues/6870) Old inactive recurring transactions do not lose
  categories when the categories are deleted
- [Issue 6974](https://github.com/firefly-iii/firefly-iii/issues/6974) Auto budget amount fix.
- [Issue 6876](https://github.com/firefly-iii/firefly-iii/issues/6876) Date field is validated in recurring transactions
- [Issue 6581](https://github.com/firefly-iii/firefly-iii/issues/6581) Fields were not cleared in the transaction screen
  in some cases

### Fixed

- [Issue 7079](https://github.com/firefly-iii/firefly-iii/issues/7079) Bad date range in chart

### Removed

- [Issue 4198](https://github.com/firefly-iii/firefly-iii/issues/4198) The total available budget amount bar on
  the `/budgets` page is no longer manageable but will be auto-calculated
- Cryptocurrencies in default currency set
- Unused environment variables

### API

- URLs with underscores in them have been updated to use dashes instead (`piggy_banks` -> `piggy-banks`).
- [Issue 6130](https://github.com/firefly-iii/firefly-iii/issues/6130) You can now create a reconciliation transaction
- New `v2` endpoints, see new documentation at https://api-docs.firefly-iii.org
- Various API fixes

### Security

- [Issue 6826](https://github.com/firefly-iii/firefly-iii/issues/6826) Hide 2FA information when printing, thanks
  @jstebenne!
- Blocked users can access API, and users can unblock themselves using the API. This was reported in CVE-2023-0298.
- Several other low-key fixes.

## 6.0.0-beta.2 - 2023-02-20

### Warnings

- ⚠️ Make a backup of your database first!
- ⚠️ This version requires
    *
  *PHP
  8.2
  **.

You can access the new V3 layout under `/v3/`. If you decide to use or test it:

- ⚠️ Read the instructions under the ☠️ icon FIRST.
- ⚠️ The new layout is not yet finished. Use it to change your data at your own risk.

### Added

- Add max upload to debug page.

### Fixed

- Missing indexes in bill overview.
- Various dark mode fixes

### Security

- Bad escape in transaction currencies could cause XSS attacks.

### API

- All v1 and v2 routes checked and documented properly.

## 6.0.0-beta.1 - 2023-02-12

### Warnings

- ⚠️ Make a backup of your database first!
- ⚠️ This version requires
    *
  *PHP
  8.2
  **.

You can access the new V3 layout under `/v3/`. If you decide to use or test it:

- ⚠️ Read the instructions under the ☠️ icon FIRST.
- ⚠️ The new layout is not yet finished. Use it to change your data at your own risk.

### Added

- Introduce Jetbrains Qodana code scanning
- Reintroduced test framework

### Fixed

- [Issue 6834](https://github.com/firefly-iii/firefly-iii/issues/6834) Better check on IBANs
- Various small bugs

## 6.0.0-alpha.2 - 2023-02-05

### Warnings

- ⚠️ Make a backup of your database first!
- ⚠️ This version requires
    *
  *PHP
  8.2
  **.

You can access the new V3 layout under `/v3/`. If you decide to use or test it:

- ⚠️ Read the instructions under the ☠️ icon FIRST.
- ⚠️ The new layout is not yet finished. Use it to change your data at your own risk.

### Added

- Warning for remote user guard
- Improved validation
- Can now validate a user's financial administration
- Dark mode CSS

### Changed

- Various errors no longer throw a 500 but a 422 (validation failed)

### Removed

- Cryptocurrencies in default currency set
- Unused environment variables

### Fixed

- Bad escape in JS code.
- [Issue 6869](https://github.com/firefly-iii/firefly-iii/issues/6869) Liability created via API is not applying opening
  balance.
- [Issue 6870](https://github.com/firefly-iii/firefly-iii/issues/6870) Old inactive recurring transactions do not lose
  categories when the categories are deleted
- [Issue 6876](https://github.com/firefly-iii/firefly-iii/issues/6876) Date field is validated in recurring transactions
- [Issue 6974](https://github.com/firefly-iii/firefly-iii/issues/6974) Auto budget amount fix.
- Date validation in routes
- Shorter titles in object groups

### API

- Various API fixes

## 6.0.0-alpha.1 - 2023-01-16

This is the first release of the new 6.0.0 series of Firefly III. It should upgrade the database automatically BUT
please make a backup of your database first! I guarantee nothing.

This release was previously tagged "5.8.0" but due to backwards incompatible changes in liability management and a
destructive upgrade process, this is now "6.0.0". It will again be alpha.1.

### Warnings

- ⚠️ Make a backup of your database first!
- ⚠️ This version requires
    *
  *PHP
  8.2
  **.

You can access the new V3 layout under `/v3/`. If you decide to use or test it:

- ⚠️ Read the instructions under the ☠️ icon FIRST.
- ⚠️ The new layout is not yet finished. Use it to change your data at your own risk.

### Added

- ⚠️ Upgrade code for liability management. See
  also [this Gist](https://gist.github.com/JC5/909385c5086f9e07ba2c32e047446d68).
- New language: Catalan!

### Changed

- The translations now have a warning in the comments so people don't submit translations.

### Fixed

- [Issue 6824](https://github.com/firefly-iii/firefly-iii/issues/6824) Fix issue with bills.
- [Issue 6828](https://github.com/firefly-iii/firefly-iii/issues/6828) Catch bad number in API
- [Issue 6829](https://github.com/firefly-iii/firefly-iii/issues/6829) Better error catching in API
- [Issue 6831](https://github.com/firefly-iii/firefly-iii/issues/6831) TypeError when using remote authentication
- [Issue 6834](https://github.com/firefly-iii/firefly-iii/issues/6834) Will use IBAN in account names if account exists
  already with a different IBAN
- [Issue 6842](https://github.com/firefly-iii/firefly-iii/issues/6842) Switch from expense to revenue when importing
  data.
- [Issue 6855](https://github.com/firefly-iii/firefly-iii/issues/6855) Do not validate currency if currency is NULL,
  thanks @eps90!
- Piggy bank actions are created correctly
- Bad spelling in header check

### Security

- [Issue 6826](https://github.com/firefly-iii/firefly-iii/issues/6826) Hide 2FA information when printing, thanks
  @jstebenne!
- Blocked users can access API, and users can unblock themselves using the API. This was reported in CVE-2023-0298.

## 5.8.0-alpha.1 - 2023-01-08

This is the first release of the new 5.8.0 series of Firefly III. It should upgrade the database automatically BUT
make a backup of your database first! I guarantee nothing.

### Warnings

- ⚠️ Make a backup of your database first!
- ⚠️ This version requires
    *
  *PHP
  8.2
  **.

You can access the new V3 layout under `/v3/`. If you decide to use or test it:

- ⚠️ Read the instructions under the ☠️ icon FIRST.
- ⚠️ The new layout is not yet finished. Use it to change your data at your own risk.

### Added

Lots of new stuff that I invite you to test and break.

- [Issue 4975](https://github.com/firefly-iii/firefly-iii/issues/4975) Rules can copy/move description to notes and vice
  versa
- [Issue 5031](https://github.com/firefly-iii/firefly-iii/issues/5031) You can invite users to your installation when
  registration is off
- [Issue 5213](https://github.com/firefly-iii/firefly-iii/issues/5213) You can trigger recurring transactions beforehand
- [Issue 5592](https://github.com/firefly-iii/firefly-iii/issues/5592) Transactions have a little history box to show
  how rules changed them
- [Issue 5752](https://github.com/firefly-iii/firefly-iii/issues/5752) Firefly III can send Slack notifications instead
  of emails
- [Issue 5862](https://github.com/firefly-iii/firefly-iii/issues/5862) Search can filter on reconciled transactions
- [Issue 6086](https://github.com/firefly-iii/firefly-iii/issues/6086) All search filters can be negative by putting `-`
  in front of them
- [Issue 6441](https://github.com/firefly-iii/firefly-iii/issues/6441) Buttons to purge deleted data, which is easy for
  data imports
- [Issue 6457](https://github.com/firefly-iii/firefly-iii/issues/6457) Rule trigger 'transaction exists', that will
  always trigger
- [Issue 6526](https://github.com/firefly-iii/firefly-iii/issues/6526) Option to disable rules and/or webhooks when
  saving transactions
- [Issue 6605](https://github.com/firefly-iii/firefly-iii/issues/6605) You can search for external ID values
- Working beta of the new layout under `/v3/`
- New authentication screens that support dark mode.
- There is a page for webhooks.

### Changed

- Firefly III requires PHP 8.2
- Liabilities are no longer part of your net worth.
- Liabilities no longer need two transactions to be managed properly (see the documentation)

### Removed

- [Issue 4198](https://github.com/firefly-iii/firefly-iii/issues/4198) The total available budget amount bar on
  the `/budgets` page is no longer manageable but will be auto-calculated

### Fixed

Not many bugfixes (yet).

- [Issue 6581](https://github.com/firefly-iii/firefly-iii/issues/6581) Fields were not cleared in the transaction screen
  in some cases

### API

New `/v2/` endpoints are being implemented that prepare the application for (among other things) the ability
to manage multiple financial administrations. The documentation for these endpoints will be at
https://api-docs.firefly-iii.org/.

- [Issue 6130](https://github.com/firefly-iii/firefly-iii/issues/6130) You can now create a reconciliation transaction

## 5.7.18 - 2023-01-03

### Fixed

- [Issue 6775](https://github.com/firefly-iii/firefly-iii/issues/6775) OAuth authentication was broken for Authelia and
  other remote user providers.
- [Issue 6787](https://github.com/firefly-iii/firefly-iii/issues/6787) SQLite value conversion broke several functions

## 5.7.17 - 2022-12-30

### Fixed

- [Issue 6742](https://github.com/firefly-iii/firefly-iii/issues/6742) Error when a rule tries to add or remove an
  amount from a piggy bank
- [Issue 6743](https://github.com/firefly-iii/firefly-iii/issues/6743) Error when opening piggy bank overview
- [Issue 6753](https://github.com/firefly-iii/firefly-iii/issues/6753) Rules are not finding any transactions with
  trigger 'Amount is greater than 0'

## 5.7.16 - 2022-12-25

### Added

- You can now search for SEPA CT, thanks @dasJ!

### Changed

- Links go to [Mastodon](https://fosstodon.org/@ff3), not Twitter.
- Most if not all remaining float values removed. None were used in financial math.
- Expand Laravel Passport settings.

### Fixed

- [Issue 6597](https://github.com/firefly-iii/firefly-iii/issues/6597) Edit existing split transaction's source did not
  work properly.
- [Issue 6610](https://github.com/firefly-iii/firefly-iii/issues/6610) Fix search for attachments
- [Issue 6625](https://github.com/firefly-iii/firefly-iii/issues/6625) Page of the links is not displayed due to an
  error
- [Issue 6701](https://github.com/firefly-iii/firefly-iii/issues/6701) Ensure remote_guard_alt_email if changed, thanks
  @nebulade!
- Remove some null pointers in the code.
- Add missing locale data
- Fixed typo, thx @charlesteets!
- Various issues with piggy banks
- Clear cache after a transaction is deleted.
- Be more clear about registrations being disabled.

### Security

- Updated all packages and dependencies.

### API

- Fix API endpoint that would not accept two of the same dates.

## 5.7.15 - 2022-11-02

### Fixed

- You can no longer set the currency of expense and revenue accounts.
- Form elements are not spell checked anymore (privacy).
- [Issue 6556](https://github.com/firefly-iii/firefly-iii/issues/6556) Wrong value used in bill chart
- [Issue 6564](https://github.com/firefly-iii/firefly-iii/issues/6564) Right-Align numbers to match monetary value
  digits
- [Issue 6589](https://github.com/firefly-iii/firefly-iii/issues/6589) Webhook not fired after destroying transaction
- Add missing locale data

## 5.7.14 - 2022-10-19

### Fixed

- Bulk editing transactions works.
- Negative budgets no longer work.

## 5.7.13 - 2022-10-17

### Added

- [Issue 6502](https://github.com/firefly-iii/firefly-iii/issues/6502) A few students from @D7032E-Group-6 added MTD and
  YTD, thanks!

### Fixed

- [Issue 6461](https://github.com/firefly-iii/firefly-iii/issues/6461) Broken link in `/public` directory warning.
- [Issue 6475](https://github.com/firefly-iii/firefly-iii/issues/6475) Method name mixup.
- [Issue 6471](https://github.com/firefly-iii/firefly-iii/issues/6471) Fix float conversion
- [Issue 6510](https://github.com/firefly-iii/firefly-iii/issues/6510) Destroy transaction now also triggers liability
  recalculation.
- Amount check for budget amounts was too low.
- Some other small fixes

### API

- [Issue 6481](https://github.com/firefly-iii/firefly-iii/issues/6481) Mixup in API validation, fixed by @janw

## 5.7.12 - 2022-09-12

### Fixed

- [Issue 6287](https://github.com/firefly-iii/firefly-iii/issues/6287) Catch error when trying to email with invalid
  settings.
- [Issue 6423](https://github.com/firefly-iii/firefly-iii/issues/6423) Fix redis error, thanks @canoine!
- [Issue 6421](https://github.com/firefly-iii/firefly-iii/issues/6421) Fix issue with SQLite.
- [Issue 6379](https://github.com/firefly-iii/firefly-iii/issues/6379) Fix issue when user has lots of currencies but
  short list settings.
- [Issue 6333](https://github.com/firefly-iii/firefly-iii/issues/6333) Fix broken chart for reconciliation.
- [Issue 6332](https://github.com/firefly-iii/firefly-iii/issues/6332) Fix issue with uploading zipped PDF's.

## 5.7.11 - 2022-09-05

### Added

- [Issue 6254](https://github.com/firefly-iii/firefly-iii/issues/6254) Use Piggy Bank's start date in monthly suggestion
  by @rickdoesdev
- Add best practices badge.
- Various sanity checks on large amounts.

### Removed

- Service worker is removed.

### Fixed

- [Issue 6260](https://github.com/firefly-iii/firefly-iii/issues/6260)
- [Issue 6271](https://github.com/firefly-iii/firefly-iii/issues/6271) Improve settings for Redis, by @canoine
- [Issue 6283](https://github.com/firefly-iii/firefly-iii/issues/6283) Convert to deposit means the transaction loses
  its bill.
- Fix issue with foreign currencies in transaction form.
- Fix various issues with SQLite.
- [Issue 6379](https://github.com/firefly-iii/firefly-iii/issues/6379) Some foreign currencies not list for setting on
  new transactions
- Make 2FA code + validation more robust. Thanks to @jtmoss3991, @timaschew and @Ottega.

## 5.7.10 - 2022-07-16

### Fixed

- [Issue 6122](https://github.com/firefly-iii/firefly-iii/issues/6122) Type error on data import and display
- SQLite query issues fixed
- Fix nullpointer.
- [Issue 6168](https://github.com/firefly-iii/firefly-iii/issues/6168) Missing date overview in no-category list.
- [Issue 6165](https://github.com/firefly-iii/firefly-iii/issues/6165) Account numbers could not be shared between
  expense and revenue accounts.
- [Issue 6150](https://github.com/firefly-iii/firefly-iii/issues/6150) The first remote user would not get admin.
- [Issue 6118](https://github.com/firefly-iii/firefly-iii/issues/6118) Piggy bank events would not get copied when
  transaction was copied.

### Security

- Update packages

## 5.7.9 - 2022-06-01

### Fixed

- Symfony 6.1 requires PHP 8.1, so back to 6.0 for the time being.

## 5.7.8 - 2022-06-01

### Fixed

- Symfony 6.1 requires PHP 8.1, so back to 6.0 for the time being.

## 5.7.7 - 2022-06-01

### Fixed

- Fixed an issue where the login form would overflow a database field.
- [Issue 6113](https://github.com/firefly-iii/firefly-iii/issues/6113) Fix issue with number formatting.
- [Issue 5996](https://github.com/firefly-iii/firefly-iii/issues/5996) Catch bad library

### Added

- @turrisxyz added a dependency review, thanks!

## 5.7.6 - 2022-05-19

### Fixed

- [Issue 6058](https://github.com/firefly-iii/firefly-iii/issues/6058) Bad type-casting could break Firefly III on Home
  Assistant.
- [Issue 6059](https://github.com/firefly-iii/firefly-iii/issues/6059) Fix issue with missing list of bills when
  creating a recurring transaction from a transaction.
- Added missing DB integrity checks.

### Security

- Updated various packages

## 5.7.5 - 2022-05-06

### Fixed

- Fixed an issue where missing method names would break the API.
- [Issue 6040](https://github.com/firefly-iii/firefly-iii/issues/6040) Could not add or remove money from piggy banks
  without a target.
- [Issue 6009](https://github.com/firefly-iii/firefly-iii/issues/6009) `has_no_attachments:true` would not return
  transactions with
  *deleted* transactions.
- [Issue 6050](https://github.com/firefly-iii/firefly-iii/issues/6050) ja_JP is part of the Docker image

## 5.7.4 - 2022-05-03

### Fixed

- Fixed issue in method names.

## 5.7.3 - 2022-05-03

### Fixed

- Searching for `updated_at_before` and `created_at_before` works again.
- [Issue 6000](https://github.com/firefly-iii/firefly-iii/issues/6000) Bad math when dealing with multi-currency
  reconciliation.
- Remove unused CSS
- Fix bad migration.

### API

- Add error code to error message.

## 5.7.2 - 2022-04-13

### Fixed

- Not configuring email would break registration.
- Extra validation on piggy bank amounts.

## 5.7.1 - 2022-04-05

### Fixed

- Fixes an issue with showing piggy banks
- [Issue 5961](https://github.com/firefly-iii/firefly-iii/issues/5961) Fixes an issue registering new users

## 5.7.0 - 2022-04-04

- ⚠️ This release no longer supports LDAP.
- ⚠️ This is the last release that supports PHP 8.0
- 👍 Want to try the new v3 layout? At your own risk, browse to `/v3/`.

Please refer to the [documentation](https://docs.firefly-iii.org/xfirefly-iii/) and support channels if you run into
problems:

- [Gitter.im](https://gitter.im/firefly-iii/firefly-iii)
- [Twitter](https://twitter.com/Firefly_III/)
- [GitHub Issues](https://github.com/firefly-iii/firefly-iii/issues)
- [GitHub Discussions](https://github.com/firefly-iii/firefly-iii/discussions)

### Added

- Error email message now includes HTTP headers.
- [Issue 5373](https://github.com/firefly-iii/firefly-iii/issues/5373) You can give budgets notes, although they're not
  visible yet.
- [Issue 5648](https://github.com/firefly-iii/firefly-iii/issues/5648) The Docker image supports custom locales,
  see `.env.example` for instructions.
- [Issue 3984](https://github.com/firefly-iii/firefly-iii/issues/3984) [issue 5636](https://github.com/firefly-iii/firefly-iii/issues/5636) [issue 4903](https://github.com/firefly-iii/firefly-iii/issues/4903) [issue 5326](https://github.com/firefly-iii/firefly-iii/issues/5326)
  Lots of new search and rule operators. For the full list,
  see [search.php](https://github.com/firefly-iii/firefly-iii/blob/main/config/search.php) (a bit technical).
- [Issue 5269](https://github.com/firefly-iii/firefly-iii/issues/5269) It's possible to add piggy banks that have no
  explicit target amount goal.
- [Issue 4893](https://github.com/firefly-iii/firefly-iii/issues/4893) Bills can be given an end date and an extension
  date and will warn you about those dates.

### Changed

- [Issue 5757](https://github.com/firefly-iii/firefly-iii/issues/5757) Upgrade to Laravel 9.

### Deprecated

- [Issue 5911](https://github.com/firefly-iii/firefly-iii/issues/5911) Removed support for LDAP.

### Fixed

- [Issue 5810](https://github.com/firefly-iii/firefly-iii/issues/5810) Could not search for `no_notes:true` in some
  cases.
- [Issue 5869](https://github.com/firefly-iii/firefly-iii/issues/5869) Converting transactions would sometimes fail.
- [Issue 5870](https://github.com/firefly-iii/firefly-iii/issues/5870) Fixed broken link to instructions.
- [Issue 5903](https://github.com/firefly-iii/firefly-iii/issues/5903) API budget limits was broken due to upgraded
  package.
- [Issue 5852](https://github.com/firefly-iii/firefly-iii/issues/5852) It was not possible to recreate a currency.
- [Issue 5882](https://github.com/firefly-iii/firefly-iii/issues/5882) `no_external_url:true` was broken.
- [Issue 5770](https://github.com/firefly-iii/firefly-iii/issues/5770) Liabilities spent amount would be doubled.
- [Issue 4013](https://github.com/firefly-iii/firefly-iii/issues/4013) Date in email message was not localized.
- [Issue 5949](https://github.com/firefly-iii/firefly-iii/issues/5949) Deleting a transaction would sometimes send you
  back to a 404.

## x.x.x - 20xx-xx-xx

### Added

- Initial release.

### Changed

- Initial release.

### Deprecated

- Initial release.

### Removed

- Initial release.

### Fixed

- Initial release.

### Security

- Initial release.

### API

- Initial release.

# Full change log

Can be found here: https://docs.firefly-iii.org/references/firefly-iii/changelog/


