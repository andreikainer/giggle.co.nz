<?php

require_once('class.form.php');
require_once('class.tools.php');
require_once('class.post.php');
require_once('class.category.php');
require_once('class.follower.php');
require_once("class.pagination.php");

// View Class contains the html to be called / rendered
// The string $sHTML gets filled up with HTML code and then returned according to the called function 


class View {
	
	public static function renderDoctype() // testing
	{
		$sHTML = '<!DOCTYPE html>';
	  	return $sHTML;
	}
	
	public static function renderHeader() // head tag info
	{
		$sHTML = '<html>
					<head>
					    <title>giggle.nz</title>
					    <meta name="keywords" content="blog, jokes, funny, quotes" />
					    <meta name="description" content="Exchange jokes in New Zealand" />
					    <link rel="stylesheet" type="text/css" href="assets/reset.css" />
					    <link rel="stylesheet" type="text/css" href="assets/style.css" />
					    <script type="text/javascript" src="assets/site.js" async></script>
					    <script type="text/javascript" src="assets/inputHint.js"></script>
					</head>
					<body>';
		return $sHTML;
	}

	//######## HEADER ##########
	// specify the header for logged in users as well as visitors
	// show Menu and Logout for logged in users
	// show Login and Register for visitors
	
	public static function renderHeaderData($uUser) // Page header (top menu)
	{
		$sHTML = '<div id="headerline"></div>
					<div id="container"><div id="header"><div id="loginBox">';

//######## FOR LOGGED IN USERS ##########
// show Menu and Logout for logged in users

		if($uUser->ID > 0) // show for logged in user
		{
			$sHTML .= '<a href="index.php?blog='.$uUser->blog->ID.'"><img class="profilePicture" alt="avatar" width="30px" height="30px" src="includes/timthumb.php?src=../assets/images/avatars/'.$uUser->photo.'&amp;w=30&amp;h=30" /></a>';
			$sHTML .= '<a href="logout.php"><span class="button" id="logoutButton">Logout</span></a> <span id="adminButton" class="button">Menu</span>';
			if($uUser->blog)
			{
				$sHTML .= '<a class="userName" href="index.php?blog='.$uUser->blog->ID.'">You are logged in as: '.htmlspecialchars($uUser->name).'</a>';
				$sHTML .= '<div id="userMenu">
								<a href="index.php">All Jokes</a>
								<a href="index.php?action=timeline">Favorites</a>
								<a href="index.php?blog='.$uUser->blog->ID.'">My Jokes</a>
								<a href="index.php?action=post_create">Create new Joke</a>
								<a href="index.php?action=posts">Edit my Jokes</a>
								<a href="index.php?action=categories">My Categories</a>
							</div>';		
			}
			// else // ######## LOGIN FORM ##########
			// {
			// 	$sHTML .= '<a class="userName" href="index.php">'.htmlspecialchars($uUser->name).'</a>';
			// 	$sHTML .= '<div id="userMenu">
			// 					<a href="index.php">Main page</a>
			// 					<a href="index.php?action=timeline">Timeline</a>
			// 					<a href="index.php?action=blog_create">Create new blog</a>
			// 					<a href="index.php?action=blog_create">Create new post</a>
			// 					<a href="index.php?action=blog_create">Posts</a>
			// 					<a href="index.php?action=blog_create">Categories</a>
			// 				</div>';	
			// }
		}
		else //######## FOR UNLOGGED VISTIORS ##########
		{
			$loginForm = new Form("loginForm");
			if(isset($_POST['authSubmit']))
			{									
				$loginForm->data = $_POST;
				$loginForm->checkEmpty('userLogin','Please enter login');						// validate the entered data -> Login
				$loginForm->checkEmpty('userPassword','Please enter password');					// validate the entered data -> Password
				if(!$uUser->loadByLogin($_POST['userLogin']) && $loginForm->valid)
					// if the onject user is not the same as the entered $_POST user Login and the input is valid
				{
					$loginForm->raiseCustomError('userLogin', 'User not found');
				}
				else if(!$uUser->active && $loginForm->valid)
				{
					$loginForm->raiseCustomError('userLogin', 'Access denied');
				}
				else if($loginForm->valid){
					if($uUser->password != Tools::passwordEncrypt($_POST['userPassword']))  // if entered Password does not match raise error message
					{
						$loginForm->raiseCustomError('userPassword', 'Wrong password');
					}
				}
				if($loginForm->valid)
				{
					$_SESSION['userID'] = $uUser->ID;
					Tools::redirect('index.php?action=blog_create'); 					// if the entered data is correct redirect to the blog_create page
				}
				else 
				{
					$loginForm->makeHiddenField('loginFormError', 'loginFormError');
				}
			}
			
			$loginForm->makeInput('userLogin', 'Login', 'text');
			$loginForm->makeInput('userPassword', 'Password', 'text');
			$loginForm->makeLink('loginFormClose','Close');
			$loginForm->makeSubmitButton('authSubmit', 'Login');

			######## REGISTRATION FORM ##########
			
			$registerForm = new Form("registerForm");
			
			if(isset($_POST['regSubmit']))
			{
				// validate the input data and raise custom error
				// actual validation is happening in the form class
				$registerForm->data = $_POST;
				$registerForm->checkEmpty('userName','Enter your name');
				$registerForm->checkEmpty('userLoginReg','Enter your login');
				$registerForm->checkEmpty('userEmail','Enter your email');
				$registerForm->checkEmpty('userPassword1','Enter your password');
				$registerForm->checkEmpty('userPassword2','Confirm your password');
				$registerForm->checkEmail('userEmail','Email is not valid');
				if($uUser->loadByLogin($_POST['userLoginReg']))
				{
					$registerForm->raiseCustomError('userLoginReg', 'Login exists already');
				}
				$registerForm->checkPassword('userPassword1', 'userPassword2', 'No Password match'); // Password 1 and password 2 match test
				$registerForm->checkUpload("avatar", "image/jpeg", MAX_SIZE);
				//print_r($_FILES);
				if($registerForm->valid)
				{
					$uUser->name = $_POST['userName'];
					$uUser->login =  $_POST['userLoginReg'];
					$uUser->password = Tools::passwordEncrypt($_POST['userPassword1']);
					$uUser->active = true;
					$uUser->email = $_POST['userEmail'];
					$uUser->save();
			        $sPhotoName = 'avatar_'.$uUser->ID.'.jpg';
			        $registerForm->upload('avatar', 'avatars/'.$sPhotoName);
			        $uUser->photo = $sPhotoName;
			        $uUser->save();
			        echo '<script>alert("You successfully created an account. Please Login to use all the features on this website")</script>';
			        Tools::redirect('index.php');
				}
				else 
				{
					$registerForm->makeHiddenField('registerFormError', 'registerFormError'); // if this hidden field is created something went wrong in the registration process
				}
			}
			// render the from
			// @param1: form name
			// @param2: form label
			// @param3: form type

			$registerForm->makeInput('userName', 'Name', 'text');
			$registerForm->makeInput('userLoginReg', 'Login', 'text');
			$registerForm->makeInput('userEmail', 'Email', 'text');
			$registerForm->makeInput('userPassword1', 'Password', 'text');
			$registerForm->makeInput('userPassword2', 'Confirm', 'text');
			$registerForm->makeUpLoadBox('Avatar', 'avatar');
			$registerForm->makeHiddenField('MAX_FILE_SIZE', MAX_SIZE);
			$registerForm->makeLink('registerFormClose','Close');
			$registerForm->makeSubmitButton('regSubmit', 'Register');
			
			$sHTML .= '<span class="button" id="loginButton">Login</span> <span class="button" id="registerButton">Register</span>';
			$sHTML .= $loginForm->HTML;
			$sHTML .= $registerForm->HTML;
		}
		
		####### always visible #########

		// search field
		$searchForm = new Form("searchForm", "index.php");
		$searchForm->makeSearch('searchString', 'searchSubmit');
		$sHTML .= $searchForm->HTML;
		
		$sHTML .= '<div id="logo"></div><br><p id="underliner">SHARE YOUR JOKE</p>'; // Logo and Slogan
		
		$sHTML .= '</div></div>';
		
		return $sHTML;
	}
	
