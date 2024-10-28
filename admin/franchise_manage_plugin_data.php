<?php
if( ! defined( 'ABSPATH' ) ) exit;

global $wpdb;
global $franchise;
global $current_user;

$configs       = $franchise->configs();
$totalBranches = $wpdb->get_var( "SELECT count(*) AS total FROM {$configs['table_list']} WHERE 1=1" );

$table_config = $wpdb->get_var(" SHOW TABLES LIKE '{$franchise->table_config}' ");
$table_list   = $wpdb->get_var(" SHOW TABLES LIKE '{$franchise->table_list}' ");

$db_version = '';
if($table_config == false && $table_list == false){
    delete_option('franchise_db_version');
}else{
    $templates  = $franchise->templates();
    $configs    = $franchise->configs();
    $mypages    = $franchise->find_page();
    $db_version = get_option('franchise_db_version');
}
?>
<div class="wrap">
    <h1 id="add-new-user"><?php echo __('Data management', 'bbsefranchise')?></h1>
    <div id="ajax-response"></div>
    <p>
        <?php echo __('Backup or restore plug-in data.', 'bbsefranchise')?><br>
        <strong style="color:red">
        - <?php echo __('The data management functionality provided by this plug-in makes no warranty for the backed up data.', 'bbsefranchise')?><br>
        - <?php echo __('In addition to the backup function provided by the plug-in, please use another method to safely backup / store.', 'bbsefranchise')?>
        </strong>
    </p>
    <?php if($db_version){?>
    <p>
        <?php echo __('Database version', 'bbsefranchise')?> : <?php echo $db_version?><br>
        <?php echo __('All saved branches', 'bbsefranchise')?> : <?php echo $totalBranches?>
    </p>
    <?php }else{?>
    <p>
        <?php echo __('No database is currently installed.', 'bbsefranchise')?><br>
        <?php echo __('You can disable the plug-in and then reactivate the database by installing it, or you can restore it if you have backed up data.', 'bbsefranchise')?>
    </p>
    <?php }?>
    <?php if(!$db_version){?>
    <form method="post" name="franchise_form1" id="franchiseForm1">
        <?php wp_nonce_field( 'bbse_franchise_data_manage', '_bbse_franchise_nonce' ); ?>
    </form>
    <?php }?>
    <table class="form-table">
    <tbody>
    <?php if($db_version){?>
    <tr class="form-field ">
        <th scope="row"><?php echo __('Backup data', 'bbsefranchise')?></th>
        <td>
        <form method="post" name="franchise_form1" id="franchiseForm1" action="<?php echo admin_url()?>admin-ajax.php">
            <?php wp_nonce_field( 'bbse_franchise_data_manage', '_bbse_franchise_nonce' ); ?>
            <input type="hidden" name="action" value="bbse_franchise_data_backup">
            <button type="submit" class="b _c1 backup_now"><?php echo __('Download', 'bbsefranchise')?></button>
            <p class="description">(<?php echo __('The database will be downloaded in XML file format.', 'bbsefranchise')?>)</p>
        </form>
        </td>
    </tr>
    <?php }?>
    <tr class="form-field ">
        <th scope="row"><?php echo __('Restore data', 'bbsefranchise')?></th>
        <td>
            <form method="post" name="franchise_form2" id="franchiseForm2" enctype="multipart/form-data" action="<?php echo admin_url()?>admin-ajax.php">
            <input type="hidden" id="_bbse_franchise_nonce2" name="_bbse_franchise_nonce" value="">
            <input type="hidden" name="_wp_http_referer" value="">
            <input type="hidden" name="action" value="bbse_franchise_data_restore">
            <input type="file" style="width:300px;" name="bbse_franchise_xml_file" id="bbse_franchise_xml_file">
            <input type="submit" value="<?php echo __('Upload file.', 'bbsefranchise')?>" class="b _c1 restore_now">
            <p class="description">(<?php echo __('You must upload it in a downloaded XML file format.', 'bbsefranchise')?> <strong style="color:red"><?php echo __('Please note that Restore will restore all data currently deleted!', 'bbsefranchise')?></strong>)</p>
            </form>
        </td>
    </tr>
    <?php if($db_version){?>
    <tr class="form-field ">
        <th scope="row"><?php echo __('Remove database', 'bbsefranchise')?></th>
        <td>
            <button type="button" class="b _c1 delete_now"><?php echo __('Remove it!', 'bbsefranchise')?></button>
            <p class="description">(<?php echo __('Use it when you want to clear the plug-in completely, or to initialize it for reasons of restoration failure, data error, etc.', 'bbsefranchise')?>)</p>
        </td>
    </tr>
    <?php }?>
    </tbody>
    </table>
