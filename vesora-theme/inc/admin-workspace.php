<?php
/** Vesora's administration presentation. Authentication and permissions stay native. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_filter( 'admin_body_class', static function ( $classes ) { return $classes . ' vesora-admin'; } );
add_action( 'admin_enqueue_scripts', static function () {
	if ( is_network_admin() ) { return; }
	wp_enqueue_style( 'vesora-admin-workspace', get_template_directory_uri() . '/assets/css/admin-workspace.css', array( 'dashicons' ), VESORA_VERSION );
	wp_enqueue_script( 'vesora-admin-workspace', get_template_directory_uri() . '/assets/js/admin-workspace.js', array(), VESORA_VERSION, true );
}, 100 );

add_action( 'admin_menu', static function () {
	global $menu;
	$labels = array( 'index.php' => 'Tổng quan', 'upload.php' => 'Thư viện', 'edit.php?post_type=product' => 'Sản phẩm', 'edit.php?post_type=vesora_sales' => 'Nhân sự', 'users.php' => 'Tài khoản' );
	foreach ( $menu as &$item ) { if ( isset( $labels[ $item[2] ] ) ) { $item[0] = $labels[ $item[2] ]; } }
	unset( $item );
}, 110 );

/** Links are independently capability-gated, never a substitute for WP authorization. */
function vesora_workspace_links() {
	$items = array(
		array( 'title' => 'Sản phẩm', 'description' => 'Ảnh, mô tả, màu và độ dài tóc.', 'icon' => 'products', 'url' => 'edit.php?post_type=product', 'cap' => 'edit_products', 'add' => 'post-new.php?post_type=product', 'type' => 'product' ),
		array( 'title' => 'Blogs', 'description' => 'Viết bài, chỉnh ảnh bìa và xuất bản.', 'icon' => 'edit-page', 'url' => 'edit.php', 'cap' => 'edit_posts', 'add' => 'post-new.php', 'type' => 'post' ),
		array( 'title' => 'Khách hàng tiềm năng', 'description' => 'Theo dõi yêu cầu tư vấn và trạng thái xử lý.', 'icon' => 'email-alt', 'url' => 'edit.php?post_type=vesora_lead', 'cap' => 'manage_options' ),
		array( 'title' => 'Trang chủ', 'description' => 'Banner, video, chứng chỉ và hình ảnh.', 'icon' => 'format-gallery', 'url' => 'admin.php?page=vesora-homepage-images', 'cap' => 'edit_theme_options' ),
		array( 'title' => 'Đội ngũ tư vấn', 'description' => 'Hồ sơ nhân sự, WhatsApp và Instagram.', 'icon' => 'businessperson', 'url' => 'edit.php?post_type=vesora_sales', 'cap' => 'edit_posts', 'type' => 'vesora_sales' ),
		array( 'title' => 'Thông tin liên hệ', 'description' => 'Địa chỉ văn phòng, nhà máy và kênh liên hệ.', 'icon' => 'location', 'url' => 'admin.php?page=vesora-contact-settings', 'cap' => 'edit_theme_options' ),
		array( 'title' => 'Thư viện ảnh & tài liệu', 'description' => 'Tải lên, tìm và sử dụng lại media.', 'icon' => 'format-image', 'url' => 'upload.php', 'cap' => 'upload_files' ),
		array( 'title' => 'Tài khoản & phân quyền', 'description' => 'Quản lý tài khoản và vai trò nhân viên.', 'icon' => 'admin-users', 'url' => 'users.php', 'cap' => 'list_users' ),
	);
	return array_values( array_filter( $items, static function ( $item ) {
		if ( ! current_user_can( $item['cap'] ) ) { return false; }
		if ( isset( $item['type'] ) ) {
			$type = get_post_type_object( $item['type'] );
			return $type && current_user_can( $type->cap->edit_posts );
		}
		return true;
	} ) );
}

function vesora_workspace_posts( $type, $statuses, $limit = 5 ) {
	$object = get_post_type_object( $type );
	if ( ! $object || ! current_user_can( $object->cap->edit_posts ) ) { return null; }
	$args = array( 'post_type' => $type, 'post_status' => $statuses, 'posts_per_page' => $limit, 'orderby' => 'modified', 'order' => 'DESC', 'ignore_sticky_posts' => true );
	if ( ! current_user_can( $object->cap->edit_others_posts ) ) { $args['author'] = get_current_user_id(); }
	return new WP_Query( $args );
}

