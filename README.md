# WPCV Admin Enhancements

Provides a contact quick search in the admin bar, a configurable welcome panel with CiviCRM report links, and other UI enhancements.

**Alpha:** experimental plugin under active development; features and settings may change most notably the welcome panel.

## Features

- Contact search in the WordPress admin bar using a SearchKit autocomplete display.
- Customizable welcome panel with up to eight ordered CiviCRM report links.
- Extends the Activity widget with content-type selection and labels.
- Cleanup CiviCRM menu
- Shared network settings on multisite, with reports resolved per site.

## Installation

1. Place this plugin in `wp-content/plugins/wpcv-admin` and activate it; network activate on multisite.
2. Follow the [configuration guide](docs/configuration.md) to set up the features.

## Roadmap

Possible next steps, not yet implemented:

- **At-a-glance performance:** an optional compact overview above the report links, with totals, trends and comparisons across clearly defined periods.
- **Component-based summaries:** offer useful starting points for contributions, memberships and upcoming events, shown only for enabled components and chosen by administrators. Allow custom Search Displays to replace those defaults.
- **From insight to action:** link summaries to supporting reports, such as memberships expiring soon or upcoming event registrations.


## Developers

It may make sense to port changes from this experimental plugin into the [CiviCRM WordPress plugin](https://lab.civicrm.org/dev/wordpress) or [CiviCRM Admin Utilities](https://github.com/christianwach/civicrm-admin-utilities) after a feature is stable and widely liked.

For code layout and implementation details, see the [developer guide](docs/developers.md).
