#!/usr/bin/env bash

if [ $# -lt 3 ]; then
	echo "usage: $0 <db-name> <db-user> <db-pass> [db-host] [wp-version]"
	exit 1
fi

DB_NAME=$1
DB_USER=$2
DB_PASS=$3
DB_HOST=${4-localhost}
WP_VERSION=${5-latest}

WP_TESTS_DIR=/tmp/wordpress-tests-lib
WP_CORE_DIR=${WP_CORE_DIR-/tmp/wordpress/}

#remove trailing slash
WP_CORE_DIR=${WP_CORE_DIR%/}

set -ex

download() {
	if [ `which curl` ]; then
		curl -s "$1" > "$2";
	elif [ `which wget` ]; then
		wget -nv -O "$2" "$1"
	fi
}

wp_core_version(){
	local version='trunk'

	if [ -f $WP_CORE_DIR/wp-includes/version.php ]; then
		if grep -q "wp_version = " $WP_CORE_DIR/wp-includes/version.php; then
			version=$(grep "wp_version = " $WP_CORE_DIR/wp-includes/version.php|awk -F\' '{print $2}')
		fi
	fi

	echo $version
}

wp_api_version(){
	local latest=''
	local api_url="http://api.wordpress.org/core/version-check/1.5/"

	if [ `which curl` ]; then
		latest=$(curl -s "$api_url" | head -n 4 | tail -n 1)
	elif [ `which wget` ]; then
		latest=$(wget -S -q -O - "$api_url" | head -n 4 | tail -n 1);
	fi

	echo $latest
}

wp_download_exists(){
	if [ `which curl` ]; then
		$(curl --output /dev/null --silent --head --fail "$1");
	elif [ `which wget` ]; then
		$(wget --spider $1 >/dev/null 2>&1);
	fi
}

install_wp() {

	if [ $WP_VERSION == 'latest' ]; then

		local archive_name='latest'
		local latest=$(wp_api_version)
		local url=https://wordpress.org/"wordpress-$latest".tar.gz

		# check if latest version exists
		if wp_download_exists $url; then
			WP_VERSION=$latest
			archive_name="wordpress-$latest"
		fi
	else
		local archive_name="wordpress-$WP_VERSION"
	fi

	local core_version=$(wp_core_version)

	if [ $core_version != 'trunk' ]; then
		if [ $core_version == $WP_VERSION ]; then
			return
		fi
	fi

	mkdir -p $WP_CORE_DIR

	if wp_download_exists "https://wordpress.org/${archive_name}.tar.gz"; then

		download https://wordpress.org/${archive_name}.tar.gz  /tmp/wordpress.tar.gz
		tar --strip-components=1 -zxmf /tmp/wordpress.tar.gz -C "$WP_CORE_DIR"

		download https://raw.github.com/markoheijnen/wp-mysqli/master/db.php "$WP_CORE_DIR/wp-content/db.php"
	else
		echo "Error: WordPress version not found."
		exit
	fi
}

install_test_suite() {
	# portable in-place argument for both GNU sed and Mac OSX sed
	if [[ $(uname -s) == 'Darwin' ]]; then
		local ioption='-i .bak'
	else
		local ioption='-i'
	fi

	# get the version from the installed WordPress version
	local core_version=$(wp_core_version)

	if [ $core_version != 'trunk' ]; then
		core_version="tags/"$core_version
	fi

	# Set up the testing suite from the core version
	mkdir -p $WP_TESTS_DIR

	if wp_download_exists "https://develop.svn.wordpress.org/$core_version/wp-tests-config-sample.php"; then

		svn export --quiet --force https://develop.svn.wordpress.org/$core_version/tests/phpunit/includes/ $WP_TESTS_DIR/includes
		cd $WP_TESTS_DIR

		download https://develop.svn.wordpress.org/$core_version/wp-tests-config-sample.php "$WP_TESTS_DIR"/wp-tests-config.php
		sed $ioption "s:dirname( __FILE__ ) . '/src/':'$WP_CORE_DIR/':" "$WP_TESTS_DIR"/wp-tests-config.php
		sed $ioption "s/youremptytestdbnamehere/$DB_NAME/" "$WP_TESTS_DIR"/wp-tests-config.php
		sed $ioption "s/yourusernamehere/$DB_USER/" "$WP_TESTS_DIR"/wp-tests-config.php
		sed $ioption "s/yourpasswordhere/$DB_PASS/" "$WP_TESTS_DIR"/wp-tests-config.php
		sed $ioption "s|localhost|${DB_HOST}|" "$WP_TESTS_DIR"/wp-tests-config.php
	fi
}

install_db() {
	# parse DB_HOST for port or socket references
	local PARTS=(${DB_HOST//\:/ })
	local DB_HOSTNAME=${PARTS[0]};
	local DB_SOCK_OR_PORT=${PARTS[1]};
	local EXTRA=""

	if ! [ -z $DB_HOSTNAME ] ; then
		if [ $(echo $DB_SOCK_OR_PORT | grep -e '^[0-9]\{1,\}$') ]; then
			EXTRA=" --host=$DB_HOSTNAME --port=$DB_SOCK_OR_PORT --protocol=tcp"
		elif ! [ -z $DB_SOCK_OR_PORT ] ; then
			EXTRA=" --socket=$DB_SOCK_OR_PORT"
		elif ! [ -z $DB_HOSTNAME ] ; then
			EXTRA=" --host=$DB_HOSTNAME --protocol=tcp"
		fi
	fi

	# create database
	mysqladmin create $DB_NAME --user="$DB_USER" --password="$DB_PASS"$EXTRA
}

install_wp
install_test_suite
install_db
