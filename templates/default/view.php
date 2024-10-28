<?php
if ( ! defined( 'ABSPATH' ) ) exit;

global $franchise;
$template_image_url =  esc_url(BBSE_FRANCHISE_TEMPLATES_URL.$franchise->template);

foreach($_GET as $key => $value){
    $getconvertkey = 'gq_'.$key;
    if($value){
        $$getconvertkey = htmlentities(sanitize_text_field(trim($value)), ENT_QUOTES | ENT_IGNORE, "UTF-8");
    }
}
?>
<div class="map_view_wrap">
    <div class="map_view">
    <?php
    if ($franchise->useapi != 'none'){
        $arg = array('address'=>$DATA->mapaddress, 'branchname'=>$DATA->branchname );

        switch ($franchise->useapi){
            case 'daum':
                echo $franchise->draw_daum_map($arg);
            break;

            default:
            case 'naver':
                echo $franchise->draw_naver_map($arg);
            break;
        }
    }
    ?>
    </div>

    <table class="map_detail_table">
    <caption class="hidden"><?php echo __('Details', 'bbsefranchise')?></caption>
    <colgroup>
        <col style="width:20%">
        <col>
    </colgroup>
    <thead>
    <tr>
        <th scope="row"><?php echo __('Name of branch', 'bbsefranchise')?></th>
        <td><?php echo sanitize_text_field(stripslashes($DATA->branchname))?></td>
    </tr>
    <tr>
        <th scope="row"><?php echo __('Address', 'bbsefranchise')?></th>
        <td>
        <?php
        if (get_locale() == 'ko_KR'){
            echo $DATA->address1." ".sanitize_text_field(stripslashes($DATA->address2));
        }else{
            echo sanitize_text_field(stripslashes($DATA->address2)).", ".$DATA->address1;
        }
        ?>
        </td>
    </tr>
    <?php if ($DATA->phone || $DATA->mobile || $DATA->fax || $DATA->email){?>
    <tr>
        <th scope="row"><?php echo __('Contacts', 'bbsefranchise')?></th>
        <td class="phoneList">
        <?php
        if ($DATA->phone)  $contacts[] = '<span>'.__('Phone', 'bbsefranchise').' : '.$DATA->phone.'</span>';
        if ($DATA->mobile) $contacts[] = '<span>'.__('Mobile', 'bbsefranchise').' : '.sanitize_text_field($DATA->mobile).'</span>';
        if ($DATA->fax)    $contacts[] = '<span>'.__('Fax.', 'bbsefranchise').' : '.sanitize_text_field($DATA->fax).'</span>';
        if ($DATA->email)  $contacts[] = '<span>'.__('email', 'bbsefranchise').' : '.sanitize_email($DATA->email).'</span>';
        echo implode('<br>', $contacts);
        ?>
        </td>
    </tr>
    <?php
    }
    if ($DATA->tag){
    ?>
    <tr>
        <th scope="row"><?php echo __('ETC.', 'bbsefranchise')?></th>
        <td><?php echo sanitize_text_field(stripslashes($DATA->tag))?></td>
    </tr>
    <?php }?>
    </thead>
    <?php if( trim($DATA->memo) ){?>
    <tbody>
    <tr>
        <td colspan="2">
            <div class="td_content">
                <?php echo force_balance_tags(apply_filters( 'the_content', stripslashes(trim($DATA->memo))));?>
            </div>
        </td>
    </tr>
    </tbody>
    <?php }?>
    <tfoot>
    <tr>
        <td colspan="2">
        <?php
        $link = array();
        if ( isset($gq_paged) && !empty($gq_paged) && is_numeric($gq_paged) ){
            $link['paged'] = $gq_paged;
        }

        if ( isset($gq_sido) && !empty($gq_sido) ){
            $link['sido'] = $gq_sido;
        }
        if ( isset($gq_category) && !empty($gq_category) ){
            $link['category'] = $gq_category;
        }

        if ( isset($gq_keyword) && !empty($gq_keyword) ){
            $link['keyword'] = $gq_keyword;
        }
        ?>
            <a href="<?php echo esc_url(get_the_permalink().'?'.http_build_query($link))?>" title="<?php echo __('Back to list', 'bbsefranchise')?>" class="list_btn"><?php echo __('Back to list', 'bbsefranchise')?></a>
        </td>
    </tr>
    </tfoot>
    </table>
</div>