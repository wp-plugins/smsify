$ = jQuery;
function smsifyDisplayCredits(credits) {
	/*** Credit desplay ***/
	var content = '<div class="credits-container"><span class="smsify-credits">Credits: ' + credits +  ', Buy more --&gt;</span><span class="buy-more"><a href="http://www.smsify.com.au/pricing" target="_blank" title="Buy more credits"><img src="/wp-content/plugins/smsify/images/cart.png" alt="Buy more credits" title="Buy more credits"/></a></span></div>';
	$(content).insertAfter("#icon-edit-comments");
}