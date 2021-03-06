<?php

?>

<!-- Check if the page is embedded in an iframe -->
<script type="text/javascript">

	function pageIsEmbedded () {

		/*
		 *
		 * When links in an iframe are opened on a new tab,
		 * 	things tend to loop infinitely if the iframe is checking whether it is embedded or not
		 * Hence, the following
		 *
		 * [reference](https://stackoverflow.com/a/7769187)
		 *
		 */
		// Check if the page is the top-most context
		try {
			return window.self !== window.top;
		} catch ( e ) {
			return true;
		}

	}
	if ( pageIsEmbedded() ) {
		var domHTMLElement = window.document.documentElement;
		domHTMLElement.className += " embedded js_embedded";
	}

</script>

<?php if ( $page == 'quote' or $page == 'pricing-individual' ) : ?>

	<script type="text/javascript">

		$( function () {
			$( ".js_user_bar" ).addClass( "show" );
		} )

	</script>

<?php endif; ?>


<section class="pricing-section">

	<!-- Notification (Toasts) Bar -->
	<div class="toaster row fixed">
		<div class="container">
			<div class="columns small-12 large-10 large-offset-1 xlarge-8 xlarge-offset-2 js_notification_section">
				<?php if ( $page == 'quote' ) : ?>
					<div class="toast row fill-dark gradient js_user_bar">
						<span class="welcome inline-middle h6 columns small-12 medium-6 large-8"><?php if ( ! empty( $user[ 'name' ] ) ) echo 'Welcome back, ' . $user[ 'name' ] ?></span>
						<span class="login inline-middle columns small-6 medium-3 large-2">
							<a class="button block fill-off-blue" href="login">
								<span>Login with</span>
								<img class="inline-middle" src="media/pricing/google-plus-light.svg">
							 </a>
						</span>
						<span class="logout inline-middle columns small-6 medium-3 large-2">
							<a class="button block fill-red" href="logout">
								<span>Sign Out</span>
								<img class="inline-middle" src="media/pricing/log-out-light.svg">
							</a>
						</span>
					</div>
				<?php endif; ?>
				<?php if ( $page == 'pricing-individual' ) : ?>
					<!-- If users aren't logged in yet, they'll have to wait a while -->
					<div class="toast row fill-dark gradient js_user_bar">
						<span class="welcome inline-middle h6 columns small-12 medium-6 large-8 js_message">Loading #<?php echo $unit ?> .....</span>
						<span class="login inline-middle columns small-12 medium-offset-3 medium-3 large-offset-2 large-2 hidden">
							<a class="button block fill-off-blue" href="pricing">
								<span>Click here</span>
							 </a>
						</span>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

