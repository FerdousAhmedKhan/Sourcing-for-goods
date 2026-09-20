<?php
/**
 * Plugin Name: Sourcing for Goods GEO and Public Contact
 * Description: Adds conservative homepage GEO structure and enforces approved contact and exact claim corrections in frontend HTML.
 * Version: 2026.08.11.7
 * Author: Ezzogenics
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const SFG_GEO_HOME_PAGE_ID = 11;
const SFG_GEO_CANONICAL    = 'https://www.sourcingforgoods.com/';
const SFG_GEO_OPERATOR_ID  = 'https://ezzogenics.com/#organization';
const SFG_GEO_BCA_RECORD   = 'https://www.bca.gov.sg/eBACS/BCA_DIRECTORY/Company/CompanyDetails?uenNo=201208665Z';
const SFG_GEO_CUSTOMS_INFO = 'https://www.customs.gov.sg/doing-business/import-operations/import-procedures/obtain-a-customs-import-permit/';

/**
 * Identify a normal public frontend request before any output is buffered.
 *
 * The final response callback separately requires a complete HTML document and
 * an HTML content type. These request guards ensure admin, AJAX, REST/JSON and
 * feed responses are never candidates for public-contact replacement.
 */
function sfg_geo_is_public_html_request() {
	if ( is_admin() || wp_doing_ajax() || wp_is_json_request() || is_feed() ) {
		return false;
	}

	return true;
}

/**
 * Limit every GEO/content/schema change to the known production homepage.
 */
function sfg_geo_is_target_homepage() {
	if ( ! sfg_geo_is_public_html_request() ) {
		return false;
	}

	return is_front_page() && SFG_GEO_HOME_PAGE_ID === (int) get_queried_object_id();
}

/**
 * Replace unsupported title superlatives with a descriptive title.
 */
function sfg_geo_document_title( $title ) {
	if ( ! sfg_geo_is_target_homepage() ) {
		return $title;
	}

	return 'Sourcing for Goods | Product Sourcing Enquiries from China';
}
add_filter( 'pre_get_document_title', 'sfg_geo_document_title', 20 );

/**
 * Output one homepage description and a deliberately small schema graph.
 *
 * No unsupported licence, review, rating, delivery promise, price promise or
 * business statistic is emitted. The legal operator name, UEN, registered
 * address and current BCA registry fields are included only because the linked
 * official record was checked on the evidence date shown in visible content.
 */
