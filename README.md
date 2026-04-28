# WPCV Welcome Panel

WordPress plugin for adding a custom dashboard welcome panel on single and multisite WordPress installs so users can quickly access important links, reports, and common tasks.

## Overview

This plugin replaces the default WordPress welcome panel with a branded dashboard panel for WordPress / CiviCRM sites.

It is single and multisite compatible:
- Up to 6 cards with title, link, description fields
- Access rules per card with simplified permission selector
- Configurable panel color and icon set based on access rule
- On Multisite, one network-wide configuration

## Usage

- Manage multisite settings at `Network Admin > Settings > Network Welcome Panel` or single site at `Settings > Welcome Panel` and add quick links
- The panel is shown to users with `access_civicrm`

## FAQ

### Does the plugin automatically match CiviCRM page permissions?

No. The plugin does not try to inspect or inherit the permissions used by individual CiviCRM pages. For example, if a card links to the Contributions Dashboard, you should set the card's access rule to something like `access_civicontribute`.

### What makes this integrated with CiviCRM?

Not much yet, honestly. It mainly bakes in some top level permissions for CiviCRM. Tldr: It's meant as a proof of concept for client sites using CiviCRM, mainly to give users quick links to common Wordpress and CiviCRM-related pages and tasks.

### Why not use ACF for the admin settings form?

ACF could work well for a single-site options page and would make field management and icon selection easier. It is less ideal for multisite, since ACF does not natively support a network admin options page.

## Disclaimer

This plugin started as hardcoded internal solution from StackExchange before its public release. It has since been cleaned up and adapted to fit my clients' needs, and I am sharing it in case it is useful to others as well.

If it happens to fit your use case too, great. The plugin is provided on an `as is` basis, without any guarantee of support, compatibility, or ongoing development.
