// Generated on 2015-11-16 using generator-angular 0.14.0
'use strict';

// # Globbing

module.exports = function (grunt) {

	// Automatically load required Grunt tasks
	require('jit-grunt')(grunt);

	var packages = grunt.file.readJSON('package.json'),
		changelog = grunt.file.read('CHANGELOG.md');

	var jsFilesRelease = {
		'<%= paths.dist %>/admin/js/admin.js': ['<%= paths.src %>/admin/scripts/admin.js'],
		'<%= paths.dist %>/admin/js/themer.js': ['<%= paths.src %>/admin/scripts/themer.js'],
		'<%= paths.dist %>/admin/js/widgets-page.js': ['<%= paths.src %>/admin/scripts/widgets-page.js'],
		'<%= paths.dist %>/admin/js/arcw.js': [
			'<%= paths.src %>/admin/scripts/polyfills.js',
			'<%= paths.src %>/admin/scripts/arcw.js'
		]
	};
	var jsFilesDev = {
		'<%= paths.dev %>/admin/js/admin.js': ['<%= paths.src %>/admin/scripts/admin.js'],
		'<%= paths.dev %>/admin/js/themer.js': ['<%= paths.src %>/admin/scripts/themer.js'],
		'<%= paths.dev %>/admin/js/widgets-page.js': ['<%= paths.src %>/admin/scripts/widgets-page.js'],
		'<%= paths.dev %>/admin/js/arcw.js': [
			'<%= paths.src %>/admin/scripts/polyfills.js',
			'<%= paths.src %>/admin/scripts/arcw.js'
		]
	};

	grunt.initConfig({

		packages: packages,
		changelog: changelog,

		paths: {
			src: "src",
			dist: "dist",
			dev: "dev",
			sass: "src/admin/scss",
			themes: "src/themes/scss"
		},

		watch: {
			sass: {
				files: ['<%= paths.sass %>/{,**/}*.scss'],
				tasks: ['compass:dev']
			},

			themes: {
				files: ['<%= paths.themes %>/{,**/}*.scss'],
				tasks: ['compass:themes']
			},

			files: {
				files: ['<%= paths.src %>/**/*.{png,svg,jpg,php,txt,css,mo}'],
				tasks: ['newer:copy:dev']
			},
			scripts: {
				files: ['<%= paths.src %>/admin/scripts/{,**/}*.js'],
				tasks: ['uglify:dev']
			},
			livereload: {
				options: {livereload: false},
				files: ['<%= paths.dist %>/**/*.css', '<%= paths.dist %>/**/*.js']
			}
		},

		// Compiles Sass to CSS and generates necessary files if requested
		compass: {
			options: {
				importPath: './bower_components'
			},
			dev: {
				options: {
					sassDir: '<%= paths.sass %>',
					cssDir: '<%= paths.dev %>/admin/css/',
					sourcemap: true
				}
			},
			themes: {
				options: {
					sassDir: '<%= paths.themes %>',
					cssDir: '<%= paths.dev %>/themes/',
					sourcemap: true
				}
			},

			release: {
				options: {
					sassDir: '<%= paths.sass %>',
					cssDir: '<%= paths.dist %>/admin/css/',
					environment: 'production',
					outputStyle: 'compressed'
				}
			},
			releaseThemes: {
				options: {
					sassDir: '<%= paths.themes %>',
					cssDir: '<%= paths.dist %>/themes/',
					environment: 'production',
					outputStyle: 'compressed'
				}
			}
		},

		uglify: {
			release: {
				options: {
					mangle: true
				},
				files: jsFilesRelease
			},
			dev: {
				options: {
					compress: false,
					sourceMapIncludeSources: true,
					sourceMap: true,
				},
				files: jsFilesDev
			}
		},

		copy: {
			dev: {
				expand: true,
				cwd: '<%= paths.src %>/',
				src: [
					'**/*.*',
					'!admin/scripts/**',
					'!admin/scss/**'
				],
				dest: '<%= paths.dev %>/'
			},
			release: {
				expand: true,
				cwd: '<%= paths.src %>/',
				src: [
					'**/*.*',
					'!admin/scripts/**',
					'!admin/scss/**'
				],
				dest: '<%= paths.dist %>/'
			}
		},

		replace: {
			debugDisable: {
				options: {
					patterns: [
						{
							match: /define\( 'ARCW_DEBUG', true \);/g,
							replacement: "define( 'ARCW_DEBUG', false );"
						}
					]
				},
				files: [
					{expand: true, flatten: true, src: ['<%= paths.dist %>/archives-calendar.php'], dest: 'dist/'}
				]
			},
			debugRemove: {
				options: {
					patterns: [
						{
							match: /debug[ ]?\(.+\);/g,
							replacement: ""
						}
					]
				},
				files: [
					{
						expand: true,
						flatten: true,
						cwd: '<%= paths.dist %>/',
						src: ['*.php'],
						dest: '<%= paths.dist %>'
					}
				]
			},
			version: {
				options: {
					patterns: [
						{
							match: "version",
							replacement: "<%= packages.version %>"
						}
					]
				},
				files: [
					{
						expand: true,
						flatten: true,
						cwd: '<%= paths.dist %>/',
						src: ['*.{php,txt}'],
						dest: '<%= paths.dist %>'
					}
				]
			},
			devVersion: {
				options: {
					patterns: [
						{
							match: "version",
							replacement: "<%= packages.version %>"
						}
					]
				},
				files: [
					{
						expand: true,
						flatten: true,
						cwd: '<%= paths.dev %>/',
						src: ['*.{php,txt}'],
						dest: '<%= paths.dev %>'
					}
				]
			},
			changelog: {
				options: {
					patterns: [{
						match: "changelog",
						replacement: "<%= changelog %>"
					}]
				},
				files: [
					{
						expand: true,
						flatten: true,
						src: ['<%= paths.dist %>/readme.txt'],
						dest: '<%= paths.dist %>/'
					}
				]
			},
			changelogTitles: {
				options: {
					patterns: [{
						match: /[#][ ]?(Changelog)/g,
						replacement: "== $1 =="
					}, {
						match: /[#]{2}[ ]?(.+)/g,
						replacement: "= $1 ="
					}]
				},
				files: [
					{
						expand: true,
						flatten: true,
						src: ['<%= paths.dist %>/readme.txt'],
						dest: '<%= paths.dist %>/'
					}
				]
			}
		},


		clean: {
			dev: ['<%= paths.dev %>/admin/js/*.js'],
			release: ['<%= paths.dist %>']
		}

	});

	grunt.registerTask('default', '', function () {
		grunt.fatal('Use "serve" or "release" tasks');
	});

	grunt.registerTask('serve', [
		'clean:dev',
		'compass:dev',
		'compass:themes',
		'uglify:dev',
		'newer:copy:dev',
		'replace:devVersion',
		'watch'
	]);

	grunt.registerTask('release', [
		'clean:release',
		'compass:release',
		'compass:releaseThemes',
		'uglify:release',
		'copy:release',
		'replace'
	]);

};