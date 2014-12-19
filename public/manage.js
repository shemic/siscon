//SIDEBAR
var sidebar = 0;
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
	
	
	function allHide()
	{
		$('.page-sidebar li').each(function()
		{
			$(this).removeClass("open");
			$(this).removeClass("active");
			$(this).find('.sub-menu').slideUp(500);
		});
	}

	
	$('.page-sidebar').on('click' , '.menulist' , function(e)
	{
		allHide();

		$(this).parent().addClass("open");
		$(this).parent().addClass("active");
		$(this).parent().find('.sub-menu').slideDown(500);
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
	$(window.parent.document).find(name).load(function()
	{
        var main = $(window.parent.document).find(name);
        if($(window).height())
        {
			var thisheight = $(window).height()-100;
			main.height(thisheight);
			$(".page-sidebar").height(thisheight);
		}
    });
};


		var LINK = '';
		var link_array = [];
    $(document).ready(function() {       
       // sidebarMenu();
       // menu('.sub-menu a');
       // menu('.link');
       init();
    });
    
    function init()
	{

        /*验证*/
        $(".post").validationEngine();

        /*自动匹配*/
        /*
        $('#product_search').typeahead({
            source: function (query, process) {
                var parameter = {query: query};
                $.getJSON('test_json.txt?ss166', parameter, function (data) {
                    process(data);
                });
            }
        });
        */
        
        if($(".upload").length)
		{
			$(".upload").each(function(i)
			{
				loadUpload(i,$(this),$(this).attr('key'),config.upload, config.front);//三个参数说明1:第几个上传框2：文件对象3：图片的基本配置标题
			})
		}
	}
	
	function add()
	{
		$("#mul_div .reorder").val(m+1);
		$("#mul").append($("#mul_div").html());
		var one = $("#mul .one");
		one.find('.reorder').val(m+1);
		m++;
		one.find('.delete').click(function()
		{
			one.remove();
		})
		$("#mul .add").removeClass('one');
	}
	
	function menu(id)
	{
		$(id).each(function()
	    {
			if($(this).attr('href'))
			{
				var link = $(this).attr('href');
				$(this).attr('href', 'javascript:;');
                //$(this).attr('target', 'page');
                
				$(this).click(function()
				{
					load(link, 'init');
				})
                
			}
	    });
	}
	
	function up()
	{
		if(link_array)
		{
			link_array.pop();
			var link = link_array.pop();
			if(link)
			{
				load(link, 'init');
			}
		}
	}
	
	function ref()
	{
		load(LINK, 'init', {}, false);
	}

	function load(link, type, send, state)
	{
		LINK = link;
        if(state != false)
        {
		    link_array.push(link);
        }
		var ly = layer.load(0);
		send = send ? send : {};
		if(link.indexOf('#') != -1)
		{
			link = $(link).val();
		}
		$.get(link, send, function(t)
		{
			layer.closeAll();
			if(type == 'init')
			{
				reorder = 1;
				$("#main").html(t);
				menu('.link');
				init();
			}
			else
			{
				type();
			}
			
		})
	}

	function show(t, send)
	{
		if(t == '操作成功' || t == '1' || t.indexOf('http://') != -1)
		{
			//alert(1);
			//history.back();
			//return;
			if($("#preview").length && t.indexOf('http://') != -1)
			{
				window.open(t);
			}
			location.href = $("#update_link").val();
			return;
			load($("#update_link").val(), 'init', send);
		}
		else
		{
			layer.alert(t);
		}
	}
	
	function shownext(t, send)
	{
		location.href = t;
		//load(t, 'init', send);
	}

	function del(e, link, next)
	{
		layer.confirm('您确定进行此项操作？',function(index)
		{
			layer.close(index);
			load(link,function()
			{
				if(e)
				{
					e.remove();
				}
				else
				{
					load(next, 'init');
				}
			});
		});
	}
	function oper(e, link, next)
	{
		layer.confirm('您确定进行此项操作？',function(index)
		{
			layer.close(index);
			$.get(link,function()
			{
				ref();
			});
		});
	}

	function search()
	{
		$("#submit_form").attr('method', 'get').attr('target', '');
		$("#submit_form").submit();
		return;
		var post = {};
		$(".search input").each(function()
		{
			if($(this).attr('name')) post[$(this).attr('name')] = $(this).val();
		});

		$(".search select").each(function()
		{
			if($(this).attr('name')) post[$(this).attr('name')] = $(this).val();
		});
		show(1, post);

	}
    function show_model(e)
    {
        var id = e.val().split('|');
        var pid = id[0];
        var mid = id[1];
        var did = id[2];
        e.val('-1');
        if(pid && mid)
        {
            //iframe层例二
            $.layer({
					type: 2,
					title: false,
					fix: false,
					closeBtn: true,
					shadeClose: true,
					shade: [0.1,'#fff', true],
					border : [5, 0.3, '#666', true],
					offset: ['100px',''],
					area: ['990px','500px'],
					iframe: {src: config.host+'page/manage/model_data_list/pid='+pid+'&mid='+mid+'&id='+did},
					success: function(){
					layer.msg('点击层外任意处，可关闭该iframe层', 1, 4);
					}
				});
            /*$.get('<?php echo siscon::link("page/manage/model_data_list/")?>pid='+pid+'&mid='+mid, function(t)*/
            /*{*/
                /*layer.msg(t);*/
            /*})*/
        }
    }
    
    function show_feature_data(e)
    {
		var parent = e.parent().parent();
        var name = parent.find('.name').val();
        var content = parent.find('.content').val();
        var pic = parent.find('.pic').val();
        var id = parent.find('.id').val();
        if(parent)
        {
            layer.msg('前端攻城师贤心', 2, -1);
        }
	}
