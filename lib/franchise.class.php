<?php
/*
$kakaoappkey : 예비필드 var1사용 (db ver. 2017011601이후 추가)
*/

class FRANCHISE
{
    public $table_config;
    public $table_list;
    public $table_category;
    public $charset;

    public $pageid;
    public $listtitle;
    public $listcomment;
    public $usemapnavi;
    public $template;
    public $category;
    public $perpage;
    public $useapi;
    public $naverapiid;
    public $naverapisecret;
    public $daumapikey;
    public $kakaoappkey; //예비필드 var1사용

    /**
     * construct class
     */
    public function __construct()
    {
        global $wpdb;

        $this->table_config = 'bbse_franchise_config';
        $this->table_list   = 'bbse_franchise';
        $this->charset      = $wpdb->get_charset_collate();

        $this->check_tables();

        $configs = $wpdb->get_row("SELECT * FROM {$this->table_config} WHERE prefix='{$wpdb->prefix}'");
        if ($configs) {
            $this->pageid         = $configs->pageid;
            $this->listtitle      = $configs->listtitle;
            $this->listcomment    = $configs->listcomment;
            $this->usemapnavi     = $configs->usemapnavi;
            $this->template       = $configs->template;
            $this->category       = $configs->category;
            $this->perpage        = $configs->perpage;
            $this->useapi         = $configs->useapi;
            $this->naverapiid     = $configs->naverapiid;
            $this->naverapisecret = $configs->naverapisecret;
            $this->daumapikey     = $configs->daumapikey;
            $this->kakaoappkey    = $configs->var1;
        }

        if (is_admin()) {
            add_filter('wp_default_editor', create_function('', 'return "tinymce";'));
        }
    }

    /**
     * for multi language
     */
    public function bbsefranchise_load_textdomain()
    {
        //BLANK
    }

    /**
     * Activation
     */
    public function activation()
    {
        $this->check_tables();
    }

    /**
     * db tables check
     */
    public function check_tables()
    {
        global $wpdb;
        $installed_version = get_option('franchise_db_version');
        $table_config      = $wpdb->get_var(" SHOW TABLES LIKE '{$this->table_config}' ");
        $table_list        = $wpdb->get_var(" SHOW TABLES LIKE '{$this->table_list}' ");

        if (!$installed_version || ($installed_version && $installed_version < BBSE_FRANCHISE_DB_VER) || empty($table_config[0]) || empty($table_list[0])) {
            $this->_dbdelta_bbse_franchise();

            if (!$installed_version) {
                add_option('franchise_db_version', BBSE_FRANCHISE_DB_VER, '', 'no');
            } elseif ($installed_version && $installed_version < BBSE_FRANCHISE_DB_VER) {
                update_option('franchise_db_version', BBSE_FRANCHISE_DB_VER, 'no');
            }
        }
    }

    /**
     * db tables create
     */
    public function _dbdelta_bbse_franchise()
    {
        global $wpdb;

        //create config table
        $config_scheme = '';
        $config_scheme = " CREATE TABLE `{$this->table_config}` (
                            `uid` int(11) NOT NULL AUTO_INCREMENT,
                            `prefix` varchar(255) NOT NULL,
                            `pageid` int(11) NOT NULL,
                            `listtitle` varchar(255) NOT NULL,
                            `listcomment` varchar(255) NOT NULL,
                            `usemapnavi` enum('y','n') NOT NULL DEFAULT 'y',
                            `template` varchar(255) NOT NULL,
                            `category` text NOT NULL,
                            `perpage` tinyint(4) NOT NULL,
                            `useapi` varchar(10) NOT NULL,
                            `naverapiid` varchar(255) NOT NULL,
                            `naverapisecret` varchar(255) NOT NULL,
                            `daumapikey` varchar(255) NOT NULL,
                            `var1` text NOT NULL,
                            `var2` text NOT NULL,
                            `var3` text NOT NULL,
                            `var4` text NOT NULL,
                            `var5` text NOT NULL,
                            `var6` text NOT NULL,
                            `var7` text NOT NULL,
                            `var8` text NOT NULL,
                            `var9` text NOT NULL,
                            `var10` text NOT NULL,
                            PRIMARY KEY (`uid`),
                            KEY `prefix` (`prefix`(250))
                        ) {$this->charset}; ";