<?php if ( $page != 'pricing-individual' ) : ?>
	<div class="landing row">
		<div class="landing-bg">
			<picture>
				<source srcset="account/media/landing-large.jpg<?php echo $ver ?>" media="(min-width: 640px)">
				<img class="block" src="account/media/landing-small.jpg<?php echo $ver ?>">
			</picture>
		</div>
		<div class="landing-content">
			<div class="container">
				<div class="text-light text-center columns small-12 large-10 large-offset-1 xlarge-8 xlarge-offset-2">
					<picture>
						<source srcset="account/media/logo-color.svg<?php echo $ver ?>" media="(min-width: 640px)">
						<img class="logo" src="account/media/logo-mobile.svg<?php echo $ver ?>">
					</picture>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>

	<!-- Pricing Engine Loading Stub -->
	<div class="page-loading-stub js_pricing_engine_loading_stub hidden">
		<?php if ( ( $page == 'quote' or $page == 'pricing-individual' ) and ! $userIsLoggedIn ) : ?>
			<div></div>
		<?php else : ?>
			<div>Initializing the Pricing Engine</div>
		<?php endif; ?>
	</div>
	<!-- END : Pricing Engine Loading Stub -->

	<!-- If page is the Individual Unit View and the user is not logged in, then show the login prompt -->
	<?php if ( $singleUnitIsBeingViewed and ! $userIsLoggedIn ) : ?>
		<!-- Phone Trap (Login Prompt) -->
		<div class="container">
			<div class="row">
				<div class="columns small-12 large-10 large-offset-1 xlarge-8 xlarge-offset-2" style="position: relative">
					<div class="page-phone-trap row js_page_login_prompt" data-loginner="Individual Unit View" data-context="Pricing Engine | Individual Unit View">
						<div class="trap-content columns small-12 medium-6">
							<div class="title h3 text-light bold pre-wrap js_login_prompt_heading">
								<!-- Content will be managed in JavaScript -->
							</div>
							<div class="phone show">
								<form class="loginner_form_phone">
									<div class="title h6 text-light block">Enter Mobile Number</div>
									<div class="phone-country-code button select">
										<select class="invisible-select js_phone_country_code" name="phone-country-code">
											<?php require __DIR__ . '/phone-country-codes.php'; ?>
										</select>
										<span class="visible-select label js_phone_country_code_label">IN +91</span>
									</div>
									<input class="label input inline-middle" type="text" name="phone-number" required>
									<button class="submit inline-middle" type="submit">Send</button>
									<div class="note label clearfix">
										<span class="error float-right text-red">&nbsp;</span>
									</div>
								</form>
							</div>
							<div class="otp">
								<form class="loginner_form_otp">
									<div class="title h6 text-light block">An OTP has been sent to this number.</div>
									<input class="label input inline-middle" type="text" name="otp" placeholder="Enter OTP" required>
									<button class="submit inline-middle" type="submit">Submit</button>
									<div class="note label clearfix">
										<span class="link float-left text-green js_reenter_phone" tabindex="-1">Enter Different Number</span>
										<span class="link float-right text-green hidden" tabindex="-1">Resend OTP</span>
									</div>
									<div class="disclaimer small text-dark js_privacy_disclaimer"></div>
								</form>
							</div>
						</div>
						<div class="trap-bg" style="background-image: url( account/media/phone-trap-bg.png )"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- END : Phone Trap (Login Prompt) -->
	<?php endif; ?>

	<div class="pricing row js_pricing_engine_content <?php if ( $page != 'quote' ) : ?>hidden<?php endif; ?>">
		<div class="container">

			<!-- Unit Filtration -->
			<section class="section-unit-search-and-organize">

				<!-- Loading Stub -->
				<div class="loading-stub js_loading_stub"></div>
				<!-- END : Loading Stub -->

				<!-- Content -->
				<div class="content js_content">

					<!-- Heading -->
					<div class="heading columns small-12 large-10 large-offset-1 xlarge-8 xlarge-offset-2">
						<?php if ( $page == 'quote' ) : ?>
							<div class="h1 text-center thin text-light" style="margin-top: 7rem;">Generate a Quote</div>
						<?php else : ?>
							<div class="h1 text-center thin text-light js_page_heading"></div>
						<?php endif; ?>
					</div>

					<!-- If it's the Quote page and the executive ain't logged in, do not show past the page heading -->
					<?php if ( $page == 'quote' and ! $userIsLoggedIn ) : ?>
						<?php die(); ?>
					<?php endif; ?>

					<!-- Unit Classification -->
					<div class="unit-filter columns small-12 large-10 large-offset-1 xlarge-8 xlarge-offset-2 js_unit_classification hidden">
						<!-- Markup will be managed by JavaScript -->
					</div>

					<!-- Units Available -->
					<div class="unit-available text-center columns small-12 large-10 large-offset-1 xlarge-8 xlarge-offset-2">
						<div class="h4 regular js_units_in_list">
							<!-- Markup will be managed by JavaScript -->
						</div>
					</div>

					<!-- Selected Unit Filters (Unit Filter Bubbles) -->
					<div class="unit-filter-bubbles columns small-12 large-10 large-offset-1 xlarge-8 xlarge-offset-2 js_unit_filter_bubbles hidden">
						<span class="title label bold text-uppercase text-neutral">Filter By :</span>
						<div class="bubbles js_selected_unit_filters"><!--
							Markup will be managed by JavaScript
						 --></div>
						<span class="empty label bold text-uppercase text-neutral js_default_filter_text"><!--
							Markup will be managed in JavaScript
						 --></span>
					</div>

					<!-- Unit Direct Search -->
					<div class="unit-search columns small-12 medium-6 large-5 large-offset-1 xlarge-4 xlarge-offset-2 js_unit_search hidden">
						<form class="js_search_form">
							<label>
								<span class="visuallyhidden">Search</span>
								<input class="search-field block js_search_query" type="search" placeholder="Search by House Number">
								<input class="clear js_clear_search" type="reset" val="clear">
							</label>
							<input class="search-button js_search_button" type="submit">
						</form>
					</div>

					<!-- Unit Order -->
					<div class="unit-order columns small-12 medium-6 large-5 xlarge-4 js_unit_sort_section hidden">
						<span class="title label bold text-uppercase text-neutral">Order By :</span>
						<select class="select label js_sort_by"></select>
					</div>

				</div>
				<!-- END : Content -->

			</section>



			<!-- Unit Listing -->
			<div class="unit-list columns small-12 large-10 large-offset-1 xlarge-8 xlarge-offset-2 js_unit_listing_section hidden" data-loginner="Unit Listing" data-context="Pricing Engine | Unit Listing">

