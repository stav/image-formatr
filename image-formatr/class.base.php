<?php
if (!class_exists("ImageFormatrBase")) {
    class ImageFormatrBase {

        // additional pages image dimension administration settings
        const FRONT      = 0;
        const NOT_FRONT  = 1;
        const SINGLE     = 2;
        const NOT_SINGLE = 3;

        // image viewer slideshow group
        var $group = "main";

        // the image class list to remove
        var $remove_classes = array();

        // the image class exclusion list
        var $exclude_classes = array();

        /**
         * Add the image viewer resources.
         */
        function enqueue()
        {
            if (!is_admin()) {
                wp_enqueue_style ('image-formatr', plugins_url('prettyPhoto.css', __FILE__), array(), false, 'all');
                wp_enqueue_script ('prettyPhoto', plugins_url('prettyPhoto.js', __FILE__), array('jquery'), '3.1.4', true );
            }
        }

        /**
         * Print the on-load JavaScript at the bottom of the page which
         * is actually preferred to loading in the head for a faster
         * perceived load time.
         */
        function print_scripts()
        {
            if( $this->get_option('prettyuse') and !is_admin() ) {
            echo <<< FOOTER
<script type="text/javascript" charset="utf-8">
  jQuery(document).ready(function(){
    jQuery("a[rel^='prettyPhoto']").prettyPhoto({theme:'dark_rounded'});
  });
</script>

FOOTER;
            }
        }

        /**
         * Use the native PHP getimagesize() function to get the image
         * width & height.
         */
        function getimagesize($src)
        {
            $url  = parse_url(get_option('siteurl'));
            $site = "http://" . $url["host"]; // no trailing slash
            $size = array();

            // site relative?
            if (substr($src,0,1) == '/')
                $url = $site . $src;
            else
                $url = $src;

            try {
                $size = getimagesize($url);
            }
            catch (Exception $e)
            {
                error_log("Cannot getimagesize(): {$e->getMessage()}");
            }

            return $size;
        }

        /**
         * return the option for the given key
         */
        function get_option ( $key )
        {
            if( array_key_exists($key, $this->options) )
                return $this->options[$key];

            return '';
        }

        /**
         * Flickr
         * borrowed from Trent Gardner's Flickr Manager
         */
        function getRequest($url) {
            if(function_exists('curl_init')) {
                $session = curl_init($url);
                curl_setopt($session, CURLOPT_HEADER, false);
                curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($session);
                if( curl_errno($session) == 0 )
                    $rsp_obj = unserialize($response);
                else
                    $rsp_obj = false;
                curl_close($session);
            } else {
                $handle = fopen($url, "rb");
                $contents = '';
                while (!feof($handle)) {
                    $contents .= fread($handle, 8192);
                }
                fclose($handle);
                $rsp_obj = unserialize($contents);
            }
            return $rsp_obj;
        }
        /**
         * Flickr
         * borrowed from Trent Gardner's Flickr Manager
         */
        function getSignature($params) {
            ksort($params);

            $api_sig = $this->flickr->secret;

            foreach ($params as $k => $v){
                $api_sig .= $k . $v;
            }

            return md5($api_sig);
        }
        /**
         * Flickr
         * borrowed from Trent Gardner's Flickr Manager
         */
        function call_flickr_api ( $method, $params, $sign = false, $rsp_format = "php_serial" )
        {
            if( !is_array($params) ) $params = array();

            $call_includes = array('api_key' => $this->flickr->apikey,
                                   'method'  => $method,
                                   'format'  => $rsp_format);

            $params = array_merge($call_includes, $params);

            if ($sign) $params = array_merge($params, array('api_sig' => $this->getSignature($params)));

            $url = "http://api.flickr.com/services/rest/?" . http_build_query($params);
#$debug_sma_eval ='$url';$debug_sma_title =__METHOD__.':'.__LINE__;include('debug_output_sma.php'); #SMA

            return $this->getRequest($url);
        }

    } //End Class ImageFormatrBase

} //End class_exists check
