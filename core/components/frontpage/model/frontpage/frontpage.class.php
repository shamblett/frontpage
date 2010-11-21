<?php
/**
 * Main Frontpage class
 *
 * @category  Front end editing
 * @author    S. Hamblett <steve.hamblett@linux.com>
 * @copyright 2010 S. Hamblett
 * @license   GPLv3 http://www.gnu.org/licenses/gpl.html
 * @link      none
 *
 * @package frontpage
 */

/**
 * Main Frontpage class
 *
 *
 * @category   Front end editing
 * @author     S. Hamblett <steve.hamblett@linux.com>
 * @copyright  2010 S. Hamblett
 * @license    GPLv3 http://www.gnu.org/licenses/gpl.html
 * @link       none
 * @see        none
 * @deprecated no
 *
 * @package frontpage
 */

class Frontpage {

    /* Constants */

    /**
     * @var config local configuration settings
     * @access public
     */
    var $config = array();

    /**#@+
     * Constructor
     *
     * @param object &$modx class we are using.
     *
     * @return Frontpage A unique Frontpage instance.
     */
    function Frontpage(&$modx) {
        $this->modx =& $modx;
    }

    /**
     * Initalize the class
     *
     * @access public
     * @param string $ctx context we are using.
     *
     * @return void
     */
    function initialize($config = null, $ctx = 'web') {

        /* Setup our configuration */
        $this->config['base_path'] = $this->modx->getOption('frontpage.core_path',
                                                            null,
                                                            $this->modx->getOption('core_path').'components/frontpage/');
        $this->config['core_path'] = $this->config['base_path'];
        $this->config['assets_url'] = $this->modx->getOption('frontpage.assets.path',
                                                            null,
                                                            $this->modx->getOption('assets_url').'components/frontpage/');
        $this->config = array_merge($config, $this->config);

        /* Add the Frontpage model into MODx */
        $this->modx->addPackage('frontpage', $this->config['core_path'].'model/');

        /* Load the 'default' lang foci, which is default.inc.php. */
        $this->modx->lexicon->load('frontpage:default');

    }

