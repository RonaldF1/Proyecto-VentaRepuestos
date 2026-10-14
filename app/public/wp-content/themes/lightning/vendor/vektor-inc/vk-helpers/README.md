# VK Helpers

```
composer require vektor-inc/vk-helpers
```

PHPUnit test
```
npm install
npm run phpunit
```

## Customizer Controls

This package ships two global classes for the WordPress Customizer:

- `VK_Custom_Html_Control` — outputs a heading, sub-heading and arbitrary HTML inside a Customizer section.
- `VK_Custom_Text_Control` — text input with optional `input_before` / `input_after` strings and configurable `input_type` / `input_attrs`.

Both classes are declared inside the `customize_register` action with a `class_exists` guard, so they coexist safely with themes / plugins that ship the same class name (Lightning, Katawara, etc.).

### Example

```php
add_action(
	'customize_register',
	function ( $wp_customize ) {
		// HTML control (heading + description).
		$wp_customize->add_setting( 'vk_demo_html', array( 'default' => '' ) );
		$wp_customize->add_control(
			new VK_Custom_Html_Control(
				$wp_customize,
				'vk_demo_html',
				array(
					'label'            => __( 'Demo Section', 'your-textdomain' ),
					'label_tag'        => 'h3',
					'custom_title_sub' => __( 'Sub heading', 'your-textdomain' ),
					'custom_html'      => '<p>Optional explanation HTML.</p>',
					'section'          => 'title_tagline',
				)
			)
		);

		// Text control with number input and "px" suffix.
		$wp_customize->add_setting( 'vk_demo_width', array( 'default' => 100 ) );
		$wp_customize->add_control(
			new VK_Custom_Text_Control(
				$wp_customize,
				'vk_demo_width',
				array(
					'label'        => __( 'Width', 'your-textdomain' ),
					'section'      => 'title_tagline',
					'input_type'   => 'number',
					'input_after'  => 'px',
					'input_attrs'  => array(
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					),
				)
			)
		);
	},
	20
);
```

### Available properties

`VK_Custom_Html_Control`:

| Property | Type | Default | Description |
|---|---|---|---|
| `label_tag` | string | `h2` | Heading tag used to render `label`. Allowed: `h2` / `h3` / `h4` / `h5` / `h6`. |
| `custom_title_sub` | string | `''` | Sub-heading rendered as `h3.admin-custom-h3`. |
| `custom_html` | string | `''` | Arbitrary HTML body. Filtered through `wp_kses_post`. |

`VK_Custom_Text_Control`:

| Property | Type | Default | Description |
|---|---|---|---|
| `input_before` | string | `''` | HTML rendered before the input. |
| `input_after` | string | `''` | HTML rendered after the input. |
| `input_type` | string | `text` | HTML5 input type. Allowed: `text` / `number` / `email` / `url` / `tel` / `password` / `search`. Disallowed values fall back to `text`. |
| `input_attrs` | array | `array()` | Extra attributes on the `input`. Attribute names must match the HTML attribute-name token pattern (`/^[a-zA-Z_:][a-zA-Z0-9:._-]*$/`); names containing whitespace or other invalid characters are dropped. `type`, `value`, `style` and any `on*` event handlers are blocked. Boolean values become HTML5 boolean attributes (e.g. `'required' => true`). |


== Changelog ==

* 0.3.0
  [ Feature ] Add VK_Custom_Html_Control and VK_Custom_Text_Control for the Customizer (with `input_type`, `input_attrs` and `label_tag` support). Previously shipped in `vk-admin` 0.6.0 / 0.6.1 / 0.7.0; consolidated here.

* 0.2.1
  [ Bug fix ] Fix an issue where the correct post type was not retrieved on the post edit screen.
  [ Bug fix ] Fix Unit Test

* 0.2.0
  [ Bug fix ] Fix color modifi

* 0.1.0
  [ Other ] Add VK_Helpers alias

* 0.0.5
  [ Bug fix ] Cope with PHP8.2

* 0.0.4
  [ Bug fix ] Cope with PHP8.1
