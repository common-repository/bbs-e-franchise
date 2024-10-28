<?php
if( ! defined( 'ABSPATH' ) ) exit;

global $franchise;
global $current_user;
$templates = $franchise->templates();
$configs   = $franchise->configs();
$mypages   = $franchise->find_page();
?>
<div class="wrap">
    <h1 id="add-new-user"><?php echo __('Preferences', 'bbsefranchise')?></h1>
    <div id="ajax-response"></div>
    <p><?php echo __('Set up the plug-in.', 'bbsefranchise')?></p>
    <form method="post" name="franchise" id="franchise">
    <?php wp_nonce_field( 'bbse_franchise_config_save', '_bbse_franchise_nonce' ); ?>
      <input type="hidden" name="action" id="action" value="bbse_franchise_config_save">
        <input type="hidden" name="mode" value="edit">
        <table class="form-table">
        <tbody>
        <tr class="form-field ">
            <th scope="row"><?php echo __('shortcode', 'bbsefranchise')?></th>
            <td>
                <input type="text" name="copytoclipboard" id="copytoclipboard" value="[bbse_franchise]" class="shortcode" readonly>
                <span class="copytoclipboard button"><?php echo __('Copy to clipboard', 'bbsefranchise')?>  </span>
                <p class="description">(<?php echo __('Copy the shortcode and insert it into the desired "page".', 'bbsefranchise')?>)</p>
            </td>
        </tr>

        <tr class="form-field ">
            <th scope="row"><label for="pageid"><?php echo __('page ID where the shortcode was inserted', 'bbsefranchise')?></label></th>
            <td>
                <select name="pageid" id="pageid">
                    <option value="">:::<?php echo __('Select shortcode inserted page', 'bbsefranchise')?>:::</option>
                    <?php
                    foreach ($mypages as $k=>$v){
                        $selected = '';
                        if( !empty($configs['pageid']) && $configs['pageid']== $v['ID']){
                            $selected = 'selected';
                            $postid   = $v['ID'];
                        }
                    ?>
                    <option value="<?php echo esc_attr($v['ID'])?>" data-link="<?php echo esc_url(get_permalink($v['ID']))?>" <?php echo $selected?> ><?php echo $v['post_title']?></option>
                    <?php }?>
                </select>
                <?php if($configs['pageid']){?>
                &nbsp;<span class="view-page-container"><a href="<?php echo esc_url(get_permalink($postid))?>" target="_blank" title="<?php echo __('View page', 'bbsefranchise')?>" class="button"><?php echo __('View page', 'bbsefranchise')?></a></span>
                <?php }?>
                <p class="description">(<?php echo __('Select the page where the shortcode is inserted.', 'bbsefranchise')?>)</p>
            </td>
        </tr>

        <tr class="form-field ">
            <th scope="row"><label for="listtitle"><?php echo __('List title', 'bbsefranchise')?></label></th>
            <td><input name="listtitle" type="text" id="listtitle" value="<?php echo !empty($configs['listtitle']) ? stripslashes(esc_attr($configs['listtitle'])) : ''?>" maxlength="254" placeholder="<?php echo __('The title to be displayed in the search part of the list page.', 'bbsefranchise')?>">
            <p class="description">(<?php echo __('The title to be displayed in the search part of the list page.', 'bbsefranchise')?>)</p>
            </td>
        </tr>

        <tr class="form-field ">
            <th scope="row"><label for="listcomment"><?php echo __('List text', 'bbsefranchise')?></label></th>
            <td><input name="listcomment" type="text" id="listcomment" value="<?php echo !empty($configs['listcomment']) ? stripslashes(esc_attr($configs['listcomment'])) : ''?>" maxlength="254" placeholder="<?php echo __('It is a simple descriptive phrase to be displayed in the search part of the list page.', 'bbsefranchise')?>">
            <p class="description">(<?php echo __('It is a simple descriptive phrase to be displayed in the search part of the list page.', 'bbsefranchise')?>)</p>
            </td>
        </tr>

        <tr class="form-field ">
            <th scope="row"><label for="usemapnavi"><?php echo __('Map navigation from the list', 'bbsefranchise')?></label></th>
            <td>
                <label><input name="usemapnavi" id="usemapnavi" type="checkbox" value="y" <?php echo ($configs['usemapnavi']=="y" || $configs['usemapnavi']=="") ? "checked":''?>>  <?php echo __('Use map navigation from the list.', 'bbsefranchise')?></label>
            </td>
        </tr>

        <tr class="form-field ">
            <th scope="row"><label for="template"><?php echo __('Use skin template', 'bbsefranchise')?><span class="description">(<?php echo __('Required', 'bbsefranchise')?>)</span></label></th>
            <td>
                <select name="template" id="template" required>
                    <option value="" <?php echo empty($configs['template']) ? 'selected' : ''?>>:::<?php echo __('Select skin template', 'bbsefranchise')?>:::</option>
                    <?php foreach($templates as $v){?>
                    <option value="<?php echo esc_attr($v)?>" <?php echo (!empty($configs['template']) && $configs['template'] == $v)?'selected':''?> ><?php echo $v?></option>
                    <?php }?>
                </select>
            </td>
        </tr>

        <tr class="form-field ">
            <th scope="row"><label for="category"><?php echo __('Category', 'bbsefranchise')?><!--  <span class="description">(필수)</span> --></label></th>
            <td><input name="category" type="text" id="category" value="<?php echo !empty($configs['category']) ? stripslashes(esc_attr($configs['category'])) : ''?>" maxlength="254" placeholder=" , 로 구분하세요.">
            <p class="description">(<?php echo __('Enter \',\' separated by. Only one duplicate entered value is stored.', 'bbsefranchise')?>)</p>
            </td>
        </tr>

        <tr class="form-field ">
            <th scope="row"><label for="perpage"><?php echo __('Number of listings per page', 'bbsefranchise')?></label></th>
            <td><input name="perpage" type="number" id="perpage" value="<?php echo !empty($configs['perpage']) ? esc_attr($configs['perpage']) : '15'?>" min="1" max="100" style="width:50px;">
            <p class="description">(<?php echo __('If it is not set at the initial setting, WordPress setting value is used.', 'bbsefranchise')?>)</p>
            </td>
        </tr>

        <tr class="form-field ">
            <th scope="row"><label for="useapi"><?php echo __('Map API you want to use', 'bbsefranchise')?></label></th>
            <td>
                <label> <input type="radio" name="useapi" value="naver" <?php echo (!empty($configs['useapi']) && $configs['useapi'] == 'naver') ? 'checked' : ''?>> <?php echo __('Naver Map', 'bbsefranchise')?> </label>&nbsp; &nbsp;
                <label> <input type="radio" name="useapi" value="daum" <?php echo (!empty($configs['useapi']) && $configs['useapi'] == 'daum') ? 'checked' : ''?>> <?php echo __('Daum Map', 'bbsefranchise')?> </label>&nbsp; &nbsp;
                <label> <input type="radio" name="useapi" id="useapi" value="none" <?php echo (empty($configs['useapi']) || $configs['useapi'] == 'none') ? 'checked' : ''?>> <?php echo __('OFF', 'bbsefranchise')?> </label>&nbsp; &nbsp;
                <p class="description">(<?php echo __('If not selected at first time, it will be set to \'OFF\'.', 'bbsefranchise')?>)</p>
            </td>
        </tr>

        <tr class="form-field navermap <?php echo (!empty($configs['useapi']) && $configs['useapi'] == 'naver')?'ondisplay':'offdisplay'?>">
            <th scope="row"><label for="naverapiid"><?php echo __('Naver Map API', 'bbsefranchise')?></label></th>
            <td>
                <label class="note"><input name="naverapiid" type="text" id="naverapiid" value="<?php echo !empty($configs['naverapiid']) ? stripslashes(esc_attr($configs['naverapiid'])) : ''?>" maxlength="60" placeholder="Client ID" style="width:30%;" title="앱 클라이언트 아이디입니다."></label><br>
                <label class="note"><input name="naverapisecret" type="text" id="naverapisecret" value="<?php echo !empty($configs['naverapisecret']) ? stripslashes(esc_attr($configs['naverapisecret'])) : ''?>" maxlength="60"  placeholder="Client SECRET"  style="width:30%;" title="앱 클라이언트 시크리트티입니다."></label>
                <p class="description">- <?php echo __('You need to register your app to use the Map API.', 'bbsefranchise')?></p>
                <p class="description">- <a href="https://developers.naver.com/register" class="infoBtn" target="_blank"><?php echo __('In the application registration, set \'Non login open API\' and \'Web service URL\' to get ID and Secret.', 'bbsefranchise')?></a></p>
                <p class="description">- <em><?php echo __('Please be sure to check the \'Non-Login Open API\' - \'Map API\' in the \'API Permission Management\' tab after issuance.', 'bbsefranchise')?></em></p>
            </td>
        </tr>

        <tr class="form-field daummap <?php echo (!empty($configs['useapi']) && ($configs['useapi'] == 'daum' || $configs['useapi'] == 'kakao'))?'ondisplay':'offdisplay'?>">
            <th scope="row"><label for="daumapikey"><?php echo __('Daum Map API', 'bbsefranchise')?></label></th>
            <td>

                <label class="note"><input name="kakaoappkey" type="text" id="kakaoappkey" value="<?php echo !empty($configs['kakaoappkey']) ? stripslashes(esc_attr($configs['kakaoappkey'])) : ''?>" maxlength="60" placeholder="APP Key" style="width:30%;" title="APP Key"></label>
                <p class="description">- <?php echo __('To use the Map API, you\'ll need to generate a key after creating your app.', 'bbsefranchise')?><a href="https://developers.kakao.com/" class="infoBtn" target="_blank"><?php echo __('Get a key', 'bbsefranchise')?></a></p>
                <p class="description">- <?php echo __('Developer registration and app creation', 'bbsefranchise')?></p>
                <p class="description">- <?php echo __('Add web platform: Select application - [Settings] - [General] - [Add platform] - Select web and add', 'bbsefranchise')?></p>
                <p class="description">- <?php echo __('Site domain registration: Select [Web] platform and register [Site domain]. (For example, http: // localhost: 8080)', 'bbsefranchise')?></p>
                <p class="description">- <?php echo __('Use the [JavaScript Key] at the top of the page as the appkey for the Maps API.', 'bbsefranchise')?></p>

                <br><br>

                <label class="note"><input name="daumapikey" type="text" id="daumapikey" value="<?php echo !empty($configs['daumapikey']) ? stripslashes(esc_attr($configs['daumapikey'])) : ''?>" maxlength="60" placeholder="API Key" style="width:30%;" title="API Key"></label>
                <p class="description">- <?php echo __('To use the Map API, you\'ll need to generate a key after creating your app.', 'bbsefranchise')?><a href="http://developers.daum.net/console" class="infoBtn" target="_blank"><?php echo __('Get a key', 'bbsefranchise')?></a></p>
                <p class="description">- <?php echo __('After creating the app, get the key for \'Web browser\' or \'All platforms\' in the \'REST / JS\' item of the app.', 'bbsefranchise')?></p>
            </td>
        </tr>

        </tbody>
        </table>
        <p class="submit"><button type="submit" name="addbranch" id="addbranchsub" class="button button-primary sendit"><?php echo __('Save settings', 'bbsefranchise')?></button></p>
    </form>