function vesora_workspace_recent_list( $type, $title ) {
	$query = vesora_workspace_posts( $type, array( 'publish', 'draft', 'pending', 'future', 'private' ) );
	if ( ! $query ) { return; }
	?>
	<section class="vesora-workspace-panel">
		<header><h2><?php echo esc_html( $title ); ?></h2><a href="<?php echo esc_url( admin_url( 'edit.php' . ( 'post' === $type ? '' : '?post_type=' . $type ) ) ); ?>">Xem tất cả <span aria-hidden="true">→</span></a></header>
		<?php if ( $query->posts ) : ?>
		<ul class="vesora-recent-list">
			<?php foreach ( $query->posts as $entry ) : ?>
				<?php if ( ! current_user_can( 'edit_post', $entry->ID ) ) { continue; } ?>
				<li>
					<div><a href="<?php echo esc_url( get_edit_post_link( $entry->ID, 'raw' ) ); ?>"><?php echo esc_html( $entry->post_title ?: 'Chưa đặt tiêu đề' ); ?></a><span>Cập nhật <?php echo esc_html( get_the_modified_date( 'd/m/Y', $entry ) ); ?></span></div>
					<span class="vesora-status vesora-status--<?php echo esc_attr( $entry->post_status ); ?>"><?php echo esc_html( array( 'publish' => 'Đã đăng', 'draft' => 'Bản nháp', 'pending' => 'Chờ duyệt', 'future' => 'Đã lên lịch', 'private' => 'Riêng tư' )[ $entry->post_status ] ?? $entry->post_status ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php else : ?><p class="vesora-workspace-empty">Chưa có nội dung. Bạn có thể bắt đầu bằng thao tác tạo mới phía trên.</p><?php endif; ?>
	</section>
	<?php
}

function vesora_workspace_dashboard() {
	$screen = get_current_screen();
	if ( ! $screen || 'dashboard' !== $screen->id || ! current_user_can( 'read' ) ) { return; }
	$user = wp_get_current_user();
	$links = vesora_workspace_links();
	?>
	<div class="vesora-workspace">
		<header class="vesora-workspace-heading">
			<div><p class="vesora-workspace-brand">VESORA HAIR / WORKSPACE</p><h1>Xin chào, <?php echo esc_html( $user->display_name ); ?>.</h1><p>Chọn công việc bạn muốn thực hiện hôm nay.</p></div>
			<a class="button vesora-view-site" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener">Xem website <span class="dashicons dashicons-external" aria-hidden="true"></span></a>
		</header>
		<?php if ( $links ) : ?>
		<nav class="vesora-workspace-actions" aria-label="Thao tác nhanh">
			<?php foreach ( $links as $item ) : ?>
				<?php if ( empty( $item['add'] ) ) { continue; } $object = get_post_type_object( $item['type'] ); if ( ! $object || ! current_user_can( $object->cap->create_posts ) ) { continue; } ?>
				<a class="button button-primary" href="<?php echo esc_url( admin_url( $item['add'] ) ); ?>"><span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span><?php echo 'post' === $item['type'] ? 'Viết blog mới' : 'Thêm sản phẩm'; ?></a>
			<?php endforeach; ?>
			<?php if ( current_user_can( 'upload_files' ) ) : ?><a class="button" href="<?php echo esc_url( admin_url( 'media-new.php' ) ); ?>">Tải ảnh / tài liệu</a><?php endif; ?>
		</nav>
		<div class="vesora-workspace-metrics">
			<?php foreach ( array( 'product' => 'Sản phẩm đã đăng', 'post' => 'Bài blog đã đăng' ) as $type => $label ) : $query = vesora_workspace_posts( $type, array( 'publish' ), 1 ); if ( ! $query ) { continue; } ?>
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . $type . '&post_status=publish' ) ); ?>"><span><?php echo esc_html( $label ); ?></span><strong><?php echo esc_html( number_format_i18n( $query->found_posts ) ); ?></strong><small>Mở danh sách <span aria-hidden="true">↗</span></small></a>
			<?php endforeach; ?>
			<?php $drafts = vesora_workspace_posts( 'post', array( 'draft' ), 1 ); if ( $drafts ) : ?>
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_status=draft' ) ); ?>"><span>Blog đang soạn</span><strong><?php echo esc_html( number_format_i18n( $drafts->found_posts ) ); ?></strong><small>Tiếp tục chỉnh sửa <span aria-hidden="true">↗</span></small></a>
			<?php endif; ?>
			<?php if ( current_user_can( 'manage_options' ) && function_exists( 'vesora_new_lead_count' ) ) : ?>
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=vesora_lead' ) ); ?>"><span>Yêu cầu tư vấn mới</span><strong><?php echo esc_html( number_format_i18n( vesora_new_lead_count() ) ); ?></strong><small>Mở Vesora Leads <span aria-hidden="true">↗</span></small></a>
			<?php endif; ?>
		</div>
		<section class="vesora-workspace-section"><h2>Quản lý website</h2>
			<div class="vesora-workspace-links">
			<?php foreach ( $links as $item ) : ?>
				<a href="<?php echo esc_url( admin_url( $item['url'] ) ); ?>"><span class="dashicons dashicons-<?php echo esc_attr( $item['icon'] ); ?>" aria-hidden="true"></span><div><h3><?php echo esc_html( $item['title'] ); ?></h3><p><?php echo esc_html( $item['description'] ); ?></p></div><span class="vesora-link-arrow" aria-hidden="true">↗</span></a>
			<?php endforeach; ?>
			</div>
		</section>
		<?php else : ?>
			<p class="vesora-workspace-panel">Tài khoản này chưa có quyền biên tập. Liên hệ quản trị viên nếu bạn cần quản lý nội dung, hoặc <a href="<?php echo esc_url( admin_url( 'profile.php' ) ); ?>">cập nhật hồ sơ cá nhân</a>.</p>
		<?php endif; ?>
		<div class="vesora-workspace-recent">
			<?php vesora_workspace_recent_list( 'post', 'Blog cập nhật gần đây' ); ?>
			<?php vesora_workspace_recent_list( 'product', 'Sản phẩm cập nhật gần đây' ); ?>
		</div>
		<section class="vesora-workspace-guide"><span class="dashicons dashicons-info-outline" aria-hidden="true"></span><div><h2>Trước khi xuất bản</h2><p>Kiểm tra ảnh đại diện, nội dung và đường dẫn. Dùng Preview để xem trước; chỉ bấm Publish / Update khi đã sẵn sàng. Tài khoản của bạn chỉ hiển thị những khu vực được cấp quyền.</p><a href="<?php echo esc_url( admin_url( 'profile.php' ) ); ?>">Hồ sơ & mật khẩu của tôi</a></div></section>
	</div>
	<?php
}
add_action( 'all_admin_notices', 'vesora_workspace_dashboard', 20 );