function sfg_geo_render_homepage_head() {
	if ( ! sfg_geo_is_target_homepage() ) {
		return;
	}

	$description = 'Product and building material sourcing enquiries from China. Share specifications, quantity, destination and timeline so the request can be reviewed.';
	$logo        = 'https://www.sourcingforgoods.com/wp-content/uploads/2024/06/Ezzogenics-sourcing-goods.png';
	$language    = get_bloginfo( 'language' );
	$faq_schema  = array();
	foreach ( sfg_geo_faq_items() as $faq ) {
		$faq_schema[] = array(
			'@type'          => 'Question',
			'name'           => $faq['question'],
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => $faq['answer'],
			),
		);
	}
	$graph       = array(
		'@context' => 'https://schema.org',
		'@graph'   => array(
			array(
				'@type' => 'Organization',
				'@id'   => SFG_GEO_CANONICAL . '#organization',
				'name'  => 'Sourcing for Goods',
				'url'   => SFG_GEO_CANONICAL,
				'logo'  => array(
					'@type' => 'ImageObject',
					'url'   => $logo,
				),
				'parentOrganization' => array( '@id' => SFG_GEO_OPERATOR_ID ),
			),
			array(
				'@type'      => 'Organization',
				'@id'        => SFG_GEO_OPERATOR_ID,
				'name'       => 'Ezzogenics Pte Ltd',
				'legalName'  => 'EZZOGENICS PTE. LTD.',
				'url'        => 'https://ezzogenics.com/',
				'telephone'  => '+65 9632 0750',
				'address'    => array(
					'@type'           => 'PostalAddress',
					'streetAddress'   => '15 Kaki Bukit Road 4, #01-44, Bartley Biz Centre',
					'addressLocality' => 'Singapore',
					'postalCode'      => '417808',
					'addressCountry'  => 'SG',
				),
				'identifier' => array(
					'@type'      => 'PropertyValue',
					'propertyID' => 'Singapore UEN',
					'value'      => '201208665Z',
					'url'        => SFG_GEO_BCA_RECORD,
				),
				'sameAs'     => array( SFG_GEO_BCA_RECORD ),
			),
			array(
				'@type'     => 'WebSite',
				'@id'       => SFG_GEO_CANONICAL . '#website',
				'name'      => 'Sourcing for Goods',
				'url'       => SFG_GEO_CANONICAL,
				'inLanguage'=> $language,
				'publisher' => array( '@id' => SFG_GEO_OPERATOR_ID ),
				'about'     => array( '@id' => SFG_GEO_CANONICAL . '#organization' ),
			),
			array(
				'@type'      => 'Service',
				'@id'        => SFG_GEO_CANONICAL . '#sourcing-enquiry-service',
				'name'       => 'Product sourcing enquiry and coordination',
				'serviceType'=> 'Product sourcing enquiry and coordination',
				'description'=> 'A website for submitting product and building material sourcing enquiries from China. Requests proceed through a written scope and supplier quotation or availability review. Inspection, warehousing and shipping coordination apply only when separately commissioned in writing.',
				'url'        => SFG_GEO_CANONICAL,
				'provider'   => array( '@id' => SFG_GEO_OPERATOR_ID ),
				'brand'      => array( '@id' => SFG_GEO_CANONICAL . '#organization' ),
			),
			array(
				'@type'      => 'FAQPage',
				'@id'        => SFG_GEO_CANONICAL . '#buyer-faq',
				'url'        => SFG_GEO_CANONICAL . '#sfg-buyer-faq',
				'inLanguage' => $language,
				'mainEntity' => $faq_schema,
			),
		),
	);

	echo "\n<meta name=\"description\" content=\"" . esc_attr( $description ) . "\" />\n";
	echo '<meta property="og:title" content="' . esc_attr( sfg_geo_document_title( '' ) ) . '" />' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
	echo '<meta property="og:url" content="' . esc_url( SFG_GEO_CANONICAL ) . '" />' . "\n";
	echo '<meta property="og:type" content="website" />' . "\n";
	echo '<script type="application/ld+json" id="sfg-geo-structured-data">';
	echo wp_json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT );
	echo '</script>' . "\n";
}
add_action( 'wp_head', 'sfg_geo_render_homepage_head', 5 );

/**
 * Homepage copy replacements. Each one removes an unsupported guarantee,
 * superlative or clear language error while preserving the current design.
 */
function sfg_geo_heading_replacements() {
	return array(
		'SOURCE AND IMPORT BUILDING MATERIALS FROM CHINA?' => 'Product and building material sourcing enquiries from China',
		'Tell us details of products you need.' => 'Tell us what you need to source.',
		'We will send you quotation immediately' => 'Share specifications, quantity, destination and timeline for review.',
		'China Best Sourcing Agent, One-stop Sourcing Service.' => 'Product sourcing enquiry and coordination.',
		'Why we are the best?' => 'Sourcing support by request',
		'Warehousing' => 'Optional warehousing coordination',
		'After-sales Services' => 'Post-order review by request',
		'Products We Source' => 'Product categories for sourcing enquiries',
		'Light' => 'Lighting',
		'Led lights, Crystal Chandeliers, lamps and so on.' => 'LED lights, chandeliers and lamps.',
		'Porcelain porcelain tiles, glazed tiles, rustic tiles and so on.' => 'Porcelain, glazed and rustic tiles.',
		'Windows and Door' => 'Windows and doors',
		'Reputable interior and exterior windows and doors.' => 'Interior and exterior windows and doors.',
		'You can focus on business, and we handle everything until you get products. Working with us, you will save both time and money. let us know what you want to IMPORT' => 'Tell us what you need. The sourcing scope, responsibilities, fees and exclusions should be confirmed in writing before work begins.',
		'Our Buisiness' => 'Our business',
		'Copyright © EZZOGENICS. All Right Reserved.' => 'Copyright © EZZOGENICS. All rights reserved.',
	);
}