	######## NEW BLOG ##########
	// create new form and blog object

	
	// @param1 = Object User
	public static function createNewBlog($uUser) 						// function to create a new blog
	{
		$sHTML = '';
		
		if(!$uUser->blog)											// if the 
		{
			$blogForm = new Form("categoryForm");
			
			if(isset($_POST['blogSubmit']))							// when blogSubmit button is clicked
			{
				$blogForm->data = $_POST;							// __set() magic method to set the input value
				$blogForm->checkEmpty('blogName','Please enter your name here.'); // check if a blog name was entered
				if($blogForm->valid)								// if it is valid
				{
					$bBlog = new Blog();							// create the new blog object
					$bBlog->name = $_POST['blogName'];
					$bBlog->active = true;
					$bBlog->userID = $uUser->ID;					// save the new Blog entry in the database
					$bBlog->save();
					Tools::redirect('index.php?blog='.$bBlog->ID);	// and redirect to the blog id page 
				}
			}
				
			$blogForm->makeInput('blogName', 'Blog name', 'text');  // the input field for the blog name
			$blogForm->makeSubmitButton('blogSubmit', 'Create');	// the submit button for the blog name input
			
			$sHTML .= $blogForm->HTML; 								// close the fieldset and the form
		}
		else 
		{
			Tools::redirect('index.php'); // if the user is not logged in redirect to landing page
		}
		
		return $sHTML;
	}
	
