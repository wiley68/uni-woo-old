<?php
	function uniKopLoad(bool $reload): array {
		$orderby = 'name';
		$order = 'asc';
		$hide_empty = false ;
		$cat_args = array(
			'orderby'    => $orderby,
			'order'      => $order,
			'hide_empty' => $hide_empty,
			'parent'     => 0
		);
		$product_categories = get_terms( 'product_cat', $cat_args );

		$uni_categories = [];
		foreach ($product_categories as $category) {
			if ($reload) {
				$uni_categories[] = array(
					'category_id' => $category->term_id,
					'kop'         => '',
					'promo'       => '',
					'kimb'        => '',
					'kimb_time'   => '',
					'stats'       => array (
						'kimb_3'  =>  '',
						'glp_3'   =>    '',
						'kimb_4'  =>  '',
						'glp_4'   =>    '',
						'kimb_5'  =>  '',
						'glp_5'   =>    '',
						'kimb_6'  =>    '',
						'glp_6'   =>    '',
						'kimb_9'  =>    '',
						'glp_9'   =>    '',
						'kimb_10' =>    '',
						'glp_10'  =>    '',
						'kimb_12' =>    '',
						'glp_12'  =>    '',
						'kimb_15' =>    '',
						'glp_15'  =>    '',
						'kimb_18' =>    '',
						'glp_18'  =>    '',
						'kimb_24' =>    '',
						'glp_24'  =>    '',
						'kimb_30' =>    '',
						'glp_30'  =>    '',
						'kimb_36' =>    '',
						'glp_36'  =>    ''
					)
				);
			} else {
				$uni_categories[] = array(
					'category_id' => $category->term_id,
					'name'        => $category->name,
					'kop'         => '',
					'promo'       => '',
					'kimb'        => '',
					'kimb_time'   => '',
					'stats'       => array (
						'kimb_3'  =>  '',
						'glp_3'   =>    '',
						'kimb_4'  =>  '',
						'glp_4'   =>    '',
						'kimb_5'  =>  '',
						'glp_5'   =>    '',
						'kimb_6'  =>    '',
						'glp_6'   =>    '',
						'kimb_9'  =>    '',
						'glp_9'   =>    '',
						'kimb_10' =>    '',
						'glp_10'  =>    '',
						'kimb_12' =>    '',
						'glp_12'  =>    '',
						'kimb_15' =>    '',
						'glp_15'  =>    '',
						'kimb_18' =>    '',
						'glp_18'  =>    '',
						'kimb_24' =>    '',
						'glp_24'  =>    '',
						'kimb_30' =>    '',
						'glp_30'  =>    '',
						'kimb_36' =>    '',
						'glp_36'  =>    ''
					)
				);
			}
		}

		$uni_categories_kop = [];
		if (file_exists(plugin_dir_path( __FILE__ ) . "../keys/kop.json")) {
			$kopdata = file_get_contents(plugin_dir_path( __FILE__ ) . "../keys/kop.json");
			$uni_categories_kop = json_decode($kopdata, true);
			for ($i = 0; $i < sizeof($uni_categories); $i++) {
				foreach ($uni_categories_kop as $uni_category_kop) {
					if ((int)$uni_categories[$i]['category_id'] == (int)$uni_category_kop['category_id']) {
						$uni_categories[$i]['kop'] = $uni_category_kop['kop'];
						$uni_categories[$i]['promo'] = $uni_category_kop['promo'];
						$uni_categories[$i]['kimb'] = $reload ? '' : $uni_category_kop['kimb'];
						$uni_categories[$i]['kimb_time'] = $reload ? '' : $uni_category_kop['kimb_time'];
						$uni_categories[$i]['stats']['kimb_3'] = $reload ? '' : $uni_category_kop['stats']['kimb_3'];
						$uni_categories[$i]['stats']['glp_3'] = $reload ? '' : $uni_category_kop['stats']['glp_3'];
						$uni_categories[$i]['stats']['kimb_4'] = $reload ? '' : $uni_category_kop['stats']['kimb_4'];
						$uni_categories[$i]['stats']['glp_4'] = $reload ? '' : $uni_category_kop['stats']['glp_4'];
						$uni_categories[$i]['stats']['kimb_5'] = $reload ? '' : $uni_category_kop['stats']['kimb_5'];
						$uni_categories[$i]['stats']['glp_5'] = $reload ? '' : $uni_category_kop['stats']['glp_5'];
						$uni_categories[$i]['stats']['kimb_6'] = $reload ? '' : $uni_category_kop['stats']['kimb_6'];
						$uni_categories[$i]['stats']['glp_6'] = $reload ? '' : $uni_category_kop['stats']['glp_6'];
						$uni_categories[$i]['stats']['kimb_9'] = $reload ? '' : $uni_category_kop['stats']['kimb_9'];
						$uni_categories[$i]['stats']['glp_9'] = $reload ? '' : $uni_category_kop['stats']['glp_9'];
						$uni_categories[$i]['stats']['kimb_10'] = $reload ? '' : $uni_category_kop['stats']['kimb_10'];
						$uni_categories[$i]['stats']['glp_10'] = $reload ? '' : $uni_category_kop['stats']['glp_10'];
						$uni_categories[$i]['stats']['kimb_12'] = $reload ? '' : $uni_category_kop['stats']['kimb_12'];
						$uni_categories[$i]['stats']['glp_12'] = $reload ? '' : $uni_category_kop['stats']['glp_12'];
						$uni_categories[$i]['stats']['kimb_15'] = $reload ? '' : $uni_category_kop['stats']['kimb_15'];
						$uni_categories[$i]['stats']['glp_15'] = $reload ? '' : $uni_category_kop['stats']['glp_15'];
						$uni_categories[$i]['stats']['kimb_18'] = $reload ? '' : $uni_category_kop['stats']['kimb_18'];
						$uni_categories[$i]['stats']['glp_18'] = $reload ? '' : $uni_category_kop['stats']['glp_18'];
						$uni_categories[$i]['stats']['kimb_24'] = $reload ? '' : $uni_category_kop['stats']['kimb_24'];
						$uni_categories[$i]['stats']['glp_24'] = $reload ? '' : $uni_category_kop['stats']['glp_24'];
						$uni_categories[$i]['stats']['kimb_30'] = $reload ? '' : $uni_category_kop['stats']['kimb_30'];
						$uni_categories[$i]['stats']['glp_30'] = $reload ? '' : $uni_category_kop['stats']['glp_30'];
						$uni_categories[$i]['stats']['kimb_36'] = $reload ? '' : $uni_category_kop['stats']['kimb_36'];
						$uni_categories[$i]['stats']['glp_36'] = $reload ? '' : $uni_category_kop['stats']['glp_36'];
						break;
					}
				}
			}
		}

		return $uni_categories;
	}

	if(array_key_exists('uni_hidden', $_POST) && $_POST['uni_hidden'] == 'Y') {
		if (array_key_exists('unipayment_status', $_POST)){
			$unipayment_status = $_POST['unipayment_status'];
		}else{
			$unipayment_status = '';
		}
		update_option('unipayment_status', $unipayment_status);
		if (array_key_exists('unipayment_unicid', $_POST)){
			$unipayment_unicid = $_POST['unipayment_unicid'];
		}else{
			$unipayment_unicid = '';
		}
		update_option('unipayment_unicid', $unipayment_unicid);
		if (array_key_exists('unipayment_reklama', $_POST)){
			$unipayment_reklama = $_POST['unipayment_reklama'];
		}else{
			$unipayment_reklama = '';
		}
		update_option('unipayment_reklama', $unipayment_reklama);
		if (array_key_exists('unipayment_cart', $_POST)){
			$unipayment_cart = $_POST['unipayment_cart'];
		}else{
			$unipayment_cart = '';
		}
		update_option('unipayment_cart', $unipayment_cart);
		if (array_key_exists('unipayment_debug', $_POST)){
			$unipayment_debug = $_POST['unipayment_debug'];
		}else{
			$unipayment_debug = '';
		}
		update_option('unipayment_debug', $unipayment_debug);
		if (array_key_exists('unipayment_gap', $_POST)){
			$unipayment_gap = $_POST['unipayment_gap'];
		}else{
			$unipayment_gap = 0;
		}
		update_option('unipayment_gap', $unipayment_gap);
		?>
		<div class="updated"><p><strong><?php echo 'Настройките са записани успешно.'; ?></strong></p></div>
		<?php
	} else {
		$unipayment_status = get_option('unipayment_status');
		$unipayment_unicid = get_option('unipayment_unicid');
		$unipayment_reklama = get_option('unipayment_reklama');
		$unipayment_cart = get_option('unipayment_cart');
		$unipayment_debug = get_option('unipayment_debug');
		$unipayment_gap = get_option('unipayment_gap') == "" ? 0 : intval(get_option('unipayment_gap'));
	}

	if (isset($_POST['uni_force_cache_refresh_btn']) && check_admin_referer('uni_force_cache_refresh_action', 'uni_force_cache_refresh_nonce')) {
		$cid = get_option('unipayment_unicid');
		if (!empty($cid)) {
			delete_transient('uni_params_' . md5($cid));
			delete_transient('uni_calc_' . md5($cid . '_pc'));
			delete_transient('uni_calc_' . md5($cid . '_mobile'));

			$uni_categories_empty = uniKopLoad(true);
			$jsondata = json_encode($uni_categories_empty, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
			if ( ! function_exists( 'WP_Filesystem' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}
			global $wp_filesystem;
			WP_Filesystem();
			$file_path = plugin_dir_path(__FILE__) . "../keys/kop.json";
			if ( is_object($wp_filesystem) && $wp_filesystem->put_contents( $file_path, $jsondata, FS_CHMOD_FILE ) ) {
				echo '<div class="updated"><p><strong>Кешът е обновен успешно.</strong></p></div>';
			} else {
				echo '<div class="error"><p>Неуспешно обновяване на кеш файла! Моля, проверете правата на папката<code>/keys</code>.</p></div>';
			}
		}
	}

	/** mapping KOP */
	$uni_categories = uniKopLoad(false);
	/** mapping KOP */
?>
<div class="wrap">
	<?php    echo "<h2>" . 'УНИ Кредит - всички настройки на модула' . "</h2>"; ?>
	<form name="uni_form" method="post" enctype="multipart/form-data" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="uni_hidden" value="Y">
		
		<?php    echo "<h4>Системни настройки</h4>"; ?>
		<table cellspacing="4" cellpadding="4" border="0" width="900px">
			<tr>
				<td width="300px" style="vertical-align:top;">
				UNI Credit покупки на Кредит
				</td>
				<td width="600px;" style="vertical-align:top;">
					<input type="checkbox" class="checkbox" id="unipayment_status" name="unipayment_status" <?php if ($unipayment_status == 'on') {echo 'checked';} ?> />
					<span style="font-size:80%;">Дава възможност на Вашите клиенти да закупуват стока на изплащане с UNI Credit.</span>
				</td>
			</tr>
			<tr>
				<td width="300px" style="vertical-align:top;">
				Уникален идентификационен код на магазина Ви
				</td>
				<td width="600px;" style="vertical-align:top;">
					<input type="text" name="unipayment_unicid" value="<?php echo $unipayment_unicid; ?>" size="36" style="width:300px;"><br />
					<span style="font-size:80%;">Уникален идентификационен код на магазина Ви в системата на УНИ Кредит.</span>
				</td>
			</tr>
			<tr>
				<td width="300px" style="vertical-align:top;">
				Визуализиране на реклама
				</td>
				<td width="600px;" style="vertical-align:top;">
					<input type="checkbox" class="checkbox" id="unipayment_reklama" name="unipayment_reklama" <?php if ($unipayment_reklama == 'on') {echo 'checked';} ?> />
					<span style="font-size:80%;">Можете да включвате или изключвате показването на реклама в началната страница на магазина.</span>
				</td>
			</tr>
			<tr>
				<td width="300px" style="vertical-align:top;">
				Директно добавяне на продукта в кошницата
				</td>
				<td width="600px;" style="vertical-align:top;">
					<input type="checkbox" class="checkbox" id="unipayment_cart" name="unipayment_cart" <?php if ($unipayment_cart == 'on') {echo 'checked';} ?> />
					<span style="font-size:80%;">Ако изберете тази опция, при натискане на бутона на калкулатора в продуктовата страница, избрания продукт директно ще се добавя в кошницата.</span>
				</td>
			</tr>
			<tr>
				<td width="300px" style="vertical-align:top;">
				Режим отстраняване на грешки
				</td>
				<td width="600px;" style="vertical-align:top;">
					<input type="checkbox" class="checkbox" id="unipayment_debug" name="unipayment_debug" <?php if ($unipayment_debug == 'on') {echo 'checked';} ?> />
					<span style="font-size:80%;">Моля изберете тази опция ако искате да включите режима за отстраняване на грешки</span>
				</td>
			</tr>
			<tr>
				<td width="300px" style="vertical-align:top;">
				Свободно място над бутона
				</td>
				<td width="600px;" style="vertical-align:top;">
					<input type="number" name="unipayment_gap" value="<?php echo $unipayment_gap; ?>" step="1" min="0" style="width:300px;"><br />
					<span style="font-size:80%;">Свободно място над бутона в px.</span>
				</td>
			</tr>
		</table>
		<hr />
		<p class="submit">
		<input type="submit" name="Submit" class="button button-primary" value="<?php echo 'Запиши промените'; ?>" />
		<form method="post">
			<?php wp_nonce_field('uni_force_cache_refresh_action', 'uni_force_cache_refresh_nonce'); ?>
			<input type="submit" name="uni_force_cache_refresh_btn" class="button button-secondary" value="Ръчно обнови кеша на параметрите">
			<p class="description">Натисни бутона, ако искаш веднага да презаредиш конфигурацията от сървъра на УНИ Кредит.</p>
		</form>
		</p>
	</form>
</div>
<hr />
<?php if ($uni_categories){ ?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><i class="fa fa-pencil"></i>Съответствие между Категории и КОП на SmartUCF UNI Credit системата</h3>
	</div>
	<div class="panel-body">
	
		<div style="display:flex;">
			<div style="width:100px;">
				<span class="input-group-addon"><strong>№ категория</strong></span>
			</div>
			<div style="width:400px;">
				<span class="input-group-addon"><strong>Име на категория</strong></span>
			</div>
			<div style="width:300px;">
				<span class="input-group-addon"><strong>Стандартен КОП</strong></span>
			</div>
			<div>
				<span class="input-group-addon"><strong>Промо КОП</strong></span>
			</div>
		</div>
	<?php foreach ($uni_categories as $category){ ?>
		<div style="display:flex;">
			<div style="width:100px;">
				<span class="input-group-addon" name="uni_category_id">
					<?php echo $category['category_id']; ?>
				</span>
			</div>
			<div style="width:400px;">
				<span class="input-group-addon" name="uni_category_name">
					<?php echo $category['name']; ?>
				</span>
			</div>
			<div style="width:300px;">
				<input type="text" name="uni_category_kop" value="<?php echo $category['kop']; ?>" aria-label="...">
			</div>
			<div>
				<input type="text" name="uni_category_promo" value="<?php echo $category['promo']; ?>" aria-label="...">
			</div>
		</div>
		<input type="hidden" name="uni_category_kimb" value="<?php echo $category['kimb']; ?>">
		<input type="hidden" name="uni_category_kimb_time" value="<?php echo $category['kimb_time']; ?>">
		<input type="hidden" name="uni_category_kimb_3" value="<?php echo $category['stats']['kimb_3']; ?>">
		<input type="hidden" name="uni_category_glp_3" value="<?php echo $category['stats']['glp_3']; ?>">
		<input type="hidden" name="uni_category_kimb_4" value="<?php echo $category['stats']['kimb_4']; ?>">
		<input type="hidden" name="uni_category_glp_4" value="<?php echo $category['stats']['glp_4']; ?>">
		<input type="hidden" name="uni_category_kimb_5" value="<?php echo $category['stats']['kimb_5']; ?>">
		<input type="hidden" name="uni_category_glp_5" value="<?php echo $category['stats']['glp_5']; ?>">
		<input type="hidden" name="uni_category_kimb_6" value="<?php echo $category['stats']['kimb_6']; ?>">
		<input type="hidden" name="uni_category_glp_6" value="<?php echo $category['stats']['glp_6']; ?>">
		<input type="hidden" name="uni_category_kimb_9" value="<?php echo $category['stats']['kimb_9']; ?>">
		<input type="hidden" name="uni_category_glp_9" value="<?php echo $category['stats']['glp_9']; ?>">
		<input type="hidden" name="uni_category_kimb_10" value="<?php echo $category['stats']['kimb_10']; ?>">
		<input type="hidden" name="uni_category_glp_10" value="<?php echo $category['stats']['glp_10']; ?>">
		<input type="hidden" name="uni_category_kimb_12" value="<?php echo $category['stats']['kimb_12']; ?>">
		<input type="hidden" name="uni_category_glp_12" value="<?php echo $category['stats']['glp_12']; ?>">
		<input type="hidden" name="uni_category_kimb_15" value="<?php echo $category['stats']['kimb_15']; ?>">
		<input type="hidden" name="uni_category_glp_15" value="<?php echo $category['stats']['glp_15']; ?>">
		<input type="hidden" name="uni_category_kimb_18" value="<?php echo $category['stats']['kimb_18']; ?>">
		<input type="hidden" name="uni_category_glp_18" value="<?php echo $category['stats']['glp_18']; ?>">
		<input type="hidden" name="uni_category_kimb_24" value="<?php echo $category['stats']['kimb_24']; ?>">
		<input type="hidden" name="uni_category_glp_24" value="<?php echo $category['stats']['glp_24']; ?>">
		<input type="hidden" name="uni_category_kimb_30" value="<?php echo $category['stats']['kimb_30']; ?>">
		<input type="hidden" name="uni_category_glp_30" value="<?php echo $category['stats']['glp_30']; ?>">
		<input type="hidden" name="uni_category_kimb_36" value="<?php echo $category['stats']['kimb_36']; ?>">
		<input type="hidden" name="uni_category_glp_36" value="<?php echo $category['stats']['glp_36']; ?>">
	<?php } ?>
	<hr />
	<button class="button button-primary" id="uni_btn_create_kop">Запиши промените в табицата на съответствия на КОП</button>
	</div>
</div>
<?php } ?>