    /**
     * Run function
     *
     * @access public
     *
     * @return toolbar menu
     */
    function run() {

        $controls = '';
        $cssURL = $this->config['assets_url'] . "css/";
        $cssImagesURL = $this->config['assets_url'] . "css/images";
        $imagesURL = $this->config['assets_url'] . "images/";
        $jsURL = $this->config['assets_url'] . "js/";
        $allowAccess = false;

        /* Get the current document identifier */
        $docId = $this->modx->documentIdentifier;

        /* Don't do any processing if we are on the Create or Edit page */
        $editResource = $this->modx->getObject('modSystemSetting',
                                               array('key' => 'edit_resource',
                                                     'namespace' => 'frontpage'));
        if ( !$editResource ) return;
        $editPage = $editResource->get('value');

        $createResource = $this->modx->getObject('modSystemSetting',
                                                 array('key' => 'create_resource',
                                                     'namespace' => 'frontpage'));
        if ( !$createResource ) return;
        $createPage = $createResource->get('value');

        if ( ($editPage == $docId) || ($createPage == $docId)) return;
        
        /* Get the document output */
        $output = &$this->modx->documentOutput;

        /* Get the user id and the current document parent */
        $userId = $_SESSION['webInternalKey'];       
        $parentId = $this->modx->resource->get('parent');

        /* Add check for allowed roles */
        if ( $this->config['performRoleCheck'] === true ) {

            $groupMembership = $this->modx->getCollection('modUserGroupMember',
                                                          array('member'=>$userId));
            $allowedRoles = explode(',', $this->config['contentManagerRoles']);
            foreach ( $groupMembership as $group ) {

                if ( $allowAccess ) break;
                $role = $group->get('role');
                $groupRole = $this->modx->getObject('modUserGroupRole',
                                                    array('id' => $role));
                if ( $groupRole == null ) continue;
                $roleName = $groupRole->get('name');
                if ( $roleName == 'Super User') {

                     $allowAccess = true;
                     break;
                 }
                 foreach ( $allowedRoles as $allowedRole ) {

                     if ( $allowedRole == $roleName ) {

                         $allowAccess = true;
                         break;
                     }
                 }
             }

        } else {

            $allowAccess = true;
        }

        /* If no access allowed return */
        if ( !$allowAccess ) return;


        /* Edit button */
        if ( $this->config['showEdit'] == true ) {

            if ($this->modx->hasPermission('save_document') || $this->modx->resource->checkPolicy('save')) {

                $editURL = $this->_editButtonLink();
                $editText = $this->modx->lexicon('editbutton');
                $editButton = '
                    <li>
                    <a class="fpButton fpEdit colorbox" href="' . $editURL . '&amp;frontpage=1&amp;source=' . $docId . '"><span> ' . $editText . '</span></a>
                    </li>
                    ';
                $controls .= $editButton;

            }
       }

        /* Create button */
        if ( $this->config['showCreate'] == true ) {

            if ($this->modx->hasPermission('new_document')) {

                $createURL = $this->_createButtonLink();
                $createText = $this->modx->lexicon('createbutton');
                $createButton = '
                    <li>
                    <a class="fpButton colorbox" href="' . $createURL . '&amp;frontpage=1&amp;source=' . $docId . '&amp;parent=' . $parentId . '"><span> ' . $createText . '</span></a>
                    </li>
                    ';
                $controls .= $createButton;

            }

        }

        /* If no permissions exit */
        if ( $controls == '' ) return;

        /* Add the action buttons */
        $editor = '
            <div id="fpEditorClosed"></div>
            <div id="fpEditor">
            <a id="fpClose" class="fpButton fpClose" href="#" onclick="javascript: return false;">X</a>
            <ul>
            <li><a id="fpLogoClose" class="fpClose" href="#" onclick="javascript: return false;"></a></li>
            ' . $controls . '
            </ul>
            </div>';

        /* Add the CSS */
        $css = '
            <link rel="stylesheet" type="text/css" href="' . $cssURL . 'style.css" />
            <!--[if IE]><link rel="stylesheet" type="text/css" href="' . $cssUrl . 'ie.css" /><![endif]-->
            <!--[if lte IE 7]><link rel="stylesheet" type="text/css" href="' . $cssUrl . 'ie7.css" /><![endif]-->
            ';

        /* Autohide toolbar */
        if ($this->config['autohideToolbar'] == false) {

            $css .= '
                <style type="text/css">
                #fpEditor, #fpEditorClosed { top: 0px; }
                </style>
                ';
        }

         /* Insert jQuery and ColorBox in head if needed */
         if ($this->config['loadfrontendjq'] == true ) {

             $head .= '<script src="' . $jsURL . 'jquery.colorbox-min.js" type="text/javascript"></script>';

         }

         /* Insert jQuery in head if needed */
         if ($this->config['loadJQuery'] == true)
            $head .= '<script src="' . $this->config['jQueryPath'] . '" type="text/javascript"></script>';

         /* Toolbar */
            $head .= '
                <link type="text/css" media="screen" rel="stylesheet" href="' . $cssURL . 'colorbox.css" />
                <style type="text/css">
                .cboxIE #cboxTopLeft{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' . $cssImagesUrl . 'internet_explorer/borderTopLeft.png, sizingMethod=\'scale\');}
                .cboxIE #cboxTopCenter{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' . $cssImagesUrl . 'internet_explorer/borderTopCenter.png, sizingMethod=\'scale\');}
                .cboxIE #cboxTopRight{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' . $cssImagesUrl . 'internet_explorer/borderTopRight.png, sizingMethod=\'scale\');}
                .cboxIE #cboxBottomLeft{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' . $cssImagesUrl . 'internet_explorer/borderBottomLeft.png, sizingMethod=\'scale\');}
                .cboxIE #cboxBottomCenter{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' . $cssImagesUrl . 'internet_explorer/borderBottomCenter.png, sizingMethod=\'scale\');}
                .cboxIE #cboxBottomRight{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' . $cssImagesUrl . 'internet_explorer/borderBottomRight.png, sizingMethod=\'scale\');}
                .cboxIE #cboxMiddleLeft{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' . $cssImagesURL . 'internet_explorer/borderMiddleLeft.png, sizingMethod=\'scale\');}
                .cboxIE #cboxMiddleRight{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' . $cssImagesURL . 'internet_explorer/borderMiddleRight.png, sizingMethod=\'scale\');}
                </style>
                <script type="text/javascript" src="' . $jsURL . 'jquery.colorbox-min.js"></script>
                ';

        /* Insert ColorBox jQuery definitions */
        $head .= '
            <script type="text/javascript">
            ';

        /* jQuery in noConflict mode */
        if ($this->config['noconflictjq'] == true) {

            $head .= '
                var $j = jQuery.noConflict();
                $j(document).ready(function($)
                ';
            $jvar = 'j';

        } else {

            $head .= '$(document).ready(function($)';
            $jvar = '';

        }

        /* Finish the header information */
        $head .= '
            {
                $("a.colorbox").colorbox({width:"' . $this->config['boxWidth'] .'", height:"' . $this->config['boxHeight'] . '", iframe:true, overlayClose:false});
                // Bindings
                $().bind("cbox_open", function(){
                    $("body").css({"overflow":"hidden"});
                    $("html").css({"overflow":"hidden"});
                    $("#fpEditor").css({"display":"none"});
                });
                $().bind("cbox_closed", function(){
                    $("body").css({"overflow":"auto"});
                    $("html").css({"overflow":"auto"});
                    $("#fpEditor").css({"display":"block"});
                });

                // Hide Frontpage
                $(".fpClose").click(function () {

                    $("#fpEditor").hide("normal");
                    $("#fpEditorClosed").show("normal");

                });

                // Show Frontpage
                $("#fpEditorClosed").click(function () {

                    {
                        $("#fpEditorClosed").hide("normal");
                        $("#fpEditor").show("normal");

                    }

                });

            });
            
        </script>
        ';

        /* Insert CSS into the  head */
        $head .= $css;

        /* Place Frontpage head information in head, just before </head> tag */
        $output = preg_replace('~(</head>)~i', $head . '\1', $output);

        /* Insert editor toolbar right after <body> tag */
        $output = preg_replace('~(<body[^>]*>)~i', '\1' . $editor, $output);

        return $output;
    }

