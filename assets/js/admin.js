console.log("Settings init");

(function($) { // Make $ available for jQuery

	$installBtn = $("#install");
	$installBtn.prop('disabled', false);

	$('#install_woocommerce').change(function() {
		$('.woocommerce-only').toggle(this.checked).toggleClass('is-open', this.checked);
	}).change();

	$('#install_main_theme').change(function() {
		$('.main-theme-only').toggle(this.checked).toggleClass('is-open', this.checked);
	}).change();

	$('#install_child_theme').change(function() {
		$('.child-theme-only').toggle(this.checked).toggleClass('is-open', this.checked);
	}).change();
	
	$installBtn.click(async function(e) {
		e.preventDefault();

		try {

			$installBtn.prop('disabled', true);
			console.log("begin install");

			const themes = {
				main: {
					method: $('#install_main_theme').prop('checked') ? 'zip' : null,
					url: $("#theme").val() || null
				},
				child: {
					method: $('#install_child_theme').prop('checked') ? ( $("#child-theme").val() ? 'zip' : 'new' ) : null,
					name: $('#child-theme-name').val() || null,
					url: $("#child-theme").val() || null
				}
			}
			let plugins = getTexareaAsArray("#plugins");

			if( $('#install_woocommerce').prop('checked') ) {
				plugins.push('woocommerce');
				plugins = plugins.concat( getTexareaAsArray("#woocommerce_plugins") );
			}

			console.log("full theme list", themes);
	
			// await installProcess(themes, plugins, "results");
			return $installBtn.prop('disabled', false)

		} catch(e) {
			console.log("error", e)
		}

	});

	function getTexareaAsArray( selector ) {

		var lines = $(selector).val().split(/\n/);
		var text = [];
		for (var i=0; i < lines.length; i++) {
			// only push this line if it contains a non whitespace character.
			if (/\S/.test(lines[i])) {
				text.push($.trim(lines[i]));
			}
		}

		return text

	}

	async function installProcess( themes, plugins, logId = false ) {

		console.log("Starting process...")

		try {

			for (var i = 0, len = plugins.length; i < len; i++) {
	
				logMessage(logId, `Installing ${plugins[i]}`);
	
				const installResult = await installPlugin(plugins[i], logId);
				console.log( "results", installResult );
				
				logMessage(logId, installResult.message )
	
			}

			for (const [type, theme] of Object.entries(themes)) {

				// Skip if no theme is set
				if( !theme ) continue;

				logMessage(logId, `Installing ${theme}`);

				const activate = themes.child ? type == 'child' : type == 'main';


				const installResult = await installTheme(theme, activate, logId);
				console.log( "install results", installResult );
				
				logMessage(logId, installResult.message );

			}

			console.log("End process")

		} catch(e) {
			console.log("error", e)
		}
		
	}

	async function installPlugin( plugin ) {

		try {

			var data = { 'action': plugin_prefix + 'install_plugin', plugin: plugin }
	
			result = await $.post(ajaxurl, data);
			return JSON.parse( result );

		} catch(e) {
			console.log("error", e)
		}

	}

	async function installTheme( theme, activate ) {

		try {

			var data = { 'action': plugin_prefix + 'install_theme', theme: theme, activate: activate }
	
			result = await $.post(ajaxurl, data);
			return JSON.parse( result );

		} catch(e) {
			console.log("error", e)
		}

	}

	function logMessage( id, message, error = false ) {
		
		const classes = "message " + ( error ? " error" : "" )
		$('#'+id).append( '<span class="' + classes + '">' + message + '</span>' );

	}

	const delay = time => new Promise(res=>setTimeout(res,time));

	
})( jQuery );