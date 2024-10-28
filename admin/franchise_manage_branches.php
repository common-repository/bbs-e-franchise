<?php
if( ! defined( 'ABSPATH' ) ) exit;

global $wpdb;
global $franchise;

//check config data
$prepare = NULL;
$prepare = $wpdb->prepare( "SELECT * FROM {$franchise->table_config} WHERE prefix='%s'",    array( $wpdb->prefix ) );
$vars    = $wpdb->get_var( $prepare );

$templates  = $franchise->templates();
$configs    = $franchise->configs();
$categories = explode(',', $franchise->category);

//do first clean
foreach($_GET as $key => $value){
    if($value){
        $$key = htmlentities(sanitize_text_field(trim($value)), ENT_QUOTES | ENT_IGNORE, "UTF-8");
    }
}
unset($_GET);

if(isset($uid) && $uid && is_numeric($uid)){
    //get config data
    $prepare = NULL;
    $prepare = $wpdb->prepare( "SELECT * FROM {$configs['table_list']} WHERE uid=%d AND prefix='%s'",   array( $uid, $wpdb->prefix ) );
    $row     = $wpdb->get_row( $prepare );

    $title[0] = __('Modifying the branch', 'bbsefranchise');
    $title[1] = __('Modify the branch settings.', 'bbsefranchise');
}else{
    $title[0] = __('Add branch', 'bbsefranchise');
    $title[1] = __('Add a new branch.', 'bbsefranchise');
}
?>
<div class="wrap">
    <h1 id="add-new-user"><?php echo $title[0]?></h1>
    <div id="ajax-response"></div>
    <p><?php echo $title[1]?></p>

    <?php if($vars){?>
    <form method="post" name="franchise" id="franchise">
    <?php wp_nonce_field( 'bbse_franchise_branch_save', '_bbse_franchise_nonce' ); ?>
        <input type="hidden" name="action" id="action" value="bbse_franchise_branch_save">
        <?php if(isset($uid) && is_numeric($uid)){?>
        <input name="uid" type="hidden" value="<?php echo esc_attr($uid)?>">
        <input name="mode" type="hidden" value="edit">
        <?php }else{?>
        <input name="mode" type="hidden" value="add">
        <?php }?>
        <table class="form-table">
        <tbody>
        <tr class="form-field">
            <th scope="row"><label for="branchname"><?php echo __('Hide from list', 'bbsefranchise')?></label></th>
            <td><label><input name="hide" type="checkbox" id="hide" value="Y" <?php echo (!empty($row->hide) && $row->hide == 'Y') ? 'checked' : ''?>><?php echo __('If checked, hide from user page list.', 'bbsefranchise')?></label></td>
        </tr>
        <tr class="form-field">
            <th scope="row"><label for="category"><?php echo __('Category', 'bbsefranchise')?> <span class="description"></span></label></th>
            <td>
                <select name="category" id="category">
                <option value="" <?php echo empty($row->category) ? 'selected' : ''?>>::: <?php echo __('Select category', 'bbsefranchise')?> :::</option>
                <?php foreach($categories as $v){?>
                <option value="<?php echo esc_attr($v)?>" <?php echo (!empty($row->category) && $row->category == $v) ? 'selected' : ''?> ><?php echo $v?></option>
                <?php }?>
                </select>
            </td>
        </tr>

        <tr class="form-field">
            <th scope="row"><label for="branchname"><?php echo __('Name of branch', 'bbsefranchise')?> <span class="description">(<?php echo __('Required', 'bbsefranchise')?>)</span></label></th>
            <td><input name="branchname" type="text" id="branchname" value="<?php echo !empty($row->branchname) ? stripslashes(esc_attr($row->branchname)) : ''?>" required maxlength="254"></td>
        </tr>
        <tr class="form-field">
            <th scope="row"><label for="address1"><?php echo __('Address', 'bbsefranchise')?> <span class="description">(<?php echo __('Required', 'bbsefranchise')?>)</span></label></th>
            <td>
                <input name="address1" type="text" id="address1" value="<?php echo !empty($row->address1) ? stripslashes(esc_attr($row->address1)) : ''?>" readonly placeholder="<?php echo __('Click!', 'bbsefranchise')?>">
                <input name="address2" type="text" id="address2" value="<?php echo !empty($row->address2) ? stripslashes(esc_attr($row->address2)) : ''?>">
                <input name="mapaddress"   type="hidden" value="<?php echo !empty($row->mapaddress)   ? stripslashes(esc_attr($row->mapaddress))   : ''?>">
                <input name="addresstype"  type="hidden" value="<?php echo !empty($row->addresstype)  ? stripslashes(esc_attr($row->addresstype))  : ''?>">
                <input name="languagetype" type="hidden" value="<?php echo !empty($row->languagetype) ? stripslashes(esc_attr($row->languagetype)) : ''?>">
                <input name="zipcode"      type="hidden" value="<?php echo !empty($row->zipcode)      ? stripslashes(esc_attr($row->zipcode))      : ''?>">
                <input name="sido"         type="hidden" value="<?php echo !empty($row->sido)         ? stripslashes(esc_attr($row->sido))         : ''?>">
                <input name="sigungu"      type="hidden" value="<?php echo !empty($row->sigungu)      ? stripslashes(esc_attr($row->sigungu))      : ''?>">
                <input name="bname"        type="hidden" value="<?php echo !empty($row->bname)        ? stripslashes(esc_attr($row->bname))        : ''?>">

            </td>
        </tr>
        <tr class="form-field">
            <th scope="row"><label for="phone"><?php echo __('Phone number', 'bbsefranchise')?></label></th>
            <td><input name="phone" type="text" id="phone" value="<?php echo !empty($row->phone) ? stripslashes(esc_attr($row->phone)) : ''?>" required maxlength="14"></td>
        </tr>
        <tr class="form-field">
            <th scope="row"><label for="mobile"><?php echo __('Mobile phone number', 'bbsefranchise')?></label></th>
            <td><input name="mobile" type="text" id="mobile" value="<?php echo !empty($row->mobile) ? stripslashes(esc_attr($row->mobile)) : ''?>" maxlength="14"></td>
        </tr>
        <tr class="form-field">
            <th scope="row"><label for="fax"><?php echo __('Faximile number', 'bbsefranchise')?></label></th>
            <td><input name="fax" type="text" id="fax" value="<?php echo !empty($row->fax) ? stripslashes(esc_attr($row->fax)) : ''?>" maxlength="14"></td>
        </tr>
        <tr class="form-field">
            <th scope="row"><label for="email"><?php echo __('eMail address', 'bbsefranchise')?></label></th>
            <td><input name="email" type="email" id="email" value="<?php echo !empty($row->email) ? stripslashes(esc_attr($row->email)) : ''?>" maxlength="254"></td>
        </tr>
        <tr class="form-field">
            <th scope="row"><label for="tag"><?php echo __('ETC.', 'bbsefranchise')?></label></th>
            <td><input name="tag" type="text" id="tag" value="<?php echo !empty($row->tag) ? stripslashes(esc_attr($row->tag)) : ''?>" maxlength="254"></td>
        </tr>

        <tr class="form-field">
            <td colspan="2">
                <?php
                $quicktags_settings = array('buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,close');
                $editor_args = array(
                    'media_buttons' => true,
                    'textarea_name' => 'memo',
                    'textarea_rows' => 20,
                    'quicktags'     => $quicktags_settings,
                );
                $memoT = (isset($row->memo) && $row->memo) ? $row->memo : '';
                wp_editor(stripslashes($memoT), 'memo', $editor_args);
                ?>
            </td>
        </tr>
        </tbody>
        </table>
        <p class="submit">
            <a href="<?php echo esc_url('?page=bbse_franchise')?>" class="button button-primary"><?php echo __('Back to list', 'bbsefranchise')?></a>
            <button type="submit" name="addbranch" id="addbranchsub" class="button button-primary sendit"><?php echo __('Save settings', 'bbsefranchise')?></button>
        </p>
    </form>
</div>
<div class="noticeBox"><?php echo __('* If the plug-in is working normally after the update, try <b> disable and re-enable </b> the plug-in.', 'bbsefranchise')?></div>
<div class="alertLayer"></div>
<script>
function openDaumPostcode(){
    new daum.Postcode({
        oncomplete: function(data){
            /*
            // -------------- debug reference information -------------- //
            //http://postcode.map.daum.net/guide#upgrade
            console.log('\n\nSTART');
            console.log('zonecode : '+data.zonecode); //신규 우편번호
            console.log('postcode : '+data.postcode); //XXX-YYY
            console.log('postcode1 : '+data.postcode1); //XXX
            console.log('postcode2 : '+data.postcode2); //YYY
            console.log('address : '+data.address); //도로명 주소인 경우 매핑된 지번 주소와 건물명은 address 값에 포함되지 않습니다.
            console.log('addressEnglish : '+data.addressEnglish); //영문주소
            console.log('address1 : '+data.address1); //제공안함
            console.log('address2 : '+data.address2); //제공안함
            console.log('relatedAddress : '+data.relatedAddress); //제공안함
            console.log('addressType : '+data.addressType); // 주소종류 R:도로명,J:지번
            console.log('userSelectedType : '+data.userSelectedType); //사용자 선택 주소종류 R:도로명,J:지번
            console.log('userLanguageType : '+data.userLanguageType); // 사용자 선택 언어 K:국문,J:영문
            console.log('roadAddress : '+data.roadAddress); //도로명주소
            console.log('roadAddressEnglish : '+data.roadAddressEnglish); //도로명주소 영문
            console.log('jibunAddress : '+data.jibunAddress); //지번주소
            console.log('jibunAddressEnglish : '+data.jibunAddressEnglish); //지번주소 영문
            console.log('autoRoadAddress : '+data.autoRoadAddress); //자동선택 도로명주소
            console.log('autoRoadAddressEnglish : '+data.autoRoadAddressEnglish); //자동선택 도로명주소 영문
            console.log('autoJibunAddress : '+data.autoJibunAddress); //자동선택 지번주소
            console.log('autoJibunAddressEnglish : '+data.autoJibunAddressEnglish); //자동선택 지번주소 영문
            console.log('buildingCode : '+data.buildingCode); //건물 관리코드
            console.log('buildingName : '+data.buildingName); //건물명
            console.log('apartment : '+data.apartment); //공동주소여부 Y/N
            console.log('sido : '+data.sido); //도시 이름
            console.log('sigungu : '+data.sigungu); //시군구 이름
            console.log('bcode : '+data.bcode); //법정동/법정리코드
            console.log('bname : '+data.bname); //법정동/법정리 이름
            console.log('hname : '+data.hname); //행정동 이름
            console.log('query : '+data.query); //사용자가 입력한 검색어
            console.log('postcodeSeq : '+data.postcodeSeq); //우편번호 일련번호
            console.log('END\n\n');
            */
            //Korean
            if(data.userLanguageType == 'K'){
                switch (data.userSelectedType){
                    case 'R': jQuery('input[name=address1]').val( data.address );      break;
                    case 'J': jQuery('input[name=address1]').val( data.jibunAddress ); break;
                }
            //English
            } else if(data.userLanguageType == 'E'){
                switch (data.userSelectedType){
                    case 'R': jQuery('input[name=address1]').val( data.roadAddressEnglish );  break;
                    case 'J': jQuery('input[name=address1]').val( data.jibunAddressEnglish ); break;
                }
            }

            jQuery('input[name=addresstype]').val( data.userSelectedType );
            jQuery('input[name=languagetype]').val( data.userLanguageType );
            jQuery('input[name=sido]').val( data.sido );
            jQuery('input[name=sigungu]').val( data.sigungu );
            jQuery('input[name=bname]').val( data.bname );
            jQuery('input[name=mapaddress]').val( data.address );

            switch (data.userSelectedType){
                case 'R': jQuery('input[name=zipcode]').val( data.zonecode ); break;
                case 'J': jQuery('input[name=zipcode]').val( data.postcode ); break;
            }
            jQuery('input[name=address2]').attr( 'placeholder', '<?php echo __('Please enter the remaining address.', 'bbsefranchise')?>' );
        }
    }).open();
}

jQuery(document)

//Save settings
.on('click', '.sendit', function(){
    if(confirm("<?php echo __('Do you want to save the settings?', 'bbsefranchise')?>")){
        tinyMCE.triggerSave();
        var $resultStr = "<?php echo __('Save failure', 'bbsefranchise')?>";
        var $addClass  = 'thegreen';
        var $reload    = false;
        jQuery('.alertLayer').removeClass('thegreen thered');
        jQuery.post(
            ajaxurl,
            {
                '_bbse_franchise_nonce' : jQuery('input[name=_bbse_franchise_nonce]').val(),
                '_wp_http_referer'      : jQuery('input[name=_wp_http_referer]').val(),

                'action'       : jQuery('input[name=action]').val(),
                'mode'         : jQuery('input[name=mode]').val(),
                'uid'          : jQuery('input[name=uid]').val(),
                'hide'         : jQuery('input[name=hide]:checked').val(),
                'category'     : jQuery('select[name=category] option:selected').val(),
                'branchname'   : jQuery('input[name=branchname]').val(),
                'addresstype'  : jQuery('input[name=addresstype]').val(),
                'languagetype' : jQuery('input[name=languagetype]').val(),
                'zipcode'      : jQuery('input[name=zipcode]').val(),
                'address1'     : jQuery('input[name=address1]').val(),
                'address2'     : jQuery('input[name=address2]').val(),
                'mapaddress'   : jQuery('input[name=mapaddress]').val(),
                'sido'         : jQuery('input[name=sido]').val(),
                'sigungu'      : jQuery('input[name=sigungu]').val(),
                'bname'        : jQuery('input[name=bname]').val(),
                'phone'        : jQuery('input[name=phone]').val(),
                'mobile'       : jQuery('input[name=mobile]').val(),
                'fax'          : jQuery('input[name=fax]').val(),
                'email'        : jQuery('input[name=email]').val(),
                'tag'          : jQuery('input[name=tag]').val(),
                'memo'         : jQuery('textarea[name=memo]').val(),
            },
            //VALIDATE RESULT
            function(response){
                if(jQuery.trim(response) == 'error^noauth'){
                    $resultStr = "<?php echo __('No authority', 'bbsefranchise')?>";
                    $addClass  = 'thered';
                }
                if(jQuery.trim(response) == 'empty^category'){
                    alert("<?php echo __('You haven\'t set any categories.', 'bbsefranchise')?>");
                    jQuery('#category').focus();
                }
                if(jQuery.trim(response) == 'empty^branchname'){
                    alert("<?php echo __('You haven\'t set a branch name.', 'bbsefranchise')?>");
                    jQuery('#branchname').focus();
                }
                if(jQuery.trim(response) == 'empty^address1'){
                    alert("<?php echo __('You haven\'t set an address.', 'bbsefranchise')?>");
                    jQuery('#address1').focus();
                }
                if(jQuery.trim(response) == 'empty^address2'){
                    alert("<?php echo __('You haven\'t set an address.', 'bbsefranchise')?>");
                    jQuery('#address2').focus();
                }
                if(jQuery.trim(response) == 'error^phone'){
                    alert("<?php echo __('Invalid phone number.', 'bbsefranchise')?>");
                    jQuery('#phone').focus();
                }
                if(jQuery.trim(response) == 'empty^phone'){
                    alert("<?php echo __('You haven\'t set a phone number.', 'bbsefranchise')?>");
                }
                if(jQuery.trim(response) == 'error^mobile'){
                    alert("<?php echo __('Invalid mobile phone number.', 'bbsefranchise')?>");
                    jQuery('#mobile').focus();
                }
                if(jQuery.trim(response) == 'error^fax'){
                    alert("<?php echo __('Invalid fax number.', 'bbsefranchise')?>");
                    jQuery('#fax').focus();
                }
                if(jQuery.trim(response) == 'error^email'){
                    alert("<?php echo __('Invalid email address.', 'bbsefranchise')?>");
                    jQuery('#email').focus();
                }
                if(jQuery.trim(response) == 'empty^memo'){
                    alert("<?php echo __('You haven\'t entered any content.', 'bbsefranchise')?>");
                }
                if(jQuery.trim(response) == 'empty^daumapikey'){
                    alert("<?php echo __('You haven\'t set up the Daum map API.', 'bbsefranchise')?>");
                    jQuery('#daumapikey').focus();
                }
                if(jQuery.trim(response) == 'ok'){
                    $resultStr = "<?php echo __('Save success', 'bbsefranchise')?>";
                    $reload    = true;
                }else if(jQuery.trim(response) == 'noupdated'){
                    $resultStr = "<?php echo __('Does not save - same data', 'bbsefranchise')?>";
                }

                jQuery('.alertLayer').addClass($addClass).text($resultStr).show('fast').delay(500).fadeOut(1000,function(){
                    if($reload == true){
                        window.location.reload(true);
                    }
                });
            }
        );
    }
    return false;
})

.on('click', 'input[name=address1]', function(){
    openDaumPostcode();
})
</script>
<?php }else{?>
<p class="description" style="font-size:2em;"><a href="<?php echo home_url()?>/wp-admin/admin.php?page=manage_plugin_page"><?php echo __('Plug-in preferences are required.', 'bbsefranchise')?></a></p>
<?php }