add_action( 'wp_dashboard_setup', static function () {
	// Replace generic editorial widgets; retain Site Health, updates and plugin alerts.
	foreach ( array( 'dashboard_quick_press', 'dashboard_primary' ) as $id ) { remove_meta_box( $id, 'dashboard', 'side' ); }
	foreach ( array( 'dashboard_right_now', 'dashboard_activity' ) as $id ) { remove_meta_box( $id, 'dashboard', 'normal' ); }
}, 100 );

add_action( 'login_enqueue_scripts', static function () {
	wp_enqueue_style( 'vesora-login', get_template_directory_uri() . '/assets/css/admin-login.css', array( 'login' ), VESORA_VERSION );
	$logo = get_template_directory_uri() . '/assets/images/Logo-desktop-black.png';
	wp_add_inline_style( 'vesora-login', '.login h1 a{background-image:url("' . esc_url( $logo ) . '");}' );
} );
add_filter( 'login_headerurl', static function () { return home_url( '/' ); } );
add_filter( 'login_headertext', static function () { return 'Vesora Hair'; } );
add_action( 'login_header', static function () {
	?>
	<aside class="vesora-login-story" aria-label="Vesora Hair Workspace">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/Logo-desktop-black.png' ); ?>" width="2099" height="698" alt="Vesora Hair"></a>
		<div><p class="vesora-login-eyebrow">DÀNH CHO ĐỘI NGŨ VESORA</p><h2>Chăm chút từng nội dung.<br>Kết nối từng khách hàng.</h2><p>Không gian làm việc để cập nhật sản phẩm, chia sẻ câu chuyện và quản lý website Vesora Hair.</p><ul><li>Sản phẩm & bộ sưu tập</li><li>Blogs & hình ảnh thương hiệu</li><li>Khách hàng & đội ngũ tư vấn</li></ul></div>
		<p class="vesora-login-note">Vesora Hair · Premium Vietnamese Hair for Global Beauty</p>
	</aside>
	<?php
} );
add_filter( 'login_message', static function ( $message ) {
	$action = isset( $_REQUEST['action'] ) && is_string( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : 'login';
	$titles = array( 'login' => array( 'Chào mừng trở lại', 'Đăng nhập bằng tài khoản được cấp để tiếp tục công việc.' ), 'lostpassword' => array( 'Khôi phục mật khẩu', 'Nhập tên đăng nhập hoặc email để nhận hướng dẫn đặt lại mật khẩu.' ), 'rp' => array( 'Đặt mật khẩu mới', 'Chọn mật khẩu mạnh và chỉ sử dụng cho tài khoản này.' ), 'resetpass' => array( 'Đặt mật khẩu mới', 'Chọn mật khẩu mạnh và chỉ sử dụng cho tài khoản này.' ) );
	if ( ! isset( $titles[ $action ] ) ) { return $message; }
	return '<div class="vesora-login-intro"><h2>' . esc_html( $titles[ $action ][0] ) . '</h2><p>' . esc_html( $titles[ $action ][1] ) . '</p></div>' . $message;
} );
