
// Store references to places in the DOM
$( function () {

	window.__UI = window.__UI || { };

	// Create Quote Widget
	__UI.$userSearchForQuoteForm = $( ".js_user_search_for_quote_form" );
	__UI.$customersNamesForQuote = __UI.$userSearchForQuoteForm.find( ".js_customers_names" );
	__UI.$createQuoteButton = __UI.$userSearchForQuoteForm.closest( ".js_section" ).find( ".js_create_quote" );

} );



/*
 *
 * Clear the quote form and disable the Create Quote button
 *
 */
function resetQuoteForm () {
	__OMEGA.customer = null;
	__UI.$userSearchForQuoteForm.get( 0 ).reset();
	__UI.$customersNamesForQuote.text( "" );
	__UI.$userSearchForQuoteForm.find( "[ type = 'submit' ]" ).text( "Search" );
	// Re-enable the form
	__UI.$userSearchForQuoteForm.find( "input, button" ).prop( "disabled", false );
	// But the Create Quote button disabled
	__UI.$createQuoteButton.prop( "disabled", true );
	$( document ).trigger( "quote-form/enable" );
}



/*
 *
 * When a unit is view, clear and reset the quote form
 *
 */
$( document ).on( "unit/view", resetQuoteForm );



/*
 *
 * When a unit is viewed,
 *	Issue a request to enable the quote form
 *
 */
$( document ).on( "unit/view/done", function () {

	$( document ).trigger( "quote-form/enable" );

} );


/*
 *
 * When the quote form is to enabled,
 *	enable it only if all the conditions are met
 *
 */
$( document ).on( "quote-form/enable", function () {

	/*
	 *
	 * If,
	 *	1. A prospect is selected,
	 *	2. There's no error caused by the executive with the modifications
	 *
	 * 	Then, enable the quote form.
	 */
	if ( __OMEGA.unitData.error ) {
		__UI.$createQuoteButton.prop( "disabled", true );
		__UI.$createQuoteButton.text( "Please fix the error(s)." );
		return;
	}
	if ( ! __OMEGA.customer ) {
		__UI.$createQuoteButton.prop( "disabled", true );
		__UI.$createQuoteButton.text( "Please select a prospect." );
		return;
	}

	__UI.$createQuoteButton.prop( "disabled", false );
	__UI.$createQuoteButton.text( __UI.$createQuoteButton.data( "text-actionable" ) );

} );


/*
 *
 * On submitting the user search form
 *
 */
$( document ).on( "submit", ".js_user_search_for_quote_form", function ( event ) {

	/* -----
	 * Prevent the default form submission behaviour
	 * 	which triggers the loading of a new page
	 ----- */
	event.preventDefault();

	// Get a reference to the form element
	var $form = $( event.target );

	/* -----
	 * Disable the form
	 ----- */
	$form.find( "input, button" ).prop( "disabled", true );
	$form.find( "[ type = 'submit' ]" ).text( "Searching" );

	/* -----
	 * Pull the data from the form
	 ----- */
	// User's ID
	$userId = $form.find( "[ name = 'user-id' ]" );

	/* -----
	 * Validate the data
	 ----- */
	// Remove any prior "error"s
	$form.find( ".js_error" ).removeClass( "js_error" );
	// User ID
	if ( ! $userId.val().trim() ) {
		$name.addClass( "js_error" );
		$name.parent().addClass( "validation-error" );
		alert( "Please provide the customer's ID." );
	}
	// If the form has even one error ( i.e. validation issue )
	// do not proceed
	if ( $form.find( ".js_error" ).length ) {
		$form.find( "input, select, button" ).prop( "disabled", false );
		$form.find( "[ type = 'submit' ]" ).text( "Search" );
		return;
	}

	/* -----
	 * Process and Assemble the data
	 ----- */
	var userId = $userId.val().trim();

	/* -----
	 * Store the data on the side
	 ----- */
	__OMEGA.customer = { uid: userId }

	/* -----
	 * Process the data
	 ----- */
	// Fetch the user
	getLead( userId )
		.then( function ( user ) {

			if ( ! user.isProspect ) {
				var message = "The user <b>" + user.firstName + "</b> is not a <i><b>prospect</b></i>.";
				notify( message, {
					level: "info",
					context: "Quote Form",
					escape: true
				} );
				resetQuoteForm();
				return;
			}

			// Issue a request to enable the quote form
			$( document ).trigger( "quote-form/enable" );

			var applicantsNames = user.name;
			if ( user.coApplicantName )
				applicantsNames += " and " + user.coApplicantName;

			__UI.$customersNamesForQuote.text( applicantsNames );

			$form.find( "input, select, button" ).prop( "disabled", false );
			$form.find( "[ type = 'submit' ]" ).text( "Search" );

			__OMEGA.customer = Object.assign( __OMEGA.customer, user );

		} )
		.catch( function ( e ) {
			notify( e.message, {
				level: "error",
				context: "Quote Form"
			} );
			resetQuoteForm();
			$form.find( "[ type = 'submit' ]" ).text( "Search" );
		} );

} );


