---
title: Why LdapRecord? | Adldap2 Migration
description: Why LdapRecord was created instead of supporting Adldap2
extends: _layouts.documentation
section: content
---

# Why LdapRecord?

[Adldap2](https://github.com/Adldap2/Adldap2) was originally a fork of the repository [adLDAP](https://github.com/adldap/adLDAP).
adLDAP contained some critical, project breaking bugs that prevented me from using it in any of my production PHP applications.
The repository owner went completely AFK and would not add contributors or accept pull requests to the repository to resolve any issues.
In haste due to my own project deadlines, Adldap2 was born.

Due to the immediacy to create something the community (and I) could use that was relatively solid in PHP projects,
Adldap2 lacked any real sense individuality. It still felt tied to its origins which felt wrong, since it was
rewritten completely from the ground up.

This brings me to my first reason for a new repository and name: **Identity**.

## Identity

**Adldap2** is simply an awful name, and due Adldap2 sharing its name with its predecessor, it never
really felt like it was *mine*. Working on it felt like I was patching someone else's repository
and work, rather than something *I* personally created.

This is something I think a lot of developers feel when working on other projects, whether it be for clients or for work.
We want to be the creators of our own projects and say -- "Hey, I **made** that". If we lack the ability to express our
own design and creative freedoms, then the desire to produce quality work falls by the wayside.

Since Adldap2 was a "successor" to the original adLDAP project, it needed to inherit much of its API so developers
could easily migrate. This brought its own problems, and brings me to my next point: **Design**.

## Design

After implementing the original adLDAP repository in my own projects, I needed to build something that was easy to
migrate to. This meant inheriting most of the original API design so other developers didn't have to completely rebuild
LDAP integration in their own projects. The design was eventually phased out over time, but it led to some bad
decisions on my part.

### Versions

> "Where is Adldap2 v1-v4"? Simple. They don't exist.

Versioning was my first bad decision. While the new Adldap2 repository had version **2** in its name, it began
its versioning at v5 to follow suit of the original repository that was left at v4. It's confusing and ugly.

This made sense to me at the time since I was initially making small adjustments to a forked version of the original
repository, but overtime as the similarities drifted apart quite drastically and I started to realize I should have started
at version 1.0 to represent an actual "successor" to the original project.

### Schema

> "Wouldn't it be great to somehow map **every** LDAP attribute in a PHP interface so your IDE could
> help you identify what attributes you were accessing without having to lookup the exact naming?"
> Steve thought innocently.

I thought this design would be **killer**. If I just build an interface that maps to LDAP attributes, I'll never
have to lookup or guess the naming of attributes. Then, I could swap the interface implementation depending on
which LDAP server I'm working with -- wonderful! Or so I thought.

Turns out, there are **thousands** of different LDAP attributes across each LDAP distribution. That means I had to
create **thousands** of interface methods that return a single string -- the attribute name. I literally excluded
schemas from being analyzed from [Scrutinizer](https://github.com/Adldap2/Adldap2/blob/master/.scrutinizer.yml),
**because it was severely degrading the score due to the sheer size of the thing**. That should have been my
first hint.

I realized this too late however -- my experience working with LDAP servers was severely lacking, and I already spent a ton
of time on it. I documented **each** interface method. I released the first major Adldap2 version (v5) with this "*feature*".
The design decision was in place, and I couldn't easily rework the entire system. *I do have my own deadlines after all.*

### Providers

> "Let's create a connection container, and we'll call it **Adldap**. Then, instead
> of connections we'll call them **Providers**. Get it? Because they "provide" a
> connection to your LDAP server?" I'm smart.

"Providers" seemed to confuse a quite a few developers (myself included), as well as the `Adldap` class.
You could create providers individually and utilize them without any `Adldap` instance.

The underlying `Adldap` container proved to be basically useless, and didn't provide much
besides the bootstrapping of event listeners. Take this code example for instance.

**This:**

```php
$ad = new Adldap();

$ad->addProvider(['...']);

$provider = $ad->connect();
```

**Is the same as:**

```php
$provider = new Provider(['...']);

$provider->connect();
```

### (Not-Really) Active Record

> *"Models are configured in your Schema, returned in search results, and are created by your Provider"*.
> Makes sense? No, no it doesn't.

Adldap2 never really implemented a **full** Active Record implementation. Models are returned in results, and to
use your own model classes, you have to swap them out in the inside of the badly designed Schema implementation.

To prevent breaking changes, overhauling documentation, creating upgrade guides and more -- I shoved a
square peg into a circle hole, creating a **half-**Active-Record-**half-**weird-ORM.

**This is Active Record:**

```php
// Creating a new user...
$user = new User();

// Getting existing users...
$users = User::get();
```

**This is not:**

```php
// Creating a new user...
$user = $provider->make()->user();

// Getting existing users...
$users = $provider->search()->users()->get();
```

## Changes

> "Okay, maybe if I remove this, change that, rebuild the other part...
> This is going to be so much upgrade documentation to write..."

After years went by, the projects ugliness grew, and in my mind I felt disappointed by it all.
Nothing in Adldap2 really screamed to me as well-designed, or easy to use. I needed to do
right by the community and myself to create something better, that's **enjoyable** to use.

When you use a project that makes you think "*Damn, that's some nice looking code there.*" -- that impression
sticks, and **I needed that**. I needed LdapRecord to *feel great* to use and to look as gorgeous as possible.

The sheer number of changes I wrote down as I went through the Adldap2 project source was staggering.
There was simply no way I could write an "upgrade" guide for everything I was looking to change.
Doing so would have generated such a massive amount of confusion, and not to mention an ever
increasing workload on my free time that I was already spending helping people get up and
running through GitHub issues.

I had to create a **brand**. Something that screams confidence and thoughtfulness. This new project **had**
to have *tons* of documentation, an easy API, a website -- and most importantly, fast & helpful support.
That is why I created **LdapRecord**. It is finally a project I feel completely connected to, something
I'm very proud of, and plan to support for as long as I'm working in this industry.

I really hope you love it. ❤️