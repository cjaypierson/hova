jQuery(document).ready(function($){ 
$("#airportCode").autocomplete({
                source: function( request, response ) {
                $.ajax({
                    url: urlGetCode,   
                    dataType: "json",
                    data: {term: request.term},
                    success: function(data) {
                                response($.map(data, function(item) {
                           			
                                return {
                                    label: item.location,
                                    id: item.code,
                         
                                    };
                            }));
                        }
                    });
                },
                minLength: 3,
                select: function(event, ui) {
               
                   
          
                    document.getElementById('airportLocation').innerHTML= ui.item.label;
                    $.ajax({
						url: urlGetIframe,
					 
						data: {term: ui.item.id},
						success: function(data) {
								 document.getElementById("contentRadar").innerHTML = data
							}
                    });
                }
                    
                
            });

 });