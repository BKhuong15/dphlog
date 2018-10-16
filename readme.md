# DPH Minify

A simple minify website. Has basic user management and a history of the links created.

# Install
1. Clone the repository to an apache vhost.
2. Copy the platform.example.php file to platform.inc.php.
3. Edit the platform.inc.php file to give your database the desired name and specify a location that is writable by the apache vhost. Path should be relative to the vhost directory.
4. Visit the host with the path '/install.php'.
5. After being redirected login with "admin", "admin". Change your password after first login by selecting "admin" at the top right of the menu.
6. Return to "home" by selecting the "home" link in the menu at the top.
7. Paste the url that should be minified into the form and submit.
8. Use the "copy link" button to copy the minified URL to the clipboard.

# Wishlist
1. Track ip of the visitors in a log.
1. Prettify site.
1. Add MySQL support.