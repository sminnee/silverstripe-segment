Segment.com Integration
=======================

This module provides integration with Segment.com, via the tagmanager module.

## Setting up

To use the module to track pageviews, do the following:

 * Log into your Segment.com account
 * Create a JavaScript source
 * Open its code snippet, and find the API key int he analytics.load('...') line. Copy this value.
 * Open the CMS. Go to the Tag Manager section.
 * Create a tag of type Segment.com.  Populate the API key field with API key you copied from segment.com
 * Save

## Tracking logged-in user details

To track the email & name of logged in users

 * Open the Segment.com snippet you created in the Tag Manager section of the CMS
 * Check the box, "Send details of logged-in user"
 * Save

## Coming soon

 * Tracking of events for visiting marked pages.