/**
 * Exact paragraph/widget replacements. These are deliberately separate from
 * heading replacements so short heading labels such as "Light" can never be
 * replaced inside unrelated body copy or attributes.
 */
function sfg_geo_text_replacements() {
	return array(
		'As China’s best sourcing agent, Ezzogenics has built a strong reputation over the years for providing high-quality building materials. Whether you are looking for furniture, kitchen cabinets, floor tiles, bathroom fittings, windows, doors, or lights, we ensure a hassle-free importing experience. Regardless of the order size, we handle payment, negotiations, quality checks, consolidation, and shipment, allowing you to import with ease and confidence.' => 'Start by submitting the product specification, quantity, destination and required timing. The request is reviewed before a written scope is confirmed. Supplier quotations and current availability are then reviewed for that request. Inspection, warehousing or shipping coordination are optional services and apply only when separately commissioned and recorded in writing.',
		'You can focus on business, and we handle everything until you get products. Working with us, you will save both time and money. let us know what you want to IMPORT' => 'Tell us what you need. The sourcing scope, responsibilities, fees and exclusions should be confirmed in writing before work begins.',
		'We strictly follow up your orders and do quality control. Reports will be sent to you after inspection.' => 'Inspection is not automatic. If it is separately commissioned, the inspection scope, timing and reporting requirements are confirmed in writing for that order.',
		'All exporting procedures such as container loading,customs declaration,shipping and so on will arranged by us.' => 'Loading, shipping and customs-related responsibilities are confirmed in writing for each order. Singapore import permits must be handled by the importer or an appropriately registered Declaring Agent.',
		'Email us what you want to purchase, we will offer the quotation for your reference.' => 'Email the product details so the request and any supplier quotation can be reviewed.',
		'Guidance about Chinese factories, markets and goods importation can be provided for your further understanding.' => 'Review product, factory and import questions against the written sourcing scope and official records.',
		'We arrange warehouse for your goods if necessary.' => 'Warehousing coordination is optional and applies only when separately commissioned in writing.',
		'No Upfront Charges.' => 'Fees and payment terms are confirmed in writing.',
		'Ezzogenics Will Guide You How To Import From China Step By Step' => 'Review the sourcing process before placing an order.',
		'Get a Free Quote' => 'Request a sourcing review',
		'Ezzogenics helps customers source many kinds of products and handle exportation for years, There is lots of practical and useful information, that comes from our experiences. Feel free to contact me for any queries.' => 'Use the sourcing checklist and official import records to review each request. Product scope, responsibilities and optional services must be confirmed in writing.',
	);
}

/**
 * Neutralise only the exact homepage strings observed in the production HTML.
 * This is also used by the final-document fallback when Elementor serves a
 * cached widget, so the allowlisted text cannot bypass the widget filter.
 */
function sfg_geo_enforce_homepage_claims( $html ) {
	foreach ( sfg_geo_text_replacements() as $from => $to ) {
		$html = sfg_geo_replace_exact_text( $html, $from, $to );
	}

	return $html;
}

/**
 * Questions and answers are defined once so the visible FAQ and FAQPage graph
 * cannot drift apart.
 */
