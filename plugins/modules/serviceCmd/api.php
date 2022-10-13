<?php
if (!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("runServiceCMD")) {

    function runServiceCMD($callReference, $cmdID,$payload = []) {
        $cmdData = _db()->_selectQ("sys_matrix_cmds","*",["id"=>$cmdID,"blocked"=>"false"])->_GET();
        
        if(!$cmdData || count($cmdData)<=0) {
            return false;
        }
        $cmdData = $cmdData[0];
        
        return runServiceCMDScript($callReference, $cmdData, $payload);
    }
    
    function runAllServiceCMD($callReference, $category, $payload = []) {
        $serviceData = _db()->_selectQ("sys_matrix_cmds","*",["category"=>$category,"blocked"=>"false"])->_GET();
        
        if(!$serviceData || count($serviceData)<=0) {
            return false;
        }
        
        $a = [];
        
        foreach($serviceData as $row) {
            $a[] = runServiceCMDScript($callReference, $row, $payload);
        }
        
        return $a;
    }
    
    function runServiceCMDScript($callReference, $cmdScript, $payload=[]) {
        if(strlen($cmdScript['cmd_params'])>0) {
            $cmdScript['cmd_params'] = json_decode($cmdScript['cmd_params'],true);
        } else {
            $cmdScript['cmd_params'] =[];
        }
        if($cmdScript['cmd_params']==null) $cmdScript['cmd_params'] =[];
        
        if(strlen($cmdScript['cmd_headers'])>0) {
            $cmdScript['cmd_headers'] = json_decode($cmdScript['cmd_headers'],true);
        } else {
            $cmdScript['cmd_headers'] =[];
        }
        if($cmdScript['cmd_headers']==null) $cmdScript['cmd_headers'] =[];
        
        $cmdScript['payload'] = processServicePayload($cmdScript['cmd_payload']);
        
        //Final Params
        $payload = array_merge($cmdScript['payload'],$payload);
        
        // if(is_array($params)) {
        //     foreach($params as $a=>$b) {
        //         $_REQUEST[$a]=$b;
        //     }
        // }
        // printArray([$cmdScript,$params]);
        
        $response = false;$status="failure";
        $ch = curl_init(); 
                
        $curlParams = [
                CURLOPT_URL => _replace($cmdScript['cmd_uri'],'%'),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "utf8",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13'
            ];
        
        //curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body 
        
        if(count($cmdScript['cmd_headers'])>0) {
            $curlParams[CURLOPT_HTTPHEADER]=$cmdScript['cmd_headers'];
        }
        
        switch(strtoupper($cmdScript['cmd_type'])) {
            case "GET":
                $curlParams[CURLOPT_CUSTOMREQUEST]="GET";
                
                break;
            case "POST":
                $curlParams[CURLOPT_CUSTOMREQUEST]="POST";
                $curlParams[CURLOPT_POST]=1;
                
                //payload
                curl_setopt($ch, CURLOPT_POSTFIELDS,$payload);//http_build_query($params));
                break;
            case "PUT":
                $curlParams[CURLOPT_CUSTOMREQUEST]="PUT";
                
                //payload
                curl_setopt($ch, CURLOPT_POSTFIELDS,$payload);//http_build_query($params));
                break;
            case "DELETE":
                $curlParams[CURLOPT_CUSTOMREQUEST]="DELETE";
                curl_setopt($ch, CURLOPT_POSTFIELDS,$payload);//http_build_query($params));
                break;
            default:
                return false;
        }
        
        curl_setopt_array($ch, $curlParams);
        $response = curl_exec($ch);
        $error_msg = curl_error($ch);
        curl_close($ch); 
        
        if($error_msg) {
            $status="failure";
        } else {
            $status="success";
        }
        
        $dated = date("Y-m-d H:i:s");
        _db()->_insertQ1("log_scmds",[
                "guid"=>$_SESSION['SESS_GUID'],
                "groupuid"=>$_SESSION['SESS_GROUP_NAME'],
                "reference"=>$callReference,
                 "status"=>$status,
                "service_id"=>$cmdScript['id'],
                "output_log"=>$response,
                "payload"=>json_encode($payload),
                "created_by"=>$_SESSION['SESS_USER_ID'],
                "created_on"=>$dated,
                "edited_by"=>$_SESSION['SESS_USER_ID'],
                "edited_on"=>$dated,
            ])->_RUN();
        
        return $response;
    }
    
    function processServicePayload($payload = "") {
        if(strlen($payload)>0) {
            //process $payload
        }
        return [];
    }
}
?>
