# Init script for VVV Auto Site Setup
source site-vars.sh
echo "Commencing $site_name Site Setup"

# Make a database, if we don't already have one
echo "Creating $site_name database (if it's not already there)"
mysql -u root --password=root -e "CREATE DATABASE IF NOT EXISTS $database"
mysql -u root --password=root -e "GRANT ALL PRIVILEGES ON $database.* TO $dbuser@localhost IDENTIFIED BY '$dbpass';"

# Install WordPress if it's not already present.
if [ ! -d htdocs ]
	then
	echo "Installing WordPress using WP-CLI"
	mkdir htdocs
	# Move into htdocs to run 'wp' commands.
	cd htdocs
	# use WP CLI to download WP
	wp core download  --allow-root
	# Use WP CLI to create a `wp-config.php` file
	wp core config --dbname="$database" --dbuser=$dbuser --dbpass=$dbpass --extra-php < ../wp-constants --allow-root
	# Use WP CLI to install WordPress
	wp core multisite-install --url=$domain --subdomains --title="$site_name" --admin_user=$admin_user --admin_password=$admin_pass --admin_email=$admin_email --allow-root

	#Install all WordPress.org plugins in the org_plugins file using CLI
	echo "Installing WordPress.org Plugins"
	# wp plugin install PLUGINNAME --allow-root 

	# Move back to root to finish up shell commands.
	cd ..
fi

# Symlink working directories
# First clear out any links already present
find htdocs/wp-content/ -maxdepth 2 -type l -exec rm -f {} \;

# The Vagrant site setup script will restart Nginx for us
echo "$site_name is now set up!";
