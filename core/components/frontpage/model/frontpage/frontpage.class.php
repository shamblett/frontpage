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

    const ALOHAEMPTYFIELD = " ";
     const ALOHAENDLINE = -6;

    /**
     * @var config local configuration settings
     * @access public
     */
    var $config = array();

    /*     * #@+
     * Constructor
     *
     * @param object &$modx class we are using.
     *
     * @return Frontpage A unique Frontpage instance.
     */

    function Frontpage(&$modx) {
        $this->modx = & $modx;
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
        $this->config['base_path'] = $this->modx->getOption('frontpage.core_path', null, $this->modx->getOption('core_path') . 'components/frontpage/');
        $this->config['core_path'] = $this->config['base_path'];
        $this->config['assets_url'] = $this->modx->getOption('frontpage.assets.path', null, $this->modx->getOption('assets_url') . 'components/frontpage/');
        $this->config = array_merge($config, $this->config);

        /* Add the Frontpage model into MODx */
        $this->modx->addPackage('frontpage', $this->config['core_path'] . 'model/');

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
        $docId = $this->modx->resource->get('id');

        /* Are we in classic or aloha mode */
        $keyAppend = "";
        if ($this->config['editMethod'] == 'aloha') {

            $keyAppend = "-aloha";
        }

        /* Don't do any processing if we are on the Create or Edit page */
        $editResource = $this->modx->getObject('modSystemSetting', array('key' => "edit_resource" . $keyAppend,
                    'namespace' => 'frontpage'));
        if (!$editResource)
            return;
        $editPage = $editResource->get('value');

        $createResource = $this->modx->getObject('modSystemSetting', array('key' => "create_resource" . $keyAppend,
                    'namespace' => 'frontpage'));
        if (!$createResource)
            return;
        $createPage = $createResource->get('value');

        if (($editPage == $docId) || ($createPage == $docId))
            return;

        /* Get the document output */
        $output = &$this->modx->resource->_output;

        /* Get the user id and the current document parent */
        $userId = $_SESSION['webInternalKey'];
        $parentId = $this->modx->resource->get('parent');

        /* Add check for allowed roles */
        if ($this->config['performRoleCheck'] === true) {

            $groupMembership = $this->modx->getCollection('modUserGroupMember', array('member' => $userId));
            $allowedRoles = explode(',', $this->config['contentManagerRoles']);
            foreach ($groupMembership as $group) {

                if ($allowAccess)
                    break;
                $role = $group->get('role');
                $groupRole = $this->modx->getObject('modUserGroupRole', array('id' => $role));
                if ($groupRole == null)
                    continue;
                $roleName = $groupRole->get('name');
                if ($roleName == 'Super User') {

                    $allowAccess = true;
                    break;
                }
                foreach ($allowedRoles as $allowedRole) {

                    if ($allowedRole == $roleName) {

                        $allowAccess = true;
                        break;
                    }
                }
            }
        } else {

            $allowAccess = true;
        }

        /* If no access allowed return */
        if (!$allowAccess)
            return;


        /* Edit button */
        if ($this->config['showEdit'] == true) {

            if ($this->modx->hasPermission('save_document') || $this->modx->resource->checkPolicy('save')) {

                $editURL = $this->_editButtonLink();
                $editText = $this->modx->lexicon('editbutton');
                $editButton = '
                    <li>
                    <a onClick="javascript:FrontPage.edit=1;return false;" class="fpButton fpEdit colorbox" href="' . $editURL . '&amp;frontpage=1&amp;source=' . $docId . '"><span> ' . $editText . '</span></a>
                    </li>
                    ';
                $controls .= $editButton;
            }
        }

        /* Create button */
        if ($this->config['showCreate'] == true) {

            if ($this->modx->hasPermission('new_document')) {

                $createURL = $this->_createButtonLink();
                $createText = $this->modx->lexicon('createbutton');
                $createButton = '
                    <li>
                    <a onClick="javascript: FrontPage.create=1;return false;" class="fpButton fpCreate colorbox" href="' . $createURL . '&amp;frontpage=1&amp;source=' . $docId . '&amp;parent=' . $parentId . '"><span> ' . $createText . '</span></a>
                    </li>
                    ';
                $controls .= $createButton;
            }
        }

        /* If no permissions exit */
        if ($controls == '')
            return;

        /* Add the action buttons */
        $editor = '
            <div id="fpEditorClosed"></div>
            <div id="fpEditor">
            <a id="fpClose" class="fpButton fpClose" href="#" onclick="javascript: return false;">Close Me</a>
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
        if ($this->config['loadfrontendjq'] == true) {

            $head .= '<script src="' . $jsURL . 'jquery.colorbox-min.js" type="text/javascript"></script>';
        }

        /* Insert jQuery in head if needed */
        if ($this->config['loadJQuery'] == 1 )
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
      
        /* Add Frontpage control */
        $head .= '
            // Frontpage Control
            var FrontPage = new Object();
            FrontPage.source = ' . $docId . ';
            FrontPage.create = 0;
            FrontPage.edit = 0;
            
            ';

        /* jQuery in noConflict mode */
        if ($this->config['jQueryNoConflict'] == true) {

            $head .= '
                var $j = jQuery.noConflict();
                $j(document).ready(function($)
                ';
            $jvar = 'j';
        } else {

            $head .= '$(document).ready(function($)';
            $jvar = '';
        }

        /* Get the create AJAX URL */
        $ajaxURL = $this->_createAjaxLink();

        /* Finish the header information */
        $head .= '
            {
                $("a.colorbox").colorbox({width:"' .
                $this->config['boxWidth'] . '", height:"' .
                $this->config['boxHeight'] . '", 
                    iframe:true, 
                    overlayClose:false,
                    fixed:true,
                    close:"I\'ve finished",
                    transition:"elastic",
                    onClosed:function(){
                        if ( FrontPage.edit == 1 ) {
                            window.location.reload();
                        }
                        if ( FrontPage.create == 1 ) {
                            $.ajax({url: "' . $ajaxURL . '",
                                    async: false,
                                    dataType: "text",
                                    success: function(data){
                                      data = decodeURI(data); 
                                      window.location = data;
                                    },
                                    error: function(a,b,c){
                                        var wearehere = 0;
                                    }
                              });
                        }
                                        
                     }
                    });
                        
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

        /* Add Aloha if requested */
      if ($this->config['activeAloha'] === true) {
          
        $alohaAJAXLink = $this->_createAlohaAjaxLink();
        /* Get the chunk, don't use getChunk */
        $alohaChunk = $this->modx->getObject('modChunk', array('name' => "jsAlohaPage"));
        /* Replace the placeholders manually */
        $alohaInclude = $alohaChunk->get('snippet');
        $alohaInclude = str_replace('[[+fp.alohaSource]]', $docId,  $alohaInclude );
        $alohaInclude = str_replace('[[+fp.alohaAjax]]', $alohaAJAXLink,  $alohaInclude);
        
         $head .=   $alohaInclude;
        
      }
      
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
        $output = &$this->modx->resource->_output;

        /* Get the Edit page resource */
        $editResource = $this->modx->getObject('modSystemSetting', array('key' => 'edit_resource',
                    'namespace' => 'frontpage'));
        /* If no edit page set just return */
        if (!$editResource)
            return $output;

        /* If we are on our edit page, close up the tags */
        $docId = $this->modx->resource->get('id');
        $editPage = $editResource->get('value');
        if ($docId == $editPage)
            $output = str_replace('[ [', '[[', $output);

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

        $keyAppend = "";
        if ($this->config['editMethod'] == 'aloha') {

            $keyAppend = "-aloha";
        }

        $editResourceSetting = $this->modx->getObject('modSystemSetting', array('key' => "edit_resource" . $keyAppend,
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

        $keyAppend = "";
        if ($this->config['editMethod'] == 'aloha') {

            $keyAppend = "-aloha";
        }

        $createResourceSetting = $this->modx->getObject('modSystemSetting', array('key' => "create_resource" . $keyAppend,
                    'namespace' => 'frontpage'));

        $createId = $createResourceSetting->get('value');
        $url = $this->modx->makeURL($createId);
        return $url;
    }

    /**
     * Create ajax resource link function
     *
     * @access private
     *
     * @return the create ajax link url
     */
    function _createAjaxLink() {

        $ajaxResourceSetting = $this->modx->getObject('modSystemSetting', array('key' => 'ajax_resource',
                    'namespace' => 'frontpage'));

        $ajaxId = $ajaxResourceSetting->get('value');
        $params = array('frontpage' => '1');

        $url = $this->modx->makeURL($ajaxId, "", $params);
        $url = html_entity_decode($url);
        return $url;
    }

    /**
     * Create ajax aloha resource link function
     *
     * @access private
     *
     * @return the create aloha ajax link url
     */
    function _createAlohaAjaxLink() {

        $ajaxAlohaResourceSetting = $this->modx->getObject('modSystemSetting', array('key' => 'ajax_resource-aloha',
                    'namespace' => 'frontpage'));

        $ajaxId = $ajaxAlohaResourceSetting->get('value');
        $params = array('frontpage' => '1');

        $url = $this->modx->makeURL($ajaxId, "", $params);
        $url = html_entity_decode($url);
        return $url;
    }

    /**
     * Edit page function
     *
     * @param $source the page id we are editing
     * 
     * @access public
     *
     */
    function editPage($source) {

        /* Get the document details */
        $sourceDoc = $this->modx->getObject('modResource', array('id' => $source));

        if (!$sourceDoc) {

            $this->modx->setPlaceholder('fp.error_message', $this->modx->lexicon('nosuchdocument'));
            return;
        }

        /* Set the place holders */
        $this->modx->setPlaceholder('fp.source', $source);
        $this->modx->setPlaceholder('fp.titlelabel', $this->modx->lexicon('titlelabel'));
        $this->modx->setPlaceholder('fp.title', $sourceDoc->get('pagetitle'));
        $this->modx->setPlaceholder('fp.longtitlelabel', $this->modx->lexicon('longtitlelabel'));
        $this->modx->setPlaceholder('fp.longtitle', $sourceDoc->get('longtitle'));
        $this->modx->setPlaceholder('fp.descriptionlabel', $this->modx->lexicon('descriptionlabel'));
        $this->modx->setPlaceholder('fp.description', $sourceDoc->get('description'));
        $this->modx->setPlaceholder('fp.aliaslabel', $this->modx->lexicon('aliaslabel'));
        $this->modx->setPlaceholder('fp.alias', $sourceDoc->get('alias'));
        $this->modx->setPlaceholder('fp.summarylabel', $this->modx->lexicon('summarylabel'));
        $this->modx->setPlaceholder('fp.summary', $sourceDoc->get('introtext'));
        $this->modx->setPlaceholder('fp.menutitlelabel', $this->modx->lexicon('menutitlelabel'));
        $this->modx->setPlaceholder('fp.menutitle', $sourceDoc->get('menutitle'));
        $this->modx->setPlaceholder('fp.menuindexlabel', $this->modx->lexicon('menuindexlabel'));
        $this->modx->setPlaceholder('fp.menuindex', $sourceDoc->get('menuindex'));
        $this->modx->setPlaceholder('fp.hidemenulabel', $this->modx->lexicon('hidemenulabel'));
        if ($sourceDoc->get('hidemenu') == 1)
            $this->modx->setPlaceholder('fp.hidemenuscheck', "checked");
        $this->modx->setPlaceholder('fp.publishlabel', $this->modx->lexicon('publishlabel'));
        if ($sourceDoc->get('published') == 1)
            $this->modx->setPlaceholder('fp.publishcheck', "checked");
        $this->modx->setPlaceholder('fp.savebuttonlabel', $this->modx->lexicon('savebuttonlabel'));
        $this->modx->setPlaceholder('fp.cancelbuttonlabel', $this->modx->lexicon('cancelbuttonlabel'));

        /* Content with tags opened */
        $content = str_replace('[[', '[ [', $sourceDoc->get('content'));
        $this->modx->setPlaceholder('fp.formcontent', $content);

        return;
    }

    /**
     * Edit Aloha page function
     *
     * @param $source the page id we are editing
     * 
     * @access public
     *
     */
    function editPageAloha($source) {

        /* Get the document details */
        $sourceDoc = $this->modx->getObject('modResource', array('id' => $source));

        if (!$sourceDoc) {

            $this->modx->setPlaceholder('fp.error_message', $this->modx->lexicon('nosuchdocument'));
            return;
        }

        /* Set the place holders */
        $this->modx->setPlaceholder('fp.source', $source);
        $this->modx->setPlaceholder('fp.titlelabel', $this->modx->lexicon('titlelabel'));
        $content = $sourceDoc->get('pagetitle');
        $this->modx->setPlaceholder('fp.title', $content);
        $this->modx->setPlaceholder('fp.longtitlelabel', $this->modx->lexicon('longtitlelabel'));
        $content = $sourceDoc->get('longtitle');
        $this->modx->setPlaceholder('fp.longtitle', $content);
        $this->modx->setPlaceholder('fp.descriptionlabel', $this->modx->lexicon('descriptionlabel'));
        $content = $sourceDoc->get('description');
        $this->modx->setPlaceholder('fp.description', $content);
        $this->modx->setPlaceholder('fp.aliaslabel', $this->modx->lexicon('aliaslabel'));
        $content = $sourceDoc->get('alias');
        $this->modx->setPlaceholder('fp.alias', $content);
        $this->modx->setPlaceholder('fp.summarylabel', $this->modx->lexicon('summarylabel'));
        $content = $sourceDoc->get('introtext');
        $this->modx->setPlaceholder('fp.summary', $content);
        $this->modx->setPlaceholder('fp.menutitlelabel', $this->modx->lexicon('menutitlelabel'));
        $content = $sourceDoc->get('menutitle');
        $this->modx->setPlaceholder('fp.menutitle', $content);
        $this->modx->setPlaceholder('fp.menuindexlabel', $this->modx->lexicon('menuindexlabel'));
        $content = $sourceDoc->get('menuindex');
        $this->modx->setPlaceholder('fp.menuindex', $content);
        $this->modx->setPlaceholder('fp.hidemenulabel', $this->modx->lexicon('hidemenulabel'));
        if ($sourceDoc->get('hidemenu') == 1) {
            $this->modx->setPlaceholder('fp.hidemenucheck', "1");
        } else {
            $this->modx->setPlaceholder('fp.hidemenucheck', "0");
        }
        $this->modx->setPlaceholder('fp.publishlabel', $this->modx->lexicon('publishlabel'));
        if ($sourceDoc->get('published') == 1) {
            $this->modx->setPlaceholder('fp.publishcheck', "1");
        } else {
            $this->modx->setPlaceholder('fp.publishcheck', "0");
        }

        /* Content with tags opened */
        $content = str_replace('[[', '[ [', $sourceDoc->get('content'));
        $this->modx->setPlaceholder('fp.formcontent', $content);

        /* AJAX page */
        $ajaxPage = $this->_createAlohaAJAXLink();
        $this->modx->setPlaceholder('fp.alohaajax', $ajaxPage);

        return;
    }

    /**
     * Create page function
     *
     * @param $source the page id we are editing
     * @param $parent the parent of the page id we are editing
     * 
     * @access public
     *
     */
    function createPage($source, $parent) {

        /* If parent is not -1 this is a create, otherwise a refresh from save */
        if ($parent != -1) {

            /* Create a document */
            $newDoc = $this->modx->newObject('modResource');

            if (!$newDoc) {

                $this->modx->setPlaceholder('fp.error_message', $this->modx->lexicon('cantcreate'));
                return;
            }

            /* Get the parent document template */
            $templateToUse = 0;

            if ($parent != 0) {

                $parentDoc = $this->modx->getObject('modResource', array('id' => $parent));
                if (!$parentDoc) {

                    $this->modx->setPlaceholder('fp.error_message', $this->modx->lexicon('cantgetparent'));
                    return;
                }

                $templateToUse = $parentDoc->get('template');
            }

            /* Set default document parameters from the parent */
            if ($parent != 0) {

                $newDoc->set('context_key', $parentDoc->get('context_key'));
                $newDoc->set('richtext', $parentDoc->get('richtext'));
            } else {

                $newDoc->set('context_key', 'web');
            }

            /* Get the kind of template we are using */
            $templateSetting = $this->modx->getObject('modSystemSetting', array('key' => 'default_template',
                        'namespace' => 'frontpage'));
            if (!$templateSetting) {

                $newDoc->set('template', $templateToUse);
            } else {

                $template = $templateSetting->get('value');
                if ($template == 'parent') {

                    $newDoc->set('template', $templateToUse);
                } else {

                    $newDoc->set('template', $template);
                }
            }

            /* Parent */
            $newDoc->set('parent', $parent);

            /* Save the new document */
            $success = $newDoc->save();
            if ($success === false) {

                $this->modx->setPlaceholder('fp.error_message', $this->modx->lexicon('cantsave'));
                return;
            }

            /* Resource groups */
            if ($parent != 0) {

                $parentResourceGroups = $this->modx->getCollection('modResourceGroupResource', array('document' => $parent));

                foreach ($parentResourceGroups as $parentResourceGroup) {

                    $resourceGroup = $parentResourceGroup->get('document_group');
                    $newResourceGroup = $this->modx->newObject('modResourceGroupResource', array('document' => $newDoc->get('id'),
                                'document_group' => $resourceGroup));
                    $newResourceGroup->save();
                }
            }

            /* Set the source document to the new one */
            $source = $newDoc->get('id');
            $sourceDoc = $newDoc;

            /* Set the newly created document as a session variable */
            $_SESSION['fp_newCreate'] = $source;
        } else {

            $sourceDoc = $this->modx->getObject('modResource', array('id' => $source));
            if (!$sourceDoc) {

                $this->modx->setPlaceholder('fp.error_message', $this->modx->lexicon('nosuchdocument'));
                return;
            }
        } // Create or refresh

        /* Set the place holders */
        $this->modx->setPlaceholder('fp.source', $source);
        $this->modx->setPlaceholder('fp.titlelabel', $this->modx->lexicon('titlelabel'));
        $this->modx->setPlaceholder('fp.title', $sourceDoc->get('pagetitle'));
        $this->modx->setPlaceholder('fp.longtitlelabel', $this->modx->lexicon('longtitlelabel'));
        $this->modx->setPlaceholder('fp.longtitle', $sourceDoc->get('longtitle'));
        $this->modx->setPlaceholder('fp.descriptionlabel', $this->modx->lexicon('descriptionlabel'));
        $this->modx->setPlaceholder('fp.description', $sourceDoc->get('description'));
        $this->modx->setPlaceholder('fp.aliaslabel', $this->modx->lexicon('aliaslabel'));
        $this->modx->setPlaceholder('fp.alias', $sourceDoc->get('alias'));
        $this->modx->setPlaceholder('fp.summarylabel', $this->modx->lexicon('summarylabel'));
        $this->modx->setPlaceholder('fp.summary', $sourceDoc->get('introtext'));
        $this->modx->setPlaceholder('fp.menutitlelabel', $this->modx->lexicon('menutitlelabel'));
        $this->modx->setPlaceholder('fp.menutitle', $sourceDoc->get('menutitle'));
        $this->modx->setPlaceholder('fp.menuindexlabel', $this->modx->lexicon('menuindexlabel'));
        $this->modx->setPlaceholder('fp.menuindex', $sourceDoc->get('menuindex'));
        $this->modx->setPlaceholder('fp.hidemenulabel', $this->modx->lexicon('hidemenulabel'));
        $this->modx->setPlaceholder('fp.createparentlabel', $this->modx->lexicon('createparentlabel'));
        if ($sourceDoc->get('hidemenu') == 1)
            $this->modx->setPlaceholder('fp.hidemenuscheck', "checked");
        $this->modx->setPlaceholder('fp.publishlabel', $this->modx->lexicon('publishlabel'));
        if ($sourceDoc->get('published') == 1)
            $this->modx->setPlaceholder('fp.publishcheck', "checked");
        $this->modx->setPlaceholder('fp.folderlabel', $this->modx->lexicon('folderlabel'));
        if ($sourceDoc->get('isfolder') == 1)
            $this->modx->setPlaceholder('fp.foldercheck', "checked");
        $this->modx->setPlaceholder('fp.savebuttonlabel', $this->modx->lexicon('savebuttonlabel'));
        $this->modx->setPlaceholder('fp.cancelbuttonlabel', $this->modx->lexicon('cancelbuttonlabel'));

        /* Content with tags opened */
        $content = str_replace('[[', '[ [', $sourceDoc->get('content'));
        $this->modx->setPlaceholder('fp.formcontent', $content);
    }

    /**
     * Create page function
     *
     * @param $source the page id we are editing
     * @param $parent the parent of the page id we are editing
     * 
     * @access public
     *
     */
    function createPageAloha($source, $parent) {

        /* If parent is not -1 this is a create, otherwise a refresh from save */
        if ($parent != -1) {

            /* Create a document */
            $newDoc = $this->modx->newObject('modResource');

            if (!$newDoc) {

                $this->modx->setPlaceholder('fp.error_message', $this->modx->lexicon('cantcreate'));
                return;
            }

            /* Get the parent document template */
            $templateToUse = 0;

            if ($parent != 0) {

                $parentDoc = $this->modx->getObject('modResource', array('id' => $parent));
                if (!$parentDoc) {

                    $this->modx->setPlaceholder('fp.error_message', $this->modx->lexicon('cantgetparent'));
                    return;
                }

                $templateToUse = $parentDoc->get('template');
            }

            /* Set default document parameters from the parent */
            if ($parent != 0) {

                $newDoc->set('context_key', $parentDoc->get('context_key'));
                $newDoc->set('richtext', $parentDoc->get('richtext'));
            } else {

                $newDoc->set('context_key', 'web');
            }

            /* Get the kind of template we are using */
            $templateSetting = $this->modx->getObject('modSystemSetting', array('key' => 'default_template',
                        'namespace' => 'frontpage'));
            if (!$templateSetting) {

                $newDoc->set('template', $templateToUse);
            } else {

                $template = $templateSetting->get('value');
                if ($template == 'parent') {

                    $newDoc->set('template', $templateToUse);
                } else {

                    $newDoc->set('template', $template);
                }
            }

            /* Parent */
            $newDoc->set('parent', $parent);

            /* Save the new document */
            $success = $newDoc->save();
            if ($success === false) {

                $this->modx->setPlaceholder('fp.error_message', $this->modx->lexicon('cantsave'));
                return;
            }

            /* Resource groups */
            if ($parent != 0) {

                $parentResourceGroups = $this->modx->getCollection('modResourceGroupResource', array('document' => $parent));

                foreach ($parentResourceGroups as $parentResourceGroup) {

                    $resourceGroup = $parentResourceGroup->get('document_group');
                    $newResourceGroup = $this->modx->newObject('modResourceGroupResource', array('document' => $newDoc->get('id'),
                                'document_group' => $resourceGroup));
                    $newResourceGroup->save();
                }
            }

            /* Set the source document to the new one */
            $source = $newDoc->get('id');
            $sourceDoc = $newDoc;

            /* Set the newly created document as a session variable */
            $_SESSION['fp_newCreate'] = $source;
        } else {

            $sourceDoc = $this->modx->getObject('modResource', array('id' => $source));
            if (!$sourceDoc) {

                $this->modx->setPlaceholder('fp.error_message', $this->modx->lexicon('nosuchdocument'));
                return;
            }
        } // Create or refresh

        /* Set the place holders */
        $this->modx->setPlaceholder('fp.source', $source);
        $this->modx->setPlaceholder('fp.parent', $parent);
        $this->modx->setPlaceholder('fp.titlelabel', $this->modx->lexicon('titlelabel'));
        $content = $sourceDoc->get('pagetitle');
        $this->modx->setPlaceholder('fp.title', $content);
        $this->modx->setPlaceholder('fp.longtitlelabel', $this->modx->lexicon('longtitlelabel'));
        $content = $sourceDoc->get('longtitle');
        $this->modx->setPlaceholder('fp.longtitle', $content);
        $this->modx->setPlaceholder('fp.descriptionlabel', $this->modx->lexicon('descriptionlabel'));
        $content = $sourceDoc->get('description');
        $this->modx->setPlaceholder('fp.description', $content);
        $this->modx->setPlaceholder('fp.aliaslabel', $this->modx->lexicon('aliaslabel'));
        $content = $sourceDoc->get('alias');
        $this->modx->setPlaceholder('fp.alias', $content);
        $this->modx->setPlaceholder('fp.summarylabel', $this->modx->lexicon('summarylabel'));
        $content = $sourceDoc->get('introtext');
        $this->modx->setPlaceholder('fp.summary', $content);
        $this->modx->setPlaceholder('fp.menutitlelabel', $this->modx->lexicon('menutitlelabel'));
        $content = $sourceDoc->get('menutitle');
        $this->modx->setPlaceholder('fp.menutitle', $content);
        $this->modx->setPlaceholder('fp.menuindexlabel', $this->modx->lexicon('menuindexlabel'));
        $content = $sourceDoc->get('menuindex');
        $this->modx->setPlaceholder('fp.menuindex', $content);
        $this->modx->setPlaceholder('fp.hidemenulabel', $this->modx->lexicon('hidemenulabel'));
        if ($sourceDoc->get('hidemenu') == 1) {
            $this->modx->setPlaceholder('fp.hidemenucheck', "1");
        } else {
              $this->modx->setPlaceholder('fp.hidemenucheck', "0");
        }
        $this->modx->setPlaceholder('fp.publishlabel', $this->modx->lexicon('publishlabel'));
        if ($sourceDoc->get('published') == 1) {
            $this->modx->setPlaceholder('fp.publishcheck', "1");
        } else {
             $this->modx->setPlaceholder('fp.publishcheck', "0");
        }
        $this->modx->setPlaceholder('fp.folderlabel', $this->modx->lexicon('folderlabel'));
        if ($sourceDoc->get('isfolder') == 1) {
            $this->modx->setPlaceholder('fp.foldercheck', "1");
        } else {
             $this->modx->setPlaceholder('fp.foldercheck', "0");
        }
        /* Content with tags opened */
        $content = str_replace('[[', '[ [', $sourceDoc->get('content'));
        $this->modx->setPlaceholder('fp.formcontent', $content);

        /* AJAX page */
        $ajaxPage = $this->_createAlohaAJAXLink();
        $this->modx->setPlaceholder('fp.alohaajax', $ajaxPage);
    }

    /**
     * Save Edit page function
     *
     * @param $source the page id we are editing
     * 
     * @access public
     *
     */
    function saveEditPage($source) {

        $docId = $this->modx->resource->get('id');

        /* Save the document fields */
        $sourceDoc = $this->modx->getObject('modResource', array('id' => $source));
        if (!$sourceDoc) {

            $this->modx->setPlaceholder('fp.error_message', $this->modx->lexicon('nosuchdocument'));
            return;
        }

        $sourceDoc->set('pagetitle', $_POST['title']);
        $sourceDoc->set('longtitle', $_POST['longtitle']);
        $sourceDoc->set('description', $_POST['description']);
        $sourceDoc->set('alias', $_POST['alias']);
        $sourceDoc->set('introtext', $_POST['summary']);
        $sourceDoc->set('menutitle', $_POST['menutitle']);
        $sourceDoc->set('menuindex', $_POST['menuindex']);
        $hidemenu = 0;
        if (isset($_POST['hidemenus']))
            $hidemenu = 1;
        $sourceDoc->set('hidemenu', $hidemenu);

        /* Publish dates */
        if (isset($_POST['publish'])) {

            $sourceDoc->set('published', 1);
            $now = time();
            $sourceDoc->set('publishedon', $now);
            $user = $this->modx->user->get('id');
            $sourceDoc->set('publishedby', $user);
        } else {

            $sourceDoc->set('published', 0);
        }

        /* Content with tags closed up again */
        $content = str_replace('[ [', '[[', $_POST['formcontent']);
        $sourceDoc->set('content', $content);

        /* Save it */
        $success = $sourceDoc->save();
        if ($success === false) {
            $this->modx->setPlaceholder('fp.error_message', $this->modx->lexicon('cantsave'));
            return;
        }

        /* Clear the cache */
        $this->_clearCache($sourceDoc);

        /* Redirect to ourselves to redraw the page */
        $params = array('frontpage' => '1',
            'source' => $source);
        $url = $this->modx->makeURL($docId, "", $params);
        $this->modx->sendRedirect($url);
    }

    /**
     * Save Create page function
     *
     * @param $source the page id we are editing
     * 
     * @access public
     *
     */
    function saveCreatePage($source) {

        $parentChanged = false;

        $docId = $this->modx->resource->get('id');

        /* Ok, save the document fields */
        $sourceDoc = $this->modx->getObject('modResource', array('id' => $source));
        if (!$sourceDoc) {

            $this->modx->setPlaceholder('fp.error_message', $this->modx->lexicon('nosuchdocument'));
            return;
        }

        /*  If the user has specified a create parent process this */
        if ($_POST['createparent'] != "") {

            $createParent = $_POST['createparent'];

            /* Check for an alias and get the id */
            if (!is_numeric($createParent)) {

                $isContainer = false;
                $parentDoc = $this->modx->getObject('modResource', array('alias' => $createParent));
                if ($parentDoc) {

                    $isContainer = $parentDoc->get('isfolder');

                    /* If a container add the container suffix */
                    if ($isContainer) {

                        $createParent .= $this->modx->getOption('container_suffix', null, '/');
                    } else {

                        /* Get the parent resources content type */
                        $parentType = $parentDoc->get('contentType');
                        $parentContentType = $this->modx->getObject('modContentType', array('mime_type' =>
                                    $parentType));
                        if ($parentContentType) {

                            $suffix = $parentContentType->get('file_extensions');
                        } else {

                            /* Default it */
                            $suffix = '.html';
                        }

                        $createParent .= $suffix;
                    }

                    $createParent = $this->modx->aliasMap[$createParent];
                }
            }
        }

        /* Validity check */
        if (is_numeric($createParent)) {

            /* Check for parent changed, update the resource */
            $parent = $sourceDoc->get('parent');
            if ($parent != $createParent) {

                /* Get the parent resource and set up our document from it */
                $parentDoc = $this->modx->getObject('modResource', array('id' => $createParent));
                if ($parentDoc) {

                    $parentDocId = $parentDoc->get('id');
                    $parentTemplate = $parentDoc->get('template');
                    $sourceDoc->set('parent', $parentDocId);

                    /* Template */
                    $templateSetting = $this->modx->getObject('modSystemSetting', array('key' => 'default_template',
                                'namespace' => 'frontpage'));

                    if ($templateSetting) {

                        $template = $templateSetting->get('value');
                        if ($template == 'parent') {

                            $sourceDoc->set('template', $parentTemplate);
                        }
                    }

                    /* If its not a container, it is now */
                    $isContainer = false;
                    $isContainer = $parentDoc->get('isfolder');
                    if (!$isContainer) {

                        $parentDoc->set('isfolder', 1);
                        $parentDoc->save();
                    }
                }

                /* Delete any existing resource groups */
                $this->modx->removeCollection('modResourceGroupResource', array('document' => $parent));

                /* Create the new ones */
                $parentResourceGroups = $this->modx->getCollection('modResourceGroupResource', array('document' => $createParent));

                foreach ($parentResourceGroups as $parentResourceGroup) {

                    $resourceGroup = $parentResourceGroup->get('document_group');
                    $newResourceGroup = $this->modx->newObject('modResourceGroupResource', array('document' => $sourceDoc->get('id'),
                                'document_group' => $resourceGroup));
                    $newResourceGroup->save();
                }
            }
        }

        $sourceDoc->set('pagetitle', $_POST['title']);
        $sourceDoc->set('longtitle', $_POST['longtitle']);
        $sourceDoc->set('description', $_POST['description']);
        $sourceDoc->set('alias', $_POST['alias']);
        $sourceDoc->set('introtext', $_POST['summary']);
        $sourceDoc->set('menutitle', $_POST['menutitle']);
        $sourceDoc->set('menuindex', $_POST['menuindex']);
        $hidemenu = 0;
        if (isset($_POST['hidemenus']))
            $hidemenu = 1;
        $sourceDoc->set('hidemenu', $hidemenu);
        /* Publish dates */
        if (isset($_POST['publish'])) {

            $sourceDoc->set('published', 1);
            $now = time();
            $sourceDoc->set('publishedon', $now);
            $user = $this->modx->user->get('id');
            $sourceDoc->set('publishedby', $user);
        } else {

            $sourceDoc->set('published', 0);
        }
        $folder = 0;
        if (isset($_POST['folder']))
            $folder = 1;
        $sourceDoc->set('isfolder', $folder);

        /* Content with tags closed up again */
        $content = str_replace('[ [', '[[', $_POST['formcontent']);
        $sourceDoc->set('content', $content);

        /* Save it */
        $success = $sourceDoc->save();
        if ($success === false) {
            $this->modx->setPlaceholder('fp.error_message', $this->modx->lexicon('cantsave'));
            return;
        }

        /* Clear the cache */
        $this->_clearCache($sourceDoc);

        /* Redirect to ourselves to redraw the page */
        $params = array('frontpage' => '1',
            'source' => $source,
            'parent' => '-1');
        $url = $this->modx->makeURL($docId, "", $params);
        $this->modx->sendRedirect($url);
    }

    /**
     * Clear the cache function
     *
     * @param $resource the resource we are using
     * 
     * @access private
     *
     */
    private function _clearCache($resource) {


        $cacheManager = $this->modx->getCacheManager();
        $this->modx->getVersionData();
        if (version_compare($this->modx->version['full_version'], '2.1.0-rc1', '<=')) {

            $cacheManager->clearCache(array(
                "{$resource->context_key}/resources/",
                "{$resource->context_key}/context.cache.php",
                    ), array(
                'objects' => array('modResource', 'modContext', 'modTemplateVarResource'),
                'publishing' => true
                    )
            );
        } else {

            $this->modx->cacheManager->refresh(array(
                'db' => array(),
                'auto_publish' => array('contexts' => array($resource->get('context_key'))),
                'context_settings' => array('contexts' => array($resource->get('context_key'))),
                'resource' => array('contexts' => array($resource->get('context_key'))),
            ));
        }
    }

    /**
     * Create AJAX redirect page function
     *
     * @param $source the page id we are editing
     * 
     * @access public
     *
     */
    function ajaxCreateRedirect() {

        $url = $this->modx->makeURL($_SESSION['fp_newCreate']);
        return $url;
    }

    /**
     * Process Aloha checkboxes
     *
     * @param $content, the elements content
     * 
     * @access private
     *
     */
    function _processAlohaCheckbox($content) {

        $returnContent = 0;

        if (( stristr($content, '1') != 0) || ( stristr($content, 'yes') != 0) ) {

            $returnContent = 1;
        }

        return $returnContent;
    }

    /**
     * Aloha AJAX processor
     *
     * @param $source, the source document
     * @param $content, the elements content
     * @param $element, the element identifier
     * 
     * @access public
     *
     */
    function ajaxAloha($source, $content, $element) {

        /* Check permissions, we only need to check for save here as we can't create using this 
         * callback, we must have a resource already so we must have been allowed to create.
         */
        if ((!$this->modx->hasPermission('save_document')) || (!$this->modx->resource->checkPolicy('save'))) {

            /* Just return, no need for an error message */
            return;
        }

        /* Get the resource */
        $resource = $this->modx->getObject('modResource', array('id' => $source));
        if (!$resource) {

            $this->modx->setPlaceholder('fp.error_message', $this->modx->lexicon('cantsave'));
            return;
        }

        /* Prepare the content */
        $content = trim($content);

        /* Update the content part dependant on element type */
        switch ($element) {

            case "alohaTitle":
                $resource->set('pagetitle', $content);
                break;

            case "alohaLongTitle":
                $resource->set('longtitle', $content);
                break;

            case "alohaDescription":
                $resource->set('description', $content);
                break;

            case "alohaAlias":
                $resource->set('alias', $content);
                break;

            case "alohaSummary":
                $resource->set('introtext', $content);
                break;

            case "alohaMenuTitle":
                $resource->set('menutitle', $content);
                break;

            case "alohaMenuIndex":
                $resource->set('menuindex', $content);
                break;

            case "alohaHideMenu":
                $content = $this->_processAlohaCheckbox($content);
                $resource->set('hidemenu', $content);
                break;

            case "alohaPublish":
                $content = $this->_processAlohaCheckbox($content);
                $resource->set('published', $content);
                break;

            case "alohaisFolder":
                $content = $this->_processAlohaCheckbox($content);
                $resource->set('isfolder', $content);
                break;

            case "alohaContent":
                $resource->set('content', $content);
                break;
            
            default:
                break;
        }

        /* Save the resource */
        $success = $resource->save();
        if ($success === false) {
            $this->modx->setPlaceholder('fp.error_message', $this->modx->lexicon('cantsave'));
            return;
        }

        return $element;
    }

}