</div>
<?php
//echo wp_create_nonce( 'delete_post-' . $post_id );
?>
<script>
jQuery(document)

.ready(function(){
    // backup data

    // restore data
    jQuery('#franchiseForm2').ajaxForm({
        beforeSubmit: function (data, frm, opt){
            if(confirm("<?php echo __('Please be careful.', 'bbsefranchise')?>\n<?php echo __('Restore all database tables after deletion (all data is lost).', 'bbsefranchise')?>\n<?php echo __('Do you want to restore your data?', 'bbsefranchise')?>")){
                var varArray = ['_bbse_franchise_nonce', '_wp_http_referer'];
                for(index in data){
                    for(key in varArray){
                        if(data[index]['name'] == varArray[key]){
                            data[index]['value'] = jQuery('#franchiseForm1 input[name='+varArray[key]+']').val();
                        }
                    }
                }

                if(jQuery('#bbse_franchise_xml_file').val() == ''){
                    alert("<?php echo __('Please select a file.', 'bbsefranchise')?>");
                    jQuery('#bbse_franchise_xml_file').focus();
                    return false;
                }else{
                    return true;
                }
            }else{
                return false;
            }
        },
        success: function(response, status){
            if(jQuery.trim(response) == 'success'){
                alert("<?php echo __('Restoration complete', 'bbsefranchise')?>");
                window.location.reload();
            }else if(jQuery.trim(response) == 'error noauth'){
                alert("<?php echo __('No authority', 'bbsefranchise')?>");
            }else if(jQuery.trim(response) == 'error file_type'){
                alert("<?php echo __('Only XML files are supported.', 'bbsefranchise')?>");
            }else{
                var response_parsed = response.split('|||');
                if(jQuery.trim(response_parsed[0]) == 'error permission'){
                    alert("<?php echo __('Failed to save file.', 'bbsefranchise')?>\n\n<?php echo __('You do not seem to have enough authority.', 'bbsefranchise')?>\n<?php echo __('Please check the permissions of the parent directory of the following path.', 'bbsefranchise')?>\n'+response_parsed[1]+'\n\n<?php echo __('Granting the permissions of the wp-content directory to 707 will most likely be resolved.', 'bbsefranchise')?>");
                }else{
                    alert("<?php echo __('Restore failed!!', 'bbsefranchise')?>");
                }
            }
        },
        error: function(err){
            alert("<?php echo __('Communication with the server failed.', 'bbsefranchise')?>");
        }
    });
})

//delete database
.on('click', '.delete_now', function(){
    if(confirm("<?php echo __('Please be careful.', 'bbsefranchise')?>\n<?php echo __('All database tables will be deleted (all data will be lost).', 'bbsefranchise')?>\n<?php echo __('Do you proceed with database table deletion?', 'bbsefranchise')?>")){
        jQuery.post(
            ajaxurl,
            {
                '_bbse_franchise_nonce' : jQuery('#franchiseForm1 input[name=_bbse_franchise_nonce]').val(),
                '_wp_http_referer'      : jQuery('#franchiseForm1 input[name=_wp_http_referer]').val(),

                'action' : 'bbse_franchise_delete_tables',
            },
            function(response){
                if(jQuery.trim(response) == 'deleted'){
                    alert("<?php echo __('Delete done.', 'bbsefranchise')?>");
                }else if(jQuery.trim(response) == 'fail'){
                    alert("<?php echo __('Failed to delete', 'bbsefranchise')?>");
                }else if(jQuery.trim(response) == 'noauth'){
                    alert("<?php echo __('No authority', 'bbsefranchise')?>");
                }
                window.location.reload();
            }
        );
    }
    return false;
});
</script>