
// This Javascpript file is used to handle input Hints

var fields=Array();

function inputHint_init(id,emptyValue)              // function to display hints inside input fields
{
    inputField = document.getElementById(id);       // set which inoput field to look for
    fields[id] = new Array();                       // create a new array
    fields[id]["emptyValue"] = emptyValue;          // set the empty Value error messages
    fields[id]["isFocused"] = false;                // set bollean isFocused to true

    inputField.onfocus = inputHint_focus;         // call the methods on events onfocus and onblur
    inputField.onblur = inputHint_blur;

    if (inputField.value == "")                     // if the input is empty on submit 
    {
        inputField.value = emptyValue;              // display the placeholder text (empty value hint)
        inputField.style.color ='#aaaaaa';          // in gray text color
        fields[id]["isEmpty"] = true;               // set the boolean isEmpty to true
    } else {                                        // if the entered data is wrong on submit
        fields[id]["isEmpty"]=true;                 
        inputField.value = emptyValue;              // display the error message
        inputField.style.color = '#aaaaaa';         // in gray text color
    }
}

function inputHint_focus(e)
{
    if (e == null) { e = window.event }                     // when an event occurs (klick, keydown etc...)
    
    element = (e.target != null) ? e.target : e.srcElement; // set the target of var element, prefix for IE and other browsers
    
    fields[element.id]["isFocused"]=true;                   // set the boolean isFocused 
    
    if (fields[element.id]["isEmpty"])                      // if boolean "isEmpty" = true
    {
        element.style.color='#000000';                      // set the text color to black
        element.value='';                                   // and empty the placeholder text
    }
}

function inputHint_blur(e)
{
    if (e == null) { e = window.event }                     // when an event occurs (klick, keydown etc...)
    
    element = (e.target != null) ? e.target : e.srcElement; // and the target of the event is not empty, prefix for IE and other browsers   
    
    if (element.value=='')                                  // if the input text is empty on blur (leave the field)
    {
        //fields[element.id]["isEmpty"]=true;
        element.value=fields[element.id]["emptyValue"];     // display the placeholder text (empty value hint)
        element.style.color='#aaaaaa';                      // in gray text color
    } else {
        fields[element.id]["isEmpty"]=false;                // set the boolean "isEmpty" to false
    }
    fields[element.id]["isFocused"]=false;                  // set the boolean "isFocused" to false
}

function inputHint_isEmpty(id)
{
    return fields[id]["isFocused"]?(document.getElementById(id).value==''):fields[id]["isEmpty"];
};

// function clickHandler(e){                                // function to determine whitch elment was klicked on the page
//  var elem, evt = e ? e:event;                            // for development only
//  if (evt.srcElement)  elem = evt.srcElement;
//  else if (evt.target) elem = evt.target;
 
//  alert (''
//   +'You clicked the following HTML element: \n <'
//   +elem.tagName.toUpperCase()
//   +'>'
//  )
//  return true;
// }

//document.onclick=clickHandler;

// EOF input.Hint.js file