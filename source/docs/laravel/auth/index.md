---
title: Authentication Overview
description: Authenticating LDAP users into your application
extends: _layouts.documentation
section: content
---

LdapRecord-Laravel comes with two ways to authenticate LDAP users into your application.

The first and most common way of offering LDAP authentication is called **Synchronized Database LDAP Authentication**.
This means that the LDAP user that successfully logs in is created & synchronized to your local applications database.
This is helpful as you can attach typical relational database information to them, such as blog posts, photos, etc.

Let's walk through the process on how this works:

1. An LDAP user attempts to login to your application
2. LdapRecord attempts to locate the user in your LDAP directory
3. If a user is found, LDAP authentication now occurs and the users password is sent to your directory and validated
4. If authentication passes, a local database record is created in your `users` database table with the users attributes synchronized
5. 