/*
 *
 * On submitting the quote form
 *
 */
$( document ).on( "click", ".js_create_quote", function ( event ) {

	/* -----
	 * Disable the form
	 ----- */
	__UI.$userSearchForQuoteForm.find( "input, button" ).prop( "disabled", true );

	/* -----
	 * Assemble the data
	 ----- */
	// Re-compute the data, taking into account the prospect's details
	computeUnitData();

	var customer = __OMEGA.customer;
	var meta = getDataFromSheet( __OMEGA.workbook.Sheets[ "Settings" ] );
	var crm = getDataFromSheet( __OMEGA.workbook.Sheets[ "Output (CRM)" ] );
	// var mail = getDataFromSheet( __OMEGA.workbook.Sheets[ "Output (Mail)" ] );

	var quote = {
		timestamp: getDateAndTimeStamp(),
		meta: meta,
		user: __OMEGA.user,
		customer: customer,
		crm: crm,
		unit: __OMEGA.unitData,
		pdf: getComputedUnitDataForPrint()
		// mail: mail,
		// ...
	};

	makeAQuote( quote )
		.then( function () {
			notify( "The quote has been made. It will accessible on the CRM shortly.", {
				level: "info",
				context: "Quote Form"
			} );
			// Keep the "Generate Quote" button disabled, but with a message
			__UI.$createQuoteButton.prop( "disabled", true );
			__UI.$createQuoteButton.text( "A quote is being made." );
			// Reset the form
			setTimeout( function () {
				resetQuoteForm();
			}, 1000 );
		} )
		.catch( function () {
			notify( "Something went wrong. Please try again after a while.", {
				level: "error",
				context: "Quote Form"
			} );
			// Re-enable the form
			__UI.$userSearchForQuoteForm.find( "input, button" ).prop( "disabled", false );
		} )

} );





function makeAQuote ( quote ) {

	var ajaxRequest = $.ajax( {
		url: __OMEGA.settings[ "API Endpoint" ] + "/quotes",
		method: "POST",
		data: JSON.stringify( quote ),
		contentType: "application/json",
		dataType: "json",
		xhrFields: {
			withCredentials: true
		}
	} );

	return new Promise( function ( resolve, reject ) {

		ajaxRequest.done( function ( response ) {
			resolve();
		} )

		ajaxRequest.fail( function ( jqXHR, textStatus, e ) {
			var statusCode = -1;
			var message;
			if ( jqXHR.responseJSON ) {
				statusCode = jqXHR.responseJSON.statusCode;
				message = jqXHR.responseJSON.message;
			}
			else if ( typeof e == "object" ) {
				message = e.stack;
			}
			else {
				message = jqXHR.responseText;
			}
			reject( { code: statusCode, message: message } );
		} );

	} );

}
