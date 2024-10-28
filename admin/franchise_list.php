<?php
if( ! defined( 'ABSPATH' ) ) exit;

global $franchise, $wpdb;
$configs = $franchise->configs();

if($configs['pageid']){
    $viewLink = get_permalink($configs['pageid']);
}else{
    $pageInfo = $franchise->where_page();
    $viewLink = get_permalink($pageInfo->ID);
}

//do first clean
foreach($_GET as $key => $value){
    if($value){
        $$key = htmlentities(sanitize_text_field(trim($value)), ENT_QUOTES | ENT_IGNORE, "UTF-8");
        $cleaned_get_query[$key] = $$key;
    }
}
unset($_GET);

$andQuery['prefix'] = " prefix = '{$wpdb->prefix}' ";

if(isset($show) && $show == 'hide'){
    $andQuery['hide'] = " hide = 'Y' ";
}

if(!isset($show) || empty($show) || $show == 'show' ){
    $andQuery['hide'] = " hide = 'N' ";
}

$addAndQuery = ' ( '.implode(' AND ', $andQuery).' ) ';

if(isset($skeyword) && !empty($skeyword)){
    $orQuery = array();
    $orQuery[]  = " category LIKE '%{$skeyword}%' ";
    $orQuery[]  = " branchname LIKE '%{$skeyword}%' ";
    $orQuery[]  = " address1 LIKE '%{$skeyword}%' ";
    $orQuery[]  = " address2 LIKE '%{$skeyword}%' ";
    $orQuery[]  = " phone LIKE '%{$skeyword}%' ";
    $orQuery[]  = " mobile LIKE '%{$skeyword}%' ";
    $orQuery[]  = " email LIKE '%{$skeyword}%' ";
    $orQuery[]  = " fax LIKE '%{$skeyword}%' ";
    $orQuery[]  = " tag LIKE '%{$skeyword}%' ";
    $orQuery[]  = " memo LIKE '%{$skeyword}%' ";
    $orQuery[]  = " branchname LIKE '%{$skeyword}%' ";
    $addOrQuery =  ' AND ( '.implode(' OR ', $orQuery).' ) ';
}else{
    $addOrQuery = '';
}

//get data
$paged   = ( !empty($paged) ) ? $paged : 1;
$perpage = get_option('posts_per_page');
$total   = $wpdb->get_var( "SELECT count(*) AS total FROM {$configs['table_list']} WHERE  {$addAndQuery} {$addOrQuery}" );
$pages   = ceil($total/$perpage);

if($pages > 1 && $total > $perpage){
    $start    = ($paged - 1) * $perpage;
    $end      = $perpage ;
    $addQuery = " LIMIT {$start}, {$end} ";
}else{
    $addQuery = '';
}
$query  = "SELECT * FROM {$configs['table_list']} WHERE {$addAndQuery} {$addOrQuery} ORDER BY uid DESC {$addQuery}";
$result = $wpdb->get_results( $query );

