//SIDEBAR
function sidebarMenu(){
	
	var sidebarMenu = $('.page-sidebar-menu');
	var subMenu = sidebarMenu.find('.sub-menu');
	function arrowShow(){
		subMenu.each(function(index){
			if(subMenu.eq(index).parent().hasClass('open')){
				subMenu.eq(index).siblings().find('.arrow').addClass('arrowdown');
			}
			else{
				subMenu.parent().find('.arrow').addClass('arrowup');
			}
		});
	}
	
	arrowShow();

	
	$('.page-sidebar').on('click' , 'li > a' , function(e){
		if($(this).next().hasClass('sub-menu') == false){
			return ;
		}
		var sub = $(this).next();
		
		if($(this).parent().hasClass('open')){
			$(this).parent().removeClass("open");
			$(this).find('.arrow').removeClass('arrowdown').addClass('arrowup');
			sub.slideUp(200);
		}
		else{
			$(this).parent().addClass("open");
			$(this).find('.arrow').removeClass('arrowup').addClass('arrowdown');
			sub.slideDown(200);
		}
		e.preventDefault();
	});
}

function checkAll(){
	$('.group-checkable').change(function () {
       	var checked = jQuery(this).is(":checked");
        $('.checkboxes').each(function () {
            if (checked) {
                $(this).attr("checked", true);
            } else {
                $(this).attr("checked", false);
            }
        });
        
    }); 
};


function iframeH(name){
	$(window.parent.document).find(name).load(function(){
        var main = $(window.parent.document).find(name);
        var thisheight = $(document).height()+30;
        main.height(thisheight);
    });
};