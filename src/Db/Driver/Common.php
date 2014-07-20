<?php


namespace H1Soft\H\Db\Driver;


/**
 *
 * @author Administrator
 */
abstract class Common {
   
    
    public function query($queryString,$data=false){
        
    }
    
    public function exec($queryString,$data=false){
        
    }
    
    public function startTranscation(){
        
    }
    
    public function commit(){
        
    }
    
    public function rollback(){
        
    }
    
    public function tables(){
        
    }
    
    public function error() {
    }
    
    public function escape($str) {
        return $str;
    }
    
    public function tb_name($_tbname){
        
    }
    
    public function insert($_tbname,$_data){
        
    }
    
    public function update($_tbname,$_data,$_where){
        
    }
    
    public function delete($_tbname,$_where){
        
    }
    
    public function close(){
        
    }
}
