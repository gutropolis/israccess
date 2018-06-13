$("#coming-soon").modal({
	  backdrop: 'static',
    keyboard: false,
	show:true
});

$(document).ready(function(){
$('#forget-pass').click(function(e){    
    $('.login_profile').fadeOut('slow', function(){
        $('.forget_password').fadeIn('slow');
    });
});
    
});
$( "#clickme" ).click(function() {
  $( "#hideContent" ).toggle( "slow", function() {
    // Animation complete.
  });
});
$( "#clickme1" ).click(function() {
  $( "#hideContent1" ).toggle( "slow", function() {
    // Animation complete.
  });
});
$(document).ready(function() {
	
	$('.graycontnt').removeAttr('style');
	
 
  $("#carouselExampleIndicators,#owl-single").owlCarousel({
 
      navigation : true, // Show next and prev buttons
 
      slideSpeed : 300,
      paginationSpeed : 400,
	  loop:true,
		autoplay:true,
      items : 1, 
      itemsDesktop : false,
      itemsDesktopSmall : false,
      itemsTablet: false,
      itemsMobile : false
 
  });
 
});
$(window).scroll(function() {
    if ($(this).scrollTop() > 1){  
        $('header').addClass("sticky");
    }
    else{
        $('header').removeClass("sticky");
    }
});

  $(document).ready(function() {
              $('.owl-carousel').owlCarousel({
                loop: true,
                margin: 0,
                responsiveClass: true,
                responsive: {
                  0: {
                    items: 1,
                    nav: true
                  },
                  600: {
                    items: 3,
                    nav: false
                  },
                  1000: {
                    items: 4,
                    nav: true,
                    loop: false,
                    margin: 0
                  }
                }
              })
            });

			
			$(document).ready(function() {
              $('.owl-carousel2').owlCarousel({
                loop: true,
                margin: 0,
                responsiveClass: true,
                responsive: {
                  0: {
                    items: 1,
                    nav: true
                  },
                  600: {
                    items: 2,
                    nav: false
                  },
                  1000: {
                    items: 2,
                    nav: true,
                    loop: false,
                    margin: 0
                  }
                }
              })
            }) 
			
			  var containerEl = document.querySelector('.gallerySection');

            var mixer = mixitup(containerEl);
			
			
			
			
			
			
			/*
Reference: http://jsfiddle.net/BB3JK/47/
*/

$('select').each(function(){
    var $this = $(this), numberOfOptions = $(this).children('option').length;
  
    $this.addClass('select-hidden'); 
    $this.wrap('<div class="select"></div>');
    $this.after('<div class="select-styled"></div>');

    var $styledSelect = $this.next('div.select-styled');
    $styledSelect.text($this.children('option').eq(0).text());
  
    var $list = $('<ul />', {
        'class': 'select-options'
    }).insertAfter($styledSelect);
  
    for (var i = 0; i < numberOfOptions; i++) {
        $('<li />', {
            text: $this.children('option').eq(i).text(),
            rel: $this.children('option').eq(i).val()
        }).appendTo($list);
    }
  
    var $listItems = $list.children('li');
  
    $styledSelect.click(function(e) {
        e.stopPropagation();
        $('div.select-styled.active').not(this).each(function(){
            $(this).removeClass('active').next('ul.select-options').hide();
        });
        $(this).toggleClass('active').next('ul.select-options').toggle();
    });
  
    $listItems.click(function(e) {
        e.stopPropagation();
        $styledSelect.text($(this).text()).removeClass('active');
        $this.val($(this).attr('rel'));
        $list.hide();
        //console.log($this.val());
    });
  
    $(document).click(function() {
        $styledSelect.removeClass('active');
        $list.hide();
    });
	
	

});

   
 