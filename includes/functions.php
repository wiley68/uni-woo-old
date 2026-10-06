<?php

	/** load plugin class */
	function uni_load_class_plugin(){
		if (!class_exists('WC_Payment_Gateway'))
			return;
		include(UNI_INCLUDES_DIR . '/class-gateway.php');
	}

	/** add payment gateway */
	function uni_add_gateway($gateways) {
	  $gateways[] = 'Uni_Payment_Gateway';
	  return $gateways;
	}

	/** do output buffer */
	function uni_do_output_buffer() {
		ob_start();
	}

	/** load admin menu */
	function uni_admin_options() {
		include('uni_import_admin.php');
	}

	function uni_add_meta_admin($hook) {
		wp_enqueue_script( 'uni_payment_admin', plugin_dir_url( __FILE__ ) .'../js/unipayment_admin.js', array('jquery'), null, true);
		wp_localize_script( 'uni_payment_admin', 'unipayment_admin', array(
			'ajax_url' => admin_url( 'admin-ajax.php' )
		));
	}

	function uni_wordpress_get_params($param = null,$null_return = null){
		if ($param){
			$value = (!empty($_POST[$param]) ? trim(esc_sql($_POST[$param])) : (!empty($_GET[$param]) ? trim(esc_sql($_GET[$param])) : $null_return ));
			return $value;
		} else {
			$params = array();
			foreach ($_POST as $key => $param) {
				$params[trim(esc_sql($key))] = (!empty($_POST[$key]) ? trim(esc_sql($_POST[$key])) :  $null_return );
			}
			foreach ($_GET as $key => $param) {
				$key = trim(esc_sql($key));
				if (!isset($params[$key])) { // if there is no key or it's a null value
					$params[trim(esc_sql($key))] = (!empty($_GET[$key]) ? trim(esc_sql($_GET[$key])) : $null_return );
				}
			}
			return $params;
		}
	}

	add_action( 'wp_ajax_unipayment_kop', 'unipayment_kop' );
	add_action( 'wp_ajax_nopriv_unipayment_kop', 'unipayment_kop' );

	function unipayment_kop() {
		$json = array();
		
		if (isset($_REQUEST['uni_categories_kop'])) {
			$uni_categories_kop = $_REQUEST['uni_categories_kop'];
		} else {
			$uni_categories_kop = '';
		}
		$jsondata = json_encode($uni_categories_kop, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		global $wp_filesystem;
		WP_Filesystem();
		$file_path = plugin_dir_path(__FILE__) . "../keys/kop.json";
		if ( is_object($wp_filesystem) && $wp_filesystem->put_contents( $file_path, $jsondata, FS_CHMOD_FILE ) ) {
			$json['success'] = 'success';
		} else {
			$json['success'] = 'unsuccess';
		}

		echo (json_encode($json));
		die();
	}

	add_action( 'wp_ajax_unipayment_checkout', 'unipayment_checkout' );
	add_action( 'wp_ajax_nopriv_unipayment_checkout', 'unipayment_checkout' );

	function unipayment_checkout() {
		$json = array();
		$uni_unicid = (string) get_option('unipayment_unicid');
		$paramsuni = uni_get_cached_params($uni_unicid);
		if (!is_array($paramsuni) || empty($paramsuni['uni_user']) || empty($paramsuni['uni_password'])) {
			wp_send_json_error(array('message' => 'Кредитният калкулатор временно не е достъпен.'), 503);
		}
		$uni_user = $paramsuni['uni_user'];
		$uni_password = $paramsuni['uni_password'];
		$uni_service = isset($paramsuni['uni_testenv']) && intval($paramsuni['uni_testenv']) == 1
			? (isset($paramsuni['uni_test_service']) ? $paramsuni['uni_test_service'] : '')
			: (isset($paramsuni['uni_production_service']) ? $paramsuni['uni_production_service'] : '');
		if (empty($uni_service)) {
			wp_send_json_error(array('message' => 'Кредитният калкулатор временно не е достъпен.'), 503);
		}
		$uni_sertificat = isset($paramsuni['uni_sertificat']) ? $paramsuni['uni_sertificat'] : 'No';
		$uni_liveurl = UNI_LIVEURL;
		
		if (isset($_REQUEST['uni_promo'])) {
			$uni_promo = $_REQUEST['uni_promo'];
		} else {
			$uni_promo = '';
		}
		if (isset($_REQUEST['uni_promo_data'])) {
			$uni_promo_data = $_REQUEST['uni_promo_data'];
		} else {
			$uni_promo_data = '';
		}
		if (isset($_REQUEST['uni_promo_meseci_znak'])) {
			$uni_promo_meseci_znak = $_REQUEST['uni_promo_meseci_znak'];
		} else {
			$uni_promo_meseci_znak = '';
		}
		if (isset($_REQUEST['uni_promo_meseci'])) {
			$uni_promo_meseci = $_REQUEST['uni_promo_meseci'];
		} else {
			$uni_promo_meseci = '';
		}
		if (isset($_REQUEST['uni_promo_price'])) {
			$uni_promo_price = $_REQUEST['uni_promo_price'];
		} else {
			$uni_promo_price = '';
		}
		if (isset($_REQUEST['uni_product_cat_id'])) {
			$uni_product_cat_id = $_REQUEST['uni_product_cat_id'];
		} else {
			$uni_product_cat_id = '';
		}
		if (isset($_REQUEST['uni_meseci'])) {
			$uni_meseci = $_REQUEST['uni_meseci'];
		} else {
			$uni_meseci = '';
		}
		if (isset($_REQUEST['uni_total_price'])) {
			$uni_total_price = $_REQUEST['uni_total_price'];
		} else {
			$uni_total_price = '';
		}
		if (isset($_REQUEST['uni_parva'])) {
			$uni_parva = $_REQUEST['uni_parva'];
		} else {
			$uni_parva = '';
		}
		$uni_eur = isset($paramsuni['uni_eur']) ? (int) $paramsuni['uni_eur'] : 0;
		/** WC blocks payment method */
		if (isset($_REQUEST['uni_description'])) {
			$uni_description = $_REQUEST['uni_description'];
		} else {
			$uni_description = '';
		}
		if (isset($_REQUEST['uni_egn'])) {
			$uni_egn = $_REQUEST['uni_egn'];
		} else {
			$uni_egn = '';
		}
		if (isset($_REQUEST['uni_phone2'])) {
			$uni_phone2 = $_REQUEST['uni_phone2'];
		} else {
			$uni_phone2 = '';
		}
		if (isset($_REQUEST['uni_uslovia_check'])) {
			$uni_uslovia_check = $_REQUEST['uni_uslovia_check'];
		} else {
			$uni_uslovia_check = '';
		}
		if (isset($_REQUEST['uni_proces2'])) {
			$uni_proces2 = $_REQUEST['uni_proces2'];
		} else {
			$uni_proces2 = '';
		}
		/** WC blocks payment method */
		
		if (file_exists(plugin_dir_path( __FILE__ ) . "../keys/kop.json")) {
			$kopdata = file_get_contents(plugin_dir_path( __FILE__ ) . "../keys/kop.json");
			$uni_categories_kop_file = json_decode($kopdata, true);
			$uni_key = array_search($uni_product_cat_id, array_column($uni_categories_kop_file, 'category_id'));
			// Get KOP promo
			$curr_date = date("Y-m-d H:i");
			$date_to = date("Y-m-d H:i", strtotime($uni_promo_data));
			if ($curr_date <= $date_to){
				$udata = true;
			}else{
				$udata = false;
			}
			
			if (($uni_promo == "Yes") && ($udata)) {
				if ($uni_promo_meseci_znak == "eq"){
					$uni_promo_meseci_arr = explode(",", $uni_promo_meseci);
					if ((floatval($uni_total_price) >= floatval($uni_promo_price)) && is_array($uni_promo_meseci_arr) && in_array($uni_meseci, $uni_promo_meseci_arr)){
						$uni_kop = $uni_categories_kop_file[$uni_key]['promo'];
						if (empty($uni_categories_kop_file[$uni_key]['promo'])){
							$uni_kop = $uni_categories_kop_file[$uni_key]['kop'];
						}
					}else{
						$uni_kop = $uni_categories_kop_file[$uni_key]['kop'];
					}
				}else{
					$uni_promo_meseci_arr = explode(",", $uni_promo_meseci);
					if ((floatval($uni_total_price) >= floatval($uni_promo_price)) && is_array($uni_promo_meseci_arr) && (intval($uni_meseci) >= intval($uni_promo_meseci_arr[0]))){
						$uni_kop = $uni_categories_kop_file[$uni_key]['promo'];
						if (empty($uni_categories_kop_file[$uni_key]['promo'])){
							$uni_kop = $uni_categories_kop_file[$uni_key]['kop'];
						}
					}else{
						$uni_kop = $uni_categories_kop_file[$uni_key]['kop'];
					}
				}
			}else{
				$uni_kop = $uni_categories_kop_file[$uni_key]['kop'];
			}    
		}else{
			$uni_kop = '';
		}
		// get KIMB
		if ($uni_sertificat == "Yes"){
			$url_key = $uni_liveurl . '/calculators/key/avalon_private_key.pem';
			$curl_key = curl_init();
			curl_setopt($curl_key, CURLOPT_URL, $url_key);
			curl_setopt($curl_key, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl_key, CURLOPT_HEADER, false);
			$keyFileContents = curl_exec($curl_key);
			curl_close($curl_key);
			$keyFileHandle = fopen(plugin_dir_path( __FILE__ ) . "../keys/avalon_private_key.pem", "w") or die("Unable to open file!");
			fwrite($keyFileHandle, $keyFileContents);
			fclose($keyFileHandle);
			$keyFile = plugin_dir_path( __FILE__ ) . "../keys/avalon_private_key.pem";
			$url_cert = $uni_liveurl . '/calculators/key/avalon_cert.pem';
			$curl_cert = curl_init();
			curl_setopt($curl_cert, CURLOPT_URL, $url_cert);
			curl_setopt($curl_cert, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl_cert, CURLOPT_HEADER, false);
			$certFileContents = curl_exec($curl_cert);
			curl_close($curl_cert);
			$certFileHandle = fopen(plugin_dir_path( __FILE__ ) . "../keys/avalon_cert.pem", "w") or die("Unable to open file!");
			fwrite($certFileHandle, $certFileContents);
			fclose($certFileHandle);
			$certFile = plugin_dir_path( __FILE__ ) . "../keys/avalon_cert.pem";
		}
		
		$uni_kimb = curl_init();
		if ($uni_sertificat == "Yes"){
			curl_setopt_array($uni_kimb, array(
				CURLOPT_URL => $uni_service . "getCoeff",
				// името на файл, съдържащ само личен SSL ключ в текстови формат (PEM)
				CURLOPT_SSLKEY => $keyFile,
				CURLOPT_SSLKEYPASSWD => "1234",
				// името на файл, съдържащ само клиентския сартификат в текстови формат (PEM)
				CURLOPT_SSLCERT => $certFile,
				CURLOPT_SSLCERTPASSWD => "1234",
			
				CURLOPT_SSLVERSION => 6,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 2,
				CURLOPT_TIMEOUT => 5,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => http_build_query(array(
					'user' => $uni_user,
					'pass' => $uni_password,
					'onlineProductCode' => $uni_kop,
					'installmentCount' => $uni_meseci
				)),
				CURLOPT_HTTPHEADER => array(
					"Content-Type: application/x-www-form-urlencoded",
					"cache-control: no-cache"
				),
			));
		}else{
			curl_setopt_array($uni_kimb, array(
				CURLOPT_URL => $uni_service . "getCoeff",
				CURLOPT_SSLVERSION => 6,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 2,
				CURLOPT_TIMEOUT => 5,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => http_build_query(array(
					'user' => $uni_user,
					'pass' => $uni_password,
					'onlineProductCode' => $uni_kop,
					'installmentCount' => $uni_meseci
				)),
				CURLOPT_HTTPHEADER => array(
					"Content-Type: application/x-www-form-urlencoded",
					"cache-control: no-cache"
				),
			));
		}
		$responsekimb = curl_exec($uni_kimb);
		$err = curl_error($uni_kimb);
		curl_close($uni_kimb);
		$kimb_obj = json_decode($responsekimb);
		$kimb = 0;
		$uni_glp = 0;
		if (!empty($kimb_obj->coeffList)){
			if (!empty($kimb_obj->coeffList[0])){
				$kimb = floatval($kimb_obj->coeffList[0]->coeff);
				$uni_glp = number_format(floatval($kimb_obj->coeffList[0]->interestPercent), 2, ".", "");
			}
		}
		
		$uni_obshto = number_format(floatval($uni_total_price) - floatval($uni_parva), 2, ".", "");
		$uni_mesecna = number_format(floatval($uni_obshto) * $kimb, 2, ".", "");
		$uni_obshtozaplashtane = number_format(floatval($uni_mesecna) * intval($uni_meseci), 2, ".", "");
		$uni_gprm = ((UNI_RATE(intval($uni_meseci), -1 * (floatval($uni_mesecna)), floatval($uni_obshto))* intval($uni_meseci))) / (intval($uni_meseci) / 12);
		$uni_gpr = abs((pow(1 + (float)$uni_gprm / 12, 12) - 1) * 100);
		$uni_gpr = round($uni_gpr, 2);
		$uni_gpr = ($uni_gpr <= 0.1) ? 0.00 : $uni_gpr;
		$uni_gpr_display = number_format($uni_gpr, 2, '.', '');

		$uni_obshto_second = 0;
		$uni_mesecna_second = 0;
		$uni_obshtozaplashtane_second = 0;
		switch ($uni_eur) {
			case 0:
				$uni_obshto_second = 0;
				$uni_mesecna_second = 0;
				$uni_obshtozaplashtane_second = 0;
				break;
			case 1:
				$uni_obshto_second = number_format($uni_obshto / 1.95583, 2, ".", "");
				$uni_mesecna_second = number_format($uni_mesecna / 1.95583, 2, ".", "");
				$uni_obshtozaplashtane_second = number_format($uni_obshtozaplashtane / 1.95583, 2, ".", "");
				break;
			case 2:
				$uni_obshto_second = number_format($uni_obshto * 1.95583, 2, ".", "");
				$uni_mesecna_second = number_format($uni_mesecna * 1.95583, 2, ".", "");
				$uni_obshtozaplashtane_second = number_format($uni_obshtozaplashtane * 1.95583, 2, ".", "");
				break;
			case 3:
				$uni_obshto_second = 0;
				$uni_mesecna_second = 0;
				$uni_obshtozaplashtane_second = 0;
				break;
		}
		
		$json['success'] = 'success';
		$json['uni_mesecna'] = $uni_mesecna;
		$json['uni_mesecna_second'] = $uni_mesecna_second;
		$json['uni_glp'] = $uni_glp;
		$json['uni_obshto'] = $uni_obshto;
		$json['uni_obshto_second'] = $uni_obshto_second;
		$json['uni_obshtozaplashtane'] = $uni_obshtozaplashtane;
		$json['uni_obshtozaplashtane_second'] = $uni_obshtozaplashtane_second;
		$json['uni_gpr'] = $uni_gpr_display;
		$json['uni_kop'] = $uni_kop;

		/** WC blocks payment method */
		WC()->session->set( 'uni_mesecna', sanitize_text_field( $uni_mesecna ) );
		WC()->session->set( 'uni_glp', sanitize_text_field( $uni_glp ) );
		WC()->session->set( 'uni_gpr', sanitize_text_field( $uni_gpr_display ) );
		WC()->session->set( 'uni_kop', sanitize_text_field( $uni_kop ) );
		WC()->session->set( 'uni_vnoski', sanitize_text_field( $uni_meseci ) );
		WC()->session->set( 'uni_parva', sanitize_text_field( $uni_parva ) );
		WC()->session->set( 'uni_description', sanitize_text_field( $uni_description ) );
		WC()->session->set( 'uni_egn', sanitize_text_field( $uni_egn ) );
		WC()->session->set( 'uni_phone2', sanitize_text_field( $uni_phone2 ) );
		WC()->session->set( 'uni_uslovia_check', sanitize_text_field( $uni_uslovia_check ) );
		WC()->session->set( 'uni_proces2', sanitize_text_field( $uni_proces2 ) );
		/** WC blocks payment method */
		
		echo (json_encode($json));
		die();
	}

	function uni_add_meta() {
		$uni_css_file = plugin_dir_path( __FILE__ ) . '../css/uni_style.css';
		$uni_js_front_file = plugin_dir_path( __FILE__ ) . '../js/unipayment.js';
		$uni_js_product_file = plugin_dir_path( __FILE__ ) . '../js/unipaymentproduct.js';
		$uni_js_checkout_file = plugin_dir_path( __FILE__ ) . '../js/unipaymentcheckout.js';

		$uni_css_version = file_exists( $uni_css_file ) ? filemtime( $uni_css_file ) : UNI_MOD_VERSION;
		$uni_js_front_version = file_exists( $uni_js_front_file ) ? filemtime( $uni_js_front_file ) : UNI_MOD_VERSION;
		$uni_js_product_version = file_exists( $uni_js_product_file ) ? filemtime( $uni_js_product_file ) : UNI_MOD_VERSION;
		$uni_js_checkout_version = file_exists( $uni_js_checkout_file ) ? filemtime( $uni_js_checkout_file ) : UNI_MOD_VERSION;

		//register css-s
		wp_enqueue_style( 'uni_style', plugin_dir_url( __FILE__ ) . '../css/uni_style.css', array(), $uni_css_version, 'all');
		if ( is_front_page() ){
			wp_enqueue_script( 'uni_credit', plugin_dir_url( __FILE__ ) . '../js/unipayment.js', false, $uni_js_front_version);
		}
		if ( is_product() ){
			wp_enqueue_script( 'uni_product_payment', plugin_dir_url( __FILE__ ) . '../js/unipaymentproduct.js', array('jquery'), $uni_js_product_version, true);
			wp_localize_script('uni_product_payment', 'unipayment_product', array(
				'ajaxurl' => admin_url('admin-ajax.php')
			));
		}
		if ( is_checkout() ){
			wp_enqueue_script( 'uni_checkout_payment', plugin_dir_url( __FILE__ ) . '../js/unipaymentcheckout.js', array('jquery'), $uni_js_checkout_version, true);
			wp_localize_script( 'uni_checkout_payment', 'unipayment_checkout', array(
				'ajax_url' => admin_url( 'admin-ajax.php' )
			));
		}
	}

	function uni_reklama() {
		$o = '';

		if (is_front_page()) {
			$uni_unicid = (string) get_option("unipayment_unicid");
			$uni_reklama = (string) get_option("unipayment_reklama");
			$uni_status = (string) get_option("unipayment_status");

			if ($uni_reklama === "on" && $uni_status === "on" && $uni_unicid !== '') {

				$paramsuni = uni_get_cached_params($uni_unicid);
				if (!$paramsuni) {
					return null;
				}

				$useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
				$deviceis = (preg_match('/(android|mobile|phone|tablet)/i', $useragent)) ? 'mobile' : 'pc';

				$uni_logo = esc_url(UNI_CSS_URI . '/uni_logo.jpg');
				$uni_backurl = esc_url($paramsuni['uni_backurl']);
				$uni_picture = esc_url(UNI_CSS_URI . '/unim.png');
				$uni_container_txt1 = sanitize_text_field($paramsuni['uni_container_txt1']);
				$uni_container_txt2 = sanitize_text_field($paramsuni['uni_container_txt2']);
				$uni_container_status = $paramsuni['uni_container_status'];
				$uni_status_cp = $paramsuni['uni_status'];

				if ($uni_status_cp === "Yes" && $uni_container_status === "Yes") {
					if ($deviceis === "pc") {
						$o .= '<div class="uni_float" onclick="uniChangeContainer();">';
						$o .= '<img src="' . $uni_logo . '" class="uni-my-float">';
						$o .= '</div>';
					} else {
						$o .= '<div class="uni_float" onclick="uniGoTo(\'' . $uni_backurl . '\');">';
						$o .= '<img src="' . $uni_logo . '" class="uni-my-float">';
						$o .= '</div>';
					}

					$o .= '<div class="uni-label-container">';
					$o .= '<i class="fa fa-play fa-rotate-180 uni-label-arrow"></i>';
					$o .= '<div class="uni-label-text">';
					$o .= '<div style="padding-bottom:5px;"></div>';
					$o .= '<img src="' . $uni_picture . '">';
					$o .= '<div style="font-size:16px;padding-top:3px;">' . esc_html($uni_container_txt1) . '</div>';
					$o .= '<p>' . esc_html($uni_container_txt2) . '</p>';
					$o .= '<div class="uni-label-text-a"><a href="' . $uni_backurl . '" target="_blank" rel="noopener noreferrer">ИНФОРМАЦИЯ ЗА ОНЛАЙН ПАЗАРУВАНЕ НА КРЕДИТ!</a></div>';
					$o .= '</div>';
					$o .= '</div>';
				}
			}
		}

		echo $o;
	}

	/** vizualize credit button */
	function unipayment_button() {
		if ( ! function_exists('is_product') || ! is_product() ) {
			return;
		}

		global $product;

		if ( ! $product instanceof WC_Product ) {
			return;
		}

		$uni_status = (string)get_option("unipayment_status");
		$uni_cart = (string)get_option("unipayment_cart");
		$unipayment_gap = (int)get_option("unipayment_gap");

		$unipayment_unicid = '';
		$uni_minstojnost = 150;
		$uni_price = 0;
		$uni_maxstojnost = 20000;
		$uni_zaglavie = '';
		$uni_button_position = 2;
		$uni_vnoska = 'No';
		$uni_mesecna = 0.00;
		$uni_reklama_url = '';
		$uni_mod_version = '';
		$uni_picture = '';
		$uni_mesecna_3 = 0.00;
		$uni_param_glp_3 = 0.00;
		$uni_mesecna_4 = 0.00;
		$uni_param_glp_4 = 0.00;
		$uni_mesecna_5 = 0.00;
		$uni_param_glp_5 = 0.00;
		$uni_mesecna_6 = 0.00;
		$uni_param_glp_6 = 0.00;
		$uni_mesecna_9 = 0.00;
		$uni_param_glp_9 = 0.00;
		$uni_mesecna_10 = 0.00;
		$uni_param_glp_10 = 0.00;
		$uni_mesecna_12 = 0.00;
		$uni_param_glp_12 = 0.00;
		$uni_mesecna_15 = 0.00;
		$uni_param_glp_15 = 0.00;
		$uni_mesecna_18 = 0.00;
		$uni_param_glp_18 = 0.00;
		$uni_mesecna_24 = 0.00;
		$uni_param_glp_24 = 0.00;
		$uni_mesecna_30 = 0.00;
		$uni_param_glp_30 = 0.00;
		$uni_mesecna_36 = 0.00;
		$uni_param_glp_36 = 0.00;
		global $product;
		if ($uni_status == "on" && $product && $product->get_price() > 0) {
			$uni_unicid = (string)get_option("unipayment_unicid");

			global $woocommerce;
			if( version_compare( $woocommerce->version, '2.6', ">=" ) ) {
				$uni_product_id = $product->get_id();
				$uni_product_name = $product->get_name();
			}else{
				$uni_product_id = $product->id;
				$uni_product_name = $product->name;
			}
			$uni_price = wc_get_price_including_tax($product);
			$uni_mod_version = UNI_MOD_VERSION;

			$uni_currency_code = get_woocommerce_currency();
			if ($uni_currency_code != 'EUR' && $uni_currency_code != 'BGN') {
				return null;
			}

			$paramsuni = uni_get_cached_params($uni_unicid);

			if (!$paramsuni || $paramsuni['uni_status'] != "Yes") {
				return null;
			}

			$uni_eur = (int)$paramsuni['uni_eur'];
			$uni_sign_second = $uni_eur == 2 || $uni_eur == 3 ? 'лева' : 'евро';

			// Keep cache output deterministic; popup layout is handled by CSS media queries.
			$deviceis = 'pc';

			if (intval($paramsuni['uni_testenv']) == 1) {
				$uni_service = $paramsuni['uni_test_service'];
			} else {
				$uni_service = $paramsuni['uni_production_service'];
			}
			$uni_user = $paramsuni['uni_user'];
			$uni_password = $paramsuni['uni_password'];

			$prod_categories = get_the_terms($uni_product_id, 'product_cat');
			$uni_product_cat_id = 0;

			if (!empty($prod_categories) && !is_wp_error($prod_categories)) {
				foreach ($prod_categories as $cat) {
					$term = $cat;
					while ($term->parent != 0) {
						$term = get_term($term->parent, 'product_cat');
						if (is_wp_error($term) || !$term) {
							break;
						}
					}
					$uni_product_cat_id = intval($term->term_id);
					break;
				}
			}

			$uni_shema_current = intval($paramsuni['uni_shema_current']);

			if (file_exists(plugin_dir_path( __FILE__ ) . "../keys/kop.json")) {
				$kopdata = file_get_contents(plugin_dir_path( __FILE__ ) . "../keys/kop.json");
				$uni_categories_kop = json_decode($kopdata, true);
				if (sizeof($uni_categories_kop) > 0) {
					$uni_key = array_search($uni_product_cat_id, array_column($uni_categories_kop, 'category_id'));
					// Get KOP promo
					$uni_promo_data = $paramsuni['uni_promo_data'];
					$curr_date = date("Y-m-d H:i");
					$date_to = date("Y-m-d H:i", strtotime($uni_promo_data));
					if ($curr_date <= $date_to){
						$udata = true;
					}else{
						$udata = false;
					}

					$uni_promo = $paramsuni['uni_promo'];
					$uni_promo_meseci_znak = $paramsuni['uni_promo_meseci_znak'];
					$uni_promo_meseci = $paramsuni['uni_promo_meseci'];
					$uni_promo_price = $paramsuni['uni_promo_price'];
					if (($uni_promo == "Yes") && ($udata)){
						if ($uni_promo_meseci_znak == "eq"){
							$uni_promo_meseci_arr = explode(",", $uni_promo_meseci);
							if (($uni_price >= floatval($uni_promo_price)) && is_array($uni_promo_meseci_arr) && in_array($uni_shema_current, $uni_promo_meseci_arr)){
								$uni_kop = $uni_categories_kop[$uni_key]['promo'];
								if (empty($uni_categories_kop[$uni_key]['promo'])){
									$uni_kop = $uni_categories_kop[$uni_key]['kop'];
								}
							}else{
								$uni_kop = $uni_categories_kop[$uni_key]['kop'];
							}
						}else{
							$uni_promo_meseci_arr = explode(",", $uni_promo_meseci);
							if (($uni_price >= floatval($uni_promo_price)) && is_array($uni_promo_meseci_arr) && (intval($uni_shema_current) >= intval($uni_promo_meseci_arr[0]))){
								$uni_kop = $uni_categories_kop[$uni_key]['promo'];
								if (empty($uni_categories_kop[$uni_key]['promo'])){
									$uni_kop = $uni_categories_kop[$uni_key]['kop'];
								}
							}else{
								$uni_kop = $uni_categories_kop[$uni_key]['kop'];
							}
						}
					}else{
						$uni_kop = $uni_categories_kop[$uni_key]['kop'];
					}
				}else{
					$uni_kop = '';
				}
			} else {
				$uni_kop = '';
			}

			if ($uni_kop == ''){
				return;
			}

			// /////////////////////////////////////////////////////////////////////////
			$paramsunicalc = uni_get_cached_calculation($uni_unicid, $deviceis);
			if (!$paramsunicalc) {
				return null;
			}

			/** Get KIMB one time per day **/
			// Test if KIMB actual
			$uni_param_kimb = $uni_categories_kop[$uni_key]['kimb'];
			$uni_param_kimb_3 = $uni_categories_kop[$uni_key]['stats']['kimb_3'];
			$uni_param_glp_3 = $uni_categories_kop[$uni_key]['stats']['glp_3'];
			$uni_param_kimb_4 = $uni_categories_kop[$uni_key]['stats']['kimb_4'];
			$uni_param_glp_4 = $uni_categories_kop[$uni_key]['stats']['glp_4'];
			$uni_param_kimb_5 = $uni_categories_kop[$uni_key]['stats']['kimb_5'];
			$uni_param_glp_5 = $uni_categories_kop[$uni_key]['stats']['glp_5'];
			$uni_param_kimb_6 = $uni_categories_kop[$uni_key]['stats']['kimb_6'];
			$uni_param_glp_6 = $uni_categories_kop[$uni_key]['stats']['glp_6'];
			$uni_param_kimb_9 = $uni_categories_kop[$uni_key]['stats']['kimb_9'];
			$uni_param_glp_9 = $uni_categories_kop[$uni_key]['stats']['glp_9'];
			$uni_param_kimb_10 = $uni_categories_kop[$uni_key]['stats']['kimb_10'];
			$uni_param_glp_10 = $uni_categories_kop[$uni_key]['stats']['glp_10'];
			$uni_param_kimb_12 = $uni_categories_kop[$uni_key]['stats']['kimb_12'];
			$uni_param_glp_12 = $uni_categories_kop[$uni_key]['stats']['glp_12'];
			$uni_param_kimb_15 = $uni_categories_kop[$uni_key]['stats']['kimb_15'];
			$uni_param_glp_15 = $uni_categories_kop[$uni_key]['stats']['glp_15'];
			$uni_param_kimb_18 = $uni_categories_kop[$uni_key]['stats']['kimb_18'];
			$uni_param_glp_18 = $uni_categories_kop[$uni_key]['stats']['glp_18'];
			$uni_param_kimb_24 = $uni_categories_kop[$uni_key]['stats']['kimb_24'];
			$uni_param_glp_24 = $uni_categories_kop[$uni_key]['stats']['glp_24'];
			$uni_param_kimb_30 = $uni_categories_kop[$uni_key]['stats']['kimb_30'];
			$uni_param_glp_30 = $uni_categories_kop[$uni_key]['stats']['glp_30'];
			$uni_param_kimb_36 = $uni_categories_kop[$uni_key]['stats']['kimb_36'];
			$uni_param_glp_36 = $uni_categories_kop[$uni_key]['stats']['glp_36'];

			if ($uni_categories_kop[$uni_key]['kimb_time'] == ""){
				$uni_param_kimb_time = 0;
			}else{
				$uni_param_kimb_time = $uni_categories_kop[$uni_key]['kimb_time'];
			}
			$current_time = time() - 86400;

			if (intval($current_time) > intval($uni_param_kimb_time)) {
				if ($paramsuni['uni_sertificat'] == "Yes"){
					$keyFile = plugin_dir_path( __FILE__ ) . "../keys/avalon_private_key.pem";
					$certFile = plugin_dir_path( __FILE__ ) . "../keys/avalon_cert.pem";
				}
				$uni_kimb = curl_init();
				if ($paramsuni['uni_sertificat'] == "Yes"){
					curl_setopt_array($uni_kimb, array(
						CURLOPT_URL => $uni_service . "getCoeff",
						// името на файл, съдържащ само личен SSL ключ в текстови формат (PEM)
						CURLOPT_SSLKEY => $keyFile,
						CURLOPT_SSLKEYPASSWD => "1234",
						// името на файл, съдържащ само клиентския сартификат в текстови формат (PEM)
						CURLOPT_SSLCERT => $certFile,
						CURLOPT_SSLCERTPASSWD => "1234",
						CURLOPT_SSLVERSION => 6,
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => "",
						CURLOPT_MAXREDIRS => 3,
						CURLOPT_TIMEOUT => 6,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => "POST",
						CURLOPT_POSTFIELDS => "user=" . $uni_user . "&pass=" . $uni_password . "&onlineProductCode=" . $uni_kop,
						CURLOPT_HTTPHEADER => array(
							"Content-Type: application/x-www-form-urlencoded",
							"cache-control: no-cache"
						),
					));
				}else{
					curl_setopt_array($uni_kimb, array(
						CURLOPT_URL => $uni_service . "getCoeff",
						CURLOPT_SSLVERSION => 6,
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => "",
						CURLOPT_MAXREDIRS => 3,
						CURLOPT_TIMEOUT => 6,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => "POST",
						CURLOPT_POSTFIELDS => "user=" . $uni_user . "&pass=" . $uni_password . "&onlineProductCode=" . $uni_kop,
						CURLOPT_HTTPHEADER => array(
							"Content-Type: application/x-www-form-urlencoded",
							"cache-control: no-cache"
						),
					));
				}
				$responsekimb = curl_exec($uni_kimb);
				$err = curl_error($uni_kimb);
				curl_close($uni_kimb);

				$kimb_obj = json_decode($responsekimb);
				$kimb = 0;
				if (!empty($kimb_obj->coeffList)) {
					if (!empty($kimb_obj->coeffList[0])){
						$kimb = floatval($kimb_obj->coeffList[0]->coeff);
						foreach ($kimb_obj->coeffList as $kimb_obj_item) {
							if (
								$kimb_obj_item->installmentCount == 3 || 
								$kimb_obj_item->installmentCount == 4 || 
								$kimb_obj_item->installmentCount == 5 || 
								$kimb_obj_item->installmentCount == 6 || 
								$kimb_obj_item->installmentCount == 9 || 
								$kimb_obj_item->installmentCount == 10 || 
								$kimb_obj_item->installmentCount == 12 || 
								$kimb_obj_item->installmentCount == 18 || 
								$kimb_obj_item->installmentCount == 24 || 
								$kimb_obj_item->installmentCount == 30 || 
								$kimb_obj_item->installmentCount == 36 
							){
								if ($kimb_obj_item->installmentCount == $uni_shema_current) {
									$kimb = floatval($kimb_obj_item->coeff);
								}
								$uni_categories_kop[$uni_key]['stats']['kimb_' . $kimb_obj_item->installmentCount] = strval($kimb_obj_item->coeff);
								$uni_categories_kop[$uni_key]['stats']['glp_' . $kimb_obj_item->installmentCount] = strval($kimb_obj_item->interestPercent);
							}
						}
					}
				}
				// save new kimb
				$uni_categories_kop[$uni_key]['kimb'] = strval($kimb);
				$uni_categories_kop[$uni_key]['kimb_time'] = strval(time());
				$jsondata = json_encode($uni_categories_kop, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
				if ( ! function_exists( 'WP_Filesystem' ) ) {
					require_once ABSPATH . 'wp-admin/includes/file.php';
				}
				global $wp_filesystem;
				WP_Filesystem();
				$file_path = plugin_dir_path(__FILE__) . "../keys/kop.json";
				is_object($wp_filesystem) && $wp_filesystem->put_contents( $file_path, $jsondata, FS_CHMOD_FILE );
			}else{
				// before 24 hours
				$uni_var = "uni_param_kimb_" . $uni_shema_current;
				$kimb = floatval($$uni_var);
			}

			$uni_mesecna = number_format($uni_price * $kimb, 2, ".", "");
			$uni_mesecna_3 = number_format($uni_price * floatval($uni_param_kimb_3), 2, ".", "");
			$uni_mesecna_4 = number_format($uni_price * floatval($uni_param_kimb_4), 2, ".", "");
			$uni_mesecna_5 = number_format($uni_price * floatval($uni_param_kimb_5), 2, ".", "");
			$uni_mesecna_6 = number_format($uni_price * floatval($uni_param_kimb_6), 2, ".", "");
			$uni_mesecna_9 = number_format($uni_price * floatval($uni_param_kimb_9), 2, ".", "");
			$uni_mesecna_10 = number_format($uni_price * floatval($uni_param_kimb_10), 2, ".", "");
			$uni_mesecna_12 = number_format($uni_price * floatval($uni_param_kimb_12), 2, ".", "");
			$uni_mesecna_15 = number_format($uni_price * floatval($uni_param_kimb_15), 2, ".", "");
			$uni_mesecna_18 = number_format($uni_price * floatval($uni_param_kimb_18), 2, ".", "");
			$uni_mesecna_24 = number_format($uni_price * floatval($uni_param_kimb_24), 2, ".", "");
			$uni_mesecna_30 = number_format($uni_price * floatval($uni_param_kimb_30), 2, ".", "");
			$uni_mesecna_36 = number_format($uni_price * floatval($uni_param_kimb_36), 2, ".", "");
			// /** Get KIMB one time per day **/

			switch ($uni_eur) {
				case 0:
					break;
				case 1:
					if ($uni_currency_code == "EUR") {
						$uni_mesecna = $uni_mesecna * 1.95583;
					}
					break;
				case 2:
				case 3:
					if ($uni_currency_code == "BGN") {
						$uni_mesecna = $uni_mesecna / 1.95583;
					}
					break;
			}

			$uni_mesecna_second = 0;
			$uni_price_second = 0;
			$uni_sign = 'лева';
			$uni_sign_second = 'евро';
			switch ($uni_eur) {
				case 0:
					$uni_mesecna_second = 0;
					$uni_price_second = 0;
					$uni_sign = 'лева';
					$uni_sign_second = 'евро';
					break;
				case 1:
					$uni_price_second = number_format($uni_price / 1.95583, 2, ".", "");
					$uni_mesecna_second = number_format($uni_mesecna / 1.95583, 2, ".", "");
					$uni_sign = 'лева';
					$uni_sign_second = 'евро';
					break;
				case 2:
					$uni_price_second = number_format($uni_price * 1.95583, 2, ".", "");
					$uni_mesecna_second = number_format($uni_mesecna * 1.95583, 2, ".", "");
					$uni_sign = 'евро';
					$uni_sign_second = 'лева';
					break;
				case 3:
					$uni_price_second = 0;
					$uni_mesecna_second = 0;
					$uni_sign = 'евро';
					$uni_sign_second = 'лева';
					break;
			}

			$uni_minstojnost = floatval($paramsuni['uni_minstojnost']);
			$uni_maxstojnost = floatval($paramsuni['uni_maxstojnost']);
			$uni_zaglavie = $paramsuni['uni_zaglavie'];
			$uni_button_position = $paramsuni['uni_button_position'];
			$uni_vnoska = $paramsuni['uni_vnoska'];
			$uni_reklama_url = $paramsunicalc['uni_reklama_url'];
			$uni_proces1 = intval($paramsuni['uni_proces1']);
			$uni_meseci_3 = intval($paramsuni['uni_meseci_3']);
			$uni_meseci_4 = intval($paramsuni['uni_meseci_4']);
			$uni_meseci_5 = intval($paramsuni['uni_meseci_5']);
			$uni_meseci_6 = intval($paramsuni['uni_meseci_6']);
			$uni_meseci_9 = intval($paramsuni['uni_meseci_9']);
			$uni_meseci_10 = intval($paramsuni['uni_meseci_10']);
			$uni_meseci_12 = intval($paramsuni['uni_meseci_12']);
			$uni_meseci_15 = intval($paramsuni['uni_meseci_15']);
			$uni_meseci_18 = intval($paramsuni['uni_meseci_18']);
			$uni_meseci_24 = intval($paramsuni['uni_meseci_24']);
			$uni_meseci_30 = intval($paramsuni['uni_meseci_30']);
			$uni_meseci_36 = intval($paramsuni['uni_meseci_36']);

			$modalpayment_content_uni = "modalpayment_content_uni";
			$uni_body = "uni_body";
			$uni_title_head = "uni_title_head";
			$uni_title = "uni_title";
			$uni_calc = "uni_calc";
			$uni_calc_back = "uni_calc_back";
			$uni_gpr_container = "uni_gpr_container";
			$uni_gpr_container_row = 'uni_gpr_container_row';
			$uni_gpr_column = "uni_gpr_column";
			$uni_gpr_column_right = "uni_gpr_column_right";
			$uni_txt_right = "uni_txt_right";
			$uni_panel_help_text = "uni_panel_help_text";
			$uni_btn_primary = "uni_btn_primary";
			$uni_btn_primary_inner = "uni_btn_primary_inner";
			$notify_badge = "notify-badge";
			$uni_btn_seccondary = "uni_btn_seccondary";
			$uni_btn_seccondary_inner = "uni_btn_seccondary_inner";
			$uni_meseci_txt = '<span class="uni-label-desktop">Брой месеци за погасяване *</span><span class="uni-label-mobile">Брой месеци *</span>';
			$uni_vnoska_txt = '<span class="uni-label-desktop">Размер на погасителна вноска</span><span class="uni-label-mobile">Погасителна вноска</span>';
			if ($uni_minstojnost < $uni_price && $uni_maxstojnost > $uni_price) {
				?>
				<input type="hidden" id="uni_cart" value="<?php echo $uni_cart; ?>" />
				<input type="hidden" id="uni_param_glp_3" value="<?php echo $uni_param_glp_3; ?>" />
				<input type="hidden" id="uni_param_kimb_3" value="<?php echo $uni_param_kimb_3; ?>" />
				<input type="hidden" id="uni_param_glp_4" value="<?php echo $uni_param_glp_4; ?>" />
				<input type="hidden" id="uni_param_kimb_4" value="<?php echo $uni_param_kimb_4; ?>" />
				<input type="hidden" id="uni_param_glp_5" value="<?php echo $uni_param_glp_5; ?>" />
				<input type="hidden" id="uni_param_kimb_5" value="<?php echo $uni_param_kimb_5; ?>" />
				<input type="hidden" id="uni_param_glp_6" value="<?php echo $uni_param_glp_6; ?>" />
				<input type="hidden" id="uni_param_kimb_6" value="<?php echo $uni_param_kimb_6; ?>" />
				<input type="hidden" id="uni_param_glp_9" value="<?php echo $uni_param_glp_9; ?>" />
				<input type="hidden" id="uni_param_kimb_9" value="<?php echo $uni_param_kimb_9; ?>" />
				<input type="hidden" id="uni_param_glp_10" value="<?php echo $uni_param_glp_10; ?>" />
				<input type="hidden" id="uni_param_kimb_10" value="<?php echo $uni_param_kimb_10; ?>" />
				<input type="hidden" id="uni_param_glp_12" value="<?php echo $uni_param_glp_12; ?>" />
				<input type="hidden" id="uni_param_kimb_12" value="<?php echo $uni_param_kimb_12; ?>" />
				<input type="hidden" id="uni_param_glp_18" value="<?php echo $uni_param_glp_18; ?>" />
				<input type="hidden" id="uni_param_kimb_18" value="<?php echo $uni_param_kimb_18; ?>" />
				<input type="hidden" id="uni_param_glp_24" value="<?php echo $uni_param_glp_24; ?>" />
				<input type="hidden" id="uni_param_kimb_24" value="<?php echo $uni_param_kimb_24; ?>" />
				<input type="hidden" id="uni_param_glp_30" value="<?php echo $uni_param_glp_30; ?>" />
				<input type="hidden" id="uni_param_kimb_30" value="<?php echo $uni_param_kimb_30; ?>" />
				<input type="hidden" id="uni_param_glp_36" value="<?php echo $uni_param_glp_36; ?>" />
				<input type="hidden" id="uni_param_kimb_36" value="<?php echo $uni_param_kimb_36; ?>" />
				<input type="hidden" id="uni_eur" value="<?php echo $uni_eur; ?>" />
				<input type="hidden" id="uni_currency_code" value="<?php echo $uni_currency_code; ?>" />
				<div id="uni-product-button-container" <?php if ($unipayment_gap > 0) { echo 'style="margin-top:'.$unipayment_gap.'px;"'; } ?>>
					<?php if ($uni_zaglavie != ''){ ?>
					<div class="uni_zaglavie">
						<?php echo $uni_zaglavie; ?>
					</div>
					<?php } ?>
					<?php if ($uni_vnoska == 'Yes'){ ?>
					<div id="btn_uni" class="uni_button">
						<div class="uni_button_body">
							<div class="uni_button_body_left">
								<div class="uni_button_txt1"><?php echo $uni_shema_current; ?> ВНОСКИ</div>
								<div class="uni_button_line"></div>
								<div class="uni_button_txt2"><?php echo number_format($uni_mesecna, 2, '.', ''); ?> <?php echo $uni_sign; ?> <?php if ($uni_mesecna_second != 0){ echo "<span style=\"font-size:80%;\">(" . number_format($uni_mesecna_second, 2, '.', '') . " " . $uni_sign_second . ")</span>"; } ?></div>
							</div>
							<div class="uni_button_body_right">
								<img src="<?php echo UNI_CSS_URI; ?>/uni_mini_logo.png" style="width:100%;float:right;" />
							</div>
						</div>
					</div>
					<?php } else { ?>
					<div id="btn_uni" class="uni_button_without"></div>
					<?php } ?>
				</div>

				<input type="hidden" name="uni_product_id" id="uni_product_id" value="<?php echo $uni_product_id; ?>" />
				<div id="uni-product-popup-container" class="modalpayment_uni">
					<div class="<?php echo $modalpayment_content_uni; ?>">
						<div id="uni_body" class="<?php echo $uni_body; ?>">
							<div>
								<div class="uni_body">
									<a target="_blank" href="<?php echo $uni_reklama_url; ?>">
										<picture>
											<source media="(max-width: 768px)" srcset="<?php echo esc_url(UNI_CSS_URI . '/unim.png'); ?>">
											<img class="uni_image" title="Кредитен калкулатор UNI Credit <?php echo $uni_mod_version; ?>" src="<?php echo esc_url(UNI_CSS_URI . '/uni.png'); ?>" alt="Кредитен калкулатор UNI Credit <?php echo $uni_mod_version; ?>">
										</picture>
									</a>
									<div class="uni_body_txt">
										<div class="<?php echo $uni_gpr_container; ?>">
											<?php if ($uni_eur == 0) { ?>
											<div class="<?php echo $uni_title_head; ?>">Само няколко клика до желаната покупка от 150 лева до 50 000 лева:</div>
											<?php } else if ($uni_eur == 1) { ?>
											<div class="<?php echo $uni_title_head; ?>">Само няколко клика до желаната покупка от 150 лева (75 евро) до 50 000 лева (25 000 евро):</div>
											<?php } else if ($uni_eur == 2) { ?>
											<div class="<?php echo $uni_title_head; ?>">Само няколко клика до желаната покупка от 75 евро (150 лева) до 25 000 евро (50 000 лева):</div>
											<?php } else if ($uni_eur == 3) { ?>
											<div class="<?php echo $uni_title_head; ?>">Само няколко клика до желаната покупка от 75 евро до 25 000 евро:</div>
											<?php } ?>
											<div class="<?php echo $uni_title; ?>">
												<p>1. Добавете желания от Вас продукт в кошницата.</p>
												<p>2. В меню <span>"Метод на плащане"</span> изберете <span>"На кредит с УниКредит Кънсюмър Файненсинг"</span>.</p>
												<p>3. Изберете <span>брой месечни вноски</span>, според <span>Вашите възможности и предпочитания</span>.</p>
												<p>4. Завършете кандидатстването <span>изцяло дигитално</span> или очаквайте телефонно обаждане.</p>
											</div>
											<div class="<?php echo $uni_calc_back; ?>">
												<div class="uni_calc_logo"></div>
												<div class="<?php echo $uni_calc; ?>">
													<div class="<?php echo $uni_gpr_container_row; ?>">
														<div class="<?php echo $uni_gpr_column; ?>">
															Цена на артикула
														</div>
														<div class="<?php echo $uni_gpr_column_right; ?>">
															<input type="hidden" id="uni_price" value="<?php echo $uni_price; ?>" />
															<?php if ($uni_eur == 0 || $uni_eur == 3) { ?>
															<span class="uni_red"><span id="uni_price_int"><?php echo explode('.', number_format($uni_price, 2, ".", ""))[0]; ?></span><span class="uni_sub">.<span id="uni_price_dec"><?php echo sprintf('%02d', explode('.', number_format($uni_price, 2, ".", ""))[1]); ?></span>&nbsp;<?php echo $uni_sign; ?></span></span>
															<?php }else{ ?>
															<span class="uni_red"><span id="uni_price_int"><?php echo explode('.', number_format($uni_price, 2, ".", ""))[0]; ?></span><span class="uni_sub">.<span id="uni_price_dec"><?php echo sprintf('%02d', explode('.', number_format($uni_price, 2, ".", ""))[1]); ?></span>&nbsp;<?php echo $uni_sign; ?></span> <span style="font-size:70%;">(<span id="uni_price_second_int"><?php echo explode('.', number_format($uni_price_second, 2, ".", ""))[0]; ?></span><span class="uni_sub">.<span id="uni_price_second_dec"><?php echo sprintf('%02d', explode('.', number_format($uni_price_second, 2, ".", ""))[1]); ?></span>&nbsp;<?php echo $uni_sign_second; ?></span>)</span></span>
															<?php } ?>
														</div>
													</div>
													<div class="<?php echo $uni_gpr_container_row; ?>">
														<div class="<?php echo $uni_gpr_column; ?>">
															<span class="uni_red"><?php echo $uni_meseci_txt; ?></span>
														</div>
														<div class="<?php echo $uni_gpr_column_right; ?>">
															<select id="uni_pogasitelni_vnoski_input" class="<?php echo $uni_txt_right; ?>">
															<?php if ($uni_meseci_3 && $uni_mesecna_3 != 0){ ?>
																<option value="3" <?php if ($uni_shema_current === 3){echo "selected";} ?>>3 месеца</option>
															<?php } ?>
															<?php if ($uni_meseci_4 && $uni_mesecna_4 != 0){ ?>
																<option value="4" <?php if ($uni_shema_current === 4){echo "selected";} ?>>4 месеца</option>
															<?php } ?>
															<?php if ($uni_meseci_5 && $uni_mesecna_5 != 0){ ?>
																<option value="5" <?php if ($uni_shema_current === 5){echo "selected";} ?>>5 месеца</option>
															<?php } ?>
															<?php if ($uni_meseci_6 && $uni_mesecna_6 != 0){ ?>
																<option value="6" <?php if ($uni_shema_current === 6){echo "selected";} ?>>6 месеца</option>
															<?php } ?>
															<?php if ($uni_meseci_9 && $uni_mesecna_9 != 0){ ?>
																<option value="9" <?php if ($uni_shema_current === 9){echo "selected";} ?>>9 месеца</option>
															<?php } ?>
															<?php if ($uni_meseci_10 && $uni_mesecna_10 != 0){ ?>
																<option value="10" <?php if ($uni_shema_current === 10){echo "selected";} ?>>10 месеца</option>
															<?php } ?>
															<?php if ($uni_meseci_12 && $uni_mesecna_12 != 0){ ?>
																<option value="12" <?php if ($uni_shema_current === 12){echo "selected";} ?>>12 месеца</option>
															<?php } ?>
															<?php if ($uni_meseci_15 && $uni_mesecna_15 != 0){ ?>
																<option value="15" <?php if ($uni_shema_current === 15){echo "selected";} ?>>15 месеца</option>
															<?php } ?>
															<?php if ($uni_meseci_18 && $uni_mesecna_18 != 0){ ?>
																<option value="18" <?php if ($uni_shema_current === 18){echo "selected";} ?>>18 месеца</option>
															<?php } ?>
															<?php if ($uni_meseci_24 && $uni_mesecna_24 != 0){ ?>
																<option value="24" <?php if ($uni_shema_current === 24){echo "selected";} ?>>24 месеца</option>
															<?php } ?>
															<?php if ($uni_meseci_30 && $uni_mesecna_30 != 0){ ?>
																<option value="30" <?php if ($uni_shema_current === 30){echo "selected";} ?>>30 месеца</option>
															<?php } ?>
															<?php if ($uni_meseci_36 && $uni_mesecna_36 != 0){ ?>
																<option value="36" <?php if ($uni_shema_current === 36){echo "selected";} ?>>36 месеца</option>
															<?php } ?>
														</select>
														</div>
													</div>
													<div class="<?php echo $uni_gpr_container_row; ?>">
														<div class="<?php echo $uni_gpr_column; ?>">
															<?php echo $uni_vnoska_txt; ?>
														</div>
														<div class="<?php echo $uni_gpr_column_right; ?>">
															<?php if ($uni_eur == 0 || $uni_eur == 3) { ?>
															<span class="uni_red"><span id="uni_vnoska_int"></span><span class="uni_sub">.<span id="uni_vnoska_dec"></span>&nbsp;<?php echo $uni_sign; ?></span></span>
															<?php }else{ ?>
															<span class="uni_red"><span id="uni_vnoska_int"></span><span class="uni_sub">.<span id="uni_vnoska_dec"></span>&nbsp;<?php echo $uni_sign; ?></span> <span style="font-size:70%;">(<span id="uni_vnoska_second_int"></span><span class="uni_sub">.<span id="uni_vnoska_second_dec"></span>&nbsp;<?php echo $uni_sign_second; ?></span>)</span></span>
															<?php } ?>
														</div>
													</div>
													<div class="<?php echo $uni_gpr_container_row; ?>">
														<div class="<?php echo $uni_gpr_column; ?>">
															ГЛП
														</div>
														<div class="<?php echo $uni_gpr_column_right; ?>">
															<span class="uni_red"><span id="uni_glp_int"></span>%</span>
														</div>
													</div>
													<div class="<?php echo $uni_gpr_container_row; ?>">
														<div class="<?php echo $uni_gpr_column; ?>">
															ГПР
														</div>
														<div class="<?php echo $uni_gpr_column_right; ?>">
															<span class="uni_red"><span id="uni_gpr_int"></span>%</span>
														</div>
													</div>
													<div class="<?php echo $uni_gpr_container_row; ?>">
														<div class="<?php echo $uni_panel_help_text; ?>">* Срокът на изплащане се заявява при приключване на поръчката.</div>
													</div>
												</div>
											</div>
										</div>
										<div style="padding-bottom:20px;"></div>
										<div class="uni_buttons">
											<div class="<?php echo $uni_btn_seccondary; ?>" id="uni_back_unicredit">
												<div class="<?php echo $uni_btn_seccondary_inner; ?>">
													<div class="uni_btn_seccondary_inner_text">Откажи</div>
												</div>
											</div>
											<div class="<?php echo $uni_btn_primary; ?>" id="uni_buy_unicredit">
												<div class="<?php echo $notify_badge; ?>"></div>
												<div class="<?php echo $uni_btn_primary_inner; ?>">
													<div class="uni_btn_primary_inner_text">Добавете в количката</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
		}
	}

	function UNI_RATE($nper, $pmt, $pv, $fv = 0.0, $type = 0, $guess = 0.1)    {
		$rate = $guess;
		if (abs($rate) < UNI_FINANCIAL_PRECISION) {
			$y = $pv * (1 + $nper * $rate) + $pmt * (1 + $rate * $type) * $nper + $fv;
		} else {
			$f = exp($nper * log(1 + $rate));
			$y = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;
		}
		$y0 = $pv + $pmt * $nper + $fv;
		$y1 = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;
		// find root by secant method
		$i  = $x0 = 0.0;
		$x1 = $rate;
		while ((abs($y0 - $y1) > UNI_FINANCIAL_PRECISION) && ($i < UNI_FINANCIAL_MAX_ITERATIONS)) {
			$rate = ($y1 * $x0 - $y0 * $x1) / ($y1 - $y0);
			$x0 = $x1;
			$x1 = $rate;
			if (abs($rate) < UNI_FINANCIAL_PRECISION) {
				$y = $pv * (1 + $nper * $rate) + $pmt * (1 + $rate * $type) * $nper + $fv;
			} else {
				$f = exp($nper * log(1 + $rate));
				$y = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;
			}
			$y0 = $y1;
			$y1 = $y;
			++$i;
		}
		return $rate;
	}

	add_action( 'wp_ajax_unipayment_product', 'unipayment_product' );
	add_action( 'wp_ajax_nopriv_unipayment_product', 'unipayment_product' );

	function unipayment_product() {
		$json = array();
		
		if (isset($_REQUEST['uni_vnoski'])) {
			$uni_vnoski = (int)$_REQUEST['uni_vnoski'];
		} else {
			$uni_vnoski = 12;
		}
		if (isset($_REQUEST['uni_price'])) {
			$uni_price = (float)$_REQUEST['uni_price'];
		} else {
			$uni_price = 0;
		}
		if (isset($_REQUEST['uni_param_kimb_3'])) {
			$uni_param_kimb_3 = (float)$_POST['uni_param_kimb_3'];
		} else {
			$uni_param_kimb_3 = 0;
		}
		if (isset($_REQUEST['uni_param_kimb_4'])) {
			$uni_param_kimb_4 = (float)$_REQUEST['uni_param_kimb_4'];
		} else {
			$uni_param_kimb_4 = 0;
		}
		if (isset($_REQUEST['uni_param_kimb_5'])) {
			$uni_param_kimb_5 = (float)$_REQUEST['uni_param_kimb_5'];
		} else {
			$uni_param_kimb_5 = 0;
		}
		if (isset($_REQUEST['uni_param_kimb_6'])) {
			$uni_param_kimb_6 = (float)$_REQUEST['uni_param_kimb_6'];
		} else {
			$uni_param_kimb_6 = 0;
		}
		if (isset($_REQUEST['uni_param_kimb_9'])) {
			$uni_param_kimb_9 = (float)$_REQUEST['uni_param_kimb_9'];
		} else {
			$uni_param_kimb_9 = 0;
		}
		if (isset($_REQUEST['uni_param_kimb_10'])) {
			$uni_param_kimb_10 = (float)$_REQUEST['uni_param_kimb_10'];
		} else {
			$uni_param_kimb_10 = 0;
		}
		if (isset($_REQUEST['uni_param_kimb_12'])) {
			$uni_param_kimb_12 = (float)$_REQUEST['uni_param_kimb_12'];
		} else {
			$uni_param_kimb_12 = 0;
		}
		if (isset($_REQUEST['uni_param_kimb_15'])) {
			$uni_param_kimb_15 = (float)$_REQUEST['uni_param_kimb_15'];
		} else {
			$uni_param_kimb_15 = 0;
		}
		if (isset($_REQUEST['uni_param_kimb_18'])) {
			$uni_param_kimb_18 = (float)$_REQUEST['uni_param_kimb_18'];
		} else {
			$uni_param_kimb_18 = 0;
		}
		if (isset($_REQUEST['uni_param_kimb_24'])) {
			$uni_param_kimb_24 = (float)$_REQUEST['uni_param_kimb_24'];
		} else {
			$uni_param_kimb_24 = 0;
		}
		if (isset($_REQUEST['uni_param_kimb_30'])) {
			$uni_param_kimb_30 = (float)$_REQUEST['uni_param_kimb_30'];
		} else {
			$uni_param_kimb_30 = 0;
		}
		if (isset($_REQUEST['uni_param_kimb_36'])) {
			$uni_param_kimb_36 = (float)$_REQUEST['uni_param_kimb_36'];
		} else {
			$uni_param_kimb_36 = 0;
		}
		
		$json['uni_mesecna_3'] = number_format($uni_price * floatval($uni_param_kimb_3), 2, ".", "");
		$uni_gprm_3 = ((UNI_RATE(3, -1 * (floatval($json['uni_mesecna_3'])), floatval($uni_price))* 3)) / (3 / 12);
		$json['uni_gpr_3'] = abs(number_format((pow((1 + floatval($uni_gprm_3) / 12), 12) -1) * 100, 2, ".", ""));
		$json['uni_mesecna_4'] = number_format($uni_price * floatval($uni_param_kimb_4), 2, ".", "");
		$uni_gprm_4 = ((UNI_RATE(4, -1 * (floatval($json['uni_mesecna_4'])), floatval($uni_price))* 4)) / (4 / 12);
		$json['uni_gpr_4'] = abs(number_format((pow((1 + floatval($uni_gprm_4) / 12), 12) -1) * 100, 2, ".", ""));
		$json['uni_mesecna_5'] = number_format($uni_price * floatval($uni_param_kimb_5), 2, ".", "");
		$uni_gprm_5 = ((UNI_RATE(5, -1 * (floatval($json['uni_mesecna_5'])), floatval($uni_price))* 5)) / (5 / 12);
		$json['uni_gpr_5'] = abs(number_format((pow((1 + floatval($uni_gprm_5) / 12), 12) -1) * 100, 2, ".", ""));
		$json['uni_mesecna_6'] = number_format($uni_price * floatval($uni_param_kimb_6), 2, ".", "");
		$uni_gprm_6 = ((UNI_RATE(6, -1 * (floatval($json['uni_mesecna_6'])), floatval($uni_price))* 6)) / (6 / 12);
		$json['uni_gpr_6'] = abs(number_format((pow((1 + floatval($uni_gprm_6) / 12), 12) -1) * 100, 2, ".", ""));
		$json['uni_mesecna_9'] = number_format($uni_price * floatval($uni_param_kimb_9), 2, ".", "");
		$uni_gprm_9 = ((UNI_RATE(9, -1 * (floatval($json['uni_mesecna_9'])), floatval($uni_price))* 9)) / (9 / 12);
		$json['uni_gpr_9'] = abs(number_format((pow((1 + floatval($uni_gprm_9) / 12), 12) -1) * 100, 2, ".", ""));
		$json['uni_mesecna_10'] = number_format($uni_price * floatval($uni_param_kimb_10), 2, ".", "");
		$uni_gprm_10 = ((UNI_RATE(10, -1 * (floatval($json['uni_mesecna_10'])), floatval($uni_price))* 10)) / (10 / 12);
		$json['uni_gpr_10'] = abs(number_format((pow((1 + floatval($uni_gprm_10) / 12), 12) -1) * 100, 2, ".", ""));
		$json['uni_mesecna_12'] = number_format($uni_price * floatval($uni_param_kimb_12), 2, ".", "");
		$uni_gprm_12 = ((UNI_RATE(12, -1 * (floatval($json['uni_mesecna_12'])), floatval($uni_price))* 12)) / (12 / 12);
		$json['uni_gpr_12'] = abs(number_format((pow((1 + floatval($uni_gprm_12) / 12), 12) -1) * 100, 2, ".", ""));
		$json['uni_mesecna_15'] = number_format($uni_price * floatval($uni_param_kimb_15), 2, ".", "");
		$uni_gprm_15 = ((UNI_RATE(15, -1 * (floatval($json['uni_mesecna_15'])), floatval($uni_price))* 15)) / (15 / 12);
		$json['uni_gpr_15'] = abs(number_format((pow((1 + floatval($uni_gprm_15) / 12), 12) -1) * 100, 2, ".", ""));
		$json['uni_mesecna_18'] = number_format($uni_price * floatval($uni_param_kimb_18), 2, ".", "");
		$uni_gprm_18 = ((UNI_RATE(18, -1 * (floatval($json['uni_mesecna_18'])), floatval($uni_price))* 18)) / (18 / 12);
		$json['uni_gpr_18'] = abs(number_format((pow((1 + floatval($uni_gprm_18) / 12), 12) -1) * 100, 2, ".", ""));
		$json['uni_mesecna_24'] = number_format($uni_price * floatval($uni_param_kimb_24), 2, ".", "");
		$uni_gprm_24 = ((UNI_RATE(24, -1 * (floatval($json['uni_mesecna_24'])), floatval($uni_price))* 24)) / (24 / 12);
		$json['uni_gpr_24'] = abs(number_format((pow((1 + floatval($uni_gprm_24) / 12), 12) -1) * 100, 2, ".", ""));
		$json['uni_mesecna_30'] = number_format($uni_price * floatval($uni_param_kimb_30), 2, ".", "");
		$uni_gprm_30 = ((UNI_RATE(30, -1 * (floatval($json['uni_mesecna_30'])), floatval($uni_price))* 30)) / (30 / 12);
		$json['uni_gpr_30'] = abs(number_format((pow((1 + floatval($uni_gprm_30) / 12), 12) -1) * 100, 2, ".", ""));
		$json['uni_mesecna_36'] = number_format($uni_price * floatval($uni_param_kimb_36), 2, ".", "");
		$uni_gprm_36 = ((UNI_RATE(36, -1 * (floatval($json['uni_mesecna_36'])), floatval($uni_price))* 36)) / (36 / 12);
		$json['uni_gpr_36'] = abs(number_format((pow((1 + floatval($uni_gprm_36) / 12), 12) -1) * 100, 2, ".", ""));
		
		echo (json_encode($json));
		die();
	}
	
	function uni_get_cached_params($cid) {
		if (empty($cid)) return null;

		$cache_key = 'uni_params_' . md5($cid);
		$cached = get_transient($cache_key);

		if ($cached !== false) {
			return $cached;
		}

		$url = UNI_LIVEURL . '/function/getparameters.php?cid=' . urlencode($cid);
		$response = wp_remote_get($url, [
			'timeout' => 5,
			'redirection' => 3,
			'sslverify' => false
		]);

		if (is_wp_error($response)) return null;

		$code = wp_remote_retrieve_response_code($response);
		if ($code !== 200) return null;

		$body = wp_remote_retrieve_body($response);
		$data = json_decode($body, true);

		if (!is_array($data) || !isset($data['uni_status']) || $data['uni_status'] !== 'Yes') {
			return null;
		}

		set_transient($cache_key, $data, 10 * MINUTE_IN_SECONDS);
		return $data;
	}

	function uni_get_cached_calculation($cid, $deviceis = 'pc') {
		if (empty($cid)) return null;

		$cache_key = 'uni_calc_' . md5($cid . '_' . $deviceis);
		$cached = get_transient($cache_key);

		if ($cached !== false) {
			return $cached;
		}

		$url = UNI_LIVEURL . '/function/getcalculation.php?cid=' . urlencode($cid) . '&deviceis=' . urlencode($deviceis);

		$response = wp_remote_get($url, [
			'timeout' => 6,
			'redirection' => 3,
			'sslverify' => false
		]);

		if (is_wp_error($response)) return null;

		$code = wp_remote_retrieve_response_code($response);
		if ($code !== 200) return null;

		$body = wp_remote_retrieve_body($response);
		$data = json_decode($body, true);

		if (!is_array($data) || empty($data)) {
			return null;
		}

		set_transient($cache_key, $data, 10 * MINUTE_IN_SECONDS);
		return $data;
	}