	######## BLOG POSTS ##########

	public static function renderBlogContent($bBlog) // render posts for specified blog
	{
		$sHTML = View::renderPostArray($bBlog->posts, 'index.php?blog='.$bBlog->ID.'&');
		return $sHTML;
	}
	
	public static function renderBlogHeader($sStr, $bBlog = null) // render heading for the page
	{	
		$sHTML = '<div id="blogInfo">';
		$sHTML .= '<img class="pageIcon" src="assets/images/page.png" alt="page" width="17" height="18" />'; // page icon
		$sHTML .= '<h1>'.htmlspecialchars($sStr).'</h1>';											//senatize input with build in method htmlspecialchars()
		$sHTML .= '<span class="clear"></span>';
		if($bBlog != null)  // render the categories if there are any (not null)
		{
			$sHTML .= '<img src="assets/images/categories.png" alt="page" width="17" height="18" />'; // categaories icon
			
			foreach($bBlog->categories as $category) //loop through categories of this Blog id
			{
				$sHTML .= '<a href="index.php?blog='.$bBlog->ID.'&amp;category='.$category->ID.'">#'.htmlspecialchars($category->name).'</a>';
			}
		}
		$sHTML .= '<span class="clear"></span></div>'; // clearfix
		return $sHTML;
	} 
	
	######## BLOG CATEGORIES ##########

	public static function renderPostArray($aPosts, $sUrl) // render post array and display wherever needed with the second parameter
	{
		$itemsPerPage = ITEMS_PER_PAGE; // constant set in config.php file
		$currentPage = 1;
		if(isset($_GET['page']))
		{
			$currentPage = $_GET['page']; // if keyword page is availabe, set the current page
		}
		
		$sHTML .= '<div id="blocksbox">';			
		
		// ### pagination #####

		if(count($aPosts)>0)  // if the posts array is higher then 0
		{
		    $startIndex = ($currentPage-1)*$itemsPerPage; // get the index of the current page
	    	$endIndex = $currentPage*$itemsPerPage; // get the last index of postblock items
			if($endIndex>count($aPosts)) // if the last index is higher then the postblock array
			{
				$endIndex = count($aPosts); // set it to the end
			}
			
			for($i=$startIndex;$i<$endIndex;$i++) // loop through the postblocks
		    {
		    	$post = $aPosts[$i];
		    	$sHTML .= View::renderPostBlock($post); // display the postblocks
		    }
		}
		else {
			$sHTML .= '<div class="no_posts">There are no posts to show.</div>';
		}
		
		$sHTML .= '</div>';
		$sHTML .= Pagination::renderPagination($sUrl, count($aPosts), $itemsPerPage); // create page info with the pagination class
		
		return $sHTML;
	}

	######## CATEGORY CONTENT ##########
	
	public static function renderCategoryContent($bBlog, $cCategory) // render posts for category
	{																//depreciated code, but keeping it for future reference
		$sHTML = ''; 				
		//if($cCategory->blog == $bBlog)// 1) is the BlogId higher then 0 (does it exist);
										// 2) instantiate a new blog object and fetch the data 
										// 3) this must match the existing object that got sent as Param1 in this method
		//{
		$sHTML .= View::renderPostArray($cCategory->posts, 'index.php?blog='.$bBlog->ID.'&'); // class categorie, case posts = categoryID, param2 = sUrl
		//}
		//else 
		//{
			//Tools::redirect('index.php?blog='.$bBlog->ID);
		//}
		return $sHTML;
	}

	######## TIMELINE (FAVORITES) ##########

