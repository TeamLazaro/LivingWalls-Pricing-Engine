
/*
 *
 * On submitting the action form ( i.e. user details )
 *
 */
$( document ).on( "submit", ".js_enquiry_form", function ( event ) {

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
	$form.find( "input, select, button" ).prop( "disabled", true );

	/* -----
	 * Pull the data from the form
	 ----- */
	// name
	$name = $form.find( "[ name = 'name' ]" );
	// email
	$email = $form.find( "[ name = 'email' ]" );

	/* -----
	 * Validate the data
	 ----- */
	// Remove any prior "error"s
	$form.find( ".js_error" ).removeClass( "js_error" );
	// Name
	if ( ! $name.val().trim() ) {
		$name.addClass( "js_error" );
		$name.parent().addClass( "validation-error" );
		alert( "Please enter your name." );
	}
	// If the form has even one error ( i.e. validation issue )
	// do not proceed
	if ( $form.find( ".js_error" ).length ) {
		$form.find( "input, select, button" ).prop( "disabled", false );
		return;
	}

	/* -----
	 * Process and Assemble the data
	 ----- */
	var names = $name.val().split( /\s+/ );
	var firstName = names[ 0 ];
	var lastName = names.slice( 1 ).join( " " );
	var emailAddress = $email.val();

	var _id = __OMEGA.user._id;
	var project = __OMEGA.settings.Project;
	var userData = {
		"First Name": firstName,
		"Last Name": lastName,
		"Email": emailAddress
	}

	var meta = __OMEGA.settings;
	var crm = getDataFromSheet( __OMEGA.workbook.Sheets[ "Output (CRM)" ] );
	var mail = getDataFromSheet( __OMEGA.workbook.Sheets[ "Output (Mail)" ] );

	var enquiry = {
		meta: meta,
		crm: crm,
		mail: mail,
		unit: __OMEGA.unitData
		// ...
	};

	/* -----
	 * Store the data on the side
	 ----- */
	__OMEGA.user = Object.assign( __OMEGA.user, {
		firstName: firstName,
		lastName: lastName,
		email: emailAddress
	} );
	__OMEGA.user.name = firstName;
	if ( lastName )
		__OMEGA.user.name += " " + lastName;

	enquiry.user = __OMEGA.user;


	/* -----
	 * Process the data
	 ----- */
	// Update the user
	updateUser( _id, project, userData )
		.catch( function ( e ) {
			alert( e.message )
		} )
		// Then, make the enquiry
		.then( function () {
			var user = __OMEGA.user;
			var inputParameters = Object.assign( { }, __OMEGA.userInput.unitData, {
				Phone: user.phoneNumber,
				Name: user.name,
				Email: user.email
			} );
			enquiry.timestamp = getDateAndTimeStamp();
			inputParameters[ "Timestamp" ] = enquiry.timestamp;
			// Run the computations through the pricing engine
			computeUnitData( inputParameters );
			return getComputedUnitData();
		} )
		.then( function ( points ) {
			enquiry.pdf = points;
			return makeAnEnquiry( enquiry );
		} )
		.then( function () {
			/* -----
			 * Re-enable the form
			 ----- */
			$form.find( "input, select, button" ).prop( "disabled", false );
			$form.find( "[ name = 'phone' ]" ).prop( "disabled", true );
		} )
		.catch( function ( e ) {
			console.log( e.message );
		} );

} );

function makeAnEnquiry ( enquiry ) {

	var ajaxRequest = $.ajax( {
		url: __OMEGA.settings[ "API Endpoint" ] + "/enquiries",
		method: "POST",
		data: JSON.stringify( enquiry ),
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