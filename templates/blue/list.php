<?php
if ( ! defined( 'ABSPATH' ) ) exit;

global $franchise;
$sidoNames = array(
    '서울'         => array('en'=>'seoul',     'ko'=>__('Seoul', 'bbsefranchise')),
    '인천'         => array('en'=>'incheon',   'ko'=>__('Incheon', 'bbsefranchise')),
    '경기'         => array('en'=>'gyeonggi',  'ko'=>__('Gyeonggi', 'bbsefranchise')),
    '강원'         => array('en'=>'gangwon',   'ko'=>__('Gangwon', 'bbsefranchise')),
    '충북'         => array('en'=>'chungbuk',  'ko'=>__('Chungbuk', 'bbsefranchise')),
    '세종특별자치시' => array('en'=>'sejong',    'ko'=>__('Sejong', 'bbsefranchise')) ,
    '대전'         => array('en'=>'daejeon',   'ko'=>__('Daejeon', 'bbsefranchise') ),
    '충남'         => array('en'=>'chungnam',  'ko'=>__('Chungnam', 'bbsefranchise')),
    '전북'         => array('en'=>'jeonbuk',   'ko'=>__('Jeonbuk', 'bbsefranchise')),
    '광주'         => array('en'=>'gwangju',   'ko'=>__('Gwangju', 'bbsefranchise')),
    '전남'         => array('en'=>'jeonnam',   'ko'=>__('Jeonnam', 'bbsefranchise')),
    '경북'         => array('en'=>'gyeongbuk', 'ko'=>__('Gyeongbuk', 'bbsefranchise')),
    '대구'         => array('en'=>'daegu',     'ko'=>__('Daegu', 'bbsefranchise')),
    '울산'         => array('en'=>'ulsan',     'ko'=>__('Ulsan', 'bbsefranchise')),
    '경남'         => array('en'=>'gyeongnam', 'ko'=>__('Gyeongnam', 'bbsefranchise')),
    '제주특별자치도' => array('en'=>'jeju',      'ko'=>__('Jeju', 'bbsefranchise')),
    '부산'         => array('en'=>'busan',     'ko'=>__('Busan', 'bbsefranchise')),
);

foreach($_GET as $key => $value){
    $getconvertkey = 'gq_'.$key;
    if($value){
        $$getconvertkey = htmlentities(sanitize_text_field(trim($value)), ENT_QUOTES | ENT_IGNORE, "UTF-8");
    }
}

