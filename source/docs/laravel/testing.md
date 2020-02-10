---
title: Testing with LdapRecord
description: LdapRecord-Laravel testing guide
extends: _layouts.laravel-documentation
section: content
---

# Testing

## Introduction

Testing LDAP integration for PHP has always been quite difficult. Any type of integration
that is needed, you either need a real LDAP server to test against, or you mock
every response given and assume the logic you have in place will work until
you do live testing with a real LDAP server.

LdapRecord-Laravel comes with an LDAP directory emulator.

## Directory Emulator

LdapRecord-Laravel