	public static function renderUserTimeline($loginUser) // render user timeline
	{
		$sHTML = View::renderPostArray($loginUser->followPosts, 'index.php?action=timeline&');
		return $sHTML;
	}
	
	public static function followUser($iFollowUserID, $sBackUrl, $loginUser) // follow user
	{
		Follower::followUser($loginUser->ID, $iFollowUserID);
		Tools::redirect($sBackUrl);
	}
	
	public static function unfollowUser($iFollowUserID, $sBackUrl, $loginUser) // unfollow user
	{
		Follower::unfollowUser($loginUser->ID, $iFollowUserID);
		Tools::redirect($sBackUrl);
	}

	######## POST BLOCK ##########	
	
	public static function renderPostBlock($pPost) // render one post block
	{
		$sHTML = '';
		$sHTML.= '<div class="block">';
		if(isset($_SESSION['userID'])) // show the follow buttons only for logged in users
		{
			$loginUser = new User();
			$loginUser->load($_SESSION['userID']);
			if($pPost->user->ID != $loginUser->ID)
			{
				$sUrl = substr(strrchr($_SERVER['REQUEST_URI'], "/"), 1); // outputs: index.php
				if(Follower::ifIFollowUser($loginUser->ID, $pPost->user->ID))
				{
					$sHTML .= '<a href="index.php?action=unfollow&user='.$pPost->user->ID.'&url='.$sUrl.'"><div id="following"></div></a>'; // true received
				}
				else 
				{
					$sHTML .= '<a href="index.php?action=follow&user='.$pPost->user->ID.'&url='.$sUrl.'"><div id="follow"></div></a>'; // false received
				}
			}
		}
		$sHTML.= '<div class="topLine"></div>
			<div class="content">
				<img class="profilePicture" alt="avatar" width="30px" height="30px" src="includes/timthumb.php?src=../assets/images/avatars/'.$pPost->user->photo.'&amp;w=30&amp;h=30" />
				<a class="userName" href="index.php?blog='.$pPost->user->blog->ID.'">'.htmlspecialchars($pPost->user->name).'</a>
				<div class="postDate">'.'Posted: '.$pPost->date.'</div>
				<span class="clear"></span>
				<div class="postText"><a class="postTitle" href="index.php?post='.$pPost->ID.'">'.substr(htmlspecialchars($pPost->title), 0, 35).'</a><br>'.substr(htmlspecialchars($pPost->secondText), 0, 150).'...';
				$sHTML.= '<a class="readmore" href="index.php?post='.$pPost->ID.'">Read more</a>';
				$sHTML .= ' </div>
				<div class="commentsCount">';
				if(count($pPost->comments) == 1)
				{ $sHTML.= count($pPost->comments).' comment for this joke</div>';}
				else{ $sHTML.= count($pPost->comments).' comments for this joke</div>';}
			$sHTML .= '</div>
		</div>';
		return $sHTML;
	}
	
	public static function showCommentForm($post) // comment form for the post
	{
		$sHTML = '<div id="commentFormBlock">';
		if(isset($_SESSION['userID'])) 
		{
			$loginUser = new User();
			$loginUser->load($_SESSION['userID']);
			$sHTML .= '<img class="profilePicture" alt="avatar" width="50px" height="50px" src="includes/timthumb.php?src=../assets/images/avatars/'.$loginUser->photo.'&amp;w=50&amp;h=50" />';
			$sHTML.='<a class="userName" href="index.php?blog='.$loginUser->blog->ID.'">'.htmlspecialchars($loginUser->name).'</a>';
			$commentForm = new Form("commentForm");
			
			if(isset($_POST['commentSubmit']))
			{
				$commentForm->data = $_POST;
				$commentForm->checkEmpty('commentText','Please enter your comment.');
				if($commentForm->valid)
				{
					$cComment = new Comment();
					$cComment->text = $_POST['commentText'];
					$cComment->date = date('Y-m-d H:i:s');
					$cComment->userID = $loginUser->ID;
					$cComment->postID = $post->ID;
					$cComment->save();
					Tools::redirect('index.php?post='.$post->ID);
				}
			}
			$commentForm->makeTextArea('commentText', 'Write your comment');
			$commentForm->makeSubmitButton('commentSubmit', 'Post comment');
			$sHTML .= $commentForm->HTML;
			$sHTML .= '<span class="clear"></span>';
		}
		else 
		{
			$sHTML .= 'Only registered users can write comments.';
		}
		$sHTML .= '</div>';
		return $sHTML;
	}
	