function sfg_geo_faq_items() {
	return array(
		array(
			'question' => 'When is a quotation provided?',
			'answer'   => 'A quotation is prepared only after the requested product, quantity, destination, timing and sourcing scope have been reviewed.',
		),
		array(
			'question' => 'Are price and delivery guaranteed?',
			'answer'   => 'No. Price, supplier availability and delivery timing are not guaranteed until they are confirmed in writing for the individual request.',
		),
		array(
			'question' => 'Who handles a Singapore import permit?',
			'answer'   => 'The importer must submit the permit through TradeNet or appoint an appropriately registered Declaring Agent to apply on its behalf. This website does not claim that Sourcing for Goods or Ezzogenics is a Declaring Agent.',
		),
		array(
			'question' => 'Are inspection and shipping included automatically?',
			'answer'   => 'No. Inspection, warehousing and shipping coordination apply only when they are separately commissioned and recorded in writing for the order.',
		),
	);
}

/**
 * Replace one exact heading while retaining Elementor classes and attributes.
 */
function sfg_geo_replace_heading( $html, $from, $to, $new_tag = '' ) {
	$pattern = '~<(h[1-6])([^>]*)>\s*' . preg_quote( $from, '~' ) . '\s*</\\1>~iu';

	$result = preg_replace_callback(
		$pattern,
		static function ( $matches ) use ( $to, $new_tag ) {
			$tag = $new_tag ? $new_tag : strtolower( $matches[1] );
			return '<' . $tag . $matches[2] . '>' . esc_html( $to ) . '</' . $tag . '>';
		},
		$html,
		1
	);

	return null === $result ? $html : $result;
}

/**
 * Replace one complete text string, including the HTML entity forms Elementor
 * commonly emits for apostrophes. No fuzzy or partial matching is used.
 */
function sfg_geo_replace_exact_text( $html, $from, $to ) {
	$variants = array(
		$from,
		esc_html( $from ),
		str_replace( ' ', '&nbsp;', $from ),
		str_replace( ' ', '&#32;', $from ),
		str_replace( '’', '&#8217;', $from ),
		str_replace( '’', '&rsquo;', $from ),
		str_replace( '’', '&#x2019;', $from ),
		str_replace( "'", '&#039;', $from ),
		str_replace( "'", '&#39;', $from ),
	);

	foreach ( array_unique( $variants ) as $variant ) {
		$html = str_replace( $variant, esc_html( $to ), $html );
	}

	return $html;
}

/**
 * Exact legacy claims allowed to be neutralised in complete public HTML.
 *
 * These sources are full visible sentences observed on the production pages.
 * No keyword, substring or fuzzy matching is used. The replacement records a
 * quote-first scope without promising price, timing, availability or optional
 * coordination services.
 */
function sfg_geo_public_claim_replacements() {
	return array(
		'You can focus on business, and we handle everything until you get products. Working with us, you will save both time and money. let us know what you want to IMPORT' => 'Tell us what you need. Pricing, supplier availability and timing are not guaranteed until confirmed in writing for the request. Inspection, warehousing and shipping coordination apply only when separately commissioned in the written scope.',
		'You must arrange all exporting procedures such as transportation, customs declaration, shipment, documents and so on.' => 'Export transport, customs declaration, shipment and document responsibilities must be stated in the written quotation and agreed scope. Shipping coordination is not included unless separately commissioned in writing.',
		'Get a Free Quote' => 'Request a sourcing review',
		'Ezzogenics helps customers source many kinds of products and handle exportation for years, There is lots of practical and useful information, that comes from our experiences. Feel free to contact me for any queries.' => 'Use the sourcing checklist and official import records to review each request. Product scope, responsibilities and optional services must be confirmed in writing.',
	);
}

/**
 * Replace only the exact audited legacy claims above.
 */
function sfg_geo_enforce_public_claims( $html ) {
	foreach ( sfg_geo_public_claim_replacements() as $from => $to ) {
		$html = sfg_geo_replace_exact_text( $html, $from, $to );
	}

	return $html;
}

/**
 * Enforce the approved David-only public number in rendered public HTML. The
 * BCA directory phone is intentionally not copied into public markup or schema.
 *
 * URL forms are replaced before display forms so telephone and WhatsApp links
 * remain valid. The substitutions are an explicit allowlist of the retired
 * company-contact formats; this is not a fuzzy number rewrite.
 */
