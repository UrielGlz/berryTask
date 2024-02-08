<?php

class Tools {

    public function getMondayByWeek($week,$year){

        $jan1 = mktime(1,1,1,1,1,$year);

        $iYearFirstWeekNum = (int) strftime("%W", mktime(1,1,1,1,1,$year));

        

        if($iYearFirstWeekNum == 1){

            $week = $week - 1;

        }

        

        $weekDayJan1 = date("w",$jan1);

        $firstMonday = strtotime(((4-$weekDayJan1)%7-3). ' days ',$jan1);

        $currentMondayTS = strtotime(($week) . ' weeks', $firstMonday);        

        $currentMondayTS = date("Y-m-d",$currentMondayTS);

        

        return $currentMondayTS;

    }

    

    public function getFirstLastDateOfWeek($week,$year){

        $dateMonday = $this->getMondayByWeek($week, $year);

        list($year,$mon,$day) = explode('-',$dateMonday);

        $dateSunday = date('Y-m-d',mktime(0,0,0,$mon,$day+6,$year));

        

        return array('Monday'=>$dateMonday,'Sunday'=>$dateSunday);

    }

    

    public function getWeekByDate($date){

        return (int) strftime("%W",strtotime($date));

    }

    

    function isTrueFloat($val){

        $pattern = '/^-?[0-9]+([,\.][0-9]*)?$/';



        return (!is_bool($val) && (is_float($val) || preg_match($pattern, trim($val))));

    }    

    

    private function isValidFormatDate($value, $format = 'mm/dd/yyyy'){

        if(strlen($value) >= 6 && strlen($format) == 10){



            // find separator. Remove all other characters from $format

            $separator_only = str_replace(array('d','m','y'),'', $format);

            $separator = $separator_only[0]; // separator is first character

           

            if($separator && strlen($separator_only) == 2){ 

                // make regex 

                $regexp = str_replace('mm', '(0?[1-9]|1[0-2])', $format);

                $regexp = str_replace('dd', '(0?[1-9]|[1-2][0-9]|3[0-1])', $regexp);

                $regexp = str_replace('yyyy', '(19|20)?[0-9][0-9]', $regexp);

                $regexp = str_replace($separator, "\\" . $separator, $regexp);

              

                if($regexp != $value && preg_match('/'.$regexp.'\z/', $value)){

                    // check date

                    $arr=explode($separator,$value);

                    $day=$arr[0];

                    $month=$arr[1];

                    $year=$arr[2];

                 

                    if(@checkdate($day, $month, $year))

                        return true;

                }

            }

        }

        return false;

    } 

    

    function isValidaDateYYYMMDD($date){

        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date)){

            return true;

        }else{

            return false;

        }

    }

    

    function isValidaDateTimeYYYYMMDD($datetime){

        $datetime = explode(' ', $datetime);

        $date = $datetime[0];

        $time = $datetime[1];

        if ($this->isValidaDateYYYMMDD($date)){

            $pattern = '/(2[0-3]|[01][0-9]):([0-5][0-9])/';

            if(preg_match($pattern, trim($time))){

                return true;

            }

            return false;

        }else{

            return false;

        }

    }

    

    function setFormatDateToDB($date){ 

        if($this->isValidaDateYYYMMDD($date)){return $date;}

        if(trim($date) != ''){

            $date = explode("/", $date);

            return $date[2]."-".$date[0]."-".$date[1];

        }

        return '';// no regresar null

    }

    

    function setFormatDateToForm($date){ 

        if(trim($date) != ''){

            return $date = strftime('%m/%d/%Y',strtotime($date));

        }

        return ''; // no regresar null

   }

   

   function setFormatDateTimeToDB($datetime){

        if(trim($datetime) != ''){

            $datetime = explode(' ', $datetime);

            $date = $datetime[0];

            $date = explode("/", $date);

            $date = $date[2]."-".$date[0]."-".$date[1];

            

            $time = $datetime[1];

            $time = explode(":", $time);



            if(isset($datetime[2]) && $datetime[2] == 'PM' && $time[0] != '12'){$time[0] += 12;}

            if(isset($datetime[2]) && $datetime[2] == 'AM' && $time[0] == '12'){$time[0] -= 12;}            

            

            $time = $time[0].":".$time[1].":00";

          

            return $date." ".$time;

            

        }

        return '';// no regresar null

    }


    function setFormatTimeToDB($datetime){

        if(trim($datetime) != ''){       
            $datetime = explode(' ', $datetime);
            
            $time = $datetime;

            $time = explode(":", $time[0]);

        


            if(isset($datetime[1]) && $datetime[1] == 'PM' && $time[0] != '12'){$time[0] += 12;}

            if(isset($datetime[1]) && $datetime[1] == 'AM' && $time[0] == '12'){$time[0] -= 12;}            

            

            $time = $time[0].":".$time[1].":00";        

            return $time;

            

        }

        return '';// no regresar null

    }

    

    function setFormatDateTimeToForm($datetime){ 

        if(trim($datetime) != ''){

            $datetime = explode(' ', $datetime);

            $date = $datetime[0];            

            $date = strftime('%m/%d/%Y',strtotime($date));

            

            $time = $datetime[1];

            

            return $date." ".$time;           

        }

        return ''; // no regresar null

   }

   

   function setZeroDefault($value){

       if(trim($value) === '' || is_null($value)){

           return '0';

       }

       

       return $value;

   }

   

   function get_string_between($string, $start, $end){ 

        $string = " ".$string;

        $ini = strpos($string,$start);



        if ($ini == 0) return "";

        $ini += strlen($start);    

        $len = strpos($string,$end,$ini) - $ini;

        return substr($string,$ini,$len);

    }

    

    public function calculateAge($birthDate){

        $birthDate = $this->setFormatDateToDB($birthDate);

        $da= explode('-', $birthDate);  



        $dia = $da[2]; 

        $mes = $da[1];

        $anio = $da[0]; 



        $diac =date("d");

        $mesc =date("m");

        $anioc =date("Y");



        $edadac =  $anioc-$anio;



        if($mesc < $mes && $diac < $dia || $mesc < $mes || $diac < $dia){

            $edad_aux = $edadac - 1;

            $edadac = $edad_aux;

        } 

        return $edadac;

    }

    

    function sanear_string($string){ 

        $string = trim($string); 



        $string = str_replace(

            array('á', 'à', 'ä', 'â', 'ª', '�?', 'À', 'Â', 'Ä'),

            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),

            $string

        );



        $string = str_replace(

            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),

            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),

            $string

        );



        $string = str_replace(

            array('í', 'ì', 'ï', 'î', '�?', 'Ì', '�?', 'Î'),

            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),

            $string

        );



        $string = str_replace(

            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),

            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),

            $string

        );



        $string = str_replace(

            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),

            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),

            $string

        );



        $string = str_replace(

            array('ñ', 'Ñ', 'ç', 'Ç'),

            array('n', 'N', 'c', 'C',),

            $string

        );



        /*

        //Esta parte se encarga de eliminar cualquier caracter extraño

        $string = str_replace(

            array("\", "¨", "º", "-", "~",

                 "#", "@", "|", "!", """,

                 "·", "$", "%", "&", "/",

                 "(", ")", "?", "'", "¡",

                 "¿", "[", "^", "<code>", "]",

                 "+", "}", "{", "¨", "´",

                 ">", "< ", ";", ",", ":",

                 ".", " "),

            '',

            $string

        );*/



        return $string;

    }

 

 



}