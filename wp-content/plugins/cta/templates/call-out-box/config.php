<?php
/**
 * WordPress: WP Calls To Action Template Config File
 * Template Name:  Call Out Box
 * @package  WordPress Calls to Action
 * @author 	InboundNow
 */

do_action('wp_cta_global_config'); // The wp_cta_global_config function is for global code added by 3rd party extensions

//gets template directory name to use as identifier - do not edit - include in all template files
$key = basename(dirname(__FILE__));
$this_path = WP_CTA_PATH.'templates/'.$key.'/';
$url_path = WP_CTA_URLPATH.'templates/'.$key.'/';

$wp_cta_data[$key]['info'] =
    array(
        'data_type' => 'template', // Template Data Type
        'version' => "1.0", // Version Number
        'label' => "Call Out Box", // Nice Name
        'category' => 'Responsive, Padded', // Template Category
        'demo' => 'http://demo.inboundnow.com/go/demo-template-preview/', // Demo Link
        'description'  => 'This is a simple box template', // template description
        'path' => $this_path //path to template folder
    );


/* Define Meta Options for template */
$wp_cta_data[$key]['settings'] =
    array(
        array(
            'label' => 'Instructions', // Name of field
            'description' => "Instructions for this call to action template go here", // what field does
            'id' => 'description', // metakey. $key Prefix is appended from parent in array loop
            'type'  => 'description-block', // metafield type
            'default'  => '<p>Insert your call to action graphic into the content area below. Don\'t forget to hyperlink it to your final destination</p>', // default content
            'context'  => 'normal' // Context in screen (advanced layouts in future)
        ),
        array(
            'label' => 'Headline Text Color',
            'description' => "Use this setting to change headline color",
            'id'  => 'headline-text-color',
            'type'  => 'colorpicker',
            'default'  => 'FFFFFF',
            'context'  => 'normal'
        ),
        /* Not Working
        array(
            'label' => 'Font',
            'description' => "Choose which font to use",
            'id'  => 'font',
            'type'  => 'dropdown',
            'default'  => 'Lato',
            'context'  => 'normal',
            'options' => array ('Aclonica' => 'Aclonica', 'Allan' => 'Allan', 'Annie Use Your Telescope' => 'Annie Use Your Telescope', 'Anonymous Pro' => 'Anonymous Pro', 'Allerta Stencil' => 'Allerta Stencil', 'Allerta' => 'Allerta', 'Amaranth' => 'Amaranth', 'Anton' => 'Anton', 'Architects Daughter' => 'Architects Daughter', 'Arimo' => 'Arimo', 'Artifika' => 'Artifika', 'Arvo' => 'Arvo', 'Asset' => 'Asset', 'Astloch' => 'Astloch', 'Bangers' => 'Bangers', 'Bentham' => 'Bentham', 'Bevan' => 'Bevan', 'Bigshot One' => 'Bigshot One', 'Bowlby One' => 'Bowlby One', 'Bowlby One SC' => 'Bowlby One SC', 'Brawler' => 'Brawler ', 'Buda' => 'Buda', 'Cabin' => 'Cabin', 'Calligraffitti' => 'Calligraffitti', 'Candal' => 'Candal', 'Cantarell' => 'Cantarell', 'Cardo' => 'Cardo', 'Carter One' => 'Carter One', 'Caudex' => 'Caudex', 'Cedarville Cursive' => 'Cedarville Cursive', 'Cherry Cream Soda' => 'Cherry Cream Soda', 'Chewy' => 'Chewy', 'Coda' => 'Coda', 'Coming Soon' => 'Coming Soon', 'Copse' => 'Copse', 'Corben' => 'Corben', 'Cousine' => 'Cousine', 'Covered By Your Grace' => 'Covered By Your Grace', 'Crafty Girls' => 'Crafty Girls', 'Crimson Text' => 'Crimson Text', 'Crushed' => 'Crushed', 'Cuprum' => 'Cuprum', 'Damion' => 'Damion', 'Dancing Script' => 'Dancing Script', 'Dawning of a New Day' => 'Dawning of a New Day', 'Didact Gothic' => 'Didact Gothic', 'Droid Sans' => 'Droid Sans', 'Droid Sans Mono' => 'Droid Sans Mono', 'Droid Serif' => 'Droid Serif', 'EB Garamond' => 'EB Garamond', 'Expletus Sans' => 'Expletus Sans', 'Fontdiner Swanky' => 'Fontdiner Swanky', 'Forum' => 'Forum', 'Francois One' => 'Francois One', 'Geo' => 'Geo', 'Give You Glory' => 'Give You Glory', 'Goblin One' => 'Goblin One', 'Goudy Bookletter 1911' => 'Goudy Bookletter 1911', 'Gravitas One' => 'Gravitas One', 'Gruppo' => 'Gruppo', 'Hammersmith One' => 'Hammersmith One', 'Holtwood One SC' => 'Holtwood One SC', 'Homemade Apple' => 'Homemade Apple', 'Inconsolata' => 'Inconsolata', 'Indie Flower' => 'Indie Flower', 'IM Fell DW Pica' => 'IM Fell DW Pica', 'IM Fell DW Pica SC' => 'IM Fell DW Pica SC', 'IM Fell Double Pica' => 'IM Fell Double Pica', 'IM Fell Double Pica SC' => 'IM Fell Double Pica SC', 'IM Fell English' => 'IM Fell English', 'IM Fell English SC' => 'IM Fell English SC', 'IM Fell French Canon' => 'IM Fell French Canon', 'IM Fell French Canon SC' => 'IM Fell French Canon SC', 'IM Fell Great Primer' => 'IM Fell Great Primer', 'IM Fell Great Primer SC' => 'IM Fell Great Primer SC', 'Irish Grover' => 'Irish Grover', 'Irish Growler' => 'Irish Growler', 'Istok Web' => 'Istok Web', 'Josefin Sans' => 'Josefin Sans Regular 400', 'Josefin Slab' => 'Josefin Slab Regular 400', 'Judson' => 'Judson', 'Jura' => ' Jura Regular', 'Just Another Hand' => 'Just Another Hand', 'Just Me Again Down Here' => 'Just Me Again Down Here', 'Kameron' => 'Kameron', 'Kenia' => 'Kenia', 'Kranky' => 'Kranky', 'Kreon' => 'Kreon', 'Kristi' => 'Kristi', 'La Belle Aurore' => 'La Belle Aurore', 'Lato' => 'Lato 100', 'Latoitalic' => 'Lato 100 (plus italic)', 'Lato' => 'Lato Light 300', 'Lato' => 'Lato', 'Lato:bold' => 'Lato Bold 700', 'Lato' => 'Lato', 'League Script' => 'League Script', 'Lekton' => ' Lekton ', 'Limelight' => ' Limelight ', 'Lobster' => 'Lobster', 'Lobster Two' => 'Lobster Two', 'Lora' => 'Lora', 'Love Ya Like A Sister' => 'Love Ya Like A Sister', 'Loved by the King' => 'Loved by the King', 'Luckiest Guy' => 'Luckiest Guy', 'Maiden Orange' => 'Maiden Orange', 'Mako' => 'Mako', 'Maven Pro' => ' Maven Pro', 'Maven Pro:500' => ' Maven Pro 500', 'Maven Pro' => ' Maven Pro 700', 'Maven Pro' => ' Maven Pro', 'Meddon' => 'Meddon', 'MedievalSharp' => 'MedievalSharp', 'Megrim' => 'Megrim', 'Merriweather' => 'Merriweather', 'Metrophobic' => 'Metrophobic', 'Michroma' => 'Michroma', 'Miltonian Tattoo' => 'Miltonian Tattoo', 'Miltonian' => 'Miltonian', 'Modern Antiqua' => 'Modern Antiqua', 'Monofett' => 'Monofett', 'Molengo' => 'Molengo', 'Mountains of Christmas' => 'Mountains of Christmas', 'Muli' => 'Muli Light', 'Muli' => 'Muli Regular', 'Neucha' => 'Neucha', 'Neuton' => 'Neuton', 'News Cycle' => 'News Cycle', 'Nixie One' => 'Nixie One', 'Nobile' => 'Nobile', 'Nova Cut' => 'Nova Cut', 'Nova Flat' => 'Nova Flat', 'Nova Mono' => 'Nova Mono', 'Nova Oval' => 'Nova Oval', 'Nova Round' => 'Nova Round', 'Nova Script' => 'Nova Script', 'Nova Slim' => 'Nova Slim', 'Nova Square' => 'Nova Square', 'Nunito:light' => ' Nunito Light', 'Nunito' => ' Nunito Regular', 'OFL Sorts Mill Goudy TT' => 'OFL Sorts Mill Goudy TT', 'Old Standard TT' => 'Old Standard TT', 'Open Sans' => 'Open Sans light', 'Open Sans' => 'Open Sans regular', 'Open Sans:600' => 'Open Sans 600', 'Open Sans' => 'Open Sans bold', 'Open Sans Condensed' => 'Open Sans Condensed', 'Orbitron' => 'Orbitron Regular (400)', 'Orbitron:500' => 'Orbitron 500', 'Orbitron' => 'Orbitron Regular (700)', 'Orbitron' => 'Orbitron', 'Oswald' => 'Oswald', 'Over the Rainbow' => 'Over the Rainbow', 'Reenie Beanie' => 'Reenie Beanie', 'Pacifico' => 'Pacifico', 'Patrick Hand' => 'Patrick Hand', 'Paytone One' => 'Paytone One', 'Permanent Marker' => 'Permanent Marker', 'Philosopher' => 'Philosopher', 'Play' => 'Play', 'Playfair Display' => ' Playfair Display ', 'Podkova' => ' Podkova ', 'PT Sans' => 'PT Sans', 'PT Sans Narrow' => 'PT Sans Narrow', 'PT Sans Narrow' => 'PT Sans Narrow (plus bold)', 'PT Serif' => 'PT Serif', 'PT Serif Caption' => 'PT Serif Caption', 'Puritan' => 'Puritan', 'Quattrocento' => 'Quattrocento', 'Quattrocento Sans' => 'Quattrocento Sans', 'Radley' => 'Radley', 'Raleway' => 'Raleway', 'Redressed' => 'Redressed', 'Rock Salt' => 'Rock Salt', 'Rokkitt' => 'Rokkitt', 'Ruslan Display' => 'Ruslan Display', 'Schoolbell' => 'Schoolbell', 'Shadows Into Light' => 'Shadows Into Light', 'Shanti' => 'Shanti', 'Sigmar One' => 'Sigmar One', 'Six Caps' => 'Six Caps', 'Slackey' => 'Slackey', 'Smythe' => 'Smythe', 'Sniglet' => 'Sniglet', 'Special Elite' => 'Special Elite', 'Stardos Stencil' => 'Stardos Stencil', 'Sue Ellen Francisco' => 'Sue Ellen Francisco', 'Sunshiney' => 'Sunshiney', 'Swanky and Moo Moo' => 'Swanky and Moo Moo', 'Syncopate' => 'Syncopate', 'Tangerine' => 'Tangerine', 'Tenor Sans' => ' Tenor Sans ', 'Terminal Dosis Light' => 'Terminal Dosis Light', 'The Girl Next Door' => 'The Girl Next Door', 'Tinos' => 'Tinos', 'Ubuntu' => 'Ubuntu', 'Ultra' => 'Ultra', 'Unkempt' => 'Unkempt', 'UnifrakturCook:bold' => 'UnifrakturCook', 'UnifrakturMaguntia' => 'UnifrakturMaguntia', 'Varela' => 'Varela', 'Varela Round' => 'Varela Round', 'Vibur' => 'Vibur', 'Vollkorn' => 'Vollkorn', 'VT323' => 'VT323', 'Waiting for the Sunrise' => 'Waiting for the Sunrise', 'Wallpoet' => 'Wallpoet', 'Walter Turncoat' => 'Walter Turncoat', 'Wire One' => 'Wire One', 'Yanone Kaffeesatz' => 'Yanone Kaffeesatz', 'Yanone Kaffeesatz' => 'Yanone Kaffeesatz', 'Yanone Kaffeesatz' => 'Yanone Kaffeesatz', 'Yanone Kaffeesatz' => 'Yanone Kaffeesatz', 'Yeseva One' => 'Yeseva One', 'Zeyada' => 'Zeyada' )
            ),
        */
        array(
            'label' => 'Content Alignment',
            'description' => "Center or Align Left",
            'id'  => 'content-alignment',
            'type'  => 'dropdown',
            'default'  => 'center',
            'context'  => 'normal',
            'options' => array( 'left' => 'left' , 'center' => 'center' )
        ),
        array(
            'label' => 'Header Text',
            'description' => "Header Text",
            'id'  => 'header-text',
            'type'  => 'text',
            'default'  => 'Awesome Text that makes you want to buy',
            'context'  => 'normal'
        ),
        array(
            'label' => 'Background Color',
            'description' => "Changes background color",
            'id'  => 'content-background-color',
            'type'  => 'colorpicker',
            'default'  => '222222',
            'context'  => 'normal'
        ),
        array(
            'label' => 'Message Text',
            'description' => "Message Text",
            'id'  => 'content-text',
            'type'  => 'wysiwyg',
            'default'  => 'Insert Content Here.',
            'context'  => 'normal'
        ),
        array(
            'label' => 'Content Text Color',
            'description' => "Use this setting to change the content text color",
            'id'  => 'content-text-color',
            'type'  => 'colorpicker',
            'default'  => 'ffffff',
            'context'  => 'normal'
        ),
        array(
            'label' => 'Button Background Color',
            'description' => "Use this setting to change the template's submit button color.",
            'id'  => 'submit-button-color',
            'type'  => 'colorpicker',
            'default'  => 'db3d3d'
        ),
        array(
            'label' => 'Button Text Color',
            'description' => "Use this setting to change the template's submit button text color.",
            'id'  => 'submit-button-text-color',
            'type'  => 'colorpicker',
            'default'  => 'ffffff'
        ),
        array(
            'label' => 'Button Link',
            'description' => "Link on the button.",
            'id'  => 'submit-button-link',
            'type'  => 'text',
            'default'  => 'http://www.inboundnow.com'
        ),
        array(
            'label' => 'Button Text',
            'description' => "Text on the button.",
            'id'  => 'submit-button-text',
            'type'  => 'text',
            'default'  => 'Click here'
        ),
        array(
            'label' => 'Show Button?',
            'description' => "You can toggle off the main CTA button if you are using a form in this CTA",
            'id'  => 'show-button',
            'type'  => 'dropdown',
            'default'  => 'true',
            'options' => array('true'=>'Show Button', 'false'=>'Hide Button', ),
            'context'  => 'normal'
        ),
        array(
            'label' => 'Button Width',
            'description' => "Select whether to have a fullwidth or non full width button.",
            'id'  => 'button-width',
            'type'  => 'dropdown',
            'default'  => 'block',
            'options' => array('block'=>'Full Width', 'inline-block'=>'Not Full Width', ),
            'context'  => 'normal'
        ),
    );


