<?php
require_once(dirname(__FILE__) . '/class.base.php');

if (!class_exists("ImageFormatrAdmin")) {
    class ImageFormatrAdmin extends ImageFormatrBase {

        // default html admin options for activation
        var $def_options = array(
                'capatt'    => "title",
                'newtitle'  => "Click here to enlarge.",
                'yankit'    => "",
                'imglong'   => "180",
                'imgshort'  => "",
                'img2long'  => "",
                'img2short' => "",
                'img2page'  => "3",
                'dofx'      => "on",
                'force'     => "",
                'stdthumb'  => "on",
                'attthumb'  => "fullsize",
                'killanc'   => "on",
                'inspect'   => "",
                'addclass'  => "img",
                'remclass'  => "",
                'xcludclass'=> "wp-smiley",
                'capclass'  => "",
                'group'     => "main",
                'uninstal'  => "",
                'prettyuse' => "on",
                # legacy options used for deactivation removal
                'highuse'   => null,  # old Highslide library
                'homelong'  => null,  # replaced by img2long
                'homeshort' => null,  # replaced by img2short
                'yanktit'   => null,  # typo for yankit
        );

        function admin_init ( )
        {
            $this-> option_descriptions = array(
                'capatt'    => array(
                    'title' => 'Caption attribute',
                    'desc'  => 'The image attribute to be used as the caption.',
                    'code'  => '<img title="Gone With the Wind" alt="book">',
                               ),
                'newtitle'  => array(
                    'title' => 'Title replacement',
                    'desc'  => 'The new image title (used for the mouse-over hint in most browsers).',
                    'code'  => '<img title="'. $this->get_option('newtitle') .'">',
                    'html'  => '<p>NOTE: this will be overridden by the <em>Strip title</em> option.</p>',
                               ),
                'group'     => array(
                    'title' => 'Slideshow group',
                    'desc'  => 'You can organize images into groups by giving them a <code>group</code> attribute.',
                    'code'  => '<img group="'. $this->get_option('group') .'">',
                    'html'  => '<p>NOTE: this setting is the default group for all images <b>without</b> the <em>group</em> attribute.</p>',
                               ),
                'yankit'    => array(
                    'title' => 'Strip title',
                    'desc'  => 'Blank out the image title.',
                    'code'  => '<img title=""/>',
                    'html'  => array('NOTE: this will override the <em>Title replacement</em> option.'),
                               ),
                'killanc'   => array(
                    'title' => 'Ignore anchors',
                    'desc'  => 'Ignore any image anchors in the content we process.',
                    'code'  => '<a href="dont-ignore-me.html"><img usemya="true"></a>',
                    'html'  => '<p>NOTE: This option will be overridden with an image`s <code>usemya</code> attribute.</p>',
                               ),
                'dofx'      => array(
                    'title' => 'Popup effects',
                    'desc'  => 'Wrap the image in a popup zoom anchor.',
                    'code'  => '<a rel="prettyPhoto"><img/></a>',
                               ),
                'force'     => array(
                    'title' => 'Force root',
                    'desc'  => 'Force relative parent location of images to the root.',
                    'html'  => "<p>Interpret <code>&lt;img src=&quot;../images/1.jpg&quot;/&gt;</code> as <code>&lt;img src=&quot;/images/1.jpg&quot;/&gt;</code> which helped when I changed my permalinks.</p>",
                               ),

                'stdthumb'  => array(
                    'title' => 'Standardize thumbnails',
                    'desc'  => 'Try to size all images to the thumbnail dimensions.',
                    'code'  => '<img usemysize="true" width="200" height="132"/>',
                    'html'  => array('Try to size all the thumbnails to the dimensions below even if you have width &amp; height set in your image tags.',
                                     'So, enable this if you want to ignore any width &amp; height settings in your image tags.  This option will be overridden with an image`s <code>usemysize</code> attribute.'),
                               ),
                'inspect'   => array(
                    'title' => 'Auto determine orientation',
                    'desc'  => 'Try to determine the dimensions of the image to see if it is portrait (standing up) or layout (laying down) before deciding if the width/height is the long or short edges.',
                    'link'  => 'http://php.net/manual/en/function.getimagesize.php',
                    'html'  => array('<em>Uses PHP <a href="http://php.net/manual/en/function.getimagesize.php" target="_blank">GetImageSize</a> function.</em>',
                                     'NOTE: may cause pages with lots of images to load slowly.'),
                               ),
                'imgdefs'   => array(
                    'name'  => 'imglong, imgshort',
                    'title' => 'Default dimensions',
                    'desc'  => 'These values will be used as the width & height.',
                    'html'  => array('These values will be used as the width &amp; height in pixels by the <em>Auto determine orientation</em> setting which, if disabled will default to the <code>width</code> in the first box and the <code>height</code> in second box.',
                                     'NOTE: leave one of the boxes blank (or zero) and it will be calculated using the aspect ratio to the other box.'),
                               ),
                'imgaddl'   => array(
                    'name'  => 'img2long, img2short, img2page',
                    'title' => 'Additional dimensions',
                    'desc'  => 'These values will be used as the width & height.',
                    'html'  => array('These dimensions are used if you want to specify different settings for the front page or the single display page or everything else.',
                                     'These values will be used as the width &amp; height in pixels by the <em>Auto determine orientation</em> setting which, if disabled will default to the <code>width</code> in the first box and the <code>height</code> in second box.',
                                     'NOTE: leave one of the boxes blank (or zero) and it will be calculated using the aspect ratio to the other box.'),
                               ),
                'attthumb'   => array(
                    'title' => 'Use attached image as thumbnail',
                    'desc'  => 'Select the size to use for the thumbnail with Wordpress attachment images, if available.',
                    'html'  => array('If you attached an image to a post/page using the Wordpress <em>Add Media</em> button or through the <em>Media Library</em>, then you can use one of the auto-generated smaller sizes as the thumbnail.',
                                     'Non-attached images will just use the full-size image as the thumbnail and if the attached image size you specify is not available, the full-size image will also be used.'),
                               ),
                'addclass'  => array(
                    'title' => 'Additional classes',
                    'desc'  => 'Enter a space-separated list of classes to add to the image container div.',
                               ),
                'remclass'  => array(
                    'title' => 'Remove classes',
                    'desc'  => 'Enter a space-separated list of classes to remove from the image container div.',
                               ),
                'xcludclass'=> array(
                    'title' => 'Exclude classes',
                    'desc'  => 'Enter a space-separated list of classes to exclude images from processing, i.e. images with these classes will not be touched, just displayed "as-is".',
                    'code'  => 'wp-smiley exclude-me-class excludemetoo',
                    'html'  => '<p>Note: <tt>wp-smiley</tt> is the class used by Wordpress <em>emoticons</em>.</p>',
                               ),
                'capclass'  => array(
                    'title' => 'Caption classes',
                    'desc'  => 'Enter a space-separated list of classes to add to the image caption div.',
                    'code'  => '<div class="'. $this->get_option('capclass') .'"> Caption. </div>',
                               ),

                'flenable'  => array(
                    'title' => 'Enable Flickr',
                    'desc'  => 'Process images with the <code>flickr</code> attribute?',
                    'code'  => '<img flickr="123456789">',
                               ),
                'flusername'=> array(
                    'title' => 'Username',
                    'desc'  => 'Your screen name',
                    'link'  => 'http://www.flickr.com/account',
                               ),
                'flnsid'    => array(
                    'title' => 'Flickr Id',
                    'desc'  => 'Also known as: Name Server ID (NSID)',
                    'code'  => '12345678@N00',
                               ),
                'flfrob'    => array(
                    'title' => 'Frob',
                    'desc'  => '',
                    'code'  => '12345678901234567-1a23456bcdefghij-123456',
                               ),
                'fltoken'   => array(
                    'title' => 'Token',
                    'desc'  => '',
                    'code'  => '12345678901234567-12345678ab123cd4',
                               ),
                'flapikey'  => array(
                    'title' => 'API key',
                    'desc'  => '',
                    'code'  => '0a1234567890123bcd45678e90123456',
                               ),
                'flsecret'  => array(
                    'title' => 'Secret',
                    'desc'  => '',
                    'code'  => 'a1b23c4de5f6gh78',
                               ),

                'prettyuse' => array(
                    'title' => 'PrettyPhoto enabled',
                    'desc'  => 'Use the prettyPhoto library included with the Image Formatr plugin?',
                    'html'  => array('Uncheck this option to disable the pre-bundled prettyPhoto JavaScript Image library from loading.',
                                     'If you uncheck this option and have the Popup Effects setting checked above then you need to include your own image viewer library in your theme or in an integration plugin.'),
                              ),
                'uninstal'  => array(
                    'title' => 'Uninstall',
                    'desc'  => 'Remove all Image Formatr settings from the database upon plugin deactivation?',
                    'html'  => array('Check this box if you want to automatically uninstall this plugin when you deactivate it. This will clean up the database but you will loose all your settings and you will have the default settings if you re-activate it.',
                                     'If you`re not sure, don`t check it. If you do want to uninstall this plugin, don`t forget to click <em>Save Changes</em>.',
                                     '<em>Remember: the database is cleaned up when you "Deactivate"</em>.'),
                               ),
            );

            register_setting(
                IMAGEFORMATR_TEXTDOMAIN,         // group
                $this->settings_name,            // option name in settings table
                array($this, 'admin_validate')); // sanitize callback function

            add_settings_section('main_section', 'Main settings', array($this, 'admin_overview'), __FILE__);
            $this-> add_settings(array('capatt'  ), 'print_caption_dd', 'main_section');
            $this-> add_settings(array('newtitle'), 'print_textbox'   , 'main_section');
            $this-> add_settings(array('group'   ), 'print_textbox'   , 'main_section');
            $this-> add_settings(array('yankit', 'killanc', 'dofx', 'force'), 'print_checkbox', 'main_section');

            add_settings_section('thumb_section', 'Thumbnail settings', array($this, 'admin_overview'), __FILE__);
            $this-> add_settings(array('stdthumb'), 'print_checkbox'  , 'thumb_section');
            $this-> add_settings(array('inspect' ), 'print_checkbox'  , 'thumb_section');
            $this-> add_settings(array('imgdefs' ), 'print_img_defs'  , 'thumb_section');
            $this-> add_settings(array('imgaddl' ), 'print_img_addl'  , 'thumb_section');
            $this-> add_settings(array('attthumb' ), 'print_attach_dd', 'thumb_section');

            add_settings_section('style_section', 'Styling settings', array($this, 'admin_overview'), __FILE__);
            $this-> add_settings(array('addclass', 'remclass', 'xcludclass', 'capclass'), 'print_textbox', 'style_section');

            add_settings_section('flickr_section', 'Flickr settings', array($this, 'admin_overview'), __FILE__);
            $this-> add_settings(array('flenable'), 'print_checkbox', 'flickr_section');
            $this-> add_settings(array('flusername', 'flnsid', 'flfrob', 'fltoken', 'flapikey', 'flsecret'), 'print_textbox', 'flickr_section');

            add_settings_section('adv_section', 'Advanced settings', array($this, 'admin_overview'), __FILE__);
            $this-> add_settings(array('prettyuse', 'uninstal'), 'print_checkbox', 'adv_section');
        }

        function add_settings ( $fields, $callback, $section )
        {
            foreach ($fields as $f)
                add_settings_field( $f, $this-> option_descriptions[$f]['title'], array($this, $callback), __FILE__, $section, $f );
        }

        function print_caption_dd ( $f )
        {
            $sel_tit = ($this->get_option($f) == 'title') ? 'selected="selected"' : '';
            $sel_alt = ($this->get_option($f) == 'alt'  ) ? 'selected="selected"' : '';
            $sel_non = ($this->get_option($f) == 'x'    ) ? 'selected="selected"' : '';
            $e = <<< ELEMENT
                <select
                    id="$f"
                    name="$this->settings_name[$f]"
                  ><option value="title" $sel_tit>title</option>
                   <option value="alt"   $sel_alt>alt</option>
                   <option value="x"     $sel_non>(x) no caption</option>
                </select>
ELEMENT;
            $this-> print_element($e, $f);
        }

        function print_attach_dd ( $f )
        {
            $sel_thu = ($this->get_option($f) == 'thumbnail') ? 'selected="selected"' : '';
            $sel_med = ($this->get_option($f) == 'medium'   ) ? 'selected="selected"' : '';
            $sel_lrg = ($this->get_option($f) == 'large'    ) ? 'selected="selected"' : '';
            $sel_ful = ($this->get_option($f) == 'fullsize' ) ? 'selected="selected"' : '';
            $e = <<< ELEMENT
                <select
                    id="$f"
                    name="$this->settings_name[$f]"
                  ><option value="fullsize"  $sel_ful>full-size</option>
                   <option value="large"     $sel_lrg>large</option>
                   <option value="medium"    $sel_med>medium</option>
                   <option value="thumbnail" $sel_thu>thumbnail</option>
                </select>
ELEMENT;
            $this-> print_element($e, $f);
        }

        function print_checkbox ( $f )
        {
            $checked = $this->get_option($f) ? 'checked="checked" ' : '';
            $e = <<< ELEMENT
                <input
                    id="$f"
                    type="checkbox"
                    name="$this->settings_name[$f]"
                    $checked
                    />
ELEMENT;
            $this-> print_element($e, $f);
        }

        function print_textbox ( $f )
        {
            $e = <<< ELEMENT
                <input
                    id="$f"
                    type="text"
                    name="$this->settings_name[$f]"
                    value="{$this->get_option($f)}"
                    style="width: 300px"
                    />
ELEMENT;
            $this-> print_element($e, $f);
        }

        function print_img_defs ( $f )
        {
            $e = <<< ELEMENTS
                <input type="text" name="$this->settings_name[imglong]"  id="imglong"  value="{$this->get_option('imglong')}" size="5" />
                x
                <input type="text" name="$this->settings_name[imgshort]" id="imgshort" value="{$this->get_option('imgshort')}" size="5" />
ELEMENTS;
            $this-> print_element($e, $f);
        }

        function print_img_addl ( $f )
        {
            $checked = array('', '', '', '');
            $checked[$this->get_option('img2page')] = "checked";
            $e = <<< ELEMENTS
              <input type="text" name="$this->settings_name[img2long]"  id="img2long"  value="{$this->get_option('img2long')}" size="5" />
              x
              <input type="text" name="$this->settings_name[img2short]" id="img2short" value="{$this->get_option('img2short')}" size="5" />
              =
              <input type="radio" name="$this->settings_name[img2page]" id="img2page0" value="0" {$checked[0]} /> <label for="img2page0">front</label> |
              <input type="radio" name="$this->settings_name[img2page]" id="img2page1" value="1" {$checked[1]} /> <label for="img2page1">not front</label> |
              <input type="radio" name="$this->settings_name[img2page]" id="img2page2" value="2" {$checked[2]} /> <label for="img2page2">single</label> |
              <input type="radio" name="$this->settings_name[img2page]" id="img2page3" value="3" {$checked[3]} /> <label for="img2page3">not single</label> /
ELEMENTS;
            $this-> print_element($e, $f);
        }

        function print_element ( $e, $f )
        {
            $name = $f;
            $code = $html = $link = $title = '';
            $desc = array_key_exists($f, $this->option_descriptions) ? $this->option_descriptions[$f] : '';
            if (is_array($desc)) extract($desc);
            $desc = __($desc, IMAGEFORMATR_TEXTDOMAIN);
            $_titl = esc_attr($title);
            $_desc = esc_attr(wp_strip_all_tags($desc));
            $_code = esc_html($code);
            $desc = preg_replace( "/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,3};)/", "&amp;", strtr($desc, array(chr(38) => '&')) );  # convert & to &amp; (unless already converted)
            $code = $code ? "<p><code>$_code</code></p>" : '';
            $link = $link ? "<p>See: <a href='$link' target='_blank'>$link</a></p>" : '';
            $html = is_array($html) ? '<p>'. implode('</p><p>', $html) .'</p>' : $html;

            echo <<< INPUT
                <span title="$_desc">
                    $e
                </span>
                <input
                    value="?"
                    class="thickbox"
                    alt="#TB_inline?height=300&amp;width=400&amp;inlineId=TB-$f"
                    title="$_titl"
                    type="button"
                    />
                <div id="TB-$f" style="display: none">
                    <div>
                        <p>$desc</p>
                           $code
                           $html
                           $link
                        <p><em>Option name(s): <tt>$name</tt></em></p>
                    </div>
                </div>
INPUT;
        }

        function admin_menu ( )
        {
            if( !function_exists('current_user_can')
             || !current_user_can('manage_options') )
                return;

            add_options_page(
                __('Image Formatr', IMAGEFORMATR_TEXTDOMAIN),
                __('Image Formatr', IMAGEFORMATR_TEXTDOMAIN),
                'manage_options',
                basename(__FILE__),
                array($this, 'options_page'));
        }

        function admin_overview ( )
        {
            #echo 'Administration settings';
        }

        function admin_validate ( $input )
        {
            // only validate the integers
            $integers = array('imglong', 'imgshort', 'img2long', 'img2short');
            foreach ($input as $key => $val) {
                $this->options[$key] = $val;
                if (in_array($key, $integers))
                    $this->admin_validate_positive_integer($input, $key);
            }

            // the checkbox fields will not be present in the $input
            // so they need to be manually set to false if absent
            $checkboxes = array('yankit', 'dofx', 'killanc', 'force', 'stdthumb', 'uninstal', 'inspect', 'prettyuse', 'flenable');
            foreach ($checkboxes as $checkbox)
                if (!array_key_exists($checkbox, $input))
                    $this->options[$checkbox] = '';

            return $this->options;
        }

        function admin_validate_positive_integer ( $input, $fieldname )
        {
            if( $input[$fieldname] )
                if( !is_numeric($input[$fieldname])
                 or $input[$fieldname] < 0
                 or sprintf("%.0f", $input[$fieldname]) != $input[$fieldname] )
                    add_settings_error($this->settings_name, 'settings_updated', __("Only positive integers should be used as $fieldname."));
        }

        function options_page()
        {
            ?>
                <div class="wrap">
                    <?php screen_icon("options-general"); ?>
                    <h2><?php _e('Image Formatr', IMAGEFORMATR_TEXTDOMAIN); ?></h2>
                    <form action="options.php" method="post">
                        <?php settings_fields(IMAGEFORMATR_TEXTDOMAIN); ?>
                        <?php do_settings_sections(__FILE__); ?>
                        <p class="submit">
                            <input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
                        </p>
                    </form>
                </div>
            <?php
        }

    } //End Class ImageFormatrAdmin

} //End class_exists check