<?php if ( ! $singleUnitIsBeingViewed ) : ?>
				<!-- Phone Trap (Login Prompt) -->
				<div class="phone-trap row js_unit_listing_login_prompt">
					<div class="trap-content columns small-12 medium-6">
						<div class="title h3 text-light bold pre-wrap js_login_prompt_heading">
							<!-- Content will be managed in JavaScript -->
						</div>
						<div class="phone js_phone_form_section">
							<form class="loginner_form_phone">
								<div class="title h6 text-light block">Enter Mobile Number</div>
								<div class="phone-country-code button select">
									<select class="invisible-select js_phone_country_code" name="phone-country-code">
										<?php require __DIR__ . '/phone-country-codes.php'; ?>
									</select>
									<span class="visible-select label js_phone_country_code_label">IN +91</span>
								</div>
								<input class="label input inline-middle" type="text" name="phone-number">
								<button class="submit inline-middle" type="submit">Send</button>
								<div class="note label clearfix">
									<span class="error float-right text-red">&nbsp;</span>
								</div>
							</form>
						</div>
						<div class="otp js_otp_form_section">
							<form class="loginner_form_otp">
								<div class="title h6 text-light block">An OTP has been sent to this number.</div>
								<input class="label input inline-middle" type="text" name="otp" placeholder="Enter OTP">
								<button class="submit inline-middle" type="submit">Submit</button>
								<div class="note label clearfix">
									<span class="link float-left text-green js_reenter_phone" tabindex="-1">Enter Different Number</span>
									<span class="link float-right text-green hidden" tabindex="-1">Resend OTP</span>
								</div>
								<div class="disclaimer small text-dark js_privacy_disclaimer"></div>
							</form>
						</div>
					</div>
					<div class="trap-bg" style="background-image: url( account/media/phone-trap-bg.png )"></div>
					<div class="trap-close h3 fill-dark js_close_unit_listing_login_prompt" tabindex="-1">&times;</div>
				</div>
				<!-- END : Phone Trap (Login Prompt) -->
