module.exports = function( grunt ) {

	require( 'load-grunt-tasks' )( grunt );

	'use strict';

	// Project configuration
	grunt.initConfig( {

		pkg: grunt.file.readJSON( 'package.json' ),

		githash: {
			main: {
				options: {},
			}
		},

		addtextdomain: {
			options: {
				textdomain: 'related-posts-by-taxonomy',
			},
			target: {
				files: {
					src: [ '*.php', '**/*.php', '!node_modules/**', '!bin/**' ]
				}
			}
		},

		makepot: {
			target: {
				options: {
					domainPath: '/lang',
					mainFile: 'related-posts-by-taxonomy.php',
					potFilename: 'related-posts-by-taxonomy.pot',
					potHeaders: {
						poedit: true,
						'x-poedit-keywordslist': true
					},
					type: 'wp-plugin',
					updateTimestamp: true
				}
			}
		},

		uglify: {
			options: {
				banner: '/*\n' +
					' * ' + '<%= pkg.name %>\n' +
					' * ' + 'v<%= pkg.version %>\n' +
					' * ' + '<%= grunt.template.today("yyyy-mm-dd") %>\n' +
					' **/\n'
			},

			target: {
				files: {
					'includes/assets/js/lazy-loading.min.js': [ 'includes/assets/js/lazy-loading.js' ]
				}
			}
		},

		sass: {
			dist: {
				files: {
					'includes/assets/css/editor.css': 'editor-block/src/editor.scss'
				}
			}
		},

		watch: {
			all: {
				files: [ "editor-block/src/editor.scss" ],
				tasks: [ "sass:dist" ],
				options: {
					spawn: false
				}
			}
		},

		// Clean up build directory
		clean: {
			main: [
				'build/<%= pkg.name %>',
				'editor-block/build',
				'includes/assets/js/editor-block.js.map',
			]
		},

		// Copy the theme into the build directory
		copy: {
			main: {
				src: [
					'**',
					'!node_modules/**',
					'!bin/**',
					'!tests/**',
					'!build/**',
					'!editor-block/**',
					'!webpack.config.js',
					'!Gruntfile.js',
					'!package.json',
					'!package-lock.json',
					'!composer.lock',
					'!phpunit.xml',
					'!README.md',
					'!readme.md',
					'!travis.yml',
					'!.git/**',
					'!.gitignore',
					'!.gitmodules',
					'!.gitattributes',
					'!.editorconfig',
				],
				dest: 'build/<%= pkg.name %>/'
			}
		},

		version: {
			readmetxt: {
				options: {
					prefix: 'Stable tag: *'
				},
				src: [ 'readme.txt' ]
			},
			tested_up_to: {
				options: {
					pkg: {
						"version": "<%= pkg.tested_up_to %>"
					},
					prefix: 'Tested up to: *'
				},
				src: [ 'readme.txt', 'readme.md' ]
			},
			requires_at_least: {
				options: {
					pkg: {
						"version": "<%= pkg.requires_at_least %>"
					},
					prefix: 'Requires at least: *'
				},
				src: [ 'readme.txt', 'readme.md' ]
			},
			plugin: {
				options: {
					prefix: 'Version: *'
				},
				src: [ 'readme.md', 'related-posts-by-taxonomy.php' ]
			},
		},

		replace: {
			replace_branch: {
				src: [ 'readme.md' ],
				overwrite: true, // overwrite matched source files
				replacements: [ {
					from: /related-posts-by-taxonomy.svg\?branch=(master|develop)/g,
					to: "related-posts-by-taxonomy.svg?branch=<%= githash.main.branch %>"
				} ]
			}
		},
		run: {
			build: {
				cmd: 'npm',
				options: {
					cwd: 'editor-block'
  				},
				args: [
					'run',
					'build'
				]
	 		}
		}
	} );


	grunt.registerTask( 'i18n', [ 'addtextdomain', 'makepot' ] );

	grunt.registerTask( 'travis', [ 'githash', 'replace:replace_branch' ] );

	// Creates build
	grunt.registerTask( 'build', [ 'clean:main', 'run:build', 'sass', 'uglify', 'version', 'makepot', 'travis', 'copy:main' ] );

	grunt.util.linefeed = '\n';
};