        //create list table
        $list_scheme = '';
        $list_scheme = " CREATE TABLE {$this->table_list} (
                            `uid` int(11) NOT NULL AUTO_INCREMENT,
                            `prefix` varchar(255) NOT NULL,
                            `hide` enum('Y','N') NOT NULL DEFAULT 'N',
                            `category` varchar(255) NOT NULL,
                            `branchname` varchar(255) NOT NULL,
                            `addresstype` enum('J','R') NOT NULL DEFAULT 'J',
                            `languagetype` enum('K','E') NOT NULL DEFAULT 'K',
                            `zipcode` varchar(8) NOT NULL,
                            `address1` varchar(255) NOT NULL,
                            `address2` varchar(255) NOT NULL,
                            `mapaddress` varchar(255) NOT NULL,
                            `conutry` varchar(30) NOT NULL,
                            `sido` varchar(30) NOT NULL,
                            `sigungu` varchar(255) NOT NULL,
                            `bname` varchar(30) NOT NULL,
                            `phone` varchar(30) NOT NULL,
                            `mobile` varchar(30) NOT NULL,
                            `fax` varchar(30) NOT NULL,
                            `email` varchar(255) NOT NULL,
                            `tag` varchar(255) NOT NULL,
                            `memo` text NOT NULL,
                            `regdate` varchar(10) NOT NULL,
                            `var1` text NOT NULL,
                            `var2` text NOT NULL,
                            `var3` text NOT NULL,
                            `var4` text NOT NULL,
                            `var5` text NOT NULL,
                            `var6` text NOT NULL,
                            `var7` text NOT NULL,
                            `var8` text NOT NULL,
                            `var9` text NOT NULL,
                            `var10` text NOT NULL,
                            PRIMARY KEY (`uid`),
                            KEY `prefix` (`prefix`(250)),
                            KEY `hide` (`hide`),
                            KEY `category` (`category`(250)),
                            KEY `branchname` (`branchname`(250)),
                            KEY `address1` (`address1`(250)),
                            KEY `address2` (`address2`(250)),
                            KEY `sido` (`sido`),
                            KEY `sigungu` (`sigungu`(250)),
                            KEY `bname` (`bname`),
                            KEY `phone` (`phone`),
                            KEY `mobile` (`mobile`),
                            KEY `fax` (`fax`),
                            KEY `email` (`email`(250)),
                            KEY `tag` (`tag`(250))
                        ) {$this->charset}; ";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($config_scheme);
        dbDelta($list_scheme);
    }

    /**
     * db tables delete
     */
    protected function _delete_tables()
    {
        global $wpdb;
        if ($wpdb->query("DROP TABLE `bbse_franchise`, `bbse_franchise_config`;")) {
            delete_option('franchise_db_version');
            return 'deleted';
        } else {
            return 'fail';
        }
    }

    /**
     * Deactivation
     */
    public function deactivation()
    {
        //BLANK
        return true;
    }

    /**
     * Add administration menu
     */
    public function add_admin_menu()
    {
        add_menu_page('BBS e-Franchise', 'BBS e-Franchise', 'administrator', 'bbse_franchise', array($this, 'branches_lists_page'));
        add_submenu_page('bbse_franchise', __('List of branches', 'bbsefranchise'), __('List of branches', 'bbsefranchise'), 'administrator', 'bbse_franchise', array($this, 'branches_lists_page'));
        add_submenu_page('bbse_franchise', __('Add branch', 'bbsefranchise'), __('Add branch', 'bbsefranchise'), 'administrator', 'manage_braches_page', array($this, 'manage_braches_page'));
        add_submenu_page('bbse_franchise', __('Preferences', 'bbsefranchise'), __('Preferences', 'bbsefranchise'), 'administrator', 'manage_plugin_page', array($this, 'manage_plugin_page'));
        add_submenu_page('bbse_franchise', __('Data management', 'bbsefranchise'), __('Data management', 'bbsefranchise'), 'administrator', 'manage_plugin_data_page', array($this, 'manage_plugin_data_page'));
    }

    /**
     * Page of branches lists
     */
    public function branches_lists_page()
    {
        wp_enqueue_style('franchise-admin', BBSE_FRANCHISE_PLUGIN_WEB_URL."admin/franchise_style.css", array(), BBSE_FRANCHISE_VER);

        $this->check_tables();
        require_once(BBSE_FRANCHISE_PLUGIN_ABS_PATH."admin/franchise_list.php");
    }

    /**
     * Page of managing for branch
     */
    public function manage_braches_page()
    {
        wp_enqueue_style('franchise-admin', BBSE_FRANCHISE_PLUGIN_WEB_URL."admin/franchise_style.css", array(), BBSE_FRANCHISE_VER);

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-form');
        wp_register_script('daum-postcode-api', 'http://dmaps.daum.net/map_js_init/postcode.v2.js', false, '2');
        wp_enqueue_script('daum-postcode-api');

        $this->check_tables();
        require_once(BBSE_FRANCHISE_PLUGIN_ABS_PATH."admin/franchise_manage_branches.php");
    }

    /**
     * Page of managing for plugin
     */
    public function manage_plugin_page()
    {
        wp_enqueue_style('franchise-admin', BBSE_FRANCHISE_PLUGIN_WEB_URL."admin/franchise_style.css", array(), BBSE_FRANCHISE_VER);

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-form');
        wp_enqueue_script('jquery-color');

        $this->check_tables();
        require_once(BBSE_FRANCHISE_PLUGIN_ABS_PATH."admin/franchise_manage_plugin.php");
    }

    /**
     * Page of managing for plugins data
     */
    public function manage_plugin_data_page()
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-form');
        wp_enqueue_script('jquery-color');

        require_once(BBSE_FRANCHISE_PLUGIN_ABS_PATH."admin/franchise_manage_plugin_data.php");
    }

    /***************************************************/
    /*
            ADMINISTRATION LEVEL
    */
    /***************************************************/
    /**
     * Save plugins config data
     */
    public function config_save()
    {
        if (current_user_can('administrator') != true                                               ||
             isset($_POST['_bbse_franchise_nonce'])  != true                                        ||
             check_admin_referer('bbse_franchise_config_save', '_bbse_franchise_nonce') != true     ||
             wp_verify_nonce($_POST['_bbse_franchise_nonce'], 'bbse_franchise_config_save') != true) {
          die('error^noauth');
        }

        global $wpdb;

        $firstConfig = false;
        $myConfigs   = $wpdb->get_row("SELECT * FROM {$this->table_config} WHERE prefix='{$wpdb->prefix}'");
        if ($myConfigs->prefix != $wpdb->prefix) {
            $firstConfig = true;
        }

        // if edit mode
        if ($firstConfig == false) {
          $pageid          = $myConfigs->pageid;
          $usemapnavi      = $myConfigs->usemapnavi;
          $template        = $myConfigs->template;
          $category        = $myConfigs->category;
          $perpage         = $myConfigs->perpage;
          $useapi          = $myConfigs->useapi;
          $naverapiid      = $myConfigs->naverapiid;
          $naverapisecret  = $myConfigs->naverapisecret;
          $daumapikey      = $myConfigs->daumapikey;
          $kakaoappkey     = $myConfigs->var1;
        }

        foreach ($_POST as $key => $value) {
            $postconvertkey = 'pq_'.$key;
            if ($value) {
                ${$postconvertkey} = htmlentities(sanitize_text_field(trim($value)), ENT_QUOTES | ENT_IGNORE, "UTF-8");
            }
        } unset($_POST);

        if (!empty($pq_pageid) && $this->isInteger($pq_pageid) == true) {
            $pageid = $pq_pageid;
        } else {
            if ($firstConfig == true) {
                $pageid = '';
            }
        }

        if (!empty($pq_listtitle)) {
            $listtitle = $pq_listtitle;
        } else {
            if ($firstConfig == true) {
                $listtitle = '';
            }
        }

        if (!empty($pq_listcomment)) {
            $listcomment = $pq_listcomment;
        } else {
            if ($firstConfig == true) {
                $listcomment = '';
            }
        }

        if (!$pq_usemapnavi || $pq_usemapnavi == 'false') {
            $usemapnavi = 'n';
        } elseif ($pq_usemapnavi == 'true') {
            $usemapnavi = 'y';
        }

        // set skin template
        if (!empty($pq_template)) {
            $template = $pq_template;
        } else {
            if ($firstConfig == true) {
                $template = 'default';
            }
        }

        // set category
        if (!empty($pq_category)) {
            $catTempArr = explode(',', $pq_category);
            foreach ($catTempArr as $k => $v) {
                $catTempArr[$k] = trim($v);
            }
            $category = implode(',', array_unique($catTempArr));
        } else {
            if ($firstConfig == true) {
                $category = '';
            }
        }

        // set post/page
        if (!empty($pq_perpage) && $this->isInteger($pq_perpage) == true) {
            $perpage = $pq_perpage;
        } else {
            if ($firstConfig == true) {
                $perpage = get_option('posts_per_page');
            }
        }

        // check use map api
        if ($pq_useapi != 'naver' && $pq_useapi != 'daum') {
            $useapi = 'none';
        } else {
            $useapi = $pq_useapi;
            if ($useapi == 'naver') {
                if (empty($pq_naverapiid)) {
                    die('empty^naverapiid');
                } else {
                    $naverapiid = trim($pq_naverapiid);
                }

                if (empty($pq_naverapisecret)) {
                    die('empty^naverapisecret');
                } else {
                    $naverapisecret = trim($pq_naverapisecret);
                }
            }

            if (($useapi == 'daum' || $useapi == 'kakao') && empty($pq_daumapikey) && empty($pq_kakaoappkey)) {
                die('empty^daumapikey');
            } else {
                $daumapikey  = trim($pq_daumapikey);
                $kakaoappkey = trim($pq_kakaoappkey);
            }
        }

        // prepare data
        $dataSet = array(
            'pageid'         => $pageid,
            'listtitle'      => $listtitle,
            'listcomment'    => $listcomment,
            'usemapnavi'     => $usemapnavi,
            'template'       => $template,
            'category'       => $category,
            'perpage'        => $perpage,
            'useapi'         => $useapi,
            'naverapiid'     => $naverapiid,
            'naverapisecret' => $naverapisecret,
            'daumapikey'     => $daumapikey,
            'var1'           => $kakaoappkey,
        );

        // edit
        if ($firstConfig == false) {
            $jobresult = $wpdb->update(
                $this->table_config,
                $dataSet,
                array('prefix' => $wpdb->prefix)
            );
        // first save
        } elseif ($firstConfig == true) {
            $dataSet['category']       = $category       ? $category       : '';
            $dataSet['naverapiid']     = $naverapiid     ? $naverapiid     : '';
            $dataSet['naverapisecret'] = $naverapisecret ? $naverapisecret : '';
            $dataSet['daumapikey']     = $daumapikey     ? $daumapikey     : '';
            $dataSet['var1']           = $kakaoappkey    ? $kakaoappkey    : '';
            $dataSet['prefix']         = $wpdb->prefix;

            $jobresult = $wpdb->insert(
                $this->table_config,
                $dataSet,
                array("%d", "%s", "%s", "%s", "%s", "%s", "%d", "%s", "%s", "%s", "%s", "%s", "%s")
            );
        }

        if ($jobresult === false) {
            die('error^fail');
        } elseif ($jobresult === 0) {
            die('noupdated');
        } else {
            die('ok');
        }
    }

    /**
     * Manage branches
     */
    public function branch_save()
    {
        if (current_user_can('administrator') != true                                              ||
             isset($_POST['_bbse_franchise_nonce'])  != true                                        ||
             check_admin_referer('bbse_franchise_branch_save', '_bbse_franchise_nonce') != true     ||
             wp_verify_nonce($_POST['_bbse_franchise_nonce'], 'bbse_franchise_branch_save') != true) {
          die('error^noauth');
        }

        global $wpdb;

        $isModify = false;
        $myBranch = $wpdb->get_row("SELECT * FROM {$this->table_list} WHERE prefix='{$wpdb->prefix}'");

        //keep prev. data
        if (!empty($myBranch)) {
            $hide         = $myBranch->hide;
            $category     = $myBranch->category;
            $branchname   = $myBranch->branchname;
            $addresstype  = $myBranch->addresstype;
            $languagetype = $myBranch->languagetype;
            $zipcode      = $myBranch->zipcode;
            $address1     = $myBranch->address1;
            $address2     = $myBranch->address2;
            $mapaddress   = $myBranch->mapaddress;
            $sido         = $myBranch->sido;
            $sigungu      = $myBranch->sigungu;
            $bname        = $myBranch->bname;
            $phone        = $myBranch->phone;
            $mobile       = $myBranch->mobile;
            $fax          = $myBranch->fax;
            $email        = $myBranch->email;
            $tag          = $myBranch->tag;
            $memo         = $myBranch->memo;
            $isModify     = true;
        }

        foreach ($_POST as $key => $value) {
            $postconvertkey = 'pq_'.$key;
            if ($value) {
                if ($key == 'memo') {
                    ${$postconvertkey} = $value;
                } else {
                    ${$postconvertkey} = htmlentities(sanitize_text_field(trim($value)), ENT_QUOTES | ENT_IGNORE, "UTF-8");
                }
            }
        } unset($_POST);

        //노출 설정
        if ($pq_hide === 'Y') {
            $hide = $pq_hide;
        } else {
            $hide = 'N';
        }

        //카테고리설정
        if (!empty($pq_category)) {
            $category = $pq_category;
        } else {
            $category = '';
        }

        //지점이름
        if (!empty($pq_branchname)) {
            $branchname = $pq_branchname;
        } else {
            die('empty^branchname');
        }

        //주소
        if (empty($pq_address1)) {
            die('empty^address1');
        } else {
            $address1 = $pq_address1;
        }

        if (empty($pq_address2)) {
            die('empty^address2');
        } else {
            $address2 = $pq_address2;
        }

        if ($address1 && $address2)   {
            if (!empty($pq_addresstype))  $addresstype  = $pq_addresstype;
            if (!empty($pq_languagetype)) $languagetype = $pq_languagetype;
            if (!empty($pq_zipcode))      $zipcode      = $pq_zipcode;
            if (!empty($pq_sido))         $sido         = $pq_sido;
            if (!empty($pq_sigungu))      $sigungu      = $pq_sigungu;
            if (!empty($pq_bname))        $bname        = $pq_bname;
        } else {
            die('empty^address1');
        }

        if ($languagetype == 'K') {
            if (empty($pq_mapaddress)) {
                $mapaddress = $address1;
            } else {
                $mapaddress = $pq_mapaddress;
            }
        } else {
            if (empty($pq_mapaddress)) {
                die('empty^address1');
            } else {
                $mapaddress = $pq_mapaddress;
            }
        }

        //전화번호
        if (isset($pq_phone) && !empty($pq_phone)) {
            $phone_TMP = str_replace(array(' ', '-', ' /', '.'), '', trim($pq_phone));
            $parsed    = $this->parsePhoneNumber($phone_TMP, 'P');
            if ($parsed == false) {
                die('error^phone');
            } else {
                $phone = implode('-', $parsed);
                unset($phone_TMP);
            }
        } else {
            $phone = '';
        }

        //휴대전화번호
        if (isset($pq_mobile) && !empty($pq_mobile)) {
            $mobile_TMP = str_replace(array(' ', '-', ' /', '.'), '', trim($pq_mobile));
            $parsed     = $this->parsePhoneNumber($mobile_TMP, 'M');
            if ($parsed == false) {
                die('error^mobile');
            } else {
                $mobile = implode('-', $parsed);
                unset($mobile_TMP);
            }
        } else {
            $mobile = '';
        }

        //팩스번화번호
        if (isset($pq_fax) && !empty($pq_fax)) {
            $fax_TMP = str_replace(array(' ', '-', ' /', '.'), '', trim($pq_fax));
            $parsed  = $this->parsePhoneNumber($fax_TMP, 'P');
            if ($parsed == false) {
                die('error^fax');
            } else {
                $fax = implode('-', $parsed);
                unset($fax_TMP);
            }
        } else {
            $fax = '';
        }

        //이메일 주소
        if (isset($pq_email) && !empty($pq_email)) {
            $email = sanitize_email(trim($pq_email));
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                die('error^email');
            }
        } else {
            $email = '';
        }

        //태그
        if (isset($pq_tag) && !empty($pq_tag)) {
            $tag = trim($pq_tag);
        } else {
            $tag = '';
        }

        //메모
        if (isset($pq_memo) && !empty($pq_memo)) {
            $memo = trim($pq_memo);
        } else {
            die('empty^memo');
        }

        //데이터 준비
        $dataSet = array(
            'hide'         => $hide,
            'category'     => $category,
            'branchname'   => $branchname,
            'addresstype'  => $addresstype,
            'languagetype' => $languagetype,
            'zipcode'      => $zipcode,
            'address1'     => $address1,
            'address2'     => $address2,
            'mapaddress'   => $mapaddress,
            'sido'         => $sido,
            'sigungu'      => $sigungu,
            'bname'        => $bname,
            'phone'        => $phone,
            'mobile'       => $mobile,
            'fax'          => $fax,
            'email'        => $email,
            'tag'          => $tag,
            'memo'         => $memo,
        );

        if ($pq_mode === 'add') {
            $dataSet['prefix']  = $wpdb->prefix;
            $dataSet['regdate'] = time();
            // DO INSERT
            $jobresult = $wpdb->insert(
                $this->table_list,
                $dataSet,
                array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
            );
        } else if ($pq_mode === 'edit' && $pq_uid && $this->isInteger($pq_uid)) {
            // DO UPDATE
            $jobresult = $wpdb->update(
                $this->table_list,
                $dataSet,
                array('uid' => $pq_uid)
            );
        }

        if ($jobresult === false) {
            die('error^fail');
        } elseif ($jobresult === 0) {
            die('noupdated');
        } else {
            die('ok');
        }
    }

    /**
     * Manage list of branches
     */
    public function branch_bulkmanage()
    {
        if (current_user_can('administrator') != true                                              ||
             isset($_POST['_bbse_franchise_nonce'])  != true                                        ||
             check_admin_referer('bbse_franchise_branch_bulkmanage', '_bbse_franchise_nonce') != true     ||
             wp_verify_nonce($_POST['_bbse_franchise_nonce'], 'bbse_franchise_branch_bulkmanage') != true) {
          die('noauth');
        }

        global $wpdb;

        if ($_POST['mode'] == 'toggle') {
            foreach ($_POST['uid'] as $k => $v) {
                $hide = $_POST['hide'][$k] == 'N' ? 'Y' : 'N';
                if ($this->isInteger($v)) {
                    $wpdb->update(
                        $this->table_list,
                        array('hide' => $hide),
                        array('uid'  => $v)
                    );
                }
            }
        }

        if ($_POST['mode'] == 'delete') {
            foreach ($_POST['uid'] as $k => $v) {
                if ($this->isInteger($v)) {
                    $wpdb->delete(
                        $this->table_list,
                        array('uid' => $v)
                    );
                }
            }
        }
    }

    /**
     * Backup plugin data
     */
    public function data_backup()
    {
        if (current_user_can('administrator') != true                                               ||
             isset($_POST['_bbse_franchise_nonce'])  != true                                        ||
             check_admin_referer('bbse_franchise_data_manage', '_bbse_franchise_nonce') != true     ||
             wp_verify_nonce($_POST['_bbse_franchise_nonce'], 'bbse_franchise_data_manage') != true) {
          die(__('<p> You do not have permission. <br> If you are having the same issue even though it is licensed, contact the developer of the plugin or the operator.</p>', 'bbsefranchise'));
        } else {
            require_once BBSE_FRANCHISE_PLUGIN_ABS_PATH.'lib/databackup.class.php';
            $data_backup = new FRANCHISEdataBACKUP();
            $tbls        = $data_backup->get_tables();
            $xml_data    = NULL;
            foreach ($tbls as $key => $value) {
                $xml_data .= $data_backup->get_xml($value);
            }
            $data_backup->xml_download($xml_data);
        }
        return false;
    }

    /**
     * Restore plugin data
     */
    public function data_restore()
    {
        if (current_user_can('administrator') != true                                              ||
             isset($_POST['_bbse_franchise_nonce'])  != true                                        ||
             check_admin_referer('bbse_franchise_data_manage', '_bbse_franchise_nonce') != true     ||
             wp_verify_nonce($_POST['_bbse_franchise_nonce'], 'bbse_franchise_data_manage') != true) {
          die('error noauth');
        } else {
            require_once BBSE_FRANCHISE_PLUGIN_ABS_PATH.'lib/databackup.class.php';
            $data_backup = new FRANCHISEdataBACKUP();
            $upload_dir  = wp_upload_dir();
            if (!empty($upload_dir['error']) || strpos($upload_dir['error'], 'writable by the server?')) {
                die('error permission|||'.$upload_dir['path']);
            } else {
                $xml_file = $upload_dir['basedir'].'/'.basename($_FILES['bbse_franchise_xml_file']['name']);

                if (move_uploaded_file($_FILES['bbse_franchise_xml_file']['tmp_name'], $xml_file)) {
                    $file_ext = explode(".", $xml_file);
                    if (end($file_ext) == "xml") {
                        $data_backup->xml_import($xml_file);
                        $msg = "success";
                    } else {
                        $msg = "error file_type";
                    }

                    unlink($xml_file);
                    die($msg);
                } else {
                    die("error fail");
                }
            }
        }
        return false;
    }

    /**
     * Delete database tables
     */
    public function delete_tables()
    {
        if (current_user_can('administrator') != true                                              ||
             isset($_POST['_bbse_franchise_nonce'])  != true                                       ||
             check_admin_referer('bbse_franchise_data_manage', '_bbse_franchise_nonce') != true    ||
             wp_verify_nonce($_POST['_bbse_franchise_nonce'], 'bbse_franchise_data_manage') != true){
          die('noauth');
        } else {
            die($this->_delete_tables());
        }
        return false;
    }

    /**************/
    /* User level */
    /**************/

    /**
     * Presentation
     */
    public function franchise_shortcode($atts, $content = "")
    {
        global $wpdb;
        if (!$this->template) {
            die("ERROR");
        }

        $template     = $this->templatesCheck(BBSE_FRANCHISE_TEMPLATES_PATH, $this->template);
        $templatePath = BBSE_FRANCHISE_TEMPLATES_PATH.$template;

        wp_enqueue_style('franchise-skin', BBSE_FRANCHISE_TEMPLATES_URL.$template.'/style.css', array(), BBSE_FRANCHISE_VER, 'all');

        foreach ($_GET as $key => $value) {
            $getconvertkey = 'gq_'.$key;
            if ($value) {
                ${$getconvertkey} = htmlentities(sanitize_text_field(trim($value)), ENT_QUOTES | ENT_IGNORE, "UTF-8");
            }
        }

        $uid      = (!empty($gq_uid) && $this->isInteger($gq_uid) == true) ? $gq_uid      : false;
        $sido     = (!empty($gq_sido))                                     ? $gq_sido     : false;
        $category = (!empty($gq_category))                                 ? $gq_category : false;
        $keyword  = (!empty($gq_keyword))                                  ? $gq_keyword  : false;

        ob_start();
        //목록
        if ($uid == false) {
            $escaped        = array();
            $searchQueryArr = array();
            $searchQuery    = '';

            $paged       = get_query_var('paged') ? get_query_var('paged') : 1;
            $perpage     = $this->perpage         ? $this->perpage         : get_option('posts_per_page');
            $pagingQuery = ' LIMIT '.(($paged - 1) * $perpage).', '.$perpage.' ';

            $prepare = "";
            $prepare = " {$this->table_list} WHERE (prefix='{$wpdb->prefix}' AND hide='N') ";

            if ($this->usemapnavi == 'y' && $sido) {
                $searchQueryArr[] = ' (sido = \''.$sido.'\') ';
            }

            if ($category) {
                $searchQueryArr[] = ' (category = \''.$category.'\') ';
            }

            if ($keyword) {
                $keyword     = '%'.$keyword.'%';
                if ($this->usemapnavi == 'y' && $sido == false) {
                    $escaped[] = $wpdb->prepare(" sido LIKE '%s' ", $keyword);
                }

                if ($category == false) {
                    $escaped[] = $wpdb->prepare(" category LIKE '%s' ", $keyword);
                }

                $escaped[] = $wpdb->prepare(" branchname LIKE '%s' ", $keyword);
                $escaped[] = $wpdb->prepare(" address2 LIKE '%s' ",   $keyword);
                if ($this->usemapnavi == 'y') {
                    $escaped[] = $wpdb->prepare(" address1 LIKE '%s' ", $keyword);
                    $escaped[] = $wpdb->prepare(" sigungu LIKE '%s' ",  $keyword);
                    $escaped[] = $wpdb->prepare(" bname LIKE '%s' ",    $keyword);
                }

                $escaped[] = $wpdb->prepare(" phone LIKE '%s' ",      $keyword);
                $escaped[] = $wpdb->prepare(" mobile LIKE '%s' ",     $keyword);
                $escaped[] = $wpdb->prepare(" fax LIKE '%s' ",        $keyword);
                $escaped[] = $wpdb->prepare(" email LIKE '%s' ",      $keyword);
                $escaped[] = $wpdb->prepare(" tag LIKE '%s' ",        $keyword);

                $searchQueryArr[] = ' ('.implode(' OR ', $escaped).') ';
            }

            if (($sido || $keyword || $category) && !empty($searchQueryArr)) {
                $searchQuery = ' AND '.implode(' AND ', $searchQueryArr);
            }

            // DO SELECT
            $DATA  = $wpdb->get_results(" SELECT * FROM ".$prepare.$searchQuery.' ORDER BY uid DESC '.$pagingQuery);
            $total = $wpdb->get_var(" SELECT count(*) FROM ".$prepare.$searchQuery);

            $used_categories_R = $wpdb->get_results(" SELECT category FROM {$this->table_list} WHERE (prefix='{$wpdb->prefix}' AND hide='N') GROUP BY category");

            if ($used_categories_R) {
                foreach ($used_categories_R as $k=>$v) {
                    if ($v->category != '') {
                        $used_categories[] = $v->category;
                    }
                }
            }

            //paging
            $link       = html_entity_decode(get_pagenum_link());
            $query_args = array();
            $urlQuery   = explode('?', $link);
            if (isset($urlQuery[1])) {
                wp_parse_str($urlQuery[1], $query_args);
            }
            $pages      = ceil($total/$perpage);
            $base       = trailingslashit(remove_query_arg(array_keys($query_args),$link)).'%_%';

            $format     = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos($base, 'index.php') ? 'index.php/' : '';
            $format    .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit('page/%#%', 'paged') : '?paged=%#%';

            $paging = paginate_links(array(
                                        'current'   => $paged,
                                        'base'      => $base,
                                        'total'     => $pages,
                                        'format'    => $format,
                                        'mid_size'  => 5,
                                        'add_args'  => array_map('urlencode', $query_args),
                                        'prev_next' => true,
                                        'prev_text' => '&lt;',
                                        'next_text' => '&gt;',
                                     )
                      );
            require_once($templatePath.'/list.php');
        // view
        } else {
            $DATA  = $wpdb->get_row("SELECT * FROM {$this->table_list} WHERE (prefix='{$wpdb->prefix}' AND hide='N') AND uid = {$uid}");
            if ($DATA) {
                require_once($templatePath.'/view.php');
            } else {
                return false;
            }
        }

        $ret_contents = ob_get_contents();
        ob_end_clean();
        return $ret_contents;
    }

    /***************************************************/
    /* FUNCTIONS
    /***************************************************/

    /**
     * configs
     */
    public function configs()
    {
        $config = array();
        $config['pageid']         = $this->pageid;
        $config['listtitle']      = $this->listtitle;
        $config['listcomment']    = $this->listcomment;
        $config['usemapnavi']     = $this->usemapnavi;
        $config['template']       = $this->template;
        $config['category']       = $this->category;
        $config['perpage']        = $this->perpage;
        $config['useapi']         = $this->useapi;
        $config['naverapiid']     = $this->naverapiid;
        $config['naverapisecret'] = $this->naverapisecret;
        $config['daumapikey']     = $this->daumapikey;
        $config['kakaoappkey']    = $this->kakaoappkey;
        $config['table_config']   = $this->table_config;
        $config['table_list']     = $this->table_list;
        $config['table_category'] = $this->table_category;

        return $config;
    }

    /**
     * Skin template list
     */
    public function templates()
    {
        $dirHandle = NULL;
        $templatesList  = array();
        if (is_dir(BBSE_FRANCHISE_TEMPLATES_PATH)) {
            $dirInfo = scandir(BBSE_FRANCHISE_TEMPLATES_PATH);
            foreach ($dirInfo as $v) {
                if ($v != '.' && $v != '..' && filetype(BBSE_FRANCHISE_TEMPLATES_PATH.$v) == 'dir') {
                    $templatesList[] = $v;
                }
            }
        }
        return $templatesList;
    }

    /**
     * skin template check
     */
    public function templatesCheck($path, $templates)
    {
        if (is_dir($path.$templates)) {
            if (is_file($path.$templates.'/list.php') && is_file($path.'/'.$templates.'/view.php') && is_file($path.'/'.$templates.'/style.css')) {
                return $templates;
            } else {
                return 'default';
            }
        } else {
            return 'default';
        }
    }

    /**
     * Check phone number format. (Korean territory only)
     */
    public function parsePhoneNumber($number, $type='M')
    {
        $patterns = array('P'=> '/(02|031|032|033|041|042|043|044|051|052|053|054|055|061|062|063|064|070)(\d{3,4})(\d{4})/',
                          'M'=> '/(010|011|015|016|017|018|019)(\d{3,4})(\d{4})/');

        //대표번호 - 일반번호중 대표번호 사용하는 사업장이 있을 가능성이 많아 추가, 기타 특수 번호는 잘못된 번호로 밴.
        $patternE = '/(1666|1688|1544|1644|1661|1599|1566|1600|1670|1588|1877)(\d{4})/';
        $number   = trim(str_replace(array(' ', '-', '.'), '', $number));
        if ($this->isInteger($number)) {
            preg_match_all($patterns[$type], $number, $out, PREG_SET_ORDER);
            if (isset($out) && !empty($out)) {
                $result[0] = $out[0][1];
                $result[1] = $out[0][2];
                $result[2] = $out[0][3];
                return $result;
            } else {
                if ($type == 'P') {
                    preg_match_all($patternE, $number, $out, PREG_SET_ORDER);
                    if (isset($out) && !empty($out)) {
                        $result[0] = $out[0][1];
                        $result[1] = $out[0][2];
                        return $result;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * Draw map with Naver - mapAPI3.0 (2017/06 newer api)
     */
    public  function draw_naver_map($vars=array())
    {
        //기본값 설정
        if (empty($vars['showerror'])) {
            $vars['showerror'] = 'n';
        }

        if (empty($vars['level'])) {
            $vars['level'] = '13';
        }

        if (empty($vars['width'])) {
            $vars['width'] = '100%';
        }

        if (empty($vars['height'])) {
            $vars['height'] = '40vh';
        }

        if ($vars['showerror'] == 'y') {
            $vars['openC']  = '';
            $vars['closeC'] = '';
        } else {
            $vars['openC'] = '<!-- ';
            $vars['closeC'] = ' -->';
        }

        if (!$this->naverapiid || !$this->naverapisecret) {
            die($vars['openC'].__('Naver Map Error', 'bbsefranchise').' : '.__('API not set', 'bbsefranchise').'<br>'.$vars['closeC']);
        }

        //주소확인
        if ($vars['address']) {
            $str_addr = $vars['address'];
        } else {
            die($vars['openC'].__('Naver Map Error', 'bbsefranchise').' : '.__('Address not set', 'bbsefranchise').'<br>'.$vars['closeC']);
        }

        list($usec, $sec) = explode(" ", microtime());
        $uID = ($usec+$usec)*1000000;

        //지도 마크업 생성
        $mapHtml  = '';
        $mapHtml .= '<!-- 네이버지도 시작 -->';
        $mapHtml .= '<div class="naverMapWrap'.$uID.'">'.PHP_EOL;
        $mapHtml .= '    <div id="naver-map'.$uID.'" style="width:'.$vars['width'].';height:'.$vars['height'].'"></div>'.PHP_EOL;
        $mapHtml .= '    <script type="text/javascript" src="https://openapi.map.naver.com/openapi/v3/maps.js?clientId='.$this->naverapiid.'"></script>'.PHP_EOL;
        $mapHtml .= '    <script type="text/javascript" src="https://openapi.map.naver.com/openapi/v3/maps-geocoder.js"></script>'.PHP_EOL;
        //지도스크립트
        $mapHtml .= '    <script>'.PHP_EOL;
        $mapHtml .= '    naver.maps.Service.geocode('.PHP_EOL;
        $mapHtml .= '       {address : \''.$str_addr.'\'},'.PHP_EOL;
        $mapHtml .= '       function(status, response) {'.PHP_EOL;
        $mapHtml .= '           var item      = response.result.items[0];'.PHP_EOL;
        $mapHtml .= '           var point     = new naver.maps.Point(item.point.x, item.point.y);'.PHP_EOL;
        $mapHtml .= '           var $location = new naver.maps.LatLng(point.y, point.x);'.PHP_EOL;
        $mapHtml .= '           var $mapObj   = new naver.maps.Map(\'naver-map'.$uID.'\', {'.PHP_EOL;
        $mapHtml .= '               center             : $location,'.PHP_EOL;
        $mapHtml .= '               zoom               : 12,'.PHP_EOL;
        $mapHtml .= '               minZoom            : 1,'.PHP_EOL;
        $mapHtml .= '               zoomControl        : true,'.PHP_EOL;
        $mapHtml .= '               zoomControlOptions : {'.PHP_EOL;
        $mapHtml .= '                   position: naver.maps.Position.TOP_RIGHT'.PHP_EOL;
        $mapHtml .= '               }'.PHP_EOL;
        $mapHtml .= '           });'.PHP_EOL;

        $mapHtml .= '           var marker = new naver.maps.Marker({'.PHP_EOL;
        $mapHtml .= '               position : $location,'.PHP_EOL;
        $mapHtml .= '               map      : $mapObj'.PHP_EOL;
        $mapHtml .= '           });'.PHP_EOL;

        $mapHtml .= '           $mapObj.setOptions("mapTypeControl", true);'.PHP_EOL;
        $mapHtml .= '       }'.PHP_EOL;
        $mapHtml .= '   );'.PHP_EOL;
        $mapHtml .= '   </script>'.PHP_EOL;
        $mapHtml .= '</div>';
        $mapHtml .= '<!-- //네이버지도 마침 -->';
        //출력
        return $mapHtml;
    }

    /**
     * Draw map width Daum
     */
    public function draw_daum_map($vars=array())
    {
        //기본값확인/설정
        if (empty($vars['showerror'])) {
            $vars['showerror'] = 'n';
        }

        if (empty($vars['level'])) {
            $vars['level'] = '3';
        }

        if (empty($vars['width'])) {
            $vars['width'] = '100%';
        }

        if (empty($vars['height'])) {
            $vars['height'] = '40vh';
        }

        if ($vars['showerror'] == 'y') {
            $vars['openC']  = '';
            $vars['closeC'] = '';
        } else {
            $vars['openC'] = '<!-- ';
            $vars['closeC'] = ' -->';
        }

        //API 키 확인
        if ($this->kakaoappkey || $this->daumapikey){
            if ($this->kakaoappkey){
                $map_key  = $this->kakaoappkey;
                $key_type = 'app';
            }else {
                $map_key  = $this->daumapikey;
                $key_type = 'api';
            }
        }else{
            die($vars['openC'].__('Daum Map Error', 'bbsefranchise').' : '.__('API not set', 'bbsefranchise').'<br>'.$vars['closeC']);
        }


        //주소확인
        if ($vars['address']) {
            $str_addr = $vars['address'];
        } else {
            die($vars['openC'].__('Daum Map Error', 'bbsefranchise').' : '.__('Address not set', 'bbsefranchise').'<br>'.$vars['closeC']);
        }

        //마커내용
        $marker  = false;
        $marker .= '<div style="padding:3px 5px 5px;line-height:inherit;text-align:center;font-weight:bold;font-size:12px;box-sizing:border-box">';
        $marker .= $vars['branchname'];
        $marker .= '</div><br>';

        list($usec, $sec) = explode(" ", microtime());
        $uID = ($usec+$usec)*1000000;

        //지도 마크업 생성
        $mapHtml  = '';
        $mapHtml .= '<!-- 다음지도 시작 -->'.PHP_EOL;
        $mapHtml .= '<div class="daumMapWrap'.$uID.'">'.PHP_EOL;
        if (empty($useDirect) || $useDirect == false) {
        $mapHtml .= '   <style scoped>'.PHP_EOL;
        $mapHtml .= '       .daumMapWrap'.$uID.' {position:relative;overflow:hidden;}'.PHP_EOL;
        $mapHtml .= '       .daumMapWrap'.$uID.' .radius_border{border:1px solid #919191;border-radius:5px;}'.PHP_EOL;
        $mapHtml .= '       .daumMapWrap'.$uID.' .custom_typecontrol {position:absolute;top:10px;right:10px;overflow:hidden;width:132px;height:30px;margin:0;padding:0;z-index:3;font-size:12px;}'.PHP_EOL;
        $mapHtml .= '       .daumMapWrap'.$uID.' .custom_typecontrol span {display:block;width:65px;height:30px;line-height:30px !important;float:left;text-align:center;line-height:30px;cursor:pointer;}'.PHP_EOL;
        $mapHtml .= '       .daumMapWrap'.$uID.' .custom_typecontrol .btn {background:#fff;background:linear-gradient(#fff,  #e6e6e6);}'.PHP_EOL;
        $mapHtml .= '       .daumMapWrap'.$uID.' .custom_typecontrol .btn:hover {background:#f5f5f5;background:linear-gradient(#f5f5f5,#e3e3e3);}'.PHP_EOL;
        $mapHtml .= '       .daumMapWrap'.$uID.' .custom_typecontrol .btn:active {background:#e6e6e6;background:linear-gradient(#e6e6e6, #fff);}'.PHP_EOL;
        $mapHtml .= '       .daumMapWrap'.$uID.' .custom_typecontrol .selected_btn {color:#fff !important;background:#425470;background:linear-gradient(#425470, #5b6d8a);}'.PHP_EOL;
        $mapHtml .= '       .daumMapWrap'.$uID.' .custom_typecontrol .selected_btn:hover {color:#fff;}'.PHP_EOL;
        $mapHtml .= '       .daumMapWrap'.$uID.' .custom_zoomcontrol {position:absolute;top:50px;right:10px;width:36px;height:80px;overflow:hidden;z-index:3;background-color:#f5f5f5;}'.PHP_EOL;
        $mapHtml .= '       .daumMapWrap'.$uID.' .custom_zoomcontrol span {display:block;width:36px;height:40px;text-align:center;cursor:pointer;}'.PHP_EOL;
        $mapHtml .= '       .daumMapWrap'.$uID.' .custom_zoomcontrol span img {width:15px;padding:12px 0;border:none;}'.PHP_EOL;
        $mapHtml .= '       .daumMapWrap'.$uID.' .custom_zoomcontrol span:first-child{border-bottom:1px solid #bfbfbf;}'.PHP_EOL;
        $mapHtml .= '   </style>'.PHP_EOL;
        $mapHtml .= '   <div class="custom_typecontrol radius_border">'.PHP_EOL;
        $mapHtml .= '       <span id="btnRoadmap" class="toggleType selected_btn" data-maptype="roadmap">지도</span>'.PHP_EOL;
        $mapHtml .= '       <span id="btnSkyview" class="toggleType btn" data-maptype="skyview">스카이뷰</span>'.PHP_EOL;
        $mapHtml .= '   </div>'.PHP_EOL;
        $mapHtml .= '   <div class="custom_zoomcontrol radius_border"> '.PHP_EOL;
        $mapHtml .= '       <span class="zoomIn"><img src="http://i1.daumcdn.net/localimg/localimages/07/mapapidoc/ico_plus.png" alt="확대"></span> '.PHP_EOL;
        $mapHtml .= '       <span class="zoomOut"><img src="http://i1.daumcdn.net/localimg/localimages/07/mapapidoc/ico_minus.png" alt="축소"></span>'.PHP_EOL;
        $mapHtml .= '   </div>'.PHP_EOL;
        }
        $mapHtml .= '   <div id="daum-map'.$uID.'" style="'.$vars['width'].';height:'.$vars['height'].'"></div>'.PHP_EOL;
        if ($key_type == 'app'){
        $mapHtml .= '   <script src="//dapi.kakao.com/v2/maps/sdk.js?appkey='.$map_key.'&libraries=services"></script>'.PHP_EOL;
        }elseif ($key_type == 'api'){
        $mapHtml .= '   <script src="//apis.daum.net/maps/maps3.js?apikey='.$map_key.'&libraries=services"></script>'.PHP_EOL;
        }
        $mapHtml .= '   <script>'.PHP_EOL;
        $mapHtml .= '   var geocoder = new daum.maps.services.Geocoder();'.PHP_EOL;
        $mapHtml .= '   var callback = function(result, status) {'.PHP_EOL;
        $mapHtml .= '       var mapContainer = document.getElementById(\'daum-map'.$uID.'\');'.PHP_EOL;
        if ($key_type == 'app'){
        $mapHtml .= '       var coords       = new daum.maps.LatLng(result[0].y, result[0].x);'.PHP_EOL;
        }elseif ($key_type == 'api'){
        $mapHtml .= '       var coords       = new daum.maps.LatLng(result.addr[0].lat, result.addr[0].lng);'.PHP_EOL;
        }
        $mapHtml .= '       var mapOption = {'.PHP_EOL;
        if ($key_type == 'app'){
        $mapHtml .= '           center: new daum.maps.LatLng(result[0].y, result[0].x),'.PHP_EOL;
        }elseif ($key_type == 'api'){
        $mapHtml .= '           center: new daum.maps.LatLng(result.addr[0].lat, result.addr[0].lng),'.PHP_EOL;
        }
        $mapHtml .= '           level: '.$vars['level'].''.PHP_EOL;
        $mapHtml .= '       };'.PHP_EOL;
        $mapHtml .= '       var map    = new daum.maps.Map(mapContainer, mapOption);'.PHP_EOL;
        $mapHtml .= '       var marker = new daum.maps.Marker({'.PHP_EOL;
        $mapHtml .= '           map: map,'.PHP_EOL;
        $mapHtml .= '           position: coords'.PHP_EOL;
        $mapHtml .= '       });'.PHP_EOL;
        $mapHtml .= '       var infowindow = new daum.maps.InfoWindow({'.PHP_EOL;
        $mapHtml .= '           content: \''.$marker.'\''.PHP_EOL;
        $mapHtml .= '       });'.PHP_EOL;
        $mapHtml .= '       infowindow.open(map, marker);'.PHP_EOL;
        if (empty($useDirect) || $useDirect == false) {
        $mapHtml .= '       jQuery(\'.toggleType\').on(\'click\', function() {'.PHP_EOL;
        $mapHtml .= '           var roadmapControl = jQuery(\'#btnRoadmap\');'.PHP_EOL;
        $mapHtml .= '           var skyviewControl = jQuery(\'#btnSkyview\');'.PHP_EOL;
        $mapHtml .= '           var $maptype = jQuery(this).data(\'maptype\');'.PHP_EOL;
        $mapHtml .= '           if ($maptype === \'roadmap\') {'.PHP_EOL;
        $mapHtml .= '               map.setMapTypeId(daum.maps.MapTypeId.ROADMAP);    '.PHP_EOL;
        $mapHtml .= '               roadmapControl.removeClass(\'btn\').addClass(\'selected_btn\');'.PHP_EOL;
        $mapHtml .= '               skyviewControl.removeClass(\'selected_btn\').addClass(\'btn\');'.PHP_EOL;
        $mapHtml .= '               jQuery(\'.custom_typecontrol\').data(\'type\');'.PHP_EOL;
        $mapHtml .= '           } else {'.PHP_EOL;
        $mapHtml .= '               map.setMapTypeId(daum.maps.MapTypeId.HYBRID);    '.PHP_EOL;
        $mapHtml .= '               roadmapControl.removeClass(\'selected_btn\').addClass(\'btn\');'.PHP_EOL;
        $mapHtml .= '               skyviewControl.removeClass(\'btn\').addClass(\'selected_btn\');'.PHP_EOL;
        $mapHtml .= '           }'.PHP_EOL;
        $mapHtml .= '       });'.PHP_EOL;
        $mapHtml .= '       jQuery(\'.zoomIn\').on(\'click\', function() {'.PHP_EOL;
        $mapHtml .= '          map.setLevel(map.getLevel() - 1);'.PHP_EOL;
        $mapHtml .= '       });'.PHP_EOL;
        $mapHtml .= '       jQuery(\'.zoomOut\').on(\'click\', function() {'.PHP_EOL;
        $mapHtml .= '           map.setLevel(map.getLevel() + 1);'.PHP_EOL;
        $mapHtml .= '       });'.PHP_EOL;
        $mapHtml .= '   }'.PHP_EOL;
        }
        /*if ($vars['showerror'] == 'y') {
        $mapHtml .= '       } else {'.PHP_EOL;
        $mapHtml .= '           alert(\''.__('Daum Map Error', 'bbsefranchise').' : '.__('Address convert error', 'bbsefranchise').'\');'.PHP_EOL;
        } */
        if ($key_type == 'app'){
        $mapHtml .= '   var callbackWrap = function(result, status) {'.PHP_EOL;
        $mapHtml .= '       if (status === daum.maps.services.Status.OK) {'.PHP_EOL;
        $mapHtml .= '           callback(result, status)'.PHP_EOL;
        $mapHtml .= '       }'.PHP_EOL;
        $mapHtml .= '   }'.PHP_EOL;
        $mapHtml .= '   geocoder.addressSearch(\''.$str_addr.'\', callbackWrap);'.PHP_EOL;
        }elseif ($key_type == 'api'){
        $mapHtml .= '   geocoder.addr2coord(\''.$str_addr.'\', function(status, result) {'.PHP_EOL;
        $mapHtml .= '       if (status === daum.maps.services.Status.OK) {'.PHP_EOL;
        $mapHtml .= '           callback(result, status)'.PHP_EOL;
        $mapHtml .= '       }'.PHP_EOL;
        $mapHtml .= '   });'.PHP_EOL;
        }
        $mapHtml .= '   </script>'.PHP_EOL;
        $mapHtml .= '</div>'.PHP_EOL;

        //출력
        return $mapHtml;
    }

    /**
     * Find page with shortcode inserted.
     */
    public function where_page()
    {
        global $wpdb;

        $sCode  = 'bbse_franchise';
        $FINAL  = array();

        $query  = ' SELECT ID, post_content, post_title';
        $query .= ' FROM  `'.$wpdb->prefix.'posts` ';
        $query .= " WHERE `post_content` LIKE '%".$sCode."%' AND `post_status` = 'publish' AND `post_type` = 'page' ";
        $result = $wpdb->get_row($query);
        wp_reset_query();

        if ($result->ID && $result->post_content) {
            $M = array();
            $output = preg_match_all('/'.$sCode.'/i', $result->post_content, $M);
            if (isset($M) && $M[0][0] == $sCode) {
                $FINAL = $result;
            }
        }

        return $FINAL;
    }

    /**
     * Find page with shortcode inserted. alt.
     */
    public function find_page()
    {
        global $wpdb;

        $sCode  = 'bbse_franchise';
        $FINAL  = array();

        $query  = ' SELECT ID, post_content, post_title';
        $query .= ' FROM  `'.$wpdb->prefix.'posts` ';
        $query .= " WHERE `post_content` LIKE '%".$sCode."%' AND `post_status` = 'publish' AND `post_type` = 'page' ";
        $result = $wpdb->get_results($query);
        wp_reset_query();

        if ($result) {
            foreach ($result as $k=>$v) {
                $M      = array();
                $output = preg_match_all('/'.$sCode.'/i', $v->post_content, $M);
                if (isset($M) && $M[0][0] == $sCode) {
                    $FINAL[$k]['post_title'] = $v->post_title;
                    $FINAL[$k]['ID']         = $v->ID;
                }
            }
        }
        if (!empty($FINAL)) {
            return $FINAL;
        } else {
            return array();
        }
    }

    /**
     * Checking integer
     */
    public function isInteger($data)
    {
        return(ctype_digit(strval($data)));
    }
}