<?php
	class Form {
												// the Form class contains all the properties and methods to create and handle Forms
		private $sHTML;							// create the properties
		private $aData;
		private $aError;
	
		// assign the default magic method __construct() values
		// while receiving @param1 = form ID and class name
		// @param2 = action attribute for the form element
		// if param2 is not received the default vaulue is empty

		public function __construct($sFormID, $action = "") {
			$this->aData = array();
			$this->aError = array();
			$this->sHTML = '<form class="'.$sFormID.'" id="'.$sFormID.'" enctype="multipart/form-data" action="'.$action.'" method="post"><fieldset>'."\n";
		}
	#########################################
		// Form elements creation
	#########################################

		// @param1 = label for, input name, id, value, JS function inputHint_init => id & error message
		// @param2 = label for label element
		// @param3 = input type
		public function makeInput($sControlID, $sLabel, $type)
		{
			if($this->aError[$sControlID] != "")  // if the Error Array is not Empty (it is failed)
			{
				$this->sHTML .= '<label class="red" for="'.$sControlID.'">'.$sLabel.':</label>';  // error labeling
			}
			else {
				$this->sHTML .= '<label for="'.$sControlID.'">'.$sLabel.':</label>';				// valid labeling
			}
        
        // create the input field    
        $this->sHTML .= '<div class="field"><input type="'.$type.'" name="'.$sControlID.'" id="'.$sControlID.'" class="textBox" value="'.htmlspecialchars($this->aData[$sControlID]).'"/></div>';
            

            if($type == 'text' && $this->aError[$sControlID] != "") // if the Error Array is Empty & the input type is text
            {
            	$this->sHTML .= '<script>inputHint_init("'.$sControlID.'","'.$this->aError[$sControlID].'");</script>'; // call the JS function and send the params to JS
            }
        $this->sHTML .= "\n"; 
		} //EOF makeInput function


        // @param1 = input name, id
        // @param2 = button value
        public function makeSubmitButton($sControlID, $sLabel)
        {
	        $this->sHTML .= '<input class="button" name="'.$sControlID.'" id="'.$sControlID.'" type="submit" value="'.$sLabel.'" /><div class="clear"></div>'."\n";
        }
        

        // @param1 = span id
        // @param2 = value of the Link
        public function makeLink($sControlID, $sLabel)
        {
	        $this->sHTML .= '<span class="button" id="'.$sControlID.'">'.$sLabel.'</span>'."\n";
        }
        

        // @param1 = input name, element id
        // @param2 = button name, button id
        public function makeSearch($sControlID, $sSubmitID)
        {
	        $this->sHTML .= '<div class="field"><input type="text" name="'.$sControlID.'" id="'.$sControlID.'" class="textBox" value=""/><input class="button" name="'.$sSubmitID.'" id="'.$sSubmitID.'" type="submit" value="" /><div class="clear"></div></div>'."\n";
        }
        

        // @param1 = label for, select name, select id
        // @param2 = label for label element
        // @param3 = option data array
        // @param4 = value data array
        public function makeSelect($sControlID, $sLabel, $aData, $aValue)
        {
        	$this->sHTML .= '<label for="'.$sControlID.'">'.$sLabel.':</label>
        	<select name="'.$sControlID.'" id="'.$sControlID.'">';
        	for($i=0;$i<count($aData);$i++)   							// for loop for option data
        	{
	        	$this->sHTML .= '<option value="'.$aValue[$i].'"';
		        if($aValue[$i] == $this->aData[$sControlID])
		        {
			        $this->sHTML .= ' selected="selected"';				// generate selected value
		        }
		        $this->sHTML .= '>'.$aData[$i].'</option>';				// close the option element
        	}
	        $this->sHTML .= '</select><div class="clear"></div>';		// close the select element
        }
        
        // @param1 = label for label element
        // @param2 = label for, input name, input id               
        public function makeUpLoadBox($sLabel, $sControlID)
        {
        	if($this->aError[$sControlID] != "") 			// if the Error Array is not Empty (it is failed)
			{
				$this->sHTML .= '<label class="red" for="'.$sControlID.'">'.$sLabel.':</label>'; // error labeling
			}
			else {
				$this->sHTML .= '<label for="'.$sControlID.'">'.$sLabel.':</label>';			// valid labeling
			}
		$this->sHTML .= '<input type="file" name="'.$sControlID.'" id="'.$sControlID.'" size="10" /><div class="clear"></div>'; // closing element
		}
        
		// @param1 = input name, input id
		// @param2 = input value
        public function makeHiddenField($sControlID, $sValue)
		{	
			$this->sHTML .= '<input type="hidden" name="'.$sControlID.'" id="'.$sControlID.'" value="'.htmlspecialchars($sValue).'" />';
		}
        
        // @param1 = label for, div class, textarea name, Error array, Data Array
        // @param2 = label for label element
        public function makeTextArea($sControlID, $sLabel)
        {
	        $this->sHTML .= '<label for="'.$sControlID.'">'.$sLabel.':</label>
            <div class="message">'.$this->aError[$sControlID].'</div><div class="'.$sControlID.'"><textarea name="'.$sControlID.'" id="'.$sControlID.'">'.htmlspecialchars($this->aData[$sControlID]).'</textarea></div>
			'."\n";
        }
    #########################################
        // Form validation
    #########################################
       
        // @param1 = input name
        // @param2 = error message
        public function checkEmpty($sControlID, $sMessage)
        {
	        if(strlen($this->aData[$sControlID])==0)
	        {
		        $this->aError[$sControlID] = $sMessage;
	        }
        }
        
        // @param1 = password1
        // @param2 = password2
        // @param3 = error message
        public function checkPassword($sPassword1ControlID, $sPassword2ControlID, $sMessage) // check if the passwords match
        {
	        if($this->aData[$sPassword1ControlID] != $this->aData[$sPassword2ControlID])
	        {
	        	$this->aError[$sPassword1ControlID] = $sMessage;
		        $this->aError[$sPassword2ControlID] = $sMessage;
	        }
        }
        
        // @param1 = input name
        // @param2 = error message
        public function checkEmail($sControlID, $sMessage)
        {
        	$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/'; 
        	if (!preg_match($regex, $this->aData[$sControlID])) 
        	{
	        	$this->aError[$sControlID] = $sMessage;
        	}
        }
        
        // @param1 = error array reference
        // @param2 = error message string
        public function raiseCustomError($sControlID, $sMessage)
        {
	        $this->aError[$sControlID] = $sMessage;
        }
        

        // @param1 = $__FILES name,
        // @param2 = allowed filetype and Mime
        // @param3 = maximum upload size 
        public function checkUpload($sControlID, $sMimeType, $iSize)
	    {			
			$sErrorMessage = "";
			if(empty($_FILES[$sControlID]["name"]))
			{	
				$sErrorMessage = "No photo specified";	
			}
			else if($_FILES[$sControlID]['error'] != UPLOAD_ERR_OK)
			{		
				$sErrorMessage = "File cannot be uploaded";
			}
			else if($_FILES[$sControlID]["type"] != $sMimeType)
			{			
				$sErrorMessage = "Only ".  $sMimeType ." format can be uploaded";			
			}
			else if($_FILES[$sControlID]["size"] > $iSize)
			{	
				$sErrorMessage = "Files cannot exceed ".$iSize." bytes in size";	
			}
			
			if ($sErrorMessage != "")
			{
				$this->aError[$sControlID] = $sErrorMessage;
				//echo '<script>upload_init("'.$this->aError[$sControlID].'");</script>';  // Test to see if Error message can be passed on to JS
			}
		}
	
		// Upload function
		
		public function upload($sControlName, $sNewFileName)
		{
			$newname = dirname(__FILE__).'/../assets/images/'.$sNewFileName;		// set the new file name
			move_uploaded_file($_FILES[$sControlName]['tmp_name'],$newname);		// move to permanent directory and rename
		}
        
        // Use switch inside of magical functions __get and __set:
		// http://stackoverflow.com/questions/17704038/how-to-access-multiple-properties-with-magic-method-get-set

        public function __get($sProperty)						// Get the property. This can be accessed outside the class via magic method __get(property=string)
		{														// http://php.net/manual/en/language.oop5.overloading.php
	        switch($sProperty)
	        {    
	            case 'HTML' :
	                return $this->sHTML.'</fieldset></form>';	// return the html code to close the fieldset and the form
	                break;
	            case 'valid' :
	            	if(count($this->aError)==0)					// count if the array Error has any data stored. If it is == 0 it is TRUE
	            	{
		            	return true;
	            	}
	            	else 
	            	{	
		            	return false;							// if it is higher then 0 at least one error occured - return false
	            	}
	            default:
	                die($sProperty. ' for the GET Method in Class FORM does not exist'); // if the property is not set display the error message
	        } 
	    }
	    
	    public function __set($sProperty,$value)				// set the values of the property. This can be accessed outside the class via magic method __set(propert, value)
	    {    
	        switch($sProperty)									// set (overload) the properties to create a new form
	        {    
	            case 'data' :
	                $this->aData = $value;					
	                break;
	            default:
	                die($sProperty. ' for the SET Method in Class FORM does not exist'); // if the property is not set display the error message
	        }
	    }
        
	} // EOF Class Form
?>