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

If you're wanting to modify the Snippets, simply replace the Discuss Snippet calls in the
Resources with [[!include]] calls, such as this for the DiscussThread snippet:

[[!include? &file=\`[[++discuss.core_path]]elements/snippets/snippet.discussthread.php\`]]

Where the include snippet is just:

`<?php $o = include $file; return $o; ?>`

And that's it!
