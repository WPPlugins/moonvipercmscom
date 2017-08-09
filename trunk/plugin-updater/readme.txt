=== Plugin Updater ===
Contributors: tgardner
Donate link: http://tgardner.net/
Tags: plugins, plugin, ajax
Requires at least: 2.3
Tested up to: 2.3.2
Stable tag: 1.0.3

Allows for updating your wordpress plugins with the click of a button.

== Description ==

Project home is [tgardner.net](http://tgardner.net/ "Plugin Updater").

Update your your out of date WordPress plugins by using the new added "Update Now" option.

== Installation ==

1. Upload the folder 'plugin-updater' to the '/wp-content/plugins/' directory.
2. CHMOD 777 /wp-content/plugins
3. CHMOD 777 /wp-content/plugins/plugin-updater/packages
4. Activate the plugin through the 'Plugins' menu in WordPress.

Note: Alternatively to steps 2,3 you can use "CHOWN -R {APACHEUSER} /wp-content/plugins" where {APACHEUSER} is your apache username.

== Frequently Asked Questions ==

1. I've updated a plugin now I've got two versions appearing in my plugins menu?
This means the plugin that you updated has a non standard method of installation, generally this happens when the install process tells you to just upload the php file of the plugin to you plugins directory, rather than the whole folder. If you look in your plugins directory you will have a newly created folder containing the updated plugin. Open the readme and see how the installation process differs.


== Screenshots ==

1. Added "Update Now" button in the plugins page.
2. Plugin update in progress
