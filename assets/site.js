window.onload = function() // these functions will execute when page is loaded
{
	inputHint_init("searchString","Search"); // search field placeholder text

	if(document.getElementById("loginButton")) // if id loginButton exists
	{
		loginForm = document.getElementById("loginForm");
		registerForm = document.getElementById("registerForm");
		
		if(document.getElementById("loginFormError")) // if the login fails show the login Form again 
		{
			loginForm.style.display = 'block';
		}
		
		if(document.getElementById("registerFormError")) // if the registration process fails show the Form again
		{
			registerForm.style.display = 'block';
		}
		
		// show and unshow the Login form
		document.getElementById("loginButton").addEventListener("click", function(){
			registerForm.style.display = 'none';
			if(loginForm.style.display == 'block')
				loginForm.style.display = 'none';
			else
				loginForm.style.display = 'block';
		});
		
		// show and unshow the Register form
		document.getElementById("registerButton").addEventListener("click", function(){
			loginForm.style.display = 'none';
			if(registerForm.style.display == 'block')
				registerForm.style.display = 'none';
			else
				registerForm.style.display = 'block';
		});
		
		// close button functionality inside Login field
		document.getElementById("loginFormClose").addEventListener("click", function(){
			loginForm.style.display = 'none';
		});

		// close button functionality inside Register field
		document.getElementById("registerFormClose").addEventListener("click", function(){
			registerForm.style.display = 'none';
		});
	}


	if(document.getElementById("adminButton"))
		// show and unshow Menu button if user is logged in
	{
		userMenu = document.getElementById("userMenu");
		document.getElementById("adminButton").addEventListener("click", function(){
			if(userMenu.style.display == 'block')
				userMenu.style.display = 'none';
			else
				userMenu.style.display = 'block';
		}); // end of click event
	}
	
	// set the logo URL when clicked
	document.getElementById("logo").addEventListener("click", function(){
		window.location = "index.php";
	});
	
	assignMovesToBlocks();	// show and unshow follow buttons - see below
	renderBlocks(); 		// renderBlocks() function is called onload and onresize
} // end of onload functions

window.onresize = function()  //on resize of the user screen center the post blocks
{
	renderBlocks();
} // end of onresize functions

function renderBlocks() // function to center
{
	oBlocksContainer = document.getElementById("blocksbox"); 	// set the div
	iBlockWidth = oBlocksContainer.children[0].offsetWidth+6;	// get the width of the post block + 6px margin
	iNumBlocksToDisplay = parseInt(oBlocksContainer.parentNode.offsetWidth/iBlockWidth); //  get the number of post to display (integer) and divide by the width of one postblock,
	oBlocksContainerWidth = iBlockWidth * iNumBlocksToDisplay; // multiply both integers to get container total width
	oBlocksContainerMargin = parseInt((oBlocksContainer.parentNode.offsetWidth - oBlocksContainerWidth)/2); // 
	oBlocksContainer.style.width = oBlocksContainerWidth + "px"; // overwrite the css width
	oBlocksContainer.style.marginLeft = oBlocksContainerMargin + "px"; // overwrite the css margin left
}

function assignMovesToBlocks() // this function shows follow/unfollow button for all posts exept loggedin user posts.
{
	var oContent = document.getElementById('blocksbox');
	for(var i=0;i<oContent.children.length;i++) // get all posts blocks inside div blocksbox
	{
		var oBox = oContent.children[i];		// target this elements
		if(typeof oBox.children[0].children[0] != 'undefined') // if the variable type is defined (integer or string)
		{
			oBox.addEventListener("mouseover", function(){	// when hovered over a post box
				if(this.children[0].children[0].style.display == 'none' || this.children[0].children[0].style.display == '') // and the diplay is none or not set
				{
					this.children[0].children[0].style.display = 'block'; // show the follow / unfollow button
				}
			});
			oBox.addEventListener("mouseout", function(){				// on hover out hide follow / unfollow button
				if(this.children[0].children[0].style.display == 'block')
				{
					this.children[0].children[0].style.display = 'none';
				}
			});
		} // end of if statement
	} // end of for loop
} // end of function assignMovesToBlocks