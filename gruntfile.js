module.exports = function( grunt ) {

	var pkg = grunt.file.readJSON( 'package.json' ),
		// Files to include in a release
		distFiles =  [
			'google-calendar-events/**'
		];

	console.log( pkg.title + ' - ' + pkg.version );

	grunt.initConfig( {

		pkg: pkg,

		checktextdomain: {
			options:{
				text_domain: 'gce',
				correct_domain: false,
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				]
			},
			files: {
				src:  ['google-calendar-events/**/*.php'],
				expand: true
			}
		},

		makepot: {
			target: {
				options: {
					cwd: 'google-calendar-events',
					domainPath: '/languages',
					exclude: [],
					include: [],
					mainFile: 'google-calendar-events.php',
					potComments: '',
					potFilename: 'gce.pot',
					potHeaders: {
						poedit: true,
						'report-msgid-bugs-to': 'https://github.com/pderksen/WP-Google-Calendar-Events/issues',
						'last-translator' : 'Phil Derksen <pderksen@gmail.com>',
						'language-Team' : 'Phil Derksen <pderksen@gmail.com>',
						'x-poedit-keywordslist': true
					},
					type: 'wp-plugin',
					updateTimestamp: true,
					updatePoFiles: true
				}
			}
		},

		po2mo: {
			files: {
				src: 'google-calendar-events/languages/*.po',
				expand: true
			}
		},

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
					archive: './build/google-calendar-events-<%= pkg.version %>.zip'
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

	require('load-grunt-tasks')(grunt);

	grunt.registerTask( 'localize', ['checktextdomain', 'makepot', 'po2mo'] );
	grunt.registerTask( 'build',    ['clean', 'copy', 'compress'] );
	grunt.registerTask( 'release',  ['build'] );
	grunt.registerTask( 'deploy',   ['release', 'wp_deploy'] );

	grunt.util.linefeed = '\n';
};
