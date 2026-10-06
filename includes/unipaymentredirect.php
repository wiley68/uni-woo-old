<?php

class UnipaymentredirectPage {
	private static $instance = null;

	private $page_slug = 'unipaymentredirect';
	private $page_title = 'УНИ Кредит';

	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_filter('the_posts', [$this, 'detectPost']);
	}

	private function getParam($param, $default = '', $sanitize_callback = 'sanitize_text_field', $max_length = 512) {
		$value = '';

		if (isset($_POST[$param])) {
			$value = $_POST[$param];
		} elseif (isset($_GET[$param])) {
			$value = $_GET[$param];
		} else {
			return $default;
		}

		if (is_array($value)) return $default;

		$value = substr($value, 0, $max_length);
		$value = call_user_func($sanitize_callback, $value);
		return $value;
	}

	private function getAllParams($default = '') {
		$params = [];

		foreach (array_merge($_GET, $_POST) as $key => $value) {
			if (is_array($value)) continue;
			$clean_key = sanitize_key($key);
			$params[$clean_key] = substr(wp_strip_all_tags($value), 0, 512);
		}

		return $params;
	}

	public function detectPost($posts) {
		global $wp, $wp_query;

		if (strtolower($wp->request) === strtolower($this->page_slug) || (isset($wp->query_vars['page_id']) && $wp->query_vars['page_id'] == $this->page_slug)) {
			$posts = [];
			$posts[] = $this->createPost();

			$wp_query->is_page = true;
			$wp_query->is_singular = false;
			$wp_query->is_home = false;
			$wp_query->is_archive = false;
			$wp_query->is_category = false;
			$wp_query->is_404 = false;
			unset($wp_query->query['error']);
			$wp_query->query_vars['error'] = '';
		}

		return $posts;
	}

	private function createPost() {
		$post = new stdClass();
		$post->post_type = 'page';
		$post->post_author = 1;
		$post->post_name = $this->page_slug;
		$post->guid = get_bloginfo('wpurl') . '/' . $this->page_slug;
		$post->post_title = '';
		$post->post_content = $this->getContent();
		$post->ID = -1;
		$post->post_status = 'static';
		$post->comment_status = 'closed';
		$post->ping_status = 'closed';
		$post->comment_count = 0;
		$post->post_date = current_time('mysql');
		$post->post_date_gmt = current_time('mysql', 1);
		return $post;
	}

	private function getContent() {
		if (count($_REQUEST) > 100) return 'Твърде много параметри.';
		
		$uni_proces2 = intval($this->getParam('uni_proces2', 0, 'intval'));
		if ($uni_proces2 !== 1) return '';

		$order_id = $this->getParam('order_id');
		$uni_fname = $this->getParam('uni_fname');
		$uni_lname = $this->getParam('uni_lname');
		$uni_egn = $this->getParam('uni_egn');
		$uni_phone = $this->getParam('uni_phone');
		$uni_phone2 = $this->getParam('uni_phone2');
		$uni_email = $this->getParam('uni_email', '', 'sanitize_email');
		$uni_shipping_address = $this->getParam('uni_shipping_address');
		$uni_kop = $this->getParam('uni_kop');
		$uni_description = $this->getParam('uni_description');
		$uni_total = floatval($this->getParam('uni_total', 0, 'floatval'));
		$uni_parva = floatval($this->getParam('uni_parva', 0, 'floatval'));
		$uni_vnoski = intval($this->getParam('uni_vnoski', 0, 'intval'));
		$uni_mesecna = floatval($this->getParam('uni_mesecna', 0, 'floatval'));
		$uni_gpr = $this->getParam('uni_gpr');
		$uni_eur = intval($this->getParam('uni_eur', 0, 'intval'));

		$result_items = '';
		if (isset($_REQUEST['result_items']) && strlen($_REQUEST['result_items']) < 5000) {
			$result_items = wp_kses_post(urldecode(base64_decode($_REQUEST['result_items'])));
		}

		$uni_obshta = $uni_vnoski * $uni_mesecna;
		$uni_sign = 'лева';
		$uni_sign_second = 'евро';
		$uni_total_second = $uni_mesecna_second = $uni_obshta_second = 0;

		switch ($uni_eur) {
			case 1:
				$uni_total_second = number_format($uni_total / 1.95583, 2, ".", "");
				$uni_mesecna_second = number_format($uni_mesecna / 1.95583, 2, ".", "");
				$uni_obshta_second = number_format($uni_obshta / 1.95583, 2, ".", "");
				break;
			case 2:
				$uni_total_second = number_format($uni_total * 1.95583, 2, ".", "");
				$uni_mesecna_second = number_format($uni_mesecna * 1.95583, 2, ".", "");
				$uni_obshta_second = number_format($uni_obshta * 1.95583, 2, ".", "");
				$uni_sign = 'евро';
				$uni_sign_second = 'лева';
				break;
			case 3:
				$uni_sign = 'евро';
				$uni_sign_second = 'лева';
				break;
		}

		ob_start();
		?>
		<span class="uni_result">Резултат от заявката.</span><br><br>
		<span class="uni_subresult">Заявката е изпратена успешно.</span><br><br>
		Заявка за лизинг с UNI Credit.<br><br>
		Поръчка №: <?= esc_html($order_id) ?><br>
		Име: <?= esc_html($uni_fname) ?><br>
		Фамилия: <?= esc_html($uni_lname) ?><br>
		ЕГН: <?= esc_html($uni_egn) ?><br>
		Телефон: <?= esc_html($uni_phone) ?><br>
		Втори телефон: <?= esc_html($uni_phone2) ?><br>
		E-Mail: <?= esc_html($uni_email) ?><br>
		Адрес за доставка: <?= esc_html($uni_shipping_address) ?><br>
		KOP: <?= esc_html($uni_kop) ?><br>
		Коментар: <?= esc_html($uni_description) ?><br>
		<?= $result_items ?>
		Цена на стоките (<?= $uni_sign ?>/<?= $uni_sign_second ?>): <?= esc_html($uni_total) ?> / <?= esc_html($uni_total_second) ?><br>
		Първоначална вноска (<?= $uni_sign ?>): <?= esc_html($uni_parva) ?><br>
		Брой вноски: <?= esc_html($uni_vnoski) ?><br>
		Месечна вноска (<?= $uni_sign ?>/<?= $uni_sign_second ?>): <?= esc_html($uni_mesecna) ?> / <?= esc_html($uni_mesecna_second) ?><br>
		ГПР (%): <?= esc_html($uni_gpr) ?><br>
		Обща сума (<?= $uni_sign ?>/<?= $uni_sign_second ?>): <?= esc_html($uni_obshta) ?> / <?= esc_html($uni_obshta_second) ?><br>
		<strong>Очаквайте контакт за потвърждаване на заявката.</strong><br>
		Можете да продължите с разглеждането на нашия магазин.
		<?php
		return ob_get_clean();
	}
}