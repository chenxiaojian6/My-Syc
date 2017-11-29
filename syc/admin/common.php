<?php
 function randFileName()
 {
   $str =  '/'.md5(time().rand(1111,9999));
   return $str;
 }