<?php endif; ?>

				<!-- Content -->
				<div class="content js_content invisible" data-login=".js_unit_listing_login_prompt" display="width: 100%">
					<div class="slick js_unit_listing">
						<!-- Markup will be managed by JavaScript -->
					</div>
				</div>
				<!-- END : Content -->

			</div>



			<!-- Unit Detailed View -->
			<div class="unit-details columns small-12 large-10 large-offset-1 xlarge-8 xlarge-offset-2 js_section_unit_details_and_mods" style="display: none">

				<?php if ( $page == 'quote' ) : ?>
					<!-- Unit Link Form -->
					<div class="emi detail-section fill-off-dark js_section">
						<div class="customer-id-form">
							<div class="detail-row clearfix form row">
								<div class="form-item columns small-12 medium-6">
									<form class="js_unit_link_form">
										<label>
											<span class="label">Generate Unit Link for</span>
											<input class="block" type="text" name="user-id" required>
											<span class="symbol fill-off-dark">UID</span>
										</label>
										<button class="button search-button" type="submit">Create</button>
									</form>
								</div>
								<div class="form-item columns small-12 medium-6">
									<label>
										<span class="label js_unit_and_customers_names"></span>
										<a class="block button secondary no-overflow unit-link js_unit_link"></a>
									</label>
								</div>
							</div>
						</div>
					</div><!-- End: Unit Link Form -->
				<?php endif; ?>

				<!-- Content -->
				<div class="content js_content hidden">
					<!-- Markup will be managed by JavaScript -->
				</div>
				<!-- END : Content -->

				<!-- Action -->
				<div class="action detail-section <?php if ( $page != 'quote' ) echo 'hidden' ?>">

					<?php if ( $page != 'quote' ) : ?>

						<!-- Standard Enquiry Form -->
						<div class="standard-enquiry-form hidden js_enquiry_form_section">
							<div class="detail-row clearfix">
								<div class="title h3 bold text-center">Get Detailed Price</div>
							</div>
							<div class="detail-row clearfix">
								&nbsp;
							</div>
							<div class="detail-row clearfix form row">
								<form class="js_enquiry_form">
									<div class="form-item columns small-12 medium-6">
										<label>
											<span class="label">Full Name</span>
											<input class="block" type="text" name="name" required>
											<span class="symbol"><img src="media/pricing/person-light.svg"></span>
										</label>
									</div>
									<div class="form-item columns small-12 medium-6">
										<label>
											<span class="label">Email ID</span>
											<input class="block" type="email" name="email" required>
											<span class="symbol"><img src="media/pricing/email-light.svg"></span>
										</label>
									</div>
									<div class="form-item columns small-12 medium-6">
										<label>
											<span class="label">Mobile Number</span>
											<input class="block" type="text" name="phone" disabled>
											<span class="symbol"><img src="media/pricing/phone-light.svg"></span>
										</label>
									</div>
									<div class="form-item columns small-12 medium-6">
										<label>
											<span class="label invisible">Submit</span>
											<button class="block button primary" type="submit" data-init="Download PDF" data-post="Enquiry sent">Download PDF</button>
										</label>
									</div>
								</form>
							</div>
						</div>

					<?php else: ?>

						<!-- Create Quote Form -->
						<div class="customer-id-form js_section">
							<div class="detail-row clearfix form row">
								<div class="form-item columns small-12 medium-6">
									<form class="js_user_search_for_quote_form">
										<label>
											<span class="label">Create a Quote for</span>
											<span class="label light text-neutral js_customers_names">
												<!-- Markup will be managed in JavaScript -->
											</span>
											<input class="block" type="text" name="user-id" required>
											<span class="symbol">UID</span>
										</label>
										<button class="button search-button" type="submit">Search</button>
									</form>
								</div>
								<div class="form-item columns small-12 medium-6">
									<label>
										<span class="label invisible">Submit</span>
										<button class="block button primary js_create_quote" data-text-actionable="Send Deal to Zoho" disabled>Send Deal to Zoho</button>
									</label>
								</div>
							</div>
						</div><!-- End: Create Quote Form -->
					<?php endif; ?>

				</div><!-- END: Action -->

				<!-- Unit EMI Calculator -->
				<div class="emi detail-section fill-off-dark js_section_emi_calculator hidden">

					<div class="detail-row clearfix">
						<div class="title h3 bold text-center">Calculate EMI</div>
					</div>
					<div class="detail-row clearfix">
						&nbsp;
					</div>
					<!-- Calculator Form -->
					<div class="detail-row clearfix form row">
						<form>
							<!-- Down Payment -->
							<div class="form-item columns small-12 medium-6">
								<label>
									<span class="label">Down Payment</span>
									<input class="block js_down_payment" type="text" name="down-payment">
									<span class="symbol">₹</span>
									<span class="suffix js_down_payment_percentage"></span>
								</label>
							</div>
							<!-- Loan Amount -->
							<div class="form-item columns small-12 medium-6">
								<label>
									<span class="label">Loan Amount</span>
									<input class="block js_loan_amount" type="text" name="loan-amount">
									<span class="symbol">₹</span>
									<!-- <span class="suffix js_loan_amount_percentage"></span> -->
								</label>
							</div>
							<!-- Tenure -->
							<div class="form-item columns small-12 medium-6">
								<label>
									<span class="label">Tenure</span>
									<input class="block js_tenure" type="text" name="tenure">
									<span class="symbol"><img src="media/pricing/calendar-light.svg"></span>
									<span class="suffix">Months</span>
								</label>
							</div>
							<!-- Interest Rate -->
							<div class="form-item columns small-12 medium-6">
								<label>
									<span class="label">Interest Rate</span>
									<input class="block js_interest_rate" type="text" name="interest-rate">
									<span class="symbol">%</span>
									<span class="suffix">Per Annum</span>
								</label>
							</div>
							<!-- Submit button -->
							<!-- <div class="form-item columns small-12 medium-6">
								<label>
									<span class="label invisible">Submit</span>
									<button class="block button secondary" type="submit">Calculate</button>
								</label>
							</div> -->
						</form>
					</div><!-- END: Calculator Form -->

					<!-- Figures -->
					<div class="detail-row clearfix">
						<hr class="dashed">
					</div>
					<div class="detail-row clearfix">
						<div class="title h5 regular text-light float-left">Estimated EMI</div>
						<div class="value h5 regular text-light float-right js_estimated_emi"></div>
					</div>
					<div class="detail-row clearfix">
						<hr class="dashed">
					</div>
					<div class="detail-row clearfix hidden">
						<div class="title label regular text-neutral float-left">Loan Amount</div>
						<div class="value label regular text-neutral float-right js_principal_amount"></div>
					</div>
					<div class="detail-row clearfix hidden">
						<div class="title label regular text-neutral float-left">Interest Amount</div>
						<div class="value label regular text-neutral float-right js_interest_amount"></div>
					</div>
					<div class="detail-row clearfix hidden">
						<div class="title label regular text-light float-left">Total Amount Payable</div>
						<div class="value label regular text-light float-right js_total_amount_payable"></div>
					</div>

				</div>

			</section>




		</div>
	</div>

