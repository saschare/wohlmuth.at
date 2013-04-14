//create a global variable that stores the instances of iScroll on different pages, or multiple instance on a single page (that's up to you)
var myScroll = [];

//bind to the pageshow event for all pages that are added to the DOM by jQuery Mobile
$(document).delegate('[data-role="page"]', 'pageshow', function () {
	
	//cache current page being created and the id of the element that will be passed to iScroll
	var $page       = $(this),
		scroller_id = this.id + '_content';
		
	//only run the following code if the `.content` div exists on the page (which is the element that will be passed to iScroll)
	if ($page.find('.content').length > 0) {
		
		//check if the iScroll instance has already been setup
		if (scroller_id in myScroll) {
			
			//if the iScroll instance on the current page has already been setup, refresh it in case anything has changed
			myScroll[scroller_id].refresh();
		} else {
			
			//if the iScroll instance does not exist, initialize it
			//notice that I am saving the iScroll instance for this page in an array with the ID of the element being passed to iScroll as the associative key in the `myScroll` array
			myScroll[scroller_id] = new iScroll(scroller_id, {
				snap       : 'li',
				momentum   : false,
				hScrollbar : false,
				hScroll    : true,
				vScroll    : false,
				onScrollEnd: function () {
			document.querySelector('#indicator > li.active').className = '';
			document.querySelector('#indicator > li:nth-child(' + (this.currPageX+1) + ')').className = 'active';
		}
			});
		}
	}
});