    /**
     * checkTags function
     *
     * @access public
     *
     * @return output with tags reformed
     */
    function checkTags() {

        /* Get the document output */
        $output = &$this->modx->documentOutput;
        
        /* Get the Edit page resource */
        $editResource = $this->modx->getObject('modSystemSetting',
                                               array('key' => 'edit_resource',
                                                     'namespace' => 'frontpage'));
        /* If no edit page set just return */
        if ( !$editResource ) return $output;
              
        /* If we are on our edit page, close up the tags */
         $docId = $this->modx->documentIdentifier;
         $editPage = $editResource->get('value');
         if ( $docId == $editPage ) $output = str_replace('[ [', '[[', $output); 
         
         return $output;            
        
    }
    
    /**
     * Edit button resource link function
     *
     * @access private
     *
     * @return the editor button link
     */
     function _editButtonLink() {

         $editResourceSetting = $this->modx->getObject('modSystemSetting',
                                                       array('key' => 'edit_resource',
                                                       'namespace' => 'frontpage'));

         $editId = $editResourceSetting->get('value');
         $url = $this->modx->makeURL($editId);
         return $url;

     }

     /**
     * Create button resource link function
     *
     * @access private
     *
     * @return the create button link
     */
     function _createButtonLink() {

         $createResourceSetting = $this->modx->getObject('modSystemSetting',
                                                         array('key' => 'create_resource',
                                                         'namespace' => 'frontpage'));

         $createId = $createResourceSetting->get('value');
         $url = $this->modx->makeURL($createId);
         return $url;

     }
}
