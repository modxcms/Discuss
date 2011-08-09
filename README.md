## Discuss

A native, threaded forum solution for MODx Revolution.

This is an alpha prerelease. Therefore, things might break. But have fun. :)

Feel free to suggest ideas/improvements/bugs.

## Installation

Clone the Git repo in a directory outside of MODx.

That'll let you develop and run it straight from your Git repo. Next, build it
by copying the _build/build.config.sample.php to build.config.php, filling it out
and running _build/build.transport.php. This will create a transport zip file.

Go to Package Management, click 'Add New Package', and select 'Scan from Local'.
This will load the local package you just built. Install it, which will setup
the tables, Resources and other stuff.

Then add these system settings:

- discuss.core_path - point to path/of/yourdiscussrepo/core/components/discuss/
- discuss.assets_path - point to path/of/yourdiscussrepo/assets/components/discuss/
- discuss.assets_url - point to the web path to /url/of/yourdiscussrepo/assets/components/discuss/

Put the Discuss snippet in an empty template Resource:

`[[!Discuss]]`

Then make sure you've installed the FormIt and Login Extras.

Finally, create pages for registration, login, and updating profile, and turn discuss.sso_mode on. Map
the System Settings for each resource. Set the appropriate preHooks and postHooks for each snippet,
and you're ready to roll!