</section>


<!-- Templates -->
<?php require __DIR__ . '/templates.hbs' ?>


<!-- Global state -->
<script type="text/javascript">

	// Establish global state
	window.__OMEGA = window.__OMEGA || { };
	__OMEGA.unitData = { };

	// Initialize the API endpoint
	__OMEGA.settings = __OMEGA.settings || { };
	__OMEGA.settings.apiEndpoint = __OMEGA.settings.apiEndpoint || location.origin.replace( /\/+$/, "" ) + "/omega";
	if ( ! __envProduction ) {
		__OMEGA.settings.apiEndpoint = "http://omega.api.192.168.0.207.xip.io";
	}

</script>

<!-- JS Modules -->
<script type="text/javascript" src="js/modules/util.js<?= $ver ?>"></script>
<?php if ( $page == 'quote' ) : ?>
	<script type="text/javascript" src="js/modules/Pricing Engine/executive-user.js<?= $ver ?>"></script>
<?php else : ?>
	<script type="text/javascript" src="js/modules/Pricing Engine/regular-user.js<?= $ver ?>"></script>
<?php endif; ?>
<script type="text/javascript" src="js/modules/user.js<?= $ver ?>"></script>

<script type="text/javascript" src="plugins/SheetJS/xlsx-core-v0.13.0.min.js"></script>
<script type="text/javascript" src="plugins/xlsx-calc/xlsx-calc-v0.4.1.js"></script>
<script type="text/javascript" src="js/modules/Pricing Engine/spreadsheet-formulae.js<?= $ver ?>"></script>
<!-- <script type="text/javascript" src="plugins/handlebars/handlebars-v5.0.0a1.min.js"></script> -->
<script type="text/javascript" src="plugins/handlebars/handlebars-v4.0.11.min.js"></script>
<script type="text/javascript" src="plugins/lodash/lodash-v4.17.5.custom.min.js"></script>
<script type="text/javascript" src="js/modules/Pricing Engine/util.js<?= $ver ?>"></script>
<script type="text/javascript" src="js/modules/Pricing Engine/UI/notifications.js<?= $ver ?>"></script>
<script type="text/javascript" src="js/modules/Pricing Engine/UI/unit-filtration.js<?= $ver ?>"></script>
<script type="text/javascript" src="js/modules/Pricing Engine/UI/unit-search.js<?= $ver ?>"></script>
<script type="text/javascript" src="js/modules/Pricing Engine/UI/unit-sort.js<?= $ver ?>"></script>
<script type="text/javascript" src="js/modules/Pricing Engine/UI/unit-listing.js<?= $ver ?>"></script>
<script type="text/javascript" src="plugins/slick/slick.min.js"></script>
<script type="text/javascript" src="js/modules/Pricing Engine/UI/unit-detailed-view.js<?= $ver ?>"></script>
<script type="text/javascript" src="js/modules/Pricing Engine/UI/emi-calculator.js<?= $ver ?>"></script>
<?php if ( $page != 'quote' ) : ?>
<script type="text/javascript" src="js/modules/Pricing Engine/UI/enquiry-form.js<?= $ver ?>"></script>
<?php else : ?>
<script type="text/javascript" src="js/modules/Pricing Engine/UI/quote-form.js<?= $ver ?>"></script>
<script type="text/javascript" src="js/modules/Pricing Engine/UI/unit-link-form.js<?= $ver ?>"></script>
<?php endif; ?>
<script type="text/javascript" src="js/modules/Pricing Engine/logic/main.js<?= $ver ?>"></script>
<script type="text/javascript" src="js/modules/Pricing Engine/UI/main.js<?= $ver ?>"></script>
<?php if ( ! $singleUnitIsBeingViewed ) : ?>
<script type="text/javascript" src="js/modules/Pricing Engine/logic/unit-listing.js<?= $ver ?>"></script>
<script type="text/javascript" src="js/modules/Pricing Engine/logic/unit-search.js<?= $ver ?>"></script>
<script type="text/javascript" src="js/modules/Pricing Engine/logic/unit-sort.js<?= $ver ?>"></script>
<?php endif; ?>
<script type="text/javascript" src="js/modules/Pricing Engine/logic/unit-filters.js<?= $ver ?>"></script>
<script type="text/javascript" src="js/modules/Pricing Engine/logic/unit-detailed-view.js<?= $ver ?>"></script>
<?php if ( $page != 'quote' ) : ?>
	<script type="text/javascript" src="js/modules/login-trap.js<?= $ver ?>"></script>
	<script type="text/javascript" src="js/modules/tracking.js<?= $ver ?>"></script>