/* define dynamic template markup */
$wp_cta_data[$key]['markup'] = '<style>

#cta_container #content
{
	background: transparent;
}
#cta_container {
	margin:auto;

}
#cta_container #content {
	width: 400px;
	background: #222;
	padding-bottom: 15px;
}

#cta_container p {
	padding-right: 0px;
	padding-left: 0px;
	text-align: {{content-alignment}};
	color:#fff;
}

#cta_container p:first-child {
	margin-top: 0px;
	padding-top: 0px;
}
#cta_container p:last-child {
	margin-bottom: 0px;
	padding-bottom: 0px;
}


/*@import url(https://fonts.googleapis.com/css?family={{font}}:300,400,600); */
#cta_container {
  text-align: {{content-alignment}};
  font-family: Calibri, Helvetica, Arial, sans-serif;
  font-weight: 300;
}

#cta_container .the_content {
	font-family: Calibri, Helvetica, Arial, sans-serif;
	padding-left: 10px;
	padding-right: 10px;
	padding: 10px;
	display: block;
	width: 80%;
	margin: auto;
}
#cta_container #cta-link {
  text-decoration: none;
}
#cta_container .button {
  display: block;
  cursor: pointer;
  width: 200px;
  font-size: 22px;
  margin: auto;
  margin-top: 15px;
  margin-bottom: 15px;
  height: 50px;
  line-height: 50px;
  text-transform: uppercase;
  background: #db3d3d;
  border-bottom: 3px solid #c12424;
  color: #ffffff;
  text-decoration: none;
  border-radius: 5px;
  transition: all 0.4s ease-in-out;
}