	public static function renderPostContent($pPost) // show full post content
	{
		$sHTML = '<div id="fullpost">';
		
		$sHTML .= '<img class="profilePicture" alt="avatar" width="70px" height="70px" src="includes/timthumb.php?src=../assets/images/avatars/'.$pPost->user->photo.'&amp;w=70&amp;h=70" />';
		
		$sHTML .='<div class="postData"><a class="userName" href="index.php?blog='.$pPost->user->blog->ID.'">'.htmlspecialchars($pPost->user->name).'</a>
			<div class="postDate">'.'Posted: '.$pPost->date.'</div></div>
			<h1><a href="index.php?post='.$pPost->ID.'">'.htmlspecialchars($pPost->title).'</a></h1>
			<span class="clear"></span>
			<div class="postText"><p>'.htmlspecialchars($pPost->firstText).'</p><p>'.htmlspecialchars($pPost->secondText).'</p>';
			$sHTML .= ' </div>
			<div class="commentsCount">'.count($pPost->comments).' comments</div>
			<div class="commentsBlock">';
				$sHTML .= View::showCommentForm($pPost);
				foreach($pPost->comments as $comment)
				{
					$sHTML.= '<img class="profilePicture" alt="avatar" width="50px" height="50px" src="includes/timthumb.php?src=../assets/images/avatars/'.$comment->user->photo.'&amp;w=50&amp;h=50" />
					<div class="postData"><a class="userName" href="index.php?blog='.$comment->user->blog->ID.'">'.htmlspecialchars($comment->user->name).'</a>
					<div class="postDate">'.'Posted: '.$comment->date.'</div></div>
					<div class="postText">'.htmlspecialchars($comment->text).'
					</div><span class="clear"></span>';
				}
				$sHTML .= '</div>';
		$sHTML.='</div>';
		return $sHTML;
	}
	
	public static function showCategories($bBlog) // show list of categories
	{
		$sHTML .= '<ul id="userCategories"><li><a id="createCategory" href="index.php?action=category_create"></a></li>';
		foreach($bBlog->categories as $category)
		{
			$sHTML .= '<li><div class="catLeft"></div><a href="index.php?action=categories&edit='.$category->ID.'">#'.htmlspecialchars($category->name).'</a><div class="catRight">x '.$category->postCount.'</div></li>';
		}
		$sHTML .= '<span class="clear"></span></ul>';
		return $sHTML;
	}
	
	public static function createNewCategory($bBlog) // create new category
	{	
		$categoryForm = new Form("categoryForm");
		
		if(isset($_POST['categorySubmit']))
		{
			$categoryForm->data = $_POST;
			$categoryForm->checkEmpty('categoryName','Please enter your the name for the category.');
			if($categoryForm->valid)
			{
				$cCategory = new Category();
				$cCategory->name = $_POST['categoryName'];
				$cCategory->active = true;
				$cCategory->blogID = $bBlog->ID;
				$cCategory->save();
				Tools::redirect('index.php?action=categories');
			}
		}
			
		$categoryForm->makeInput('categoryName', 'Category name', 'text');
		$categoryForm->makeSubmitButton('categorySubmit', 'Create');
		
		$sHTML .= $categoryForm->HTML;
		
		$sHTML .= View::showCategories($bBlog);
		
		return $sHTML;
	}
	
	public static function editCategory($iCategoryID, $loginUser) // edit category
	{
		$sHTML = '';
		$cCategory = new Category();
		$cCategory->load($iCategoryID);
		if($cCategory->blog->user == $loginUser)  				// if the new instanciated class, blog, userID == the @param2 $loginUser 
		{
			$categoryForm = new Form("categoryForm");			// create a new Form named "category Form" (@param1 for class Form)
			
			$categoryData = array();
			$categoryData['categoryName'] = $cCategory->name;	// 
			
			$categoryForm->data = $categoryData;				// magic __set() method to set the 

			
			if(isset($_POST['categorySubmit']))
			{
				$categoryForm->data = $_POST;
				$categoryForm->checkEmpty('categoryName','Please enter your the name for the category.');
				if($categoryForm->valid)
				{
					$cCategory->name = $_POST['categoryName'];
					$cCategory->save();
					Tools::redirect('index.php?action=categories');
				}
			}
			
			$categoryForm->makeInput('categoryName', 'Category name', 'text');
			$categoryForm->makeSubmitButton('categorySubmit', 'Save');
			
			$sHTML .= $categoryForm->HTML;							// magic __get() method to get the closing fieldset Html code 
			$sHTML .= View::showCategories($cCategory->blog);		// display the categories by sending @param1 = new blog id
		}
		else
		{
			Tools::redirect('index.php?action=categories');
		}
		return $sHTML;
	}
	
