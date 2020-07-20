module.exports = {
	files: {
		'src/js/admin-script.js': {
			dist: 'assets/js/',
			webpack: true,
			rename: 'wcrbp-admin.js',
			watch: [ 'src/js/*.js' ]
		},
		'src/scss/admin-style.scss': {
			dist: 'assets/css/',
			combine_files: true,
			scss: true,
			autoprefixer: true,
			minify: true,
			rename: 'wcrbp-admin.css',
			watch: [ 'src/scss/*.scss' ]
		}
	},

	config: {
		bable_custom_config1: {
			presets: [ '@babel/env' ],
		}
	},
};
