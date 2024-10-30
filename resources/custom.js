var cIndex = 1;
var hdr = document.getElementById('hdr-slideshow');
 
if(hdr)
{
    $("#page").prepend(hdr);
}

var xmlPath = $('#xmlPath').html();
var installedPath = $('#installedPath').html();
var timeout = $('#timeout').html();
var urlArray = new Array();

jQuery(document).ready(function() {
	
	$.ajax({
	    url: xmlPath,
	    type: 'GET',
	    dataType: 'xml',
	    timeout: 10000,
	    error: function(){
	    //    alert('Error loading XML document');
		return 1;
	    },
	    success: function(xml){
	    	 		$(xml).find('url').each(function(){
	    							var item_text = $(this).text();
	    							urlArray.push(item_text);
	    							});
	    	 		if(urlArray.length < 2)
	    	 		{
	    	 			// alert('Display box requires at least two images');
	    	 			return 1;
	    	 		}
	    	 		else
	    	 		{
		    	 		$("#hdr1").css("background","transparent url('" + installedPath + urlArray[0] + "') no-repeat").addClass("current");
		    	 		$("#hdr2").css("background","transparent url('" + installedPath + urlArray[1] + "') no-repeat").fadeOut();
                        
                        var result =  setTimeout("fadeMe()", timeout);
	    	 		}
	    }
	});
});

function fadeMe() {

	    	 		var header1current = $("#hdr1").hasClass("current");
	    	 		
	    	 		cIndex++;
	    	 		
	    	 		if(cIndex >= urlArray.length)
	    	 		{
	    	 			cIndex = 0;
	    	 		}

	    	 		if( header1current ) 
                    {
                        	$("#hdr2").fadeIn('slow');
	    	     		    $("#hdr1").fadeOut('slow', function() {

                                $("#hdr1").removeClass("current");
                                $("#hdr1").css("background","transparent url('" + installedPath + urlArray[cIndex] + "') no-repeat");
                            
                            });
                                    	
	    	 		}
	    	 		else
	    	 		{
                        
                        $("#hdr1").fadeIn('slow');
                        $("#hdr2").fadeOut('slow', function() {
                        
                            $("#hdr1").addClass("current");
                            $("#hdr2").css("background","transparent url('" + installedPath + urlArray[cIndex] + "') no-repeat");
                        
                        });
                        
	    	 		}
	    	 		
	    	 		var result =  setTimeout("fadeMe()", timeout);

}