//paging
$link       = html_entity_decode(get_pagenum_link());
$query_args = array();
$urlQuery   = explode( '?', $link );
if( isset( $urlQuery[1] ) ){
    wp_parse_str( $urlQuery[1], $query_args );
}
$base   = trailingslashit(remove_query_arg(array_keys($query_args),$link)).'%_%';
$paging = paginate_links( array(
    'current'   => $paged,
    'base'      => $base,
    'total'     => $pages,
    'format'    => '?paged=%#%',
    'mid_size'  => 5,
    'add_args'  => array_map( 'urlencode', $query_args ),
    'prev_next' => true,
    'prev_text' => '‹',
    'next_text' => '›',
) );
?>
<div class="wrap">
    <?php wp_nonce_field( 'bbse_franchise_branch_bulkmanage', '_bbse_franchise_nonce' ); ?>
    <h1>
        <?php echo __('List of branches', 'bbsefranchise')?> <a href="<?php echo admin_url();?>admin.php?page=manage_braches_page" class="page-title-action"><?php echo __('Add branch', 'bbsefranchise')?></a>
    </h1>

    <h2 class="screen-reader-text"><?php echo __('Listing filter ', 'bbsefranchise')?></h2>
    <ul class="subsubsub">
        <li class="all"><?php echo __('Total branches', 'bbsefranchise')?> : <span class="count"><?php echo $total?></span></li>
    </ul>

    <div class="tablenav top">
        <ul class='title-sub-desc none-content'>
            <li>
                <select name='bulk_action' class='bulk_action' id='bulk_action_top'>
                    <option value=''><?php echo __('Bulk actions', 'bbsefranchise')?></option>
                    <option value="toggle"><?php echo __('Visible/Hide', 'bbsefranchise')?></option>
                    <option value='delete'><?php echo __('Delete', 'bbsefranchise')?></option>
                </select>
                <span id="doaction" class="button dobulkaction"><?php echo __('Apply', 'bbsefranchise')?></span>
            </li>
            <li>
            <form method="get">
                <input type="hidden" name="page" value="bbse_franchise">
                <?php if( isset($show) && $show == 'all' ){?>
                <input type="hidden" name="show" value="all">
                <?php }?>
                <input type="text" name="skeyword" id="skeyword" value="<?php echo isset($skeyword) ? esc_attr($skeyword) : '';?>" />
                <input type="submit" class="button" value="<?php echo __('Search', 'bbsefranchise')?>">
            </form>
            </li>
            <li>
                <label><input type="radio" name="show" value="show" class="showAll" <?php echo (isset($show) || empty($show) || $show == 'show') ? 'checked' : ''?>> <?php echo __('Show only \'visible\'', 'bbsefranchise')?></label>
                <label><input type="radio" name="show" value="hide" class="showAll" <?php echo (isset($show) && $show == 'hide') ? 'checked' : ''?>> <?php echo __('Show only \'hide\'', 'bbsefranchise')?></label>
                <label><input type="radio" name="show" value="all" class="showAll" <?php echo (isset($show) && $show == 'all') ? 'checked' : ''?>> <?php echo __('Show all', 'bbsefranchise')?></label>
            </li>
        </ul>

        <div class="tablenav-pages">
            <span class="pagination-links"><span class="tablenav-pages-navspan" aria-hidden="true">«</span>
            <?php echo $paging?>
            <span class="tablenav-pages-navspan" aria-hidden="true">»</span></span>
        </div>
        <br class="clear">
    </div>
    <h2 class="screen-reader-text"><?php echo __('List of branches', 'bbsefranchise')?></h2>
    <table class="wp-list-table widefat striped">
    <colgroup>
        <col style="width:40px;">
        <col style="width:50px;">
        <col style="">
        <col style="">
        <col style="">
        <col style="width:230px;">
        <col style="">
    </colgroup>
    <thead>
    <tr>
        <td id="cb" class="manage-column column-cb check-column">
            <label class="screen-reader-text" for="cb-select-all-1"><?php echo __('Select all', 'bbsefranchise')?></label><input id="cb-select-all-1" type="checkbox">
        </td>
        <th scope="col" id="hide"><?php echo __('Status', 'bbsefranchise')?></th>
        <th scope="col" id="category"><?php echo __('Category', 'bbsefranchise')?></th>
        <th scope="col" id="branchname"><?php echo __('Name', 'bbsefranchise')?></th>
        <th scope="col" id="address"><?php echo __('Address', 'bbsefranchise')?></th>
        <th scope="col" id="phone"><?php echo __('Phone(Mobile)', 'bbsefranchise')?></th>
        <th scope="col" id="role"><?php echo __('ETC.', 'bbsefranchise')?></th>
    </tr>
    </thead>
  <tbody id="the-list" data-wp-lists="list:user">
    <!-- loop start-->
    <?php if($result) foreach($result as $k=>$v){?>
    <tr id="row-<?php echo $v->uid?>">
        <th scope="row" class="check-column">
            <input type="checkbox" name="uids[]" class="multiuids administrator" value="<?php echo esc_attr($v->uid)?>" data-hide="<?php echo esc_attr($v->hide)?>">
        </th>
        <td class="manage"><span class="singleaction dashicons dashicons-<?php echo $v->hide=='Y'?'hidden':'visibility'?>" data-uid="<?php echo $v->uid?>" data-mode="toggle" data-hide="<?php echo $v->hide?>"  title="<?php echo __('Toggle visible or hide', 'bbsefranchise')?>"></span></td>
        <td><?php echo $v->category?></td>
        <td>
            <strong><a href="<?php echo admin_url()?>admin.php?page=manage_braches_page&amp;uid=<?php echo $v->uid?>" title="<?php echo __('Edit', 'bbsefranchise')?>"><?php echo $v->branchname?></a></strong>
            <br>
            <div class="row-actions">
                <span class="edit"><a href="<?php echo admin_url()?>admin.php?page=manage_braches_page&amp;uid=<?php echo $v->uid?>" title="<?php echo __('Edit', 'bbsefranchise')?>"><?php echo __('Edit', 'bbsefranchise')?></a></span>   &nbsp;|&nbsp;
                <?php if($v->hide == 'N'){?>
                <span class="detail"><a href="<?php echo $viewLink.'?uid='.$v->uid?>" target="_blank" title="<?php echo __('View page', 'bbsefranchise')?>"><?php echo __('View page', 'bbsefranchise')?></a></span>&nbsp;|&nbsp;
                <?php }?>
                <span class="manage"><span class="singleaction" data-uid="<?php echo $v->uid?>" data-mode="toggle" data-hide="<?php echo $v->hide?>" title="<?php echo __('Visible/Hide', 'bbsefranchise')?>"><?php echo __('Visible/Hide', 'bbsefranchise')?></span></span>&nbsp;|&nbsp;
                <span class="manage"><span class="singleaction" data-uid="<?php echo $v->uid?>" data-mode="delete" title="<?php echo __('Delete', 'bbsefranchise')?>"><?php echo __('Delete', 'bbsefranchise')?></span></span>
            </div>
            <button type="button" class="toggle-row"><span class="screen-reader-text"><?php echo __('View detail', 'bbsefranchise')?></span></button>
        </td>
        <td><?php echo $v->address1.' '.$v->address2?></td>
        <td>
            <?php echo $v->phone?>
            <?php echo $v->mobile?' ('.$v->mobile.') ':''?>
        </td>
        <td><?php echo $v->tag?></td>
    </tr>
    <?php }?>
    <!-- //loop end -->
    </tbody>
    <tfoot>
    <tr>
        <td class="manage-column column-cb check-column">
            <label class="screen-reader-text" for="cb-select-all-2"><?php echo __('Select all', 'bbsefranchise')?></label><input id="cb-select-all-2" type="checkbox">
        </td>
        <th scope="col" id="hide_btm"><?php echo __('Status', 'bbsefranchise')?></th>
        <th scope="col" id="category_btm"><?php echo __('Category', 'bbsefranchise')?></th>
        <th scope="col" id="branchname_btm"><?php echo __('Name', 'bbsefranchise')?></th>
        <th scope="col" id="address_btm"><?php echo __('Address', 'bbsefranchise')?></th>
        <th scope="col" id="phone_btm"><?php echo __('Phone(Mobile)', 'bbsefranchise')?></th>
        <th scope="col" id="role_btm"><?php echo __('ETC.', 'bbsefranchise')?></th>
    </tr>
    </tfoot>
  </table>
    <!-- //list end -->

    <div class="tablenav bottom">
        <div class="alignleft actions bulkactions">
            <select name='bulk_action_bottom' class='bulk_action' id='bulk_action_bottom'>
                <option value=''><?php echo __('Bulk actions', 'bbsefranchise')?></option>
                <option value="toggle"><?php echo __('Visible/Hide', 'bbsefranchise')?></option>
                <option value='delete'><?php echo __('Delete', 'bbsefranchise')?></option>
            </select>
            <span id="doaction_bottom" class="button dobulkaction"><?php echo __('Apply', 'bbsefranchise')?></span>
        </div>

        <div class="tablenav-pages ">
            <span class="pagination-links"><span class="tablenav-pages-navspan" aria-hidden="true">«</span>
            <?php echo $paging;?>
            <span class="tablenav-pages-navspan" aria-hidden="true">»</span></span>
        </div>
        <br class="clear">
    </div>
    <br class="clear">
    <!-- //bottom end -->
