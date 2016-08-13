# [Related Posts by Taxonomy](http://keesiemeijer.wordpress.com/related-posts-by-taxonomy) [![Build Status](https://travis-ci.org/keesiemeijer/related-posts-by-taxonomy.svg?branch=develop)](http://travis-ci.org/keesiemeijer/related-posts-by-taxonomy) #

Version:           2.2.2-alpha  
Requires at least: 3.9  
Tested up to:      4.6  

### Welcome to the GitHub repository for this plugin ###
This is the development repository for the WordPress plugin [Related Posts by Taxonomy](https://wordpress.org/plugins/related-posts-by-taxonomy).

The `master` branch is where you'll find the most recent, stable release.
The `develop` branch is the current working branch for development. Both branches are required to pass all unit tests. Any pull requests are first merged with the `develop` branch before being merged into the `master` branch. See [Pull Requests](https://github.com/keesiemeijer/related-posts-by-taxonomy/tree/develop#pull-requests)

## Description ##
This WordPress plugin displays related posts as thumbnails, links, excerpts or as full posts with a widget or shortcode. Posts with the **most terms in common** will display at the top. Use multiple taxonomies and post types to get the related posts. Include or exclude terms. Change the look and feel with your own html templates in your (child) theme.

More information can be found at the [WordPress repository](https://wordpress.org/plugins/related-posts-by-taxonomy) and at the official [plugin documentation](http://keesiemeijer.wordpress.com/related-posts-by-taxonomy/).

## Installation ##

* Clone the GitHub repository: `git clone https://github.com/keesiemeijer/related-posts-by-taxonomy.git`
* Or download it directly as a ZIP file: [https://github.com/keesiemeijer/related-posts-by-taxonomy/archive/master.zip](https://github.com/keesiemeijer/related-posts-by-taxonomy/archive/master.zip)

This will download the latest stable copy of Related Posts by Taxonomy.

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
Spanish (by [msoravilla](http://www.ludobooks.com/))  
Catalan (by [msoravilla](http://www.ludobooks.com/))  
Polish (by [koda0601](http://rekolekcje.net.pl/))  

## Contributions ##

There are various ways you can contribute:

1. Raise an [Issue](https://github.com/keesiemeijer/related-posts-by-taxonomy/issues) on GitHub
2. Send us a Pull Request with your bug fixes and/or new features
3. Translate Related Posts by Taxonomy into different languages
4. Provide feedback and suggestions on [enhancements](https://github.com/keesiemeijer/related-posts-by-taxonomy/issues?direction=desc&labels=Enhancement&page=1&sort=created&state=open)