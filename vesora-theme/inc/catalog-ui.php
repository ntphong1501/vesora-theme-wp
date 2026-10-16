<?php
/** Catalog filters and editorial tools. Existing product attributes remain authoritative. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function vesora_catalog_selection( $key ) {
	$value = isset( $_GET[ $key ] ) ? wp_unslash( $_GET[ $key ] ) : array(); // Read-only GET filters.
	$values = array_filter( (array) $value, 'is_string' );
	return array_values( array_unique( array_filter( array_map( 'sanitize_title', array_slice( $values, 0, 30 ) ) ) ) );
}

/** A cached lookup supports both local Woo attributes and global attribute taxonomies. */
function vesora_catalog_index() {
	$index = get_transient( 'vesora_catalog_facets_v1' );
	if ( false !== $index ) { return $index; }
	$index = array();
	$posts = get_posts( array( 'post_type' => 'product', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'ID', 'order' => 'ASC' ) );
	foreach ( $posts as $post ) {
		$product = wc_get_product( $post->ID );
		if ( ! $product || 'hidden' === $product->get_catalog_visibility() || 'search' === $product->get_catalog_visibility() ) { continue; }
		$row = array( 'color' => array(), 'length' => array(), 'categories' => $product->get_category_ids(), 'image' => $product->get_image_id() );
		foreach ( $product->get_attributes() as $attribute ) {
			$name = str_replace( array( 'pa_', '-', '_' ), array( '', ' ', ' ' ), strtolower( $attribute->get_name() ) );
			$kind = in_array( $name, array( 'color', 'colour', 'product color', 'product colour' ), true ) ? 'color' : ( in_array( $name, array( 'length', 'product length' ), true ) ? 'length' : '' );
			if ( ! $kind ) { continue; }
			$values = $attribute->is_taxonomy() ? wc_get_product_terms( $post->ID, $attribute->get_name(), array( 'fields' => 'names' ) ) : $attribute->get_options();
			foreach ( $values as $value ) {
				$value = trim( wp_strip_all_tags( $value ) );
				if ( '' !== $value ) { $row[ $kind ][ sanitize_title( $value ) ] = $value; }
			}
		}
		$index[ $post->ID ] = $row;
	}
	set_transient( 'vesora_catalog_facets_v1', $index, HOUR_IN_SECONDS );
	return $index;
}

function vesora_catalog_invalidate_index() { delete_transient( 'vesora_catalog_facets_v1' ); }
add_action( 'woocommerce_new_product', 'vesora_catalog_invalidate_index' );
add_action( 'woocommerce_update_product', 'vesora_catalog_invalidate_index' );
add_action( 'woocommerce_delete_product', 'vesora_catalog_invalidate_index' );
add_action( 'save_post_product', 'vesora_catalog_invalidate_index' );
add_action( 'set_object_terms', 'vesora_catalog_invalidate_index' );
add_action( 'edited_term', 'vesora_catalog_invalidate_index' );
add_action( 'deleted_term', 'vesora_catalog_invalidate_index' );
add_action( 'trashed_post', 'vesora_catalog_invalidate_index' );
add_action( 'untrashed_post', 'vesora_catalog_invalidate_index' );

function vesora_catalog_filter_query( $query ) {
	$colors = vesora_catalog_selection( 'vesora_color' );
	$lengths = vesora_catalog_selection( 'vesora_length' );
	if ( ! $colors && ! $lengths ) { return; }
	$matches = array();
	foreach ( vesora_catalog_index() as $id => $row ) {
		if ( ( ! $colors || array_intersect( $colors, array_keys( $row['color'] ) ) ) && ( ! $lengths || array_intersect( $lengths, array_keys( $row['length'] ) ) ) ) { $matches[] = $id; }
	}
	$existing = $query->get( 'post__in' );
	if ( $existing ) { $matches = array_intersect( $matches, $existing ); }
	$query->set( 'post__in', $matches ? array_values( $matches ) : array( 0 ) );
}
add_action( 'woocommerce_product_query', 'vesora_catalog_filter_query' );

function vesora_product_summary_facts() {
	global $product;
	if ( ! $product ) { return; }
	$facts = array();
	foreach ( $product->get_attributes() as $attribute ) {
		if ( ! $attribute->get_visible() ) { continue; }
		$label = wc_attribute_label( $attribute->get_name(), $product );
		$values = $attribute->is_taxonomy() ? wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'names' ) ) : $attribute->get_options();
		if ( $values ) { $facts[ $label ] = implode( ', ', $values ); }
	}
	if ( ! $facts ) { return; }
	echo '<dl class="vesora-product-facts">';
	foreach ( $facts as $label => $value ) { echo '<dt>' . esc_html( $label ) . '</dt><dd>' . esc_html( $value ) . '</dd>'; }
	echo '</dl>';
}
add_action( 'woocommerce_single_product_summary', 'vesora_product_summary_facts', 25 );

function vesora_catalog_base_url() {
	if ( is_product_taxonomy() ) {
		$url = get_term_link( get_queried_object() );
		if ( ! is_wp_error( $url ) ) { return $url; }
	}
	return is_search() ? home_url( '/' ) : wc_get_page_permalink( 'shop' );
}

