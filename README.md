# Shells: Reverse & Web-based Shells
Collection of working web shells for different languages.

# rshell.php
This is a modified version of the PHP reverse shell created by pentestmonkey: http://pentestmonkey.net/tools/web-shells/php-reverse-shell. It adds the following additional items:

- Fixed terminal errors
- Added the variable TERM to the environment so commands such as 'clear' can be used.
- Adds additional information once a reverse shell has been obtained. (work in progress)
- Added a stealth function in order to avoid detection of the script.

### Stealth Feature
Currently, when enabled, the stealth feature will do the following:

- When loaded, an HTTP 404 is returned to the user. The HTTP 404 error message depends on the web server type which currently automatically detects Apach and Nginx.
- No other output will be generated. (using pentestmonkey's printit function)
