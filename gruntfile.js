module.exports = function( grunt ) {

	var pkg = grunt.file.readJSON( 'package.json' ),
		// version = 'vX.Y.Z'
		version = pkg.version,
		// semver = 'X.Y.Z'
		semver = version.substring( 1, version.length ),
		// Files to include in a release
		distFiles =  [
			'google-calendar-events/**'
		];

	// Print current version number converted to semantic versioning
	console.log( pkg.version + ' => ' + semver );

	// Project configuration
	grunt.initConfig( {

		pkg: pkg,
		semver : semver,

		clean: {
			main: [ 'build' ]
		},

		copy: {
			main: {
				expand: true,
				src: distFiles,
				dest: 'build'
			}
		},

		compress: {
			main: {
				options: {
					mode: 'zip',
					archive: './build/google-calendar-events-<%= semver %>.zip'
				},
				expand: true,
				src: distFiles,
				dest: ''
			}
		},

		wp_deploy: {
			deploy: {
				options: {
					plugin_slug: 'google-calendar-events',
					build_dir: 'build/google-calendar-events'
				}
			}
		}

	} );

	// Load tasks
	require('load-grunt-tasks')(grunt);

	// Register tasks

	grunt.registerTask( 'release', ['clean', 'copy', 'compress'] );

	grunt.registerTask( 'deploy', ['release', 'wp_deploy'] );

	grunt.util.linefeed = '\n';
};