/** Always available, including zero-result combinations. Woo owns the ordering query. */
function vesora_catalog_ordering() {
	$options = apply_filters( 'woocommerce_catalog_orderby', array(
		'menu_order' => __( 'Default sorting', 'vesora' ),
		'popularity' => __( 'Sort by popularity', 'vesora' ),
		'date' => __( 'Sort by latest', 'vesora' ),
		'price' => __( 'Sort by price: low to high', 'vesora' ),
		'price-desc' => __( 'Sort by price: high to low', 'vesora' ),
	) );
	$selected = isset( $_GET['orderby'] ) && is_string( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : get_option( 'woocommerce_default_catalog_orderby', 'menu_order' );
	?>
	<form class="woocommerce-ordering vesora-ordering" method="get" action="<?php echo esc_url( vesora_catalog_base_url() ); ?>">
		<label class="screen-reader-text" for="vesora-orderby"><?php esc_html_e( 'Sort products', 'vesora' ); ?></label>
		<select name="orderby" id="vesora-orderby" class="orderby">
			<?php foreach ( $options as $value => $label ) : ?><option value="<?php echo esc_attr( $value ); ?>" <?php selected( $selected, $value ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?>
		</select>
		<?php wc_query_string_form_fields( null, array( 'orderby', 'submit', 'paged', 'product-page' ) ); ?>
		<noscript><button type="submit"><?php esc_html_e( 'Sort', 'vesora' ); ?></button></noscript>
	</form>
	<?php
}

function vesora_catalog_filters() {
	$selected = array( 'color' => vesora_catalog_selection( 'vesora_color' ), 'length' => vesora_catalog_selection( 'vesora_length' ) );
	$facets = array( 'color' => array(), 'length' => array() );
	$category_ids = array();
	if ( is_product_category() ) {
		$term_id = get_queried_object_id();
		$category_ids = array_merge( array( $term_id ), get_term_children( $term_id, 'product_cat' ) );
	}
	foreach ( vesora_catalog_index() as $row ) {
		if ( $category_ids && ! array_intersect( $category_ids, $row['categories'] ) ) { continue; }
		foreach ( $facets as $kind => $unused ) {
			foreach ( $row[ $kind ] as $slug => $label ) {
				if ( ! isset( $facets[ $kind ][ $slug ] ) ) { $facets[ $kind ][ $slug ] = array( 'label' => $label, 'image' => $row['image'] ); }
			}
		}
	}
	foreach ( $facets as &$facet ) { uasort( $facet, static function ( $a, $b ) { return strnatcasecmp( $a['label'], $b['label'] ); } ); }
	unset( $facet );
	?>
	<aside class="vesora-filters" aria-label="<?php esc_attr_e( 'Product filters', 'vesora' ); ?>">
		<details open>
			<summary><?php esc_html_e( 'Filter products', 'vesora' ); ?></summary>
			<form method="get" action="<?php echo esc_url( vesora_catalog_base_url() ); ?>">
				<?php foreach ( array( 'color' => 'Filter by Product Color', 'length' => 'Filter by Product Length' ) as $kind => $title ) : ?>
				<fieldset><legend><?php echo esc_html( $title ); ?></legend>
					<div class="vesora-filter-options vesora-filter-options--<?php echo esc_attr( $kind ); ?>">
					<?php foreach ( $facets[ $kind ] as $slug => $option ) : ?>
						<label class="vesora-filter-option">
							<input type="checkbox" name="vesora_<?php echo esc_attr( $kind ); ?>[]" value="<?php echo esc_attr( $slug ); ?>" <?php checked( in_array( $slug, $selected[ $kind ], true ) ); ?>>
							<span class="vesora-filter-option__tile">
							<?php if ( 'color' === $kind && $option['image'] ) : ?>
								<span class="vesora-filter-swatch"><?php echo wp_get_attachment_image( $option['image'], 'woocommerce_thumbnail', false, array( 'alt' => '', 'loading' => 'lazy' ) ); ?></span>
							<?php endif; ?>
							<span><?php echo esc_html( $option['label'] ); ?></span>
							</span>
						</label>
					<?php endforeach; ?>
					</div>
					<?php if ( ! $facets[ $kind ] ) : ?><p class="vesora-filter-empty"><?php esc_html_e( 'No options available in this collection.', 'vesora' ); ?></p><?php endif; ?>
				</fieldset>
				<?php endforeach; ?>
				<?php wc_query_string_form_fields( null, array( 'vesora_color', 'vesora_length', 'submit', 'paged', 'product-page' ) ); ?>
				<div class="vesora-filter-actions"><button type="submit" class="button"><?php esc_html_e( 'Apply filters', 'vesora' ); ?></button>
				<a href="<?php echo esc_url( is_search() ? add_query_arg( array( 's' => get_search_query(), 'post_type' => 'product' ), vesora_catalog_base_url() ) : vesora_catalog_base_url() ); ?>"><?php esc_html_e( 'Clear filters', 'vesora' ); ?></a></div>
			</form>
		</details>
	</aside>
	<?php
}

function vesora_catalog_long_description() {
	if ( ! is_product_category() || is_paged() ) { return; }
	$content = get_term_meta( get_queried_object_id(), 'vesora_catalog_content', true );
	if ( $content ) {
		echo '<section class="vesora-catalog-editorial entry-content" aria-label="' . esc_attr__( 'About this collection', 'vesora' ) . '">' . wp_kses_post( wpautop( $content ) ) . '</section>';
	}
}

function vesora_catalog_term_editor( $term ) {
	$content = $term instanceof WP_Term ? get_term_meta( $term->term_id, 'vesora_catalog_content', true ) : '';
	$editing = $term instanceof WP_Term;
	echo $editing ? '<tr class="form-field"><th scope="row"><label for="vesora_catalog_content">Giới thiệu chi tiết danh mục</label></th><td>' : '<div class="form-field"><label for="vesora_catalog_content">Giới thiệu chi tiết danh mục</label>';
	wp_nonce_field( 'vesora_catalog_content', 'vesora_catalog_nonce' );
	wp_editor( $content, 'vesora_catalog_content', array( 'textarea_name' => 'vesora_catalog_content', 'textarea_rows' => 12, 'media_buttons' => true ) );
	echo '<p class="description">Hiển thị dưới danh sách sản phẩm. Chèn ảnh/tài liệu bằng Add Media, bảng bằng nút Chèn bảng, hoặc link tới Google Sheets. Phần Description phía trên dùng cho giới thiệu ngắn bên dưới banner.</p>';
	echo $editing ? '</td></tr>' : '</div>';
}
add_action( 'product_cat_edit_form_fields', 'vesora_catalog_term_editor' );
add_action( 'product_cat_add_form_fields', 'vesora_catalog_term_editor' );

function vesora_catalog_save_term_content( $term_id ) {
	$taxonomy = get_taxonomy( 'product_cat' );
	if ( ! $taxonomy || ! current_user_can( $taxonomy->cap->edit_terms ) || ! isset( $_POST['vesora_catalog_nonce'], $_POST['vesora_catalog_content'] ) || ! is_string( $_POST['vesora_catalog_nonce'] ) || ! is_string( $_POST['vesora_catalog_content'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['vesora_catalog_nonce'] ) ), 'vesora_catalog_content' ) ) { return; }
	update_term_meta( $term_id, 'vesora_catalog_content', wp_kses_post( wp_unslash( $_POST['vesora_catalog_content'] ) ) );
}
add_action( 'created_product_cat', 'vesora_catalog_save_term_content' );
add_action( 'edited_product_cat', 'vesora_catalog_save_term_content' );

function vesora_catalog_editor_screen() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	return $screen && ( 'product' === $screen->post_type || 'product_cat' === $screen->taxonomy );
}
add_filter( 'mce_external_plugins', static function ( $plugins ) {
	if ( vesora_catalog_editor_screen() ) { $plugins['vesora_table'] = get_template_directory_uri() . '/assets/js/admin-product-editor.js?ver=' . VESORA_VERSION; }
	return $plugins;
} );
add_filter( 'mce_buttons', static function ( $buttons ) {
	if ( vesora_catalog_editor_screen() ) { $buttons[] = 'vesora_table'; }
	return $buttons;
} );
add_action( 'admin_enqueue_scripts', static function () {
	if ( vesora_catalog_editor_screen() ) { wp_enqueue_media(); }
} );
add_action( 'wp_enqueue_scripts', static function () {
	if ( function_exists( 'is_woocommerce' ) && is_woocommerce() && ! is_product() ) {
		wp_enqueue_script( 'vesora-catalog', get_template_directory_uri() . '/assets/js/catalog.js', array(), VESORA_VERSION, true );
	}
} );
add_action( 'add_meta_boxes_product', static function () {
	add_meta_box( 'vesora-product-help', 'Biên tập nội dung sản phẩm', static function () {
		echo '<p><strong>Mô tả chính:</strong> dùng trình soạn thảo bên trên để thêm tiêu đề, ảnh, bảng thông số và liên kết. Nội dung xuất hiện trong tab About this hair.</p><p><strong>Add Media:</strong> chèn ảnh hoặc link tải PDF/Excel. Dùng Insert link để dẫn tới Google Sheets. Nút <strong>Chèn bảng</strong> tạo bảng có thể nhập nội dung trực tiếp.</p><p><strong>Product short description:</strong> giới thiệu ngắn bên cạnh ảnh sản phẩm.</p><p><strong>Product data → Attributes:</strong> nhập Color và Length, phân tách các lựa chọn bằng dấu | (ví dụ 16 inch | 18 inch). Bộ lọc và tab Specifications lấy từ dữ liệu này. Có thể dùng thuộc tính toàn cục cùng tên.</p><p><strong>Product image / Product gallery:</strong> ảnh đại diện và các ảnh chi tiết. Bấm Update để lưu.</p>';
	}, 'product', 'normal', 'high' );
} );
