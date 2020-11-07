# Related Posts by Taxonomy

[![Docmentation](https://img.shields.io/badge/Documentation-%F0%9F%93%96-blueviolet)](http://keesiemeijer.wordpress.com/related-posts-by-taxonomy)
[![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/related-posts-by-taxonomy)](https://wordpress.org/plugins/related-posts-by-taxonomy/)
[![WordPress Plugin: Required WP Version](https://img.shields.io/wordpress/plugin/wp-version/related-posts-by-taxonomy)](https://wordpress.org/plugins/related-posts-by-taxonomy/)
[![WordPress Plugin: Tested WP Version](https://img.shields.io/wordpress/plugin/tested/related-posts-by-taxonomy)](https://wordpress.org/plugins/related-posts-by-taxonomy/)
[![WordPress Plugin Downloads](https://img.shields.io/wordpress/plugin/dm/related-posts-by-taxonomy)](https://wordpress.org/plugins/related-posts-by-taxonomy/)
[![Build Status](https://travis-ci.org/keesiemeijer/related-posts-by-taxonomy.svg?branch=master)](https://travis-ci.org/github/keesiemeijer/related-posts-by-taxonomy)

### Welcome to the GitHub repository for this plugin ###
This is the development repository for the WordPress plugin [Related Posts by Taxonomy](https://wordpress.org/plugins/related-posts-by-taxonomy).

The `master` branch is where you'll find the most recent, stable release.
The `develop` branch is the current working branch for development. Both branches are required to pass all unit tests. Any pull requests are first merged with the `develop` branch before being merged into the `master` branch. See [Pull Requests](#pull-requests)

## Description ##
This WordPress plugin displays related posts as thumbnails, links, excerpts or as full posts with a widget or shortcode. Posts with the **most terms in common** will display at the top. Use multiple taxonomies and post types to get the related posts. Include or exclude terms. Change the look and feel with your own html templates in your (child) theme.

Visit these resources for more information.

* [plugin documentation](http://keesiemeijer.wordpress.com/related-posts-by-taxonomy)
* [WordPress repository](https://wordpress.org/plugins/related-posts-by-taxonomy)
* [code reference](https://keesiemeijer.github.io/related-posts-by-taxonomy)

## Installation ##

* Clone the GitHub repository: `git clone https://github.com/keesiemeijer/related-posts-by-taxonomy.git`
* Or download and unzip the file: [https://github.com/keesiemeijer/related-posts-by-taxonomy/archive/master.zip](https://github.com/keesiemeijer/related-posts-by-taxonomy/archive/master.zip)
* Activate the plugin in the wp-admin.

## Pull Requests ##
When starting work on a new feature, branch off from the `develop` branch.
```bash
# clone the repository
git clone https://github.com/keesiemeijer/related-posts-by-taxonomy.git

# cd into the related-posts-by-taxonomy directory
cd related-posts-by-taxonomy

# switch to the develop branch
git checkout develop

# create new branch newfeature and switch to it
git checkout -b newfeature develop
```

## Creating a new build ##
To build the plugin without all the development files (as in the WP repository) use the following commands:
```bash
# Go to the master branch
git checkout master

# Install Grunt tasks
npm install

# Build the production plugin
grunt build
```
The plugin will be compiled in the `build` directory.

## Bugs ##
If you find an issue, let us know [here](https://github.com/keesiemeijer/related-posts-by-taxonomy/issues?state=open)!

## Support ##
This is a developer's portal for Related Posts by Taxonomy and should _not_ be used for support. Please visit the [support forums](https://wordpress.org/support/plugin/related-posts-by-taxonomy).

### Translations ###
Dutch  
French (by [Annie Stasse](http://www.artisanathai.fr/))  
Spanish (by [Ludobooks – Cuentos personalizados](http://www.ludobooks.com))  
Catalan (by [Ludobooks – Cuentos personalizados](http://www.ludobooks.com))  
Polish (by [koda0601](http://rekolekcje.net.pl/))  

## Contributions ##

There are various ways you can contribute:

1. Raise an [Issue](https://github.com/keesiemeijer/related-posts-by-taxonomy/issues) on GitHub
2. Send us a Pull Request with your bug fixes and/or new features
3. Translate Related Posts by Taxonomy into different languages
4. Provide feedback and suggestions on [enhancements](https://github.com/keesiemeijer/related-posts-by-taxonomy/issues?direction=desc&labels=Enhancement&page=1&sort=created&state=open)
