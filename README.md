# ca.peaceworks.casl

Provides some basic fields and functionality to assist with compliance for Canadian Anti-Spam Legislation. 

These fields are for all contacts and track consent for communicating with them. Contacts that don't have consent (or where it's expired) will automatically have the no-bulk-email flag set. This extension was developed by PeaceWorks Technology Solutions.

This extension will NOT un-set the no-bulk-email flag once it's set! You need to do this yourself, if appropriate.

**This extension will also not populate the consent fields for you. You need to provide your own process or automation to populate them as appropriate (for example to auto-fill them when a form is filled out).**

Running this extension **does not by itself ensure that you are compliant with CASL**. For one thing, you need to make sure your processes are in line with compliance and that you are actually populating the fields to track consent (and keeping them updated over time). For another, we are not lawyers and we make no representations of this extension as making you full compliant with current or future CASL requirements. We recommend that you still do your own due diligence.

This extension was developed by PeaceWorks Technology Solutions. The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

This extension was tested with: 

* PHP v7.1
* CiviCRM v5.15.0

## Installation (Web UI)

This extension has not yet been published for installation via the web UI.

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl ca.peaceworks.casl@https://github.com/PeaceWorksTechnologySolutions/ca.peaceworks.casl/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/PeaceWorksTechnologySolutions/ca.peaceworks.casl.git
cv en casl
```

## Usage

Once this extension is enabled, you will see some new custom fields available for all contacts. You may need to then populate these fields yourself and adjust how they appear on your contact pages (for example if you're using the [Contact Layout Editor](https://civicrm.org/extensions/contact-layout-editor) extension.

There is also a settings page that you should review (in the menu under Administer -> Communications -> CASL Settings): /civicrm/admin/casl

## Known Issues

N/a

