$(function() {

/***********
THIS SCRIPT IS FOR WINDOWS PHONES AND OTHER DEVICES THAT MAY NOT RENDER MEDIA QUERIES
	- REFERENCE: http://getbootstrap.com/getting-started/#support-ie10-width
***********/
	if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
	  var msViewportStyle = document.createElement('style')
	  msViewportStyle.appendChild(
	    document.createTextNode(
	      '@-ms-viewport{width:auto!important}'
	    )
	  )
	  document.querySelector('head').appendChild(msViewportStyle)
	}

//HEADER
	//DISPLAY MOBILE NAVIGATION ON CLICK
	$('.navToggle').click(function(){

		//Slide toggle the nav
		$('nav').slideToggle();

	});

//INDEX
	//INTERVAL FOR THE PRESENTATION SLIDER
	$('#presentationSlider').carousel({
		interval: 6500
	})

});

