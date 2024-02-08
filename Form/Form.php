<?php
/**
 * Description of Form
 *
 * @author carlos
 */
class Form {
    private $name;
    private $method;
    private $action;
    private $enctype;
    private $onSubmit;
    private $target;
    private $class;
    private $elements = array();
    private $actionController = 'insert'; // insert - update - delete
    private $id; // id del registro populado en el formulario
    private $errorRequeridos = array();
    private $_translator = array();
    
    private $defaultFormElementsColSize = 8;
    private $defaultFormLabelsColSize = 3;
    
    public function __construct() { 
        $this->_translator = new Translator('_en');
        $this->setTokenForm();
    }
    
    public function _getTranslation($text){
        return $this->_translator->_getTranslation($text);
    }
    
    public function getErrorRequeridos(){
        return $this->errorRequeridos;
    }
    
    public function setTokenForm($token = null) {
        if($token != null){
             $this->token_form = $token;
        }else{
            $this->token_form = uniqid();
        }        
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setMethod($method) {
        $this->method = $method;
    }

    public function setActionForm($action) {
        $this->action = $action;
    }
    
    public function setEnctype($enctype){
        $this->enctype = $enctype;
    }
    
    public function setOnSubmitForm($onSubmit){
        $this->onSubmit = $onSubmit;
    }

    public function setClass($class) {
        $this->class = $class;
    }
    
     public function setTarget($target) {
        $this->target = $target;
    }

    public function setActionController($actionController) {
        $this->actionController = $actionController;
    }
    
    public function setDefaultFormElementsColSize($size){
        $this->defaultFormElementsColSize = $size;
    }
    
    public function setDefaultFormLabelsColSize($size){
        $this->defaultFormLabelsColSize = $size;
    }
    
    public function getDefaultFormElementsColSize(){
        return "col-lg-".$this->defaultFormElementsColSize." col-md-".$this->defaultFormElementsColSize." col-sm-".$this->defaultFormElementsColSize ." col-xs-12";
    }
    
    public function getDefaultFormLabelsColSize(){
        return "col-lg-".$this->defaultFormLabelsColSize." col-md-".$this->defaultFormLabelsColSize." col-sm-".$this->defaultFormLabelsColSize ." col-xs-12";
    }
    
    public function getElementColSize($element){
        if(isset($element['col-size-element'])){
            return "col-lg-".$element['col-size-element']." col-md-".$element['col-size-element']." col-sm-".$element['col-size-element']." col-xs-12";
        }
        return $this->getDefaultFormElementsColSize();
    }
    
    public function getLabelColSize($element){
        if(isset($element['col-size-label'])){
            return "col-lg-".$element['col-size-label']." col-md-".$element['col-size-label']." col-sm-".$element['col-size-label']." col-xs-12";
        }
        return $this->getDefaultFormLabelsColSize();
    }
    
    public function getDefaultClassWrapper($element){
        return $class = "input-group ".$this->getElementColSize($element);
    }
    
    public function getStringWrapperAttributes($element){    
        $class = $this->getDefaultClassWrapper($element);
        if(isset($element['wrapper_attributes'])){
            $attributes = $element['wrapper_attributes'];
            if(isset($attributes['class'])){$attributes['class'] = $class." ".$attributes['class'];}
            else{$attributes['class'] = $class;}
            
            $string = '';
            foreach ($attributes as $key =>$value){
                $string .=$key."="."'$value' ";
            }
        }else{
            $string = "class='$class'";
        }
        return $string;
    } 
    
    public function openForm($tokenForm = true){
        $string = "<form id='form_".$this->getName()."'  name='".$this->getName()."' action='".$this->getAction()."' enctype='".$this->getEnctype()."' method='".$this->getMethod()."' onsubmit=\"".$this->getOnSubmit()."\" target='".$this->getTarget()."' class='".$this->getClass()."'>";
        
        if($tokenForm == true){ $string .= $this->showTokenForm();}
       
        
        return $string;
    }
    
    public function closeForm(){
        return "</form>";
    }
    
    public function showActionController(){
        return  "<input type='hidden' id='action' name='action' value='".$this->actionController."'/>";
    }
    
    public function showId(){
        return  "<input type='hidden' id='id' name='id' value='".$this->getId()."'/>";
    }
    
    public function showTokenForm(){
        return  "<input type='hidden' id='token_form' name='token_form' value='". $this->getTokenForm()."'/>";
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function addElement(array $element,$position = null) {
        if($position){
            $i=1;
            $elements = $this->getElements();
            foreach ($elements as $name => $value) {
                if($i == $position){
                    $array[$element['name']] = $element;
                }
                $array[$name] = $value;
                $i++;
            }
            $this->elements = $array;
        }else{
            $this->elements[$element['name']] = $element;
        }
    }
    
    public function getTokenForm(){
        return $this->token_form;
    }


    public function getElements() {
        return $this->elements;
    }

    public function getElement($element) {
        if(isset($this->elements[$element])){
            return $this->elements[$element];
        }
        return null;
    }

    public function getName() {
        return $this->name;
    }

    public function getMethod() {
        return $this->method;
    }
    
    public function getOnsubmit() {
        return $this->onSubmit;
    }

    public function getAction() {
        return $this->action;
    }
    
    public function getEnctype() {
        return $this->enctype;
    }

    public function getClass() {
        return $this->class;
    }
    
    public function getTarget() {
        return $this->target;
    }

    public function getActionController() {
        return $this->actionController;
    }

    public function getId() {
        return $this->id;
    }
    
    public function deleteElement($element){
        unset($this->elements[$element]);
    }
    
    public function addProperties($element,$propierties){
       foreach($propierties as $key =>$value){
            $this->elements[$element][$key] = $value;
        } 
    }
    
    public function getPropertie($element,$propiertie){
        if(isset($this->elements[$element][$propiertie])){
            return $this->elements[$element][$propiertie];
        }
    }
    
    public function addClassToElement($element,$class){
        $currentClass = $this->getPropertie($element, 'class');
        $newClass = $currentClass." ".$class;
        
        $this->addProperties($element, array('class'=>$newClass));
    }
    
    public function addOptionals($element,$optionals){
        foreach($optionals as $key =>$value){
            $this->elements[$element]['optionals'][$key] = $value;
        }
    }
    
    public function deleteOptionals($element,$optionals){
        foreach($optionals as $key =>$value){
            if(key_exists($value, $this->elements[$element]['optionals'])){
                unset($this->elements[$element]['optionals'][$value]);
            }            
        }
    }
    
    public function getNameValuesElements() {
        $array = array('submit','button');
        $nameElements = array();
        foreach ($this->elements as $key => $value) {
            if(!in_array($value['type'],$array)){
                if(isset($value['value'])){
                    $nameElements[$key] = $value['value'];
                }                
            }
        }

        return $nameElements;
    }
    
    public function getValueElement($element){
        if(isset($this->elements[$element]['value'])){
            return $this->elements[$element]['value'];
        }
        return null;
    }

    public function populate($data) {
        $elements = $this->getElements();
        foreach ($elements as $key => $value) {
            if(isset($data[$key])){
                $this->elements[$key]['value'] = $data[$key];
            }
        }
        
        if(isset($data['id'])){$this->setId($data['id']);}
        if(isset($data['action'])){$this->setActionController($data['action']);}
    }
    
    public function setValueToElement($value,$element){
        $this->elements[$element]['value'] = $value;
    }
    
    public function setLabelToElement($value,$element){
        $this->elements[$element]['label'] = $value;
    }
    
    public function setErrorToElement($error,$element){
        $this->elements[$element]['error'] = $error;
    }
    
    public function setPropiedad($element,array $propiedad){
        foreach($propiedad as $key =>$value){
            $this->elements[$element][$key] = $value;
        }
    }
    
    public function setAsMultipleSelect($element){
        $this->elements[$element]['type'] = 'select multiple';
    }
    
    public function setAsRequired(array $elements){
        foreach($elements as $element){
            if(isset($this->elements[$element])){
                $this->elements[$element]['required'] = true;
                $this->addProperties($element, array('class'=>'required'));
            }
        }
    }
    
    public function noRequired(array $elements){
        foreach($elements as $element){
            if(isset($this->elements[$element]['required'])){
                unset($this->elements[$element]['required']);
            }
        }
    }
    
    public function disabledElements($elements){
        foreach($elements as $element){
            if(isset($this->elements[$element]['optionals'])){
                $optionals = array_merge($this->elements[$element]['optionals'],array('disabled'=>'disabled'));
                $this->elements[$element]['optionals'] = $optionals;
                
            }else{
                $this->elements[$element]['optionals'] = array('disabled'=>'disabled');
            }
        }
    }
    
    public function setReadOnlydElements($elements){
        foreach($elements as $element){
            if(isset($this->elements[$element]['optionals'])){
                if($this->elements[$element]['type']!='select'){
                    $optionals = array_merge($this->elements[$element]['optionals'],array('readOnly'=>'readOnly'));
                }else{
                    $optionals = array_merge($this->elements[$element]['optionals'],array('readOnly'=>'readOnly'));
                }
                
                $this->elements[$element]['optionals'] = $optionals;
                
            }else{
                $this->elements[$element]['optionals'] = array('readOnly'=>'readOnly');
            }
        }
    }
    
    public function disabledAllElements(){
        foreach($this->elements as $element => $value){
            //echo $this->elements[$element]['name']."<br>";
            if(isset($this->elements[$element]['optionals'])){
                $optionals = array_merge($this->elements[$element]['optionals'],array('disabled'=>'disabled'));
                $this->elements[$element]['optionals'] = $optionals;
                
            }else{
                $this->elements[$element]['optionals'] = array('disabled'=>'disabled');
            }
        }//exit;  
    }
    
    public function enabledElement(array $elements){
        foreach($elements as $element){
            if(isset($this->elements[$element]['optionals'])){
                $optionals = $this->elements[$element]['optionals'];
                
                if(key_exists('disabled', $optionals)){
                    unset($optionals['disabled']);
                    $this->elements[$element]['optionals'] = $optionals;
                }
            }          
        }
    }
    
     public function enabledAllElement(){
        $elements = $this->getElements();   
        foreach($elements as $element){
            $element = $element['name'];
            if(isset($this->elements[$element]['optionals'])){
                $optionals = $this->elements[$element]['optionals'];
                
                if(key_exists('disabled', $optionals)){
                    unset($optionals['disabled']);
                    $this->elements[$element]['optionals'] = $optionals;
                }
            }          
        }
    }
    
    public function hideElement(array $elements){
        foreach($elements as $element){
            $this->elements[$element]['type'] = 'hidden';
            $this->elements[$element]['label'] = '';
        }
    }
    
    public function showForm() {?>
        <form name="<?php echo $this->name ?>" action="<?php echo $this->action ?>" enctype="<?php echo $this->enctype; ?>" method="<?php echo $this->method ?>" class="<?php echo $this->class; ?>" target="<?php echo $this->target ?>">
            <?php echo $this->showActionController();?>
            <input type="hidden" name="id" value="<?php echo $this->id ?>"/><?php
            $elements = $this->getElements();
            foreach ($elements as $element => $propiedad) {
                    $class = null;
                    $data = null;
                    if(isset($propiedad['error'])){
                        $class = ' has-error has-feedback ';
                        $data = "data-errorfor = '{$element['name']}'";
                    }?>
            
                    <div class="form-group <?php echo $class?>" <?php echo $data ?> >
                        <?php if(isset($propiedad['label'])){?> 
                        <label class="control-label <?php echo $this->getLabelColSize($element)?>"><?php echo $this->_getTranslation($propiedad['label'])?></label>
                        <?php }?>
                        <?php $this->createElement($propiedad);?>
                    </div><?php                    
            }?>                  
        </form><?php
    }

    public function createElement($propiedad,$spanToError = null) {
        if (!array_key_exists('value', $propiedad)) {$propiedad['value'] = null;}
        if (!array_key_exists('class', $propiedad)) {$propiedad['class'] = null;}
        if (!array_key_exists('id', $propiedad)) {$propiedad['id'] = $propiedad['name'];}
        if (!array_key_exists('optionals', $propiedad)) {$optionals = null;}else{
            $optionals = '';
            foreach($propiedad['optionals'] as $key => $value){
                $optionals .= $key. '="' .$value. '" ';
            }
        }?>
        <div <?php echo $this->getStringWrapperAttributes($propiedad)?>><?php
        if(isset($propiedad['prepend'])){echo $propiedad['prepend'];}
        
        $arrayInputs = array('text','password','hidden','file');
        if (in_array($propiedad['type'],$arrayInputs)){?>
            <input type="<?php echo $propiedad['type'] ?>" id="<?php echo $propiedad['id']; ?>" name="<?php echo $propiedad['name']; ?>" value="<?php echo $propiedad['value'] ?>" class="form-control <?php echo $propiedad['class'] ?>" <?php echo $optionals ?>/><?php     
        }elseif($propiedad['type'] == 'button'){?>
            <input type="<?php echo $propiedad['type'] ?>" id="<?php echo $propiedad['id']; ?>" name="<?php echo $propiedad['name']; ?>" value="<?php echo $propiedad['value'] ?>" class="<?php echo $propiedad['class'] ?>" <?php echo $optionals ?>/><?php 
        }elseif($propiedad['type'] == 'button-button'){?>
            <button id="<?php echo $propiedad['id']; ?>" name="<?php echo $propiedad['name']; ?>" class="<?php echo $propiedad['class'] ?>" <?php echo $optionals ?>><?php echo $propiedad['value'] ?></button><?php 
        }elseif($propiedad['type'] == 'submit'){?>
            <input type="<?php echo $propiedad['type'] ?>" id="<?php echo $propiedad['id']; ?>" name="<?php echo $propiedad['name']; ?>" value="<?php echo $propiedad['value'] ?>" class="<?php echo $propiedad['class'] ?>" <?php echo $optionals ?>/><?php 
        }elseif ($propiedad['type'] == 'select') {?>
            <select id="<?php echo $propiedad['id']; ?>" name="<?php echo $propiedad['name']; ?>" class="form-control <?php echo $propiedad['class'] ?>" <?php echo $optionals ?>>
                <?php if(isset($propiedad['multiOptions'])){
                        $i = 1;
                        foreach ($propiedad['multiOptions'] as $key => $value) {
                            $optGroup = explode('_', $key);
                            if($optGroup[0]=='optGroup'){ $i++;?>
                                <optgroup label="<?php echo $this->_getTranslation($value); ?>">
                        <?php   if($i>1){?>
                                     </optgroup><?php   
                                 }
                            }else{?>
                                <option value="<?php echo $key ?>" <?php if($key == $propiedad['value']){echo "selected='selected'";}?>><?php echo $this->_getTranslation($value); ?></option>
                <?php       }           
                        }
                      }?>
            </select><?php
        }elseif ($propiedad['type'] == 'input-list'){?>
            <input list="<?php echo $propiedad['list']; ?>" name="<?php echo $propiedad['name']?>" class="form-control <?php echo $propiedad['class'] ?>" <?php echo $optionals ?>/><?php 
            
        }elseif ($propiedad['type'] == 'datalist') {?>
            <datalist id="<?php echo $propiedad['idList']; ?>">
                <?php if(isset($propiedad['multiOptions'])){
                        foreach ($propiedad['multiOptions'] as $key => $value) { ?>
                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
            <?php       }
                      }?>
            </datalist><?php
        }elseif($propiedad['type'] == 'checkbox'){
            $style = 'float:left;'?>
            <input type="<?php echo $propiedad['type'] ?>" id="<?php echo $propiedad['id']; ?>" 
                   name="<?php echo $propiedad['name']; ?>" value="<?php echo $propiedad['value'] ?>" 
                   class="form-control <?php echo $propiedad['class'] ?>" <?php echo $optionals ?>
                   <?php //if($propiedad['value']){ echo " checked='checked'";}?>/><?php 
            
        }elseif($propiedad['type'] == 'textarea'){?>
            <textarea id="<?php echo $propiedad['id']?>" name="<?php echo $propiedad['name']?>" class="form-control <?php echo $propiedad['class'] ?>" <?php echo $optionals; ?>><?php echo htmlentities($propiedad['value'])?></textarea>
<?php   
        }elseif($propiedad['type']=='select multiple'){?>
            <select id="<?php echo $propiedad['id']; ?>" multiple='multiple' name="<?php echo $propiedad['name']; ?>[]" class="form-control <?php echo $propiedad['class'] ?>" <?php echo $optionals ?>>
                <?php if(isset($propiedad['multiOptions'])){
                        foreach ($propiedad['multiOptions'] as $key => $value) { 
                        if(!is_array($propiedad['value'])){$propiedad['value']= array();}?>
                            <option value="<?php echo $key ?>" <?php if(key_exists($key,$propiedad['value'])){echo "selected='selected'";}?>><?php echo $value ?></option>
            <?php       }
                      }?>
            </select><?php
        }
        
        if(isset($propiedad['append'])){echo $propiedad['append'];}
        if(isset($propiedad['error'])){?>
            <span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span><?php
        }?>   
            
        </div><?php      
    }
    
    public function createOnlyElement($propiedad){
        if (!array_key_exists('value', $propiedad)) {$propiedad['value'] = null;}
        if (!array_key_exists('class', $propiedad)) {$propiedad['class'] = null;}
        if (!array_key_exists('id', $propiedad)) {$propiedad['id'] = $propiedad['name'];}
        if (!array_key_exists('optionals', $propiedad)) {$optionals = null;}else{
            $optionals = '';
            foreach($propiedad['optionals'] as $key => $value){
                $optionals .= $key. '="' .$value. '" ';
            }
        }
        
        if($propiedad['type']=='select multiple'){?>
            <select id="<?php echo $propiedad['id']; ?>" multiple='multiple' name="<?php echo $propiedad['name']; ?>[]" class="form-control <?php echo $propiedad['class'] ?>" <?php echo $optionals ?>>
                <?php if(isset($propiedad['multiOptions'])){
                        foreach ($propiedad['multiOptions'] as $key => $value) { 
                        if(!is_array($propiedad['value'])){$propiedad['value']= array();}?>
                <option value="<?php echo $key ?>" <?php if(key_exists($key,$propiedad['value'])){echo "selected='selected'";}?>><?php echo $this->_getTranslation($value) ?></option>
            <?php       }
                      }?>
            </select><?php
        }elseif($propiedad['type']=='select'){?>
            <select id="<?php echo $propiedad['id']; ?>" name="<?php echo $propiedad['name']; ?>" class="form-control <?php echo $propiedad['class'] ?>" <?php echo $optionals ?>>
                <?php 
                if(isset($propiedad['multiOptions'])){
                    foreach ($propiedad['multiOptions'] as $key => $value) {?>
                        <option value="<?php echo $key ?>" <?php if($key == $propiedad['value']){echo "selected='selected'";}?>><?php echo $this->_getTranslation($value); ?></option><?php                
                    }
                }?>
            </select><?php
        }elseif($propiedad['type'] == 'textarea'){?>
            <textarea id="<?php echo $propiedad['id']?>" name="<?php echo $propiedad['name']?>" class="form-control <?php echo $propiedad['class'] ?>" <?php echo $optionals; ?>><?php echo htmlentities($propiedad['value'])?></textarea>
<?php   
        }else{
            return "<input type='{$propiedad['type']}' id='{$propiedad['id']}' name='{$propiedad['name']}' value='{$propiedad['value']}' class='{$propiedad['class']}' $optionals /> ";
        }        
    }
    
    public function showLabel($element){
        echo   "<label class='$element[name] control-label ".$this->getLabelColSize($element)."'> ".$this->_getTranslation($element['label'])."</label>";
    }
    
    public function showElement($element,$labelPosFijo = null){
        $element = $this->getElement($element);        
        if(!$element){return null;}   
        
        $class = null;
        $data = null;
        if(isset($element['error'])){
            $class = ' has-error has-feedback ';
            $data = "data-errorfor = '{$element['name']}'";
        }?>
            
        <div class="form-group <?php echo $class?>" <?php echo $data ?> > <?php       
        
        if($labelPosFijo){?><?php $this->createElement($element); }
        
        if(isset($element['label'])){
            $this->showLabel($element);
        }   
       
        if(!$labelPosFijo){?><?php $this->createElement($element);} ?>
            
        </div><?php      
    }
    
     public function _rawNumber($inputs){
        $elements = $this->getElements();
        $entityRepo = new EntityRepository();
        foreach ($elements as $element => $propiedad) {
            if(isset($propiedad['value'])){
                $data[$propiedad['name']] = $propiedad['value'];
            }            
        }
        
        $data = $entityRepo->_rawNumber($data, $inputs);
        foreach($data as $name => $value){
             $this->elements[$name]['value'] = $value;
        }       
    }

    public function isValid() {
        $flashmessenger = New FlashMessenger();
        $elements = $this->getElements();
        $error = null;
        foreach ($elements as $element => $propiedades) {
            if (array_key_exists('required', $propiedades) && $propiedades['required']) {
                if(isset($propiedades['value']) && !is_array($propiedades['value'])){
                    if(trim($propiedades['value'],' ') == ''){
                        $error = true;
                        $this->elements[$element]['error'] = 'X'; 
                        if(isset($propiedades['label'])){
                            $this->errorRequeridos[] = $propiedades['label'];
                        }
                    }
                }else{
                    if(isset($propiedades['value']) && count($propiedades['value'])<=0){
                        $error = true; 
                        $this->elements[$element]['error'] = 'X';                        
                        if(isset($propiedades['label'])){
                            $this->errorRequeridos[] = $propiedades['label'];
                        }
                    }
                }
            }
        }
        //Si no hay campos requeridos vacios...
        if(!$error){
            foreach ($elements as $element => $propiedades) {
                if (array_key_exists('validators', $propiedades)){
                    $objValidator = new FormValidator();
                    $validators = array();
                    if(isset($propiedades['validators'])){
                        $validators = $propiedades['validators'];
                    }                    
                    if(isset($propiedades['required'])&& $propiedades['required'] && (isset($propiedades['value']) && trim($propiedades['value'],' ')) !=''){
                        foreach($validators as $validator => $name){
                            if(!$objValidator->validate($name,$propiedades)){
                                $error = true;
                            }
                        }
                    }
                }
            }
        }else{
            //Si hay campos requeridos vacios mando msj ...
            $flashmessenger->addMessage(array('danger'=>'Todos los campos marcados con "X" son requeridos.'));
        }
       
        if ($error) {
            return null;
        }else{
            return true;
        }
        return true;
    }
    
    public function getElementString($element,$labelPosFijo = null){
        $element = $this->getElement($element);
        if(!$element){return null;}
        
        $class = null;
        $data = null;
        if(isset($element['error'])){
            $class = ' has-error has-feedback ';
            $data = "data-errorfor = '{$element['name']}'";
        }
            
        $string = "<div class='form-group' $data>";             
        
        if($labelPosFijo){$string .= $this->createElementString($element);}
        
        if(isset($element['label'])){$string .= $this->getLabelString($element);}   
       
        if(!$labelPosFijo){$string .= $this->createElementString($element);} 
            
        $string .= "</div>";
        
        return $string;
    }    
    
    public function getLabelString($element){
        return "<label class='$element[name] control-label ".$this->getLabelColSize($element)."'>".$this->_getTranslation($element['label'])."</label>";
    }
    
    public function createElementString($propiedad) {
        if (!array_key_exists('value', $propiedad)) {$propiedad['value'] = null;}
        if (!array_key_exists('class', $propiedad)) {$propiedad['class'] = null;}
        if (!array_key_exists('id', $propiedad)) {$propiedad['id'] = $propiedad['name'];}
        if (!array_key_exists('optionals', $propiedad)) {$optionals = null;}else{
            $optionals = '';
            foreach($propiedad['optionals'] as $key => $value){
                $optionals .= $key."= \"$value\"";
            }
        }
        
        $string = "<div {$this->getStringWrapperAttributes($propiedad)}>";
        if(isset($propiedad['prepend'])){$string .= $propiedad['prepend'];}
        
        $arrayInputs = array('text','password','submit','hidden','file','button');
        if (in_array($propiedad['type'],$arrayInputs)){
            $string .="<input type=\"{$propiedad['type']}\" id=\"{$propiedad['id']}\" name=\"{$propiedad['name']}\" value=\"{$propiedad['value']}\" class=\"form-control {$propiedad['class']}\" ".$optionals."/>"; 
            
        }elseif ($propiedad['type'] == 'select') {
            $string .=  "<select id=\"{$propiedad['id']}\" name=\"{$propiedad['name']}\" class=\"form-control {$propiedad['class']}\" ".$optionals." >";
            if(isset($propiedad['multiOptions'])){
                foreach ($propiedad['multiOptions'] as $key => $value) {
                    $string .="<option value=\"$key\" "; if($key == $propiedad['value']){$string .= " selected='selected' ";}$string .= ">".$this->_getTranslation($value)."</option>";
            }
                      }
            $string .= "</select>";
        }elseif($propiedad['type'] == 'button-button'){
            $string .= "<button id=\"{$propiedad['id']}\" name=\"{$propiedad['name']}\" class=\"{$propiedad['class']}\" $optionals >{$propiedad['value']}</button>"; 
        }elseif($propiedad['type'] == 'checkbox'){
            $string .= "<input type=\"{$propiedad['type']}\" id=\"{$propiedad['id']}\" name=\"{$propiedad['name']}\" value=\"{$propiedad['value']}\" class=\"form-control {$propiedad['class']}\" $optionals  />";            
        }elseif($propiedad['type'] == 'textarea'){
            $string .="<textarea id=\"{$propiedad['id']}\" name=\"{$propiedad['name']}\" class=\"form-control {$propiedad['class']}\"  $optionals >".htmlentities($propiedad['value'])."</textarea>";
        }elseif($propiedad['type']=='select multiple'){
            $string .= "<select id=\"{$propiedad['id']}\" multiple=\"multiple\" name=\"{$propiedad['name']}[]\" class=\"form-control {$propiedad['class']}\" $optionals >";
            if(isset($propiedad['multiOptions'])){
                foreach ($propiedad['multiOptions'] as $key => $value) { 
                    $selected = "";
                    if(!is_array($propiedad['value'])){$propiedad['value']= array();}
                    if(key_exists($key,$propiedad['value'])){$selected .= "selected='selected'";}
                    $string .= "<option value=\"$key\" $selected>{$this->_getTranslation($value)}</option>";
                }
            }
            $string .= "</select>";          
        }
        
        if(isset($propiedad['append'])){$string .=$propiedad['append'];}
        if(isset($propiedad['error'])){
            $string .= "<span class=\"glyphicon glyphicon-remove form-control-feedback\" aria-hidden=\"true\"></span>";
        }
            
        $string .="</div>";
        return $string;
    }
    
     public function formatDouble($data,$inputs){
        foreach($inputs as $value){
            if(isset($data[$value])){
                if(trim($data[$value]) != ''){$data[$value] = number_format($data[$value],2);}
            }
        }
        
        return $data;
    }
}