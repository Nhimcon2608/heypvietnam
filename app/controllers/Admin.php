<?php

class Admin {
    public function settings() {
        $settings = $this->settingModel->getAllSettings();
        $data = [];
        $data['error'] = '';
        $data['success'] = '';
        $data['activeTab'] = 'account';
        $data['siteSettings'] = [];
        $admin = $this->adminModel->getAdminById($_SESSION['admin_id']);
        $data['admin'] = $admin;

        // Retrieve all settings as key-value pairs
        if (!empty($settings)) {
            foreach ($settings as $setting) {
                $data['siteSettings'][$setting->setting_key] = $setting->setting_value;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if (isset($_POST['tab']) && $_POST['tab'] == 'account') {
                // ... existing code for account tab ...
            } elseif (isset($_POST['tab']) && $_POST['tab'] == 'password') {
                // ... existing code for password tab ...
            } elseif (isset($_POST['tab']) && $_POST['tab'] == 'site') {
                $data['activeTab'] = 'site';

                // Danh sách các trường dữ liệu cài đặt website
                $siteSettings = [
                    'site_name', 'site_description', 'site_keywords',
                    // Nội dung trang chủ
                    'home_title', 'home_subtitle', 'home_description', 'home_button_text', 'home_button_url',
                    // Trang chủ - Phần Giới thiệu
                    'home_about_title', 'home_about_content', 'home_about_button_text',
                    // Trang Giới thiệu
                    'about_title', 'about_hero_description',
                    'about_story_title', 'about_story_content',
                    'about_mission_title', 'about_mission_content',
                    'about_values_title',
                    'about_cta', 'about_cta_description', 'about_cta_button',
                    // Dịch vụ
                    'services_title', 'services_description',
                    'service1_title', 'service1_icon', 'service1_description',
                    'service2_title', 'service2_icon', 'service2_description',
                    'service3_title', 'service3_icon', 'service3_description',
                    // Sản phẩm nổi bật
                    'featured_products_title', 'featured_products_description',
                    // Thông tin liên hệ
                    'contact_email', 'contact_phone', 'contact_address',
                    // Mạng xã hội
                    'social_facebook', 'social_instagram', 'social_youtube',
                    // Footer
                    'footer_text', 'copyright_text'
                ];

                foreach ($siteSettings as $key) {
                    if (isset($_POST[$key])) {
                        // Check if setting already exists
                        if ($this->settingModel->getSettingByKey($key)) {
                            // Update existing setting
                            $this->settingModel->updateSetting($key, $_POST[$key]);
                        } else {
                            // Create new setting
                            $this->settingModel->createSetting($key, $_POST[$key]);
                        }
                        // Update the data array for display
                        $data['siteSettings'][$key] = $_POST[$key];
                    }
                }

                $data['success'] = 'Cài đặt website đã được cập nhật thành công!';
            }
        }

        $this->view('admin/settings', $data);
    }
} 