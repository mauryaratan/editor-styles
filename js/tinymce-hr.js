(function(tinymce) {
	tinymce.PluginManager.add('eds_mce_hr_button', function( editor, url ) {
		editor.addButton('eds_mce_hr_button', {
			icon: 'hr',
			tooltip: 'Horizontal line',
			onclick: function() {
				editor.windowManager.open( {
					title: 'Horizontal line',
					body: [
						{
							type: 'listbox',
							name: 'hr',
							label: 'Style',
							values: [
								{
									text: 'Dashed',
									value: 'eds-line-dashed'
								},
								{
									text: 'Dotted',
									value: 'eds-line-dotted'
								},
								{
									text: 'Double',
									value: 'eds-line-double'
								},
								{
									text: 'Plain',
									value: 'eds-line-plain'
								},
								{
									text: 'Strong',
									value: 'eds-line-strong'
								}
							]
						}
					],
					onsubmit: function( e ) {
						editor.insertContent( '<hr class="' + e.data.hr + '" />');
					}
				});
			}
		});
	});
})(tinymce);
