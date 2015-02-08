<?php
																					// the Pagination class creates the amount of pages to show
class Pagination
{	// @param1 current page url string
	// @param2 integer of posts array
	// @param3 integer of items to show (set in config.php) 
	public static function renderPagination($sUrl, $iTotalItemsCount, $itemsPerPage)
	{
		$sHTML = '';
		$pagesCount = ceil($iTotalItemsCount/$itemsPerPage); // ceil = round up the result and return an integer
		if($pagesCount > 1)
		{
			$sHTML = '<ul class="pagination"><li>Pages:</li>';
			for($i=1;$i<=$pagesCount;$i++)
			{
				$sHTML .= '<li><a href="'.$sUrl.'page='.$i.'">'.$i.'</a></li>';
			}
			$sHTML .= '</ul>';
		}
		return $sHTML;
	}
}
?>