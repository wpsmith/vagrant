VVV ASS
=======

This is a repository of my [Auto-site Setups](https://github.com/varying-vagrant-vagrants/vvv/wiki/Auto-site-Setup) for [Vagrant](https://github.com/Varying-Vagrant-Vagrants/VVV). You can't use these without VVV. Well. Someone could. They're here, free to use and expand on as you like.

Included are two Multisites and a plugin test bed.

* http://multisite.dev - Subdomain Multisite (wordpress-musubdomain). Predefined vhost subdomains but did not build out: foo, bar, and baz.
* http://folder.multisite.dev - Subfolder Multisite (wordpress-mufolder)
* http://local.multisite-pre.dev - Ready for Multisite but not really (wordpress-mupre)
* http://local.plugins.dev - Plugin tests (wordpress-plugins)

The basic design of my scripts are from [Luke Woodward's auto setup scripts](https://github.com/lkwdwrd/vvv-auto-setup) and the only special one is the mupre install. That one wipes itself every time you provision, so you always have a clean slate to blow up Multisite. Can you tell I do this a lot? It does come with the first define written, though, to save a step.

# Usage

1. Copy everything into a folder off the www folder of your vagrant install.
2. Provision
3. Beer

This means you'll get the same URLs I use, of course. You'd have to fork to change it, though I'm open for suggestions on improvements.