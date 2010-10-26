A note on GD & FreeType Library support.

If these options were not enable in PHP,
 either recompile with these options enabled,
 or redownload a binary package with these options enabled.

To tell if you have GD/FreeType enabled:

Open the included doc/captcha/test.php and uncomment the code block.
Save the changes.
Open your browser and type the direct link to "doc/captcha/test.php",
 ex: http://www.mysite.com/doc/captcha/test.php

You should see a page showing you your PHP configuration.
Scroll down and look for the gd section to see if they are enabled.

To enable GD/Freetype, uncomment "extension=php_gd2.dll" in php.ini,
 under "Dynamic Extensions" section.

Recomment the code block in info/captcha/test.php. This is for your security.
A blank php file displays a blank page,
 preventing unwanted users knowing your php configuration)


Last Updated
9/3/2009