if ($GLOBALS['wp_rewrite']->using_permalinks() == true){ //use permalinks
    $myBaselink = get_the_permalink().'?';
}else{                                                   //normal links
    $myBaselink = get_the_permalink().'&amp;';
}
?>
<div class="cont_wrap">
    <div class="map_search_wrap <?php echo ($franchise->usemapnavi == 'y') ? '' : 'map_hidden'?>">
        <div class="ms_search">
            <h3 class="ms_title"><?php echo $franchise->listtitle ? stripslashes($franchise->listtitle)   : '&nbsp;'?></h3>
            <p class="ms_desc"><?php echo $franchise->listcomment ? stripslashes($franchise->listcomment) : '&nbsp;'?></p>
            <div class="location_search_wrap">
                <form method="get" name="franchisesearchform" id="franchisesearchform" action="./" target="">
                    <?php
                    if ($GLOBALS['wp_rewrite']->using_permalinks() == false && ( $gq_p || $gq_page_id )){
                        if ( $gq_p && !$gq_page_id ){
                            $name  = 'p';
                            $value = $gq_p;
                        }elseif ( !$gq_p && $gq_page_id ){
                            $name  = 'page_id';
                            $value = $gq_page_id;
                        }
                    ?>
                    <input type="hidden" name="<?php echo $name?>" value="<?php echo esc_attr($value)?>">
                    <?php }?>

                    <?php if ($franchise->usemapnavi == 'y'){?>
                        <?php if ($used_categories){?>
                    <div class="ls_fieldset">
                        <label for="part" class="ls_title"><?php echo __('Category', 'bbsefranchise')?></label>
                        <select name="category" id="category">
                            <option value=""><?php echo __('View all', 'bbsefranchise')?></option>
                            <?php foreach($used_categories as $v){?>
                            <option value="<?php echo esc_attr($v)?>" <?php echo (isset($gq_category) && $v == $gq_category) ? 'selected' : ''?>><?php echo $v?></option>
                            <?php }?>
                        </select>
                    </div>
                        <?php }?>
                    <div class="ls_fieldset">
                        <label for="area" class="ls_title"><?php echo __('Select region', 'bbsefranchise')?></label>
                        <select class="select_area" name="sido" title="<?php echo __('Please select a region.', 'bbsefranchise')?>">
                            <option value=""><?php echo __('View all', 'bbsefranchise')?></option>
                            <?php foreach($sidoNames as $k => $v){?>
                            <option value="<?php echo esc_attr($k)?>" <?php echo ( isset($gq_sido) && $gq_sido && ($gq_sido == $k) ) ? 'selected' : ''?> ><?php echo $v['ko']?></option>
                            <?php }?>
                        </select>
                    </div>
                    <?php }?>
                    <div class="ls_fieldset">
                        <label for="ad_input" class="ls_title"><?php echo __('Name/Address', 'bbsefranchise')?></label>
                        <p class="ls_input">
                            <input type="text" name="keyword" value="<?php echo ( isset($gq_keyword) && $gq_keyword ) ? esc_attr($gq_keyword) : ''?>" placeholder="<?php echo __('Please enter a branch name or address.', 'bbsefranchise')?>" title="<?php echo __('Please enter a branch name or address.', 'bbsefranchise')?>">
                            <button type="submit" class="ls_btn"><?php echo __('Search', 'bbsefranchise')?></button>
                        </p>
                    </div>
                </form>
            </div>
        </div>
        <?php if ($franchise->usemapnavi == 'y'){?>
        <div class="ms_map">
            <div class="map">
                <img src="<?php echo BBSE_FRANCHISE_PLUGIN_WEB_URL?>images/korea_map.png" alt="" class="map_img">
                <div class="area_part">
                <?php
                foreach($sidoNames as $k=>$v){
                    $link = array();
                    $link['sido'] = $k;
                    if ( isset($gq_keyword) && $gq_keyword ) $link['keyword'] = $gq_keyword;
                    $locLink = $myBaselink.http_build_query($link);
                ?>
                    <span class="area <?php echo $v['en']?> <?php echo ( isset($gq_sido) && $gq_sido && ($gq_sido == $k)) ? 'active' : ''?>">
                        <a href="<?php echo esc_url($locLink)?>" title="<?php echo $k?>"><?php echo $v['ko']?></a>
                    </span>
                <?php }?>
                </div>
            </div>
        </div>
        <?php }?>
    </div>

    <div class="map_list_wrap">
        <table class="map_list">
        <caption class="hidden"><?php echo __('List', 'bbsefranchise')?></caption>
        <colgroup>
            <col style="width:6%;<?php echo ($franchise->usemapnavi == 'y') ? '' : 'display:none !important'?>">
            <?php if (isset($used_categories)){?>
            <col>
            <?php }?>
            <col>
            <col style="width:33%">
            <col style="width:14%">
            <col style="width:20%">
        </colgroup>
        <thead>
            <tr>
                <th scope="col" style="<?php echo ($franchise->usemapnavi == 'y') ? '' : 'display:none !important'?>"><?php echo __('Region', 'bbsefranchise')?></th>
                <?php if (isset($used_categories)){?>
                <th scope="col"><?php echo __('Category', 'bbsefranchise')?></th>
                <?php }?>
                <th scope="col"><?php echo __('Name', 'bbsefranchise')?></th>
                <th scope="col"><?php echo __('Address', 'bbsefranchise')?></th>
                <th scope="col"><?php echo __('Phone number', 'bbsefranchise')?></th>
                <th scope="col"><?php echo __('ETC.', 'bbsefranchise')?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($DATA){
            foreach($DATA as $k=>$v){
                $addCategory = array();
                $addSido     = array();
                $addKeyword  = array();

                $sidoLink     = '';
                $viewLink     = '';
                $categoryLink = '';
                $phoneLink    = '';

                // 맵 사용중인데 지역을 골랐으면
                if ( $franchise->usemapnavi == 'y' && $v->sido ){
                    $addSido['sido'] = $v->sido;
                    //검색에 사용된 카테고리가 있으면
                    if (!empty($gq_category)){
                        $addSido['category'] = $gq_category;
                    }
                }

            //카테고리가 있으면
            if ($v->category){
                $addCategory['category'] = $v->category;
                //검색에 사용된 지역이 있으면
                if (!empty($gq_sido)){
                    $addCategory['sido'] = $gq_sido;
                }
            }

            // 키워드 있으면
            if ( isset($gq_keyword) && $gq_keyword ){
                $addKeyword['keyword'] = $gq_keyword;
            }

            $sidoLink     = $myBaselink.http_build_query( $addSido );
            $categoryLink = $myBaselink.http_build_query( $addCategory );

            if (empty($gq_category)){
                $addCategory = array();
            }
            if (empty($gq_sido)){
                $addSido = array();
            }
            $viewLink  = $myBaselink.http_build_query( array_merge(array('uid'=> $v->uid), $addSido, $addCategory) );
            $phoneLink = $v->phone ? $v->phone : ($v->mobile ? $v->mobile : '');
        ?>
        <tr>
            <td style="<?php echo ($franchise->usemapnavi == 'y') ? '' : 'display:none !important'?>"><a href="<?php echo esc_url($sidoLink)?>"  title="<?php echo __('See this region', 'bbsefranchise')?>"><?php echo $v->sido?></a></td>
            <?php if (isset($used_categories)){?>
            <td><a href="<?php echo esc_url($categoryLink)?>" title="<?php echo __('See this category', 'bbsefranchise')?>"><?php echo $v->category?></a></td>
            <?php }?>
            <td>
                <a href="<?php echo esc_url($viewLink)?>" title="<?php echo sanitize_text_field(stripslashes($v->branchname))?>"><?php echo sanitize_text_field(stripslashes($v->branchname))?></a>
                <?php if ($phoneLink){?>
                <a href="tel:<?php echo $phoneLink?>" class="mobile_tel"><?php echo $phoneLink?></a>
                <?php }?>
            <td><a href="<?php echo esc_url($viewLink)?>" title="<?php echo __('View detail', 'bbsefranchise')?>">
            <?php
            if ($v->languagetype == 'K')      echo '<span class="address">'.sanitize_text_field(stripslashes($v->address1)).'</span>&nbsp;<span class="address">'.sanitize_text_field(stripslashes($v->address2)).'</a></span>';
            else if ($v->languagetype == 'E') echo '<span class="address">'.sanitize_text_field(stripslashes($v->address2)).'</span>,&nbsp;<span class="address">'.sanitize_text_field(stripslashes($v->address1)).'</a></span>';
            ?>
            </td>
            <td><?php echo $phoneLink?></td>
            <td><?php echo $v->tag ? sanitize_text_field(stripslashes($v->tag)) : ''?></td>
        </tr>
        <?php
            }
        } else {
            $spanCount = $franchise->usemapnavi == 'y' ? 6 : 5;
        ?>
        <tr>
            <td colspan="6" class="noResult">
            <?php echo __('No branches.', 'bbsefranchise')?>
            </td>
        </tr>
        <?php
        }
        ?>
        </tbody>
        </table>
        <?php if ($paging){?>
        <div class="franchise-navigation paging-navigation">
            <div class="pagination loop-pagination">
                <a class="page-numbers firstPage" href="<?php echo html_entity_decode( get_pagenum_link() )?>" title="<?php echo __('First page', 'bbsefranchise')?>">&lt;&lt;</a>
                <?php echo $paging?>
                <a class="page-numbers lastPage" href="<?php echo $end?>"  title="<?php echo __('Last page', 'bbsefranchise')?>">&gt;&gt;</a>
            </div>
        </div>
        <?php }?>
    </div>
</div>