</div>
<div class="noticeBox"><?php echo __('* If the plug-in is working normally after the update, try <b> disable and re-enable </b> the plug-in.', 'bbsefranchise')?></div>
<script>
//리스트 관리 함수
function franchise_delete($mode, $uid, $hide){
    jQuery.post(
        ajaxurl,
        {
            '_bbse_franchise_nonce' : jQuery('input[name=_bbse_franchise_nonce]').val(),
            '_wp_http_referer'      : jQuery('input[name=_wp_http_referer]').val(),

            'action' : 'bbse_franchise_branch_bulkmanage',
            'mode'   : $mode,
            'uid'    : $uid,
            'hide'   : $hide
        },
        function(response){
            if(jQuery.trim(response) == false){
                window.location.reload();
            }
        }
    );
}

jQuery(document)

//리스트 관리 이벤트 캡춰(벌크)
.on('click', '.dobulkaction', function(){
    var $uid  = Array();
    var $hide = Array();

    var $index = jQuery('.dobulkaction').index( jQuery(this));
    var $mode  = jQuery('.bulk_action').eq($index).find(':selected').val();

    jQuery(".multiuids:checked").each(function(i){
        $uid.push( jQuery(this).val() );
        $hide.push( jQuery(this).data('hide') );
    });

    if($mode && $uid[0]){
        franchise_delete($mode, $uid, $hide);
    }
})

//리스트 관리 이벤트 캡춰(싱글)
.on('click', '.singleaction', function(){
    var $uid  = Array();
    var $hide = Array();

    var $mode = jQuery(this).data('mode');
    $uid[0]   = jQuery(this).data('uid');

    if($mode == 'toggle'){
        $hide[0] = jQuery(this).data('hide');
    }

    if($mode && $uid[0]){
        franchise_delete($mode, $uid, $hide);
    }
})

//숨김 목록화/감추기
.on('click', '.showAll', function(){
    <?php unset($cleaned_get_query['show']); ?>
    var addShowAll = '<?php echo admin_url('admin.php').'?'.http_build_query($cleaned_get_query)?>';
    if(jQuery(this).prop('checked')){
        addShowAll += '&show='+jQuery(this).val();
    }
    window.location.href= addShowAll;
});
</script>