<?php endif; ?>

<script type="text/javascript">

<?php if ( ! $singleUnitIsBeingViewed ) : ?>
// If it is the general Pricing Engine page

	// Further establish global state
	$( function () {
		var OMEGA = window.__OMEGA;
	} );
	$( document ).on( "pricing-engine/ready", function () {
		$( document ).trigger( "pricing-engine/render" );
	} );

<?php elseif ( ! $userIsLoggedIn ) : ?>
// If it is the Individual Unit View, but the user is not logged in

	$( document ).on( "user/logged-in", function () {
		$( document ).trigger( "unit/view", {
			unitData: {
				Unit: "<?php echo $unit ?>"
			}
		} );
		// Unhide all the relevant sections of the pricing engine
		$( ".js_section_unit_details_and_mods" ).removeClass( "hidden" );
		$( ".js_pricing_engine_content" ).removeClass( "hidden" );
		$( ".js_pricing_engine_loading_stub" ).addClass( "hidden" );
	} );

	<?php if ( empty( $userId ) ) : ?>

		// If no user ID has been provided, then show the Login Prompt
		$( ".js_page_login_prompt" ).addClass( "show" );

	<?php else : ?>

		// If a user id has been provided, attempt to log in the user
		getUser( "<?php echo $userId ?>" )
			.then( function ( user ) {
				// Store the user on the side
				__OMEGA.user = user;
				// If the user exists, log the user in
				loginUser( user );
				// Then, close the login prompt
				Loginner.prompts[ "Individual Unit View" ].onLogin( user );
			} );

	<?php endif; ?>

<?php else : ?>
// If it is the Individual Unit View, but the user **is** logged in

	$( document ).on( "pricing-engine/ready", function () {

		var unit = "<?php echo $unit ?>";
		var unitIndex = __OMEGA.units
			.filter( function ( unit ) {
				return unit.Availability;
			} )
			.findIndex( function ( currentUnit ) {
				return currentUnit.Unit == unit;
			} );

		if ( unitIndex == -1 )
			window.location.href = window.location.href.replace( /\/[^/]+$/, "" );

		$( document ).trigger( "unit/view", {
			context: "single-view",
			unitData: {
				Unit: "<?php echo $unit ?>"
			}
		} );
		// Unhide all the relevant sections of the pricing engine
		$( ".js_section_unit_details_and_mods" ).removeClass( "hidden" );
		$( ".js_pricing_engine_content" ).removeClass( "hidden" );
		$( ".js_pricing_engine_loading_stub" ).addClass( "hidden" );
	} );

<?php endif; ?>

<?php if ( $page == 'pricing-individual' ) : ?>

	// Once the Individual Unit View page has loaded ( i.e. the Unit Details are shown ), load in the navigation bar content
	$( document ).on( "unit/view", function () {
		$( ".js_user_bar" ).find( ".js_message" ).text( "To see all available " + __OMEGA.settings[ "Term for \"Unit\"" ] + "s" );
		$( ".js_user_bar" ).find( ".hidden" ).removeClass( "hidden" );
	} );

<?php endif; ?>

	$( function () {

		// Build the user object
		isUserLoggedIn();
		$( document ).trigger( "spreadsheet/fetch" );

	} );

	/*
	 *
	 * Extend the XLSX-CALC library with s'more formulae
	 *
	 */
	XLSX_CALC.import_functions( spreadsheetFormulae );

</script>
