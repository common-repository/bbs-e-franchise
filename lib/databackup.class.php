<?php
class FRANCHISEdataBACKUP{
    public function __construct(){
        set_time_limit(0);
    }

    /**
     * BBS e-Franchise 테이블 목록
     */
    public function get_tables()
    {
        global $wpdb;

        $get_tables = array();
        // 기본 테이블
        $tbls_res = $wpdb->get_results("SHOW TABLES FROM ".DB_NAME, ARRAY_N);
        foreach($tbls_res as $tbl){
            if($tbl[0] == "bbse_franchise" || $tbl[0] == "bbse_franchise_config"){
                $get_tables[] = $tbl[0];
            }
        }
        return $get_tables;
    }

    /**
     * xml 데이터 생성
     */
    public function get_xml($tbls)
    {
        global $wpdb;

        $get_xml = "";
        $get_xml .= "\t<".$tbls.">".PHP_EOL;;

        // 필드정보 가져오기
        $fields_res = $wpdb->get_results("DESCRIBE `".$tbls."`", ARRAY_A);
        foreach($fields_res as $fields_rows){
            $get_xml .= "\t\t<fields>".PHP_EOL;;

            foreach($fields_rows as $key => $value){
                $get_xml .= "\t\t\t<".$key.">";
                $get_xml .= "<![CDATA[".stripslashes($value)."]]>";
                $get_xml .= "</".$key.">".PHP_EOL;;
            }

            $get_xml .= "\t\t</fields>".PHP_EOL;;
        }

        // 데이터 가져오기
        $res = $wpdb->get_results("SELECT * FROM `".$tbls."`", ARRAY_A);
        foreach($res as $rows){
            $get_xml .= "\t\t<data>".PHP_EOL;;

            foreach($rows as $key => $value){
                $get_xml .= "\t\t\t<".$key.">";
                $get_xml .= "<![CDATA[".stripslashes($value)."]]>";
                $get_xml .= "</".$key.">".PHP_EOL;;
            }

            $get_xml .= "\t\t</data>".PHP_EOL;;
        }
        $get_xml .= "\t</".$tbls.">".PHP_EOL;;

        return $get_xml;
    }

    /**
     * 파일 다운로드
     */
    public function xml_download($data, $filename="")
    {
        $XML = '';
        $filename = "bbse_franchise_".date("YmdHis", current_time('timestamp')).'.xml';
        header("Content-Type: application/xml");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        Header("Expires: 0");
        $XML .= '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        $XML .= '<bbse>'.PHP_EOL;
        $XML .= $data;
        $XML .= '</bbse>';
        echo $XML;
        exit;
    }

    /*
     * 데이터 입력
     */
    public function xml_import($file)
    {
        GLOBAL $wpdb;
        GLOBAL $franchise;

        require_once BBSE_FRANCHISE_PLUGIN_ABS_PATH."lib/XML2Array.php";
        $xml   = file_get_contents($file);
        $array = XML2Array::createArray($xml);


        // 테이블 개수만큼 반복
        foreach($array['bbse'] as $tbls => $rows){
            if(substr($tbls, 0, 14) == "bbse_franchise"){
                $drop_tbls   = $tbls;
                $create_tbls = $tbls;
            }

            // 테이블 삭제
            $wpdb->query("DROP TABLE IF EXISTS `".$drop_tbls."`");

            if(is_array($rows['fields'])){
                $keys = array_keys($rows['fields']);
                if(reset($keys) == "0") $fields = $rows['fields'];
                else $fields = $rows;
            }else{
                $fields = $rows;
            }

            $fields_count = count($fields);

            //테이블 생성
            $franchise->activation();

            if(is_array($rows['data'])){
                $keys = array_keys($rows['data']);
                if(reset($keys) == "0") $data = $rows['data'];
                else $data = $rows;
            }else{
                $data = $rows;
            }

            if(!empty($data)){
                foreach($data as $key => $row){
                    $keys      = array_keys($row);
                    $row_count = count($row);

                    $fields_arr = array();
                    for($i = 0; $i < $row_count; $i++){
                        $fields_arr[] = "`".$keys[$i]."`";
                    }
                    $fields = implode(",", $fields_arr);

                    $values_arr = array();
                    for($i = 0; $i < $row_count; $i++){
                        $values_arr[] = "'".addslashes($row[$keys[$i]]['@cdata'])."'";
                    }
                    $values = implode(",", $values_arr);
                    $query  = '';
                    $query  = "insert into `".$create_tbls."` (".$fields.") values (".$values.")";
                    $wpdb->query($query);
                }
            }
        }
    }
}