#cta_container  .button:hover {
  background: #c12424;
  border-bottom: 3px solid #db3d3d;
}

#cta_container  .clicked {
  transform: rotateY(-80deg);
}

#cta_container {
	background-color: #{{content-background-color}};
	padding-top:7px;
	padding-bottom:20px;
	padding-left:20px;
	padding-right:20px;
	color: #{{content-text-color}};
	text-align: {{content-alignment}};
}
.cta_content span,.cta_content h2,.cta_content h3,.cta_content h4,.cta_content h5,.cta_content h6 {
	color: #{{content-text-color}};
}

.cta_content {
	padding-bottom: 10px;
}
.cta_button {
 display:block;
}
#cta_container .cta_button, #cta_container input[type="button"], #cta_container button[type="submit"], #cta_container input[type="submit"] {
	text-align:center;
	background: #{{submit-button-color}};
	border-bottom: 3px solid {{submit-button-color|brightness(55)}};
	color: #{{submit-button-text-color}};
	padding-left:20px;
	padding-right:20px;
	padding-top:7px;
	padding-bottom:7px;
	text-decoration: none;
	border-radius: 5px;
	transition: all 0.4s ease-in-out;
	margin-top: 5px;
	display: {{button-width}};
	font-size: 1.3em;
}
#cta_container form input[type="button"], #cta_container form button[type="submit"], #cta_container form input[type="submit"] {
	margin: auto;
	width: 91%;
	display: block;
	font-size: 1.3em;
}
.cta_button:hover, #cta_container input[type="button"]:hover, #cta_container button[type="submit"]:hover, #cta_container input[type="submit"]:hover {
	background: {{submit-button-color|brightness(55)}};
	border-bottom: 3px solid #{{submit-button-color}};
}
#cta_container  #main-headline {
	color:#{{headline-text-color}};
	margin-top: 0px;
	margin-bottom: 0px;
	padding-top: 20px;
    padding-bottom: 10px;
	font-weight:600;
	font-size:20px;
	padding-right: 0px;
	padding-left: 0px;
	display:block;
}
#cta_container a {
	text-decoration: none;
}
.cta_content input[type=text], .cta_content input[type=url], .cta_content input[type=email], .cta_content input[type=tel], .cta_content input[type=number], .cta_content input[type=password] {
	width:90%;
}
form  {
	max-width: 330px;
	margin: auto;
}
</style>

<div id="cta_container">
  <span id="main-headline">{{header-text}}</span>

    <div class="cta_content">
	{{content-text}}
    </div>
{% if {{ show-button }} == true %}
		<a id="cta-link" href="{{submit-button-link}}">
			<span class="cta_button">{{submit-button-text}}</span>
		</a>
{% endif %}
</div>';
