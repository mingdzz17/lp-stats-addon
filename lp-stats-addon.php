<?php
/**
 * Plugin Name: LearnPress Stats Dashboard
 * Description: Hiển thị thống kê khóa học và học viên cho bài tập môn Phần mềm mã nguồn mở.
 * Version: 1.0
 * Author: Trần Quang Minh
 */

// Bảo mật: Không cho phép truy cập trực tiếp vào file
if ( ! defined( 'ABSPATH' ) ) exit;

class LP_Stats_Addon {

    public function __construct() {
        // Tạo Widget trong trang Admin Dashboard
        add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
        // Tạo Shortcode [lp_total_stats] để dùng ngoài trang chủ
        add_shortcode( 'lp_total_stats', array( $this, 'render_stats_html' ) );
    }

    /**
     * Hàm lấy dữ liệu thống kê từ LearnPress
     */
    private function get_stats() {
        global $wpdb;

        // 1. Tổng số khóa học (Post type: lp_course)
        $total_courses = wp_count_posts( 'lp_course' )->publish;

        // 2. Tổng số học viên (Lấy tổng số User trừ đi Admin)
        $user_count = count_users();
        $total_students = $user_count['total_users'];

        // 3. Số lượng khóa học đã được hoàn thành (Status: completed)
        $table_name = $wpdb->prefix . 'learnpress_user_items';
        $completed_courses = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE item_type = 'lp_course' AND status = 'completed'" );

        return array(
            'courses'   => $total_courses ? $total_courses : 0,
            'students'  => $total_students ? $total_students : 0,
            'completed' => $completed_courses ? $completed_courses : 0,
        );
    }

    /**
     * Đăng ký Widget với WordPress
     */
    public function add_dashboard_widget() {
        wp_add_dashboard_widget(
            'lp_stats_dashboard_widget',
            'LearnPress Overview Stats - Minh',
            array( $this, 'display_widget_content' )
        );
    }

    public function display_widget_content() {
        echo $this->render_stats_html();
    }

    /**
     * Giao diện hiển thị thống kê
     */
    public function render_stats_html() {
        $data = $this->get_stats();
        ob_start();
        ?>
        <div style="background: #fff; padding: 15px; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
            <p style="font-size: 14px;">📊 <strong>Tổng số khóa học:</strong> <span style="color: #2271b1;"><?php echo $data['courses']; ?></span></p>
            <hr>
            <p style="font-size: 14px;">👥 <strong>Tổng số học viên:</strong> <span style="color: #2271b1;"><?php echo $data['students']; ?></span></p>
            <hr>
            <p style="font-size: 14px;">✅ <strong>Khóa học hoàn thành:</strong> <span style="color: #d63638;"><?php echo $data['completed']; ?></span></p>
        </div>
        <?php
        return ob_get_clean();
    }
}

// Khởi chạy plugin
new LP_Stats_Addon();