function sfg_geo_enforce_public_contact( $html ) {
	$url_replacements = array(
		'tel:+6569683098'              => 'tel:+6596320750',
		'tel:6569683098'               => 'tel:+6596320750',
		'tel:69683098'                 => 'tel:+6596320750',
		'https://wa.me/6569683098'     => 'https://wa.me/6596320750',
		'http://wa.me/6569683098'      => 'https://wa.me/6596320750',
		'wa.me/6569683098'             => 'wa.me/6596320750',
		'phone=%2B6569683098'          => 'phone=%2B6596320750',
		'phone=6569683098'             => 'phone=6596320750',
		'phone=69683098'               => 'phone=6596320750',
	);
	$html             = str_ireplace( array_keys( $url_replacements ), array_values( $url_replacements ), $html );

	$display_replacements = array(
		'+65&nbsp;6968&nbsp;3098' => '+65 9632 0750',
		'+65&#160;6968&#160;3098' => '+65 9632 0750',
		'+65&#xA0;6968&#xA0;3098' => '+65 9632 0750',
		'+65 6968 3098'          => '+65 9632 0750',
		'+65-6968-3098'          => '+65 9632 0750',
		'+6569683098'             => '+65 9632 0750',
		'65 6968 3098'            => '+65 9632 0750',
		'6569683098'               => '+65 9632 0750',
		'6968 3098'                => '+65 9632 0750',
		'6968-3098'                => '+65 9632 0750',
		'69683098'                 => '+65 9632 0750',
	);
	$html = str_replace( array_keys( $display_replacements ), array_values( $display_replacements ), $html );

	return $html;
}

/**
 * Apply the approved public contact and audited claim corrections to a final
 * public HTML document.
 *
 * Elementor can serve cached header/footer template markup after individual
 * widget filters run. This callback therefore works on the complete rendered
 * document, but only after the request and content-type checks below. Homepage
 * GEO/content/schema behavior remains guarded by sfg_geo_is_target_homepage().
 */
function sfg_geo_filter_final_public_html( $html ) {
	if ( ! sfg_geo_is_public_html_request() || ! is_string( $html ) || '' === $html ) {
		return $html;
	}

	if ( false === stripos( $html, '<html' ) || false === stripos( $html, '<body' ) ) {
		return $html;
	}

	foreach ( headers_list() as $header ) {
		if ( 0 !== stripos( $header, 'Content-Type:' ) ) {
			continue;
		}

		$content_type = strtolower( trim( substr( $header, strlen( 'Content-Type:' ) ) ) );
		if ( false === strpos( $content_type, 'text/html' ) && false === strpos( $content_type, 'application/xhtml+xml' ) ) {
			return $html;
		}
	}

	$html = sfg_geo_enforce_public_contact( $html );
	if ( sfg_geo_is_target_homepage() ) {
		$html = sfg_geo_enforce_homepage_claims( $html );
	}

	return sfg_geo_enforce_public_claims( $html );
}

/**
 * Start buffering only after WordPress resolves a normal public request.
 * Non-HTML payloads are returned byte-for-byte by the final callback.
 */
function sfg_geo_start_final_public_html_buffer() {
	if ( ! sfg_geo_is_public_html_request() || headers_sent() ) {
		return;
	}

	ob_start( 'sfg_geo_filter_final_public_html' );
}
add_action( 'template_redirect', 'sfg_geo_start_final_public_html_buffer', 0 );

/**
 * Clean individual Elementor widgets, including footer-template widgets that
 * are outside the page's normal the_content output.
 */
function sfg_geo_enhance_elementor_widget( $widget_content, $widget ) {
	if ( ! sfg_geo_is_target_homepage() ) {
		return $widget_content;
	}

	$widget_id = is_object( $widget ) && method_exists( $widget, 'get_id' ) ? (string) $widget->get_id() : '';
	foreach ( sfg_geo_heading_replacements() as $from => $to ) {
		$new_tag        = '13938f8' === $widget_id && 'SOURCE AND IMPORT BUILDING MATERIALS FROM CHINA?' === $from ? 'h1' : '';
		$widget_content = sfg_geo_replace_heading( $widget_content, $from, $to, $new_tag );
	}
	$widget_content = sfg_geo_enforce_homepage_claims( $widget_content );
	$widget_content = sfg_geo_enforce_public_contact( $widget_content );

	return $widget_content;
}
add_filter( 'elementor/widget/render_content', 'sfg_geo_enhance_elementor_widget', 20, 2 );

