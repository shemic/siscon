var reorder = 1;

function loadUpload(i,e,key,u,h)
{
	e.uploadify({
				'formData' : {'key' : key},
				'swf' : h+'public/photo/uploadify.swf',
				'buttonText' : '选择文件',
				'uploader'  : u,
				'cancelImg' : 'http://js.selfimg.com.cn/image/image/uploadify-cancel.png',
				'onUploadSuccess':function(file,data,response)
				{
					console.info(data);
					var data = eval('('+data+')');
					if(data.status)
					{
						var value = e.attr('v');
						if(value == 'mul')
						{
							//alert($("#mul_div").html());
							$("#mul").append($("#mul_div").html());
							var one = $("#mul .one");
							one.find('img').attr('src',data.url);
							one.find('.pic').val(data.url);
							if(one.find('.reorder').length)
							{
								one.find('.reorder').val(reorder);
								reorder++;
							}
							one.find('.delete').click(function()
							{
								one.remove();
							})
							$("#mul .add").removeClass('one');
						}
						else
						{
							jQuery("#"+file.id).find('.data').html('上传完毕');
							jQuery('#'+value).val(data.url);
							jQuery('#show_'+value).attr('src',data.url).show();
						}
					}
					else
					{
						jQuery("#"+file.id).find('.data').html("<font color='red'>"+data.message+"</font>");
						return false;
					}
				}
	});
}

if(jQuery.browser.msie){
SWFUpload.prototype.getFlashHTML = function () {
	return ['<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" id="', this.movieName, '" type="application/x-shockwave-flash" data="', this.settings.flash_url, '" width="', this.settings.button_width, '" height="', this.settings.button_height, '" class="swfupload">',
				'<param name="wmode" value="', this.settings.button_window_mode , '" />',
				'<param name="movie" value="', this.settings.flash_url, '" />',
				'<param name="quality" value="high" />',
				'<param name="menu" value="false" />',
				'<param name="allowScriptAccess" value="always" />',
				'<param name="flashvars" value="' + this.getFlashVars() + '" />',
				'</object>'].join("");
    }
}
