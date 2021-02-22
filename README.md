The following instructions will provide you with:

- A fresh Git repository
- Blank MySQL Database
- Fresh install of wordpress (with core files in their own subdirectory)
- Blank theme
- Set up to compile Sass via Compass
- Easy system for changing config settings outside of the repo, via local config override (`wp-config-local.php`)

You may wish to tailor this README to be specific to the site you are developing.

#Setup#

1.  Clone this repo directly into the live web directory (public_html, httpdocs, etc.) of your environment:

        git clone git@github.com:moppedisco/studioyacine-wordpress-starter.git .

    You most likely will want to remove the /.git/ directory and initiate a new git repository:

        rm -rf .git
        git init

2.  Download the latest copy (or any copy you wish) of wordpress and extract it to the `/wordpress/` directory.

    OSX:

        curl -O https://wordpress.org/latest.tar.gz
        tar -xvzf latest.tar.gz

    You should probably remove the archive file too:

        rm latest.tar.gz

3.  Create databases for live, staging, development as required.

    From a linux command line this is:

         mysql -u root -p

         (enter mysql root password)

         > CREATE USER 'dbuser'@'localhost' IDENTIFIED BY 'passw0rd!';
         > CREATE DATABASE dbname;
         > GRANT ALL PRIVILEGES ON dbname.* TO dbuser@localhost;
         > exit

4.  Edit `wp-config.php` adding the database details for the LIVE (production) environment, and generating the 'Authentication Unique Keys and Salts', as with a normal Wordpress install.

5.  Create a wp-config-local.php to override the database details locally.

    Copy and paste the following into a new file and save as 'wp-config-local.php':

        <?php
        // Local server settings

        define('WP_ENV', 'development');

        // Local Database
        define('DB_NAME', 'starter');
        define('DB_USER', 'dbuser');
        define('DB_PASSWORD', 'dbpass');
        define('DB_HOST', 'localhost');

        // Turn on debug for local environment
        define('WP_DEBUG', true);

        define('WP_SITEURL', 'http://' . $_SERVER['HTTP_HOST'] . '/wordpress');
        define('WP_HOME',    'http://' . $_SERVER['HTTP_HOST']);
        define('WP_CONTENT_DIR', $_SERVER['DOCUMENT_ROOT'] . '/wp-content');
        define('WP_CONTENT_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/wp-content');

    Update this file with your local database details, '`wp-config-local.php`' can not be included in the repository as it would override the live wp-config.php if deployed, it is excluded in .gitignore so needs to be re-added for each install.

6.  Navigate to the web root in a browser and run Wordpress setup.

7.  Rename theme folder and give theme a name in style.css.

8.  Now you're probably seeing a blank page when navigating to the web root in browser! That's because Wordpress is looking for "twentyfifteen" (or whatever is currently the default theme) and it's been removed. Fix this by logging into the admin and selecting your theme in Appearance > Themes.

9.  Now would be a good time to make an initial git commit:

        git add .
        git commit -m "Initial Commit"
