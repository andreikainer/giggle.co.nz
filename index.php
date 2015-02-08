<?php
require_once("includes/header.php");
require_once("includes/class.blog.php");

// the whole website is rendered with the key=value it receives by using the superglobal $_GET
//(::) Scope Resolution Operator is a token that allows access to static, constant, and overridden properties or methods of a class. 

if(isset($_GET['action']))
{
	if(isset($_SESSION['userID'])) 												// this actions can be done only by logged in users (if $_SESSION['userID'] exists)
	{
		$loginUser = new User;													// Instantiate the new User Class and assignt to the object $loginUser
		$loginUser->load($_SESSION['userID']); 									// get the User ID from this session 
		$action = $_GET['action'];												// set the variable $action
		if($action == 'blog_create') // create new blog 						// if $_GET 'action=blog_create'
		{			
			echo View::renderBlogHeader($loginUser->name.': create a blog'); 	// echo the heading from the class VIEW by calling the method with one argument ($loginUser)
			echo View::createNewBlog($loginUser);								// echo the content from the class VIEW by calling the method with one argument ($loginUser)
		}
		else if($action == 'categories') // show category 						// if $_GET 'action=categories'
		{
			if(isset($_GET['edit'])) // edit category 							// if $_GET 'action=categories&edit='
			{
				echo View::renderBlogHeader($loginUser->name.': edit category'); // echo this heading
				echo View::editCategory($_GET['edit'], $loginUser);				// echo the content by calling the method and sending param1 = edit='ID' and param2 = User Name
			}
			else
			{
				echo View::renderBlogHeader($loginUser->name.': categories'); 	// if edit is not shown echo this heading
				echo View::showCategories($loginUser->blog);					// and this content
			}	
		}
		else if($action == 'category_create') // create category 				// if $_GET 'action=categorie_create'
		{
			echo View::renderBlogHeader($loginUser->name.': create new category'); 	// echo this heading
			echo View::createNewCategory($loginUser->blog);							// and this content
		}
		else if($action == 'posts') // show posts 								
		{
			if(isset($_GET['edit'])) // edit post 									// if $_GET 'action=posts&edit='
			{
				echo View::renderBlogHeader($loginUser->name.': edit your joke');	// echo this heading
				echo View::editPost($_GET['edit'], $loginUser);						// and this content
			}
			else
			{
				if(isset($_GET['delete'])) // delete post (make it inactive)		// if $_GET 'action=posts&delete='
				{
					$pPost = new Post();											// Instantiate the new POST() Class and assignt to the Object $pPost
					$pPost->load($_GET['delete']);									// call the function load() and the send the delete id integer
					$pPost->active = 0;												// set the active value to 0 - so the post does not show anymore but remains in the database
					$pPost->save();													// call the function save() the save the changes in the database
				}
				echo View::renderBlogHeader($loginUser->name.': jokes');			// echo the heading
				echo View::showPosts($loginUser->blog);								// and the remaining posts in the database
			}
		}
		else if($action == 'post_create') // create post 							// if $_GET 'action=posts_create'
		{
			echo View::renderBlogHeader($loginUser->name.': create new joke');		// echo this heading
			echo View::createNewPost($loginUser);									// and this content
		}
		else if($action == 'follow') // follow user 								
		{
			if(isset($_GET['user']) && isset($_GET['url']))							// if $_GET 'action=follow&user=''&url='
			{
				echo View::followUser($_GET['user'], $_GET['url'], $loginUser);		// call the method followUser(UserID from Follower, return URL, Own UserID)
			}
			else
			{
				Tools::redirect('index.php');										// if this GET data does not exist redirect to landing page
			}
		}
		else if($action == 'unfollow') // unfollow user
		{
			if(isset($_GET['user']) && isset($_GET['url']))							// if $_GET 'action=unfollow&user=''&url='
			{
				echo View::unfollowUser($_GET['user'], $_GET['url'], $loginUser);	// call the method unfollowUser(UserID from Follower, return URL, Own UserID)
			}
			else
			{
				Tools::redirect('index.php');										// if this GET data does not exist redirect to landing page
			}
		}
		else if($action == 'timeline') // show user's favorites						// if $_GET 'action=posts_create'
		{
			echo View::renderBlogHeader($loginUser->name.'\'s favorites');			// echo this heading
			echo View::renderUserTimeline($loginUser);								// and this content
		}
		else 
		{
			Tools::redirect('index.php');											// if $_GET 'action' has no value assigned redirect to index
		}
	}
	else 
	{
		Tools::redirect('index.php');												// if $_GET 'action' doesn't exsits at all redirect to index (the user is not logged in)
	}
} 																					// from here onwards you do not need to be logged in to view -> for visitors
else if(isset($_GET['blog'])) // show user's blog 									// if $_GET 'blog' exsits
{
	$bBlog = new Blog;																// Instantiate the new Blog Class and assignt to the Object $bBlog
	$bBlog->load((int)$_GET['blog']);												
	if(isset($_GET['category'])) // show the posts in the selected category
	{
		$cCategory = new Category();
		$cCategory->load((int)$_GET['category']);									// convert the value to an integer and send to the method load()
		echo View::renderBlogHeader($bBlog->name.': #'.$cCategory->name,$bBlog);	// param 1: Blogname, param2: $bBlog Object (default of NULL will be overwritten in the method)
		echo View::renderCategoryContent($bBlog, $cCategory);						// 1)sending objects $bBlog and $cCategory to function renderCategoryContent
																					// 2)create the post array for specific category and display it
	}
	else
	{
		echo View::renderBlogHeader($bBlog->name,$bBlog);							// otherwise (if get[category] is not set) display the users blog heading
		echo View::renderBlogContent($bBlog);										// and the posts for this specific blog and display it
	}
}
else if(isset($_GET['post'])) // show full post 									// if $_GET 'post'exists  
{
	$pPost = new Post();															// Instantiate the new Post Class and assignt to the Object $pPost
	$pPost->load((int)$_GET['post']);												// convert the value to an integer and send to the method load()
	echo View::renderPostContent($pPost);											// echo the full Post content
}
else if(isset($_POST['searchString'])) // search 									// if $_GET 'serachString'exists 
{
	$searchString = $_POST['searchString'];											// grab the string via $_POST
	echo View::renderBlogHeader('Searching for: '.$searchString);					// dispaly the heading
	echo View::renderPostArray(Post::getSearchPosts($searchString), 'index.php');	// and the search result content 
}
else 
{
	echo View::renderLastContent(); // landing page content 						// if the URL is 'index.php' only show the last added posts
}
//echo '<tt><pre>' . var_export($pPost, TRUE) . '</pre></tt>';	
require_once("includes/footer.php"); 												// footer information and END OF INDEX
	
?>