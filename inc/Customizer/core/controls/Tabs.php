<?php

namespace Stax\Customizer\Core\Controls;

class Tabs extends \WP_Customize_Control {

	/**
	 * The type of customize control being rendered.
	 *
	 * @since 1.1.45
	 * @var   string
	 */
	public $type = 'interface-tabs';

	/**
	 * The tabs with keys of the controls that are under each tab.
	 *
	 * @since 1.1.45
	 * @var array
	 */
	public $tabs;

	/**
	 * Controls from tabs.
	 *
	 * @var array
	 */
	public $controls;

	/**
	 * Add custom JSON parameters to use in the JS template.
	 *
	 * @return array
	 */
	public function json() {
		$json             = parent::json();
		$json['tabs']     = $this->tabs;
		$json['controls'] = $this->controls;
		return $json;
	}

	/**
	 * Underscore JS template to handle the control's output.
	 *
	 * @return void
	 */
	public function content_template() {
		?>
		<# if ( ! data.tabs ) { return; } #>

		<div class="stax-tabs-control" id="">
		<# var i = 1;
			for( tab in data.tabs) { #>
				<#
				var allControlsInTabs = ''
				_.each( data.controls[tab], function( val, key ) {
					allControlsInTabs+= key + ' '
					if(val){
						var allvals = Object.keys(val).map(function(e) {
							return val[e]
						});
						allvals = _.uniq(_.flatten(allvals))
						allvals = allvals.join(' ')
						allControlsInTabs += allvals
					}
				});
				#>
			<div class="stax-customizer-tab <# if( i === 1 ){#> active <#}#>" data-tab="{{tab}}">
				<label class="{{allControlsInTabs}}">
					<# if(data.tabs[tab]['icon']) { #>
						<i class="dashicons dashicons-{{data.tabs[tab]['icon']}}"></i>
					<# } #>
					{{data.tabs[tab]['label']}}
				</label>
			</div>
		<# i++;} #>
		</div>


		<?php
	}
}