/**
 * Build the visible buyer-answer section appended to the homepage.
 */
function sfg_geo_buyer_guide_markup() {
	$faq_markup = '<div id="sfg-buyer-faq" class="sfg-geo-section__faq" aria-labelledby="sfg-buyer-faq-title">'
		. '<h3 id="sfg-buyer-faq-title">Buyer questions answered</h3>';
	foreach ( sfg_geo_faq_items() as $faq ) {
		$faq_markup .= '<article><h4>' . esc_html( $faq['question'] ) . '</h4><p>' . esc_html( $faq['answer'] ) . '</p></article>';
	}
	$faq_markup .= '</div>';

	return '<section id="sfg-buyer-guide" class="sfg-geo-section" aria-labelledby="sfg-buyer-guide-title">'
		. '<div class="sfg-geo-section__inner">'
		. '<p class="sfg-geo-section__eyebrow">Sourcing enquiry checklist</p>'
		. '<h2 id="sfg-buyer-guide-title">Plan a clear sourcing enquiry</h2>'
		. '<p class="sfg-geo-section__lead">Clear product requirements make it possible to review the request before a quotation or service scope is confirmed.</p>'
		. '<div class="sfg-geo-section__process">'
		. '<h3>How a sourcing request is reviewed</h3>'
		. '<ol>'
		. '<li><strong>Request:</strong> The buyer shares the product specification, quantity, destination and required timing.</li>'
		. '<li><strong>Written scope:</strong> Responsibilities, fees and exclusions are recorded before any work is commissioned.</li>'
		. '<li><strong>Supplier quotation and availability:</strong> Relevant supplier quotations, current availability and assumptions are reviewed for the request. Price and delivery are not final until confirmed in writing.</li>'
		. '<li><strong>Optional services:</strong> Inspection, warehousing and shipping coordination are not automatic. Each service applies only when separately commissioned and recorded in writing.</li>'
		. '</ol>'
		. '<p>Submitting an enquiry does not confirm a supplier appointment, product availability, inspection or shipping service.</p>'
		. '</div>'
		. '<div class="sfg-geo-section__grid">'
		. '<article><h3>What should I send?</h3><p>Share the product name, reference photos or drawings, dimensions and material requirements, quantity, destination country and desired timeline.</p></article>'
		. '<article><h3>What should be confirmed?</h3><p>Ask for the supplier-search scope, quotation assumptions, inspection scope, warehousing or shipping responsibilities, fees and exclusions to be stated in writing.</p></article>'
		. '<article><h3>What evidence should I keep?</h3><p>Keep the final written quotation and agreed scope. Where those services are commissioned, also keep the relevant product specifications, inspection record and shipping documents.</p></article>'
		. '</div>'
		. '<p class="sfg-geo-section__notice"><strong>Important:</strong> Supplier availability, pricing and delivery timing are not final until they are confirmed in writing for the individual request.</p>'
		. $faq_markup
		. '<div class="sfg-geo-section__evidence">'
		. '<h3>Operator and official records</h3>'
		. '<p><strong>Website operator:</strong> Sourcing for Goods is operated by EZZOGENICS PTE. LTD. (Singapore UEN 201208665Z), registered at 15 Kaki Bukit Road 4, #01-44 Bartley Biz Centre, Singapore 417808. The <a href="' . esc_url( SFG_GEO_BCA_RECORD ) . '" rel="noopener">official BCA Contractors Registry record</a> currently lists the company under CR06 Interior Decoration &amp; Finishing Works, grade L1, with an expiry date of 1 July 2028. That registration identifies the operator; it does not certify a supplier, imported product, sourcing outcome or customs-agent status.</p>'
		. '<p><strong>Public enquiries:</strong> David, <a href="tel:+6596320750">+65 9632 0750</a>. The separate phone shown in BCA\'s directory is not used as this website\'s public contact.</p>'
		. '<p><strong>Singapore import permits:</strong> <a href="' . esc_url( SFG_GEO_CUSTOMS_INFO ) . '" rel="noopener">Singapore Customs explains</a> that import permits are submitted through TradeNet. An importer may appoint a registered Declaring Agent, or apply directly only after completing the required Declaring Agent and TradeNet registration. This website does not claim that Sourcing for Goods or Ezzogenics is a Declaring Agent.</p>'
		. '<p class="sfg-geo-section__source-note">Official sources checked on <time datetime="2026-08-11">11 August 2026</time>. Recheck the linked records for their current status before relying on them.</p>'
		. '</div>'
		. '<nav class="sfg-geo-section__links" aria-label="Sourcing information">'
		. '<a href="' . esc_url( home_url( '/services/' ) ) . '">Sourcing services</a>'
		. '<a href="' . esc_url( home_url( '/price-and-payment/' ) ) . '">Price and payment</a>'
		. '<a href="' . esc_url( home_url( '/about-shipment/' ) ) . '">About shipment</a>'
		. '<a href="' . esc_url( home_url( '/faqs/' ) ) . '">Frequently asked questions</a>'
		. '<a class="sfg-geo-section__cta" href="' . esc_url( home_url( '/contact-us/#form' ) ) . '">Send a sourcing enquiry</a>'
		. '</nav>'
		. '</div>'
		. '</section>';
}

