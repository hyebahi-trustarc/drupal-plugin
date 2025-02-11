# TrustArc Cookie Consent Manager

This Drupal module integrates the TrustArc Cookie Consent Manager (CCM) to help you comply with privacy regulations like GDPR, CCPA, and LGPD. It allows you to configure and inject the TrustArc CMP script, display a banner, provide a Cookie Preferences link, and integrate with Google Consent Mode (v2). This plugin offers a user-friendly interface for adding the cookie consent manager script to the site without requiring any theme modifications.

## Features

* **Easy Configuration:** Configure your TrustArc CMP settings directly within the Drupal admin area.
* **Script Injection:** Injects the TrustArc CMP script into your website. Supports both Advanced and Pro versions of the script.
* **Banner Display:** Allows you to specify a container for your TrustArc banner.
* **Cookie Preferences Link:** Provides a way to add a Cookie Preferences link to your site, enabling users to manage their consent.
* **Google Consent Mode (v2) Integration:** Seamlessly integrates with Google Consent Mode v2, allowing you to dynamically adjust Google tags based on user consent.
* **Event Listener for GTM:** Supports integration with TrustArc events for GTM.

## Requirements

The TrustArc Cookie Consent Manager module requires a TrustArc account. You can obtain your CMP script from the TrustArc dashboard. Contact TrustArc Technical Account Manager or support if you need help.


## Installation

* Install as you would normally install a contributed Drupal module. Visit: https://www.drupal.org/docs/9/extend/installing-modules for further information.

## Configuration

1. Configure user permissions in Administration » People » Permissions
  - Access TrustArc Cookie Consent Manager
  - Users in roles with Add Scripts all over the site permission will add/remove the scripts from any position.

2. Add TrustArc CMP scripts in settings on the Administer -> Configuration -> Development -> TrustArc Cookie Consent Manager.

## Usage

1. After enabling the module, navigate to **TrustArc** in your Drupal admin menu.
2. Configure the following settings:
  * **CCM Version:** Select between "Advanced" and "Pro" version of the TrustArc CCM.
  * **CMP Script ID:** Enter your TrustArc CMP Script ID.
  * **CMP Script Params:** Enter any additional parameters for your TrustArc script.
  * **Banner Container:** Specify the ID for your TrustArc banner (e.g., `consent_blackbar`).
  * **Display Cookie Preferences Link:** Enable this option to display a Cookie Preferences link on your site.
  * **Cookie Preferences Selector:** (Optional) If you want to inject the link into a specific element, provide a CSS query selector (e.g., `#footer`). If left blank, the link will be appended to the `<body>`.
  * **Enable Google Consent Mode:** Enable to integrate with Google Consent Mode v2.
  * **Google Consent Mode Settings:**

3. Save the changes.

## FAQ

### How do I get my TrustArc CMP Script ID?
You can obtain your TrustArc CMP Script ID from your TrustArc account. If you need assistance, please contact TrustArc support.

### Can I use this module with any Drupal theme?
Yes, the TrustArc Cookie Consent Manager module is designed to work with any Drupal theme.

### How do I update the module?
You can update the module through the Drupal admin interface, just like any other Drupal module.

### What is Google Consent Mode and how does it work with this module?
Google Consent Mode allows you to adjust how your Google tags behave based on the consent status of your users. This module integrates with Google Consent Mode v2 to dynamically adjust Google tags according to user consent.

### How do I enable data redaction and URL passthrough for Google Analytics?
You can enable these options in the module settings under the "Google Consent Mode Settings" section.

### Where can I find more documentation on the TrustArc CMP?
For more detailed documentation, please refer to the TrustArc support site or contact TrustArc support.


### What happens when I enable TrustArc Events? =
When enabled, the plugin will send events using the dataLayer object.

### Does this plugin help with GDPR or CCPA compliance? =  

This plugin integrates TrustArc’s Consent Management Platform (CMP) to facilitate compliance with regulations such as **GDPR, CCPA, LGPD, and others**. However, compliance depends on how the site owner configures consent settings, discloses data collection, and manages consent records.  

To ensure compliance, verify that:  
- Your consent banner is correctly configured within the TrustArc CMP dashboard.  
- Your website’s privacy policy reflects your use of the CMP.  
- You regularly review your consent settings to align with privacy laws.
- You implement necessary mechanisms, such as Tag Manager or other consent-aware tools, to ensure that tracking scripts and third-party services respect users' preferences.

### Links to TrustArc's terms and policies:**

* Privacy Policy: [https://trustarc.com/privacy-policy/](https://trustarc.com/privacy-policy/)
* **Terms of Service:** [https://trustarc.com/subscription-services-agreement/](https://trustarc.com/subscription-services-agreement/)

**Important Note:** Installing this plugin alone does not guarantee compliance with GDPR, CCPA, or other privacy laws. You need to configure the plugin appropriately and ensure your cookie notice and consent management practices meet the specific legal requirements.

## Maintainers

Current maintainers:

* Haissam Yebahi - [hyebahi](https://www.drupal.org/u/hyebahi)
* Felipe Brito - [felipenbrito](https://www.drupal.org/u/felipenbrito)
