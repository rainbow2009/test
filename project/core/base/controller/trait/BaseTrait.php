<?php

namespace base\controller\trait;

 
trait BaseTrait
{
protected $styles;
protected $scripts;



    protected function init($admin = false){

        if(!$admin()){
            if(USER_CSS_JS['styles']){
                 foreach(USER_CSS_JS['styles'] as $item){
                     $this->styles = PATH. TEMPLATE. trim($item,'/');
             }
        }     

        if(USER_CSS_JS['scripts']){
            foreach(USER_CSS_JS['scripts'] as $item){
                $this->styles = PATH. TEMPLATE. trim($item,'/');
            }
         }
        }else{
            if(ADMIN_CSS_JS['styles']){
                foreach(ADMIN_CSS_JS['styles'] as $item){
                    $this->styles = PATH. TEMPLATE. trim($item,'/');
            }
       }     

       if(ADMIN_CSS_JS['scripts']){
           foreach(ADMIN_CSS_JS['scripts'] as $item){
               $this->styles = PATH. TEMPLATE. trim($item,'/');
           }
        }
        }
}