/**
 * Final content filter: provides a fallback if Elementor widget caching skips
 * its widget filter, and appends the visible buyer-answer section once.
 */
function sfg_geo_enhance_homepage_content( $content ) {
	if ( ! sfg_geo_is_target_homepage() || ! in_the_loop() || ! is_main_query() || SFG_GEO_HOME_PAGE_ID !== (int) get_the_ID() ) {
		return $content;
	}

	$h1_count = preg_match_all( '/<h1\b/i', $content );
	foreach ( sfg_geo_heading_replacements() as $from => $to ) {
		$new_tag = 0 === $h1_count && 'SOURCE AND IMPORT BUILDING MATERIALS FROM CHINA?' === $from ? 'h1' : '';
		$content = sfg_geo_replace_heading( $content, $from, $to, $new_tag );
		if ( 'h1' === $new_tag && false !== stripos( $content, '<h1' ) ) {
			$h1_count = 1;
		}
	}
	$content = sfg_geo_enforce_homepage_claims( $content );
	$content = sfg_geo_enforce_public_contact( $content );

	if ( false === strpos( $content, 'id="sfg-buyer-guide"' ) ) {
		$content .= sfg_geo_buyer_guide_markup();
	}

	return $content;
}
add_filter( 'the_content', 'sfg_geo_enhance_homepage_content', 999 );

/**
 * Supply descriptive alt text only where the known homepage attachments have
 * no existing alt text. Existing editor-provided alt text always wins.
 */
