# Init script for VVV Auto Site Setup
source site-vars.sh
echo "Commencing $site_name Site Setup"

# Make a database, if we don't already have one
echo "Creating $site_name database (if it's not already there)"
mysql -u root --password=root -e "CREATE DATABASE IF NOT EXISTS $database"
mysql -u root --password=root -e "GRANT ALL PRIVILEGES ON $database.* TO $dbuser@localhost IDENTIFIED BY '$dbpass';"

# Delete WordPress if it's present to force a clean every time:
if [ -d htdocs ]
	then
        echo "Resetting install"
    	# delete everything here
    	rm -rf htdocs
fi

# And now we install every time:
	echo "Installing WordPress using WP-CLI"
	mkdir htdocs
	# Move into htdocs to run 'wp' commands.
	cd htdocs
	# use WP CLI to download WP
	wp core download  --allow-root
	# Use WP CLI to create a `wp-config.php` file
	wp core config --dbname="$database" --dbuser=$dbuser --dbpass=$dbpass --extra-php < ../wp-constants --allow-root
	# Reset, just in case...
	wp db reset --yes
	# Use WP CLI to install WordPress
	wp core install --url=$domain --title="$site_name" --admin_user=$admin_user --admin_password=$admin_pass --admin_email=$admin_email --allow-root
	# Upgrade all WordPress.org plugins
	echo "Upgrading WordPress.org Plugins"
	wp plugin update --all --allow-root 
	# Upgrade all WordPress.org themes
	echo "Upgrading WordPress.org Plugins"
	wp theme update --all --allow-root 

	# Move back to root to finish up shell commands.
	cd ..

# Symlink working directories
# First clear out any links already present
find htdocs/wp-content/ -maxdepth 2 -type l -exec rm -f {} \;

# The Vagrant site setup script will restart Nginx for us
echo "$site_name is now set up!";