	public static function showPosts($bBlog) // show posts list
	{
		$sHTML = '<ul id="userPosts"><li><a id="createPost" href="index.php?action=post_create"></a></li>';
		foreach($bBlog->posts as $post)
		{
			$sHTML .= '<li><a href="index.php?action=posts&edit='.$post->ID.'">'.htmlspecialchars($post->title).'</a> <a class="delete" href="index.php?action=posts&delete='.$post->ID.'"></a></li>';
		}
		$sHTML .= '<span class="clear"></span></ul>';
		return $sHTML;
	}
	
	public static function editPost($iPostID, $uUser) // edit post
	{
		$sHTML = '';
		$pPost = new Post();
		$pPost->load($iPostID);
		if($pPost->user == $uUser)
		{
			$postForm = new Form("newPostForm");
			
			$postData = array();
			$postData['postTitle'] = $pPost->title;
			$postData['postCategory'] = $pPost->category->ID;
			$postData['postFirstText'] = $pPost->firstText;
			$postData['postSecondText'] = $pPost->secondText;
			
			$postForm->data = $postData;
			
			if(isset($_POST['postSubmit']))
			{
				$postForm->data = $_POST;
				$postForm->checkEmpty('postTitle','Please enter a title for the joke.');
				$postForm->checkEmpty('postSecondText','Please enter your joke here.');

				if($postForm->valid)
				{
					$pPost->title = $_POST['postTitle'];
					$pPost->firstText = $_POST['postFirstText'];
					$pPost->secondText = $_POST['postSecondText'];
					$pPost->date = date('Y-m-d H:i:s');
					$pPost->userID = $uUser->ID;
					$pPost->categoryID = $_POST['postCategory'];
					$pPost->save();
					Tools::redirect('index.php?action=posts');
				}
			}
			
			$postForm->makeInput('postTitle', 'Title', 'text');
			$postForm->makeSelect('postCategory', 'Category', Category::getCategoryNames($uUser->blog->ID), Category::getCategoryIDs($uUser->blog->ID));
			$postForm->makeTextArea('postFirstText', 'First text');
			$postForm->makeTextArea('postSecondText', 'Second text');
			$postForm->makeSubmitButton('postSubmit', 'Save');
			$sHTML .= $postForm->HTML;
		}
		else 
		{
			Tools::redirect('index.php?action=posts');	
		}
		return $sHTML;
	}
	
	public static function createNewPost($uUser) // create new post
	{
		$sHTML = '';
		$aCategories = Category::getBlogCategories($uUser->blog->ID);
		if(count($aCategories)==0)
		{
			Tools::redirect('index.php?action=category_create');
		}
		
		$postForm = new Form("newPostForm");
		
		if(isset($_POST['postSubmit']))
		{
			$postForm->data = $_POST;
			$postForm->checkEmpty('postTitle','Please enter title for the post.');
			$postForm->checkEmpty('postFirstText','Please enter text for the post.');
			if($postForm->valid)
			{
				$pPost = new Post();
				$pPost->title = $_POST['postTitle'];
				$pPost->firstText = $_POST['postFirstText'];
				$pPost->secondText = $_POST['postSecondText'];
				$pPost->date = date('Y-m-d H:i:s');
				$pPost->userID = $uUser->ID;
				$pPost->categoryID = $_POST['postCategory'];
				$pPost->save();
				Tools::redirect('index.php?action=posts');
			}
		}
			
		$postForm->makeInput('postTitle', 'Title', 'text');
		$postForm->makeSelect('postCategory', 'Category', Category::getCategoryNames($uUser->blog->ID), Category::getCategoryIDs($uUser->blog->ID));
		
		$postForm->makeTextArea('postFirstText', 'Introduction');
		$postForm->makeTextArea('postSecondText', 'Text');
		$postForm->makeSubmitButton('postSubmit', 'Create');
		$sHTML .= $postForm->HTML;
		
		return $sHTML;
	}
	
	public static function renderLastContent() // render content for main page
	{
		$sHTML = View::renderPostArray(Post::getLatestPosts(), 'index.php?');
		return $sHTML;
	}
}
?>