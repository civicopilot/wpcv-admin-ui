# Configuration

Single site: Open Settings → WPCV Admin

Multisite: Network Admin → Settings → WPCV Admin on multisite.

## Welcome Panel: Reports Configuration

1. In CiviCRM Form Builder:

   - Add your Search Display to a **form**.
   - Set a **page route** for the form.
   - Enable the form’s **Reports listing** placement.

2. In Reports Configuration, select up to eight reports and drag them into order.
3. Use Open report to preview a destination, then save your selection.

Users see reports they can access. On multisite, selections are shared across the network and resolved on each site.

## Customize the panel

- **Heading:** replaces the default "Reports" title.
- **Welcome message:** optional guidance; leave blank to omit it.
- **Background color:** applies to the header and icon boxes, with automatic light or dark foreground contrast.
- **Keep welcome panel visible:** enabled by default. Turn it off to let users hide or restore the panel through Screen Options.

## UI Enhancements

- **Activity widget content:** choose content types for recently published and scheduled items and shows the content type badge.
- **Remove CiviCRM Icon menu item:** hides the CiviCRM logo and submenu, which you may not need with CAU’s command palette integration enabled. Log Out and View My Contact are already in the account menu at the top right of the WordPress admin bar for Wordpress/CiviCRM installs.

- **Consolidate CiviCRM Menu:** applies the [menu changes below](#consolidate-civicrm-menu).

## Consolidate CiviCRM Menu

An opinionated layout that reduces redundant links and groups related tasks.
Everyday work stays in top-level component menus; administration belongs under
Administer → the relevant component, such as CiviEvent.

Uncheck the setting to restore the default behavior. Changes apply across the network on multisite and do not modify stored
CiviCRM navigation records.

### Search

Removes the top-level Search menu after moving the four entries below into Contacts. Other Search entries are not moved.

### Contacts

Places these entries first:

1. Find Contacts
2. Advanced Search
3. Find Activities
4. SearchKit

Hides New Activity, New Email, and Contact Reports. 

**New Contact** groups New Individual, New Household, and New Organization and their subtype menus. 
**Import** groups Import Contacts, Import Activities, and
Import Custom Data.

### Contributions

Hides Contribution Reports, Premiums, Manage Price Sets, and
Personal Campaign Pages.

### Events

Hides Register Event Participant, Event Reports, Personal Campaign Pages, Event Templates, and Manage Price Sets.

### Mailings

Keeps New Mailing, Draft Mailings, Sent Mailings, and Archived Mailings directly accessible. 

Hides Mailing Reports.

- **A/B Testing:** groups New A/B Test and Manage A/B Tests.
- **SMS:** groups New SMS and Find Mass SMS; omitted when no SMS provider is configured.
- **Configuration:** hides Headers, Footers, and Automated Messages, Message Templates, and Site Email Addresses only when the user can access the same destinations under Administer → CiviMail.

### Memberships

Hides Membership Reports and Manage Price Sets.

### Campaigns

Keeps Campaign Dashboard first, followed by Surveys, containing:

- New Survey
- Conduct Survey
- Reserve Respondents
- Interview Respondents
- Release Respondents

Hides New Campaign and New Petition.

### Support

Moves from the top level to Administer → Support, preserving its children.

## CiviCRM Quick Search

A CiviCRM quick search appears in the WordPress site admin bar for users with CiviCRM access.

Choose a display in WPCV Quick Search → Autocomplete display and save settings. The optional companion extension [wpcvquicksearch](https://github.com/civicopilot/wpcvquicksearch) supplies a ready-to-use display that can be modified locally;
when that extension is enabled and no choice has been saved, its accessible display is selected automatically. You can choose another display without that extension, or choose No display selected to omit the admin-bar search.