function sfg_geo_homepage_image_alt( $attr, $attachment, $size ) {
	if ( ! sfg_geo_is_target_homepage() ) {
		return $attr;
	}

	$attachment_id = is_object( $attachment ) && isset( $attachment->ID ) ? (int) $attachment->ID : 0;
	$alt_by_id     = array(
		157 => 'Sourcing service process illustration',
		184 => 'Sourcing for Goods',
		188 => 'Sourcing for Goods',
	);

	if ( isset( $alt_by_id[ $attachment_id ] ) && ( ! isset( $attr['alt'] ) || '' === trim( (string) $attr['alt'] ) ) ) {
		$attr['alt'] = $alt_by_id[ $attachment_id ];
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'sfg_geo_homepage_image_alt', 20, 3 );

/**
 * Scoped styling for the appended section. No site-wide layout selectors are
 * changed, so the plugin cannot hide or mask existing overflow problems.
 */
function sfg_geo_homepage_styles() {
	if ( ! sfg_geo_is_target_homepage() ) {
		return;
	}
	?>
	<style id="sfg-geo-homepage-css">
		.sfg-geo-section{background:#f4f7f7;color:#182425;padding:clamp(3rem,6vw,5rem) 1.25rem}
		.sfg-geo-section__inner{width:min(1120px,100%);margin:0 auto}
		.sfg-geo-section__eyebrow{margin:0 0 .65rem;color:#1a6b62;font-size:.8rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase}
		.sfg-geo-section h2{margin:0;color:#172526;font-size:clamp(2rem,4vw,3.1rem);line-height:1.12}
		.sfg-geo-section__lead{max-width:760px;margin:1rem 0 2rem;color:#435253;font-size:1.08rem;line-height:1.7}
		.sfg-geo-section__process{margin:0 0 1.25rem;border:1px solid #c9d9d6;border-radius:14px;background:#fff;padding:1.4rem}
		.sfg-geo-section__process h3{margin:0 0 .8rem;color:#172526;font-size:1.2rem}
		.sfg-geo-section__process ol{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.75rem 1.25rem;margin:0;padding-left:1.3rem;color:#405152}
		.sfg-geo-section__process li{padding-left:.2rem;line-height:1.65}
		.sfg-geo-section__process p{margin:1rem 0 0;color:#657475;font-size:.94rem;line-height:1.6}
		.sfg-geo-section__grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1rem}
		.sfg-geo-section article{border:1px solid #d7e2e0;border-radius:14px;background:#fff;padding:1.4rem}
		.sfg-geo-section article h3{margin:0 0 .65rem;color:#172526;font-size:1.15rem}
		.sfg-geo-section article p{margin:0;color:#4a5a5b;line-height:1.65}
		.sfg-geo-section__notice{margin:1.25rem 0 0;border-left:4px solid #1a6b62;background:#e7efed;padding:1rem 1.1rem;line-height:1.6}
		.sfg-geo-section__faq{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem;margin-top:1.25rem}
		.sfg-geo-section__faq>h3{grid-column:1/-1;margin:0;color:#172526;font-size:1.35rem}
		.sfg-geo-section__faq article{padding:1.25rem}
		.sfg-geo-section__faq article h4{margin:0 0 .55rem;color:#172526;font-size:1.05rem}
		.sfg-geo-section__faq article p{margin:0;color:#4a5a5b;line-height:1.65}
		.sfg-geo-section__evidence{margin-top:1.25rem;border:1px solid #c9d9d6;border-radius:14px;background:#fff;padding:1.4rem}
		.sfg-geo-section__evidence h3{margin:0 0 .75rem;color:#172526;font-size:1.2rem}
		.sfg-geo-section__evidence p{margin:.65rem 0 0;color:#405152;line-height:1.7}
		.sfg-geo-section__evidence .sfg-geo-section__source-note{color:#657475;font-size:.9rem}
		.sfg-geo-section__evidence a{color:#165e56;font-weight:650;text-decoration:underline;text-underline-offset:3px}
		.sfg-geo-section__links{display:flex;flex-wrap:wrap;gap:.75rem;margin-top:1.5rem;align-items:center}
		.sfg-geo-section__links a{color:#165e56;font-weight:650;text-decoration:underline;text-underline-offset:3px}
		.sfg-geo-section__links .sfg-geo-section__cta{border-radius:8px;background:#176b61;color:#fff;padding:.75rem 1rem;text-decoration:none}
		@media (max-width:800px){.sfg-geo-section__process ol,.sfg-geo-section__grid,.sfg-geo-section__faq{grid-template-columns:1fr}.sfg-geo-section__links{align-items:flex-start;flex-direction:column}}
	</style>
	<?php
}
add_action( 'wp_head', 'sfg_geo_homepage_styles', 30 );