</div>
<div class="noticeBox"><?php echo __('* If the plug-in is working normally after the update, try <b> disable and re-enable </b> the plug-in.', 'bbsefranchise')?></div>

<div class="alertLayer"></div>

<script>
jQuery(document)

//Copy shortcode to clipboard
.on('click', '.copytoclipboard', function(){
    var target = document.getElementById('copytoclipboard');
    target.focus();
    target.setSelectionRange(0, target.value.length);
    try {
        var successful = document.execCommand('copy');
        var msg = successful ? "<?php echo __('successful', 'bbsefranchise')?>" : "<?php echo __('unsuccessful', 'bbsefranchise')?>";
        alert("<?php echo __('Copying text command was', 'bbsefranchise')?> "+msg);
    } catch (err){
        alert("<?php echo __('Unable to copy', 'bbsefranchise')?>");
    }
})

//page id select
.on('change', 'select[name=pageid]', function(){
    var $link = jQuery(this).find('option:selected').data('link');
    jQuery('.view-page-container a').attr('href', $link);
    return false;
})

//Save settings
.on('click', '.sendit', function(){
    if(confirm("<?php echo __('Do you want to save the settings?', 'bbsefranchise')?>")){
        var $resultStr = "<?php echo __('Save failure', 'bbsefranchise')?>";
        var $addClass  = 'thegreen';
        jQuery('.alertLayer').removeClass('thegreen thered');
        jQuery.post(
            ajaxurl,
            {
                '_bbse_franchise_nonce' : jQuery('input[name=_bbse_franchise_nonce]').val(),
                '_wp_http_referer'      : jQuery('input[name=_wp_http_referer]').val(),

                'action'         : jQuery('input[name=action]').val(),
                'pageid'         : jQuery('select[name=pageid] option:selected').val(),
                'listtitle'      : jQuery('input[name=listtitle]').val(),
                'listcomment'    : jQuery('input[name=listcomment]').val(),
                'usemapnavi'     : jQuery('input[name=usemapnavi]').is(":checked"),
                'template'       : jQuery('select[name=template] option:selected').val(),
                'category'       : jQuery('input[name=category]').val(),
                'perpage'        : jQuery('input[name=perpage]').val(),
                'useapi'         : jQuery('input[name=useapi]:checked').val(),
                'naverapiid'     : jQuery('input[name=naverapiid]').val(),
                'naverapisecret' : jQuery('input[name=naverapisecret]').val(),
                'kakaoappkey'    : jQuery('input[name=kakaoappkey]').val(),
                'daumapikey'     : jQuery('input[name=daumapikey]').val()
            },
            //VALIDATE RESULT
            function(response){
                if(jQuery.trim(response) == 'error^noauth'){
                    $resultStr = "<?php echo __('No authority', 'bbsefranchise')?>";
                    $addClass  = 'thered';
                }
                if(jQuery.trim(response) == 'empty^naverapiid'){
                    alert("<?php echo __('You haven\'t set up the Naver map API.', 'bbsefranchise')?>");
                    jQuery('#naverapiid').focus();
                }
                if(jQuery.trim(response) == 'empty^naverapisecret'){
                    alert("<?php echo __('You haven\'t set up the Naver map API.', 'bbsefranchise')?>");
                    jQuery('#naverapisecret').focus();
                }

                if(jQuery.trim(response) == 'empty^daumapikey'){
                    alert("<?php echo __('You haven\'t set up the Daum map API.', 'bbsefranchise')?>");
                    jQuery('#daumapikey').focus();
                }
                if(jQuery.trim(response) == 'ok'){
                    $resultStr = "<?php echo __('Save success', 'bbsefranchise')?>";
                }else if(jQuery.trim(response) == 'noupdated'){
                    $resultStr = "<?php echo __('Does not save - same data', 'bbsefranchise')?>";
                }

                jQuery('.alertLayer').addClass($addClass).text($resultStr).show('fast').delay(700).fadeOut(1500);
            }
        );
    }
    return false;
})

//MAP API select control
.on('change', 'input[name=useapi]', function(){
    switch ( jQuery(this).val() ){
        case 'naver' :
            jQuery('.navermap').removeClass('offdisplay').addClass('ondisplay');
            jQuery('.daummap').removeClass('ondisplay').addClass('offdisplay');
        break;

        case 'daum' :
            jQuery('.navermap').removeClass('ondisplay').addClass('offdisplay');
            jQuery('.daummap').removeClass('offdisplay').addClass('ondisplay');
        break;

        default :
            jQuery('.navermap').removeClass('ondisplay').addClass('offdisplay');
            jQuery('.daummap').removeClass('ondisplay').addClass('offdisplay');
        break
    }
});

</script>