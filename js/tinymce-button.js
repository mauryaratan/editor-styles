(function(tinymce) {
	tinymce.PluginManager.add('eds_mce_button_button', function( editor, url ) {
		editor.addButton('eds_mce_button_button', {
			icon: 'eds-button-button',
			tooltip: 'Add button',
			onclick: function() {
				editor.windowManager.open( {
					title: 'Insert Button',
					body: [
						{
							type: 'textbox',
							name: 'text',
							label: 'Button text'
						},
						{
							type: 'textbox',
							name: 'url',
							label: 'Button URL',
							value: 'http://'
						},
						{
							type: 'listbox',
							name: 'style',
							label: 'Style',
							values: [
								{
									text: 'Normal',
									value: 'eds-normal'
								},
								{
									text: 'Download',
									value: 'eds-download'
								}
							]
						},
						{
							type: 'listbox',
							name: 'color',
							label: 'Color',
							values: [
								{
									text: 'Primary',
									value: 'color-primary-background'
								},
								{
									text: 'Secondary',
									value: 'color-secondary-background'
								},
								{
									text: 'Green',
									value: 'eds-success'
								},
								{
									text: 'Red',
									value: 'eds-error'
								},
								{
									text: 'Orange',
									value: 'eds-important'
								}
							]
						}
					],
					onsubmit: function( e ) {
						editor.insertContent( '<a href="' + edsEscAttr( e.data.url ) + '" class="' + edsEscAttr( e.data.style ) + ' ' + edsEscAttr( e.data.color ) + ' eds-button">' + edsEscAttr( e.data.text ) + '</a>');
					}
				});
			}
		});
	});

	// @link http://stackoverflow.com/a/9756789/719811
	function edsEscAttr(s, preserveCR) {
		preserveCR = preserveCR ? '&#13;' : '\n';
		return ('' + s) /* Forces the conversion to string. */
			.replace(/&/g, '&amp;') /* This MUST be the 1st replacement. */
			.replace(/'/g, '&apos;') /* The 4 other predefined entities, required. */
			.replace(/"/g, '&quot;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/\r\n/g, preserveCR) /* Must be before the next replacement. */
			.replace(/[\r\n]/g, preserveCR